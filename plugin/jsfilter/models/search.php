<?php
/**
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

class ExtendedFilterModel {

	public static function getResults($total = false, $getIdsForModule = false) {

		$db = JFactory::getDBO();
		$mainframe = JFactory::getApplication();
		$jshopConfig = JSFactory::getConfig();
		
		$cur_currency = $jshopConfig->cur_currency;
		$query = "SELECT currency_value FROM #__jshopping_currencies WHERE currency_id = {$cur_currency}";
		$db->setQuery($query);
		$cur_currency_value = (float)$db->loadResult();
		
		require_once (JPATH_SITE.DS.'modules'.DS.'mod_jshopping_extended_filter'.DS.'helper.php');
		$moduleId = $_REQUEST["moduleId"];
		$moduleParams = modJShopExtendedFilterHelper::getModuleParams($moduleId);
		
		$lang = JFactory::getLanguage();
		$lang_name = 'name_'.$lang->getTag();
		$short_desc = 'short_description_'.$lang->getTag();
		$description = 'description_'.$lang->getTag();
		
		$query  = "SELECT prod.*, prod.`{$lang_name}` as name, prod.`{$short_desc}` as short_description";
		$query .= ", prod.`{$description}` as description, prod.product_ean as code";
		$query .= ", prod.product_tax_id as tax_id, pr_cat.category_id as category_id";
		
		//price
		$query .= ", CASE 
						WHEN currency_config.value != 0 
							THEN CASE
									WHEN curMain.currency_value >= 1
										THEN prod.product_price * curMain.currency_value
									ELSE
										prod.product_price / curMain.currency_value	
								 END
						ELSE CASE
								WHEN cur.currency_value >= 1
									THEN prod.product_price * cur.currency_value
								ELSE
									prod.product_price / cur.currency_value	
							 END
					 END as price";
		
		//connected attrs
		foreach($_GET as $param=>$value) {
			preg_match('/^attr([0-9]+)$/', $param, $matches);
			if($matches) {
				$attr_id = $matches[1];
				
				$query .= ", GROUP_CONCAT(DISTINCT attr{$attr_id}_vals.attr_{$attr_id}) as attr{$attr_id}_values";
			}
		}

		//characteristic
		foreach($_GET as $param=>$value) {
			if($value == "") continue;
		
			preg_match('/^char[0-9]+$/', $param, $matches);
			if($matches) {
				preg_match_all('!\d+!', $param, $matches);
				$char_id = $matches[0][0];
				$query .= ", chars.extra_field_$char_id as extra_field_$char_id";
			}
		}
		
		$query .= ", GROUP_CONCAT(DISTINCT attr_n_vals.attr_value_id) as attr_n_values";
		$query .= " FROM #__jshopping_products AS prod ";
		
		$query .= "LEFT JOIN #__jshopping_products_to_categories AS pr_cat ON pr_cat.product_id = prod.product_id ";
		$query .= "LEFT JOIN #__jshopping_categories AS cat ON pr_cat.category_id = cat.category_id ";
		
		//join config
		$query .= "JOIN #__jshopping_configs AS currency_config ON `key` = 'default_frontend_currency'";

		//join currency
		$query .= "LEFT JOIN #__jshopping_currencies AS cur ON cur.currency_id = prod.currency_id ";
		$query .= "LEFT JOIN #__jshopping_currencies AS curMain ON curMain.currency_id = currency_config.value ";
		
		//connected attrs
		foreach($_GET as $param=>$value) {
			preg_match('/^attr([0-9]+)$/', $param, $matches);
			if($matches) {
				$attr_id = $matches[1];
				
				$query .= "LEFT JOIN #__jshopping_products_attr AS attr{$attr_id}_vals ON attr{$attr_id}_vals.product_id = prod.product_id ";
			}
		}
		
		$query .= "LEFT JOIN #__jshopping_products_attr2 AS attr_n_vals ON attr_n_vals.product_id = prod.product_id ";

		//join characteristics
		foreach($_GET as $param=>$value) {
			if($value == "") continue;
		
			preg_match('/^char[0-9]+$/', $param, $matches);
			if($matches) {
				preg_match_all('!\d+!', $param, $matches);
				$char_id = $matches[0][0];
				
				$query .= "LEFT JOIN #__jshopping_products_to_extra_fields AS chars ON chars.product_id = prod.product_id ";
				break;
			}
		}
		
		$query .= "WHERE prod.product_publish = '1' AND cat.category_publish = '1' ";
		
		//hide out of stock
		//$query .= "AND (prod.product_quantity > 0 OR prod.unlimited = 1) ";
		
		//category		
		$category = \JFactory::getApplication()->input->get("category");
		if($category) {
			if(!is_array($category)) {
				$category = Array($category);
			}
			foreach($category as $k=>$cat) {
				if($k == 0) {
					$query .= "AND (pr_cat.category_id IN ({$cat}";
				}
				else {
					$query .= " OR pr_cat.category_id IN ({$cat}";
				}
				$childs = modJShopExtendedFilterHelper::getCategoryChildren($cat);
				$childs = array_unique($childs);
				$childs = implode(",", $childs);

				if($childs) {
					$query .= ",".$childs;
				}
				$query .= ")";
				if(($k+1) == count($category)) {
					$query .= ") ";
				}
			}
		}

		//category restriction
			$restrict = $moduleParams->restrict;
			$restmode = $moduleParams->restmode;
			if($restmode == 0) {
				$restcat = $moduleParams->restcat;
			}
			else {
				$restcat = \JFactory::getApplication()->input->get("restcata", '');	
			}
			$restsub = $moduleParams->restsub;
	
			if($restrict == 1 && $restcat != '') {
				$restcat = str_replace(" ", "", $restcat);
				$restcat = explode(",", $restcat);
				
				$restcats = '';
				foreach($restcat as $k=>$catid) {
					if($k == 0) {
						$restcats .= $catid;
					}
					else {
						$restcats .= ",".$catid;
					}
					
					if($restsub == 1) {
						$childs = modJShopExtendedFilterHelper::getCategoryChildren($catid);
						$childs = array_unique($childs);
						$childs = implode(",", $childs);
						
						if($childs) {
							$restcats .= ",".$childs;
						}
					}
				}				
				
				$query .= "AND pr_cat.category_id IN ({$restcats}) ";
			}
			
		//title
		$title = addslashes(\JFactory::getApplication()->input->get('title'));
		$title = str_replace("+", "\\\\+", $title);
		$title = str_replace("(", "\\\\(", $title);
		$title = str_replace(")", "\\\\)", $title);
		
		if($title != '') {
			$title = explode(" ", $title);
			foreach($title as $word) {
				$query .= "AND UPPER(prod.`{$lang_name}`) REGEXP UPPER('^.*{$word}.*$') ";
			}
		}
		
		//title a-z
		$title_az = \JFactory::getApplication()->input->get('title_az', '');
		if($title_az != '') {
			$query .= "AND prod.`{$lang_name}` REGEXP '^{$title_az}.*$' ";
		}
		
		//title + text
		$text = addslashes(\JFactory::getApplication()->input->get('text'));
		$text = str_replace("+", "\\\\+", $text);
		$text = str_replace("(", "\\\\(", $text);
		$text = str_replace(")", "\\\\)", $text);
		
		if($text != '') {
			$text = explode(" ", $text);
			foreach($text as $word) {
				$query .= "AND (UPPER(prod.`{$lang_name}`) REGEXP UPPER('^.*{$word}.*$')";
				$query .= " OR UPPER(prod.`{$short_desc}`) REGEXP UPPER('^.*{$word}.*$')";
				$query .= " OR UPPER(prod.`{$description}`) REGEXP UPPER('^.*{$word}.*$')";
				$query .= ") ";
			}
		}
		
		//product code
		$code = \JFactory::getApplication()->input->get('code', '');
		if($code != '') {
			$query .= "AND UPPER(prod.product_ean) REGEXP UPPER('^{$code}.*$') ";
		}
		
		//products in stock
		$stock = \JFactory::getApplication()->input->get('stock', '');
		if($stock != '') {
			$query .= "AND prod.product_quantity > {$stock} ";
		}
		
		//manufacturer
		$manufacturer = \JFactory::getApplication()->input->get('manufacturer');
		if($manufacturer) {
			if(!is_array($manufacturer)) {
				$manufacturer = Array($manufacturer);
			}
			foreach($manufacturer as $k=>$manuf) {
				if($k == 0) {
					$query .= "AND (prod.product_manufacturer_id = {$manuf}";
				}
				else {
					$query .= " OR prod.product_manufacturer_id = {$manuf}";
				}
				if(($k+1) == count($manufacturer)) {
					$query .= ") ";
				}
			}
		}
	
		//label
		$label = \JFactory::getApplication()->input->get('label');
		if($label) {
			if(!is_array($label)) {
				$label = Array($label);
			}
			foreach($label as $k=>$lab) {
				if($k == 0) {
					$query .= "AND (prod.label_id = {$lab}";
				}
				else {
					$query .= " OR prod.label_id = {$lab}";
				}
				if(($k+1) == count($label)) {
					$query .= ") ";
				}				
			}
		}

		//date
		$date_from = \JFactory::getApplication()->input->get('date-from', '');
		$date_to = \JFactory::getApplication()->input->get('date-to', '');
					
		if($date_from != "" && $date_to == "") {
			$query .= "AND prod.product_date_added >= '{$date_from} 00:00:00' ";
		}
		if($date_from == "" && $date_to != "") {
			$query .= "AND prod.product_date_added <= '{$date_to} 23:59:59' ";
		}
		if($date_from != "" && $date_to != "") {
			$query .= "AND prod.product_date_added >= '{$date_from} 00:00:00' AND prod.product_date_added <= '{$date_to} 23:59:59' ";
		}
		
		//characteristic
		foreach($_GET as $param=>$value) {
			if($value == "") continue;
		
			preg_match('/^char[0-9]+$/', $param, $matches);
			if($matches) {
				preg_match_all('!\d+!', $param, $matches);
				$char_id = $matches[0][0];
				
				if(!is_array($value)) {
					$value = Array($value);
				}
				foreach($value as $k=>$val) {
					if($k == 0) {
						$query .= "AND ((extra_field_{$char_id} REGEXP '^{$val}$'";
						$query .= " OR extra_field_{$char_id} REGEXP '^{$val},.*$'";
						$query .= " OR extra_field_{$char_id} REGEXP '^.*,{$val},.*$'";
						$query .= " OR extra_field_{$char_id} REGEXP '^.*,{$val}$')";
					}
					else {
						$query .= " OR (extra_field_{$char_id} REGEXP '^{$val}$'";
						$query .= " OR extra_field_{$char_id} REGEXP '^{$val},.*$'";
						$query .= " OR extra_field_{$char_id} REGEXP '^.*,{$val},.*$'";
						$query .= " OR extra_field_{$char_id} REGEXP '^.*,{$val}$')";
					}
					if(($k+1) == count($value)) {
						$query .= ") ";
					}
				}
			}
			
			//Range from
			preg_match('/^char([0-9]+)-from$/', $param, $matches_from);
			if($matches_from) {
				preg_match_all('!\d+!', $param, $matches);
				$char_id = $matches[0][0];

				$char_from = \JFactory::getApplication()->input->get("char".$char_id."-from", "");
				if($char_from != "") {
					if($char_from == "0") {
						$query .= "AND (extra_field_{$char_id} >= {$char_from} OR extra_field_{$char_id} = '') ";
					}
					else {
						$query .= "AND extra_field_{$char_id} >= {$char_from} ";
					}
				}
			}
			
			//Range to
			preg_match('/^char([0-9]+)-to$/', $param, $matches_to);
			if($matches_to) {
				preg_match_all('!\d+!', $param, $matches);
				$char_id = $matches[0][0];

				$char_to = \JFactory::getApplication()->input->get("char".$char_id."-to", "");
				if($char_to != "") {
					$query .= "AND extra_field_{$char_id} <= {$char_to} ";
				}
			}
			
			//Text date from
			preg_match('/^char([0-9]+)-date-from$/', $param, $matches_from);
			if($matches_from) {
				preg_match_all('!\d+!', $param, $matches);
				$char_id = $matches[0][0];

				$char_from = \JFactory::getApplication()->input->get("char".$char_id."-date-from", "");
				$char_to = \JFactory::getApplication()->input->get("char".$char_id."-date-to", "");
				
				$char_query  = "SELECT prod.product_id, extra_field_{$char_id} FROM #__jshopping_products AS prod";					
				$char_query .= " WHERE prod.product_publish = '1'";				
				$db->setQuery($char_query);
				$char_results = $db->loadObjectList();
				
				$ids = Array();
				foreach($char_results as $char_result) {
					$extraname = "extra_field_".$char_id;
					$char_val = str_replace(" ", "", $char_result->$extraname);
					$char_vals = explode(",", $char_val);
					
					$checker = 0;
					foreach($char_vals as $date_val) {
						if($char_to != "") {
							if($date_val >= $char_from && $date_val <= $char_to) {
								$checker = 1;
							}
						}
						else {
							if($date_val >= $char_from) {
								$checker = 1;
							}							
						}
					}
					if($checker == 1) {
						$ids[] = $char_result->product_id;
					}
				}
				
				if(count($ids)) {
					$query .= "AND prod.product_id IN (".implode(",", $ids).") ";
				}
				else {
					$query .= "AND prod.product_id = 0 ";
				}
			}
			
			//Text date to
			preg_match('/^char([0-9]+)-date-to$/', $param, $matches_to);
			if($matches_to) {
				preg_match_all('!\d+!', $param, $matches);
				$char_id = $matches[0][0];

				$char_to = \JFactory::getApplication()->input->get("char".$char_id."-date-to", "");
				$char_from = \JFactory::getApplication()->input->get("char".$char_id."-date-from", "");

				$char_query  = "SELECT prod.product_id, extra_field_{$char_id} FROM #__jshopping_products AS prod";					
				$char_query .= " WHERE prod.product_publish = '1'";				
				$db->setQuery($char_query);
				$char_results = $db->loadObjectList();
				
				$ids = Array();
				foreach($char_results as $char_result) {
					$extraname = "extra_field_".$char_id;
					$char_val = str_replace(" ", "", $char_result->$extraname);
					$char_vals = explode(",", $char_val);
					
					$checker = 0;
					foreach($char_vals as $date_val) {
						if($char_from != "") {
							if($date_val <= $char_to && $date_val >= $char_from) {
								$checker = 1;
							}
						}
						else {
							if($date_val <= $char_to) {
								$checker = 1;
							}							
						}
					}
					if($checker == 1) {
						$ids[] = $char_result->product_id;
					}
				}
				
				if(count($ids)) {
					$query .= "AND prod.product_id IN (".implode(",", $ids).") ";
				}
				else {
					$query .= "AND prod.product_id = 0 ";
				}				
			}
		}
		
		$query .= "GROUP BY prod.product_id ";		
		
		$query .= "HAVING prod.product_publish = '1' ";
		
		//price
		$price_from = (float)$_REQUEST['price-from'];
		$price_to = (float)$_REQUEST['price-to'];
					
		if($price_from) {
			if($cur_currency_value != 1) {
				//get price by current currency selected by user
				$query .= "AND (prod.product_price * {$cur_currency_value}) >= {$price_from} ";
			}
			else {
				$query .= "AND price >= {$price_from} ";
			}
		}
		if($price_to) {
			if($cur_currency_value != 1) {
				//get price by current currency selected by user
				$query .= "AND (prod.product_price * {$cur_currency_value}) <= {$price_to} ";
			}
			else {
				$query .= "AND price <= {$price_to} ";
			}
		}
		
		//attribute
		foreach($_GET as $param=>$value) {
			if($value == "") continue;
			
			preg_match('/^attr([0-9]+)$/', $param, $matches);

			if($matches) {
				$attr_id = $matches[1];

				if(!is_array($value)) {
					$value = Array($value);
				}
				
				foreach($value as $k=>$val) {
					if($k == 0) {
						$query .= "AND ((attr_n_values REGEXP '^{$val}$'";
						$query .= " OR attr_n_values REGEXP '^{$val},.*$'";
						$query .= " OR attr_n_values REGEXP '^.*,{$val},.*$'";
						$query .= " OR attr_n_values REGEXP '^.*,{$val}$')";
						$query .= " OR (attr{$attr_id}_values REGEXP '^{$val}$'";
						$query .= " OR attr{$attr_id}_values REGEXP '^{$val},.*$'";
						$query .= " OR attr{$attr_id}_values REGEXP '^.*,{$val},.*$'";
						$query .= " OR attr{$attr_id}_values REGEXP '^.*,{$val}$')";
					}
					else {
						$query .= " OR (attr_n_values REGEXP '^{$val}$'";
						$query .= " OR attr_n_values REGEXP '^{$val},.*$'";
						$query .= " OR attr_n_values REGEXP '^.*,{$val},.*$'";
						$query .= " OR attr_n_values REGEXP '^.*,{$val}$')";
						$query .= " OR (attr{$attr_id}_values REGEXP '^{$val}$'";
						$query .= " OR attr{$attr_id}_values REGEXP '^{$val},.*$'";
						$query .= " OR attr{$attr_id}_values REGEXP '^.*,{$val},.*$'";
						$query .= " OR attr{$attr_id}_values REGEXP '^.*,{$val}$')";
					}
					if(($k+1) == count($value)) {
						$query .= ") ";
					}
				}
			}
		}
		
		$orderby = \JFactory::getApplication()->input->get("orderby", "");
		if($orderby == "") {
			$orderby = $jshopConfig->product_sorting;
		}
						
		$orderto = \JFactory::getApplication()->input->get("orderto", "");
		if($orderto == "") {
			$orderto = $jshopConfig->product_sorting_direction;
		}
		
		switch($orderby) {
		
			case 1 :
				$query .= "ORDER BY name";
			break;			
			
			case 2 :
				$query .= "ORDER BY prod.product_price";
			break;			
			
			case 3 :
				$query .= "ORDER BY prod.product_date_added";
			break;	
			
			case 4 :
				$query .= "ORDER BY pr_cat.product_ordering";
			break;	
			
			case 5 :
				$query .= "ORDER BY prod.average_rating";
			break;	
			
			case 6 :
				$query .= "ORDER BY prod.hits";
			break;
			
			default:
				$query .= "ORDER BY name";
			break;
		}

		if($orderto == 1) {
			$query .= " DESC";
		}
		else {
			$query .= " ASC";
		}

		if(isset($_GET['debug'])) {
			var_dump($query);die;
		}

		if($total) {
			$db->setQuery($query);
			$results = $db->loadObjectList();
			
			return count($results);
		}		
		else if($getIdsForModule) {
			$db->setQuery($query);
			$results = $db->loadObjectList();
			
			return $results;			
		}
		else {
			$products_page = $jshopConfig->count_products_to_page;
			$limit = \JFactory::getApplication()->input->get("limit");
			if (!$limit) $limit = $products_page;
			$limitstart = \JFactory::getApplication()->input->get('limitstart');
						
			$db->setQuery($query, $limitstart, $limit);
			$results = $db->loadObjectList();

			//js group price plugin compatibility
			$userShop = JSFactory::getUserShop();
			$group_id = $userShop->usergroup_id;
			if (JPluginHelper::isEnabled('jshoppingproducts', 'user_group_product_price') && $group_id) {
				foreach($results as $k=>$product) {	
					$js_product = JTable::getInstance('product', 'jshop');
					$js_product->load($product->product_id);
					
					$query = "select price from #__jshopping_products_prices_group where product_id='".$product->product_id."' and `group_id`='{$group_id}'";
					$db->setQuery($query);
					$group_price = $db->loadResult();
					
					if($group_price) {
						$js_product->product_price = $group_price;
					}
					
					$attributesDatas = $js_product->getAttributesDatas();
					$js_product->setAttributeActive($attributesDatas['attributeActive']);
					
					$attributeValues = $attributesDatas['attributeValues'];
					$js_product->attributes = $js_product->getBuildSelectAttributes($attributeValues, $attributesDatas['attributeSelected']);
					
					$js_product->getCategory();
					$js_product->getExtraFields();
					$js_product->getExtendsData();
					
					$dispatcher = JDispatcher::getInstance();
					$attribs = $js_product->attribute_active;
					$dispatcher->trigger('onAfterSetAttributeActive', array(&$attribs, &$js_product));
					if($js_product->attribute_active_data->price) {
						$js_product->product_price = $js_product->attribute_active_data->price;
					}
										
					$results[$k] = $js_product;
				}
				$results = listProductUpdateData($results);
				addLinkToProducts($results, 0, 1);
			}

			if(isset($_GET['debug'])) {
				var_dump($results);die;
			}

			return $results;	
		}
	}
	
	public static function getCharType($char_id) {
		$db = JFactory::getDBO();
		$query = "SELECT type FROM #__jshopping_products_extra_fields WHERE id = {$char_id}";
		$db->setQuery($query);

		return $db->loadResult();
	}

}

?>