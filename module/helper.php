<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class modJShopExtendedFilterHelper {

	public static function getAttribute($id) {
		if($id) {
			$db = JFactory::getDBO();

			$query = "SELECT * FROM #__jshopping_attr WHERE attr_id = {$id}";
			$db->setQuery($query);	

			return $db->loadObject();
		}
	}	
	
	public static function getAttributeValues($id) {
		if($id) {
			$db = JFactory::getDBO();
			$lang = JFactory::getLanguage();
			$lang_name = "name_".$lang->getTag();

			$query = "SELECT `{$lang_name}` as name, value_id, image FROM #__jshopping_attr_values WHERE attr_id = {$id} ORDER BY value_ordering ASC";
			$db->setQuery($query);	

			return $db->loadObjectList();			
		}
	}
	
	public static function getCharacteristic($id) {
		if($id) {
			$db = JFactory::getDBO();

			$query = "SELECT * FROM #__jshopping_products_extra_fields WHERE id = {$id}";
			$db->setQuery($query);	

			return $db->loadObject();
		}
	}
	
	public static function getCharacteristicValues($id) {
		if($id) {
			$db = JFactory::getDBO();
			$lang = JFactory::getLanguage();
			$lang_name = "name_".$lang->getTag();

			$query = "SELECT `{$lang_name}` as name, id FROM #__jshopping_products_extra_field_values WHERE field_id = {$id} ORDER BY ordering ASC";
			$db->setQuery($query);	

			return $db->loadObjectList();			
		}
	}
	
	public static function getCharValuesAuto($char_id, $restrict, $restcat, $restsub, $restmode) {
	
		$db = JFactory::getDBO();
		$query = "SELECT prod.extra_field_{$char_id} FROM #__jshopping_products AS prod";
		$query .= " LEFT JOIN #__jshopping_products_to_categories AS catrel ON prod.product_id = catrel.product_id";
		
		$where = Array();
		if($restrict == 1 && $restcat != '' || ($restmode == 1 && \JFactory::getApplication()->input->get("category"))) {
				$restcat = str_replace(" ", "", $restcat);
				
				if($restcat == "" && $restmode == 1 && \JFactory::getApplication()->input->get("category")) {
					$restcat = \JFactory::getApplication()->input->get("category");
				}
				
				if(!is_array($restcat)) {
					$restcat = explode(",", $restcat);
				}
								
				$restcats = '';
				foreach($restcat as $k=>$catid) {
					if($k == 0) {
						$restcats .= $catid;
					}
					else {
						$restcats .= ",".$catid;
					}
					
					if($restsub) {
						$childs = modJShopExtendedFilterHelper::getCategoryChildren($catid);
						$childs = implode(",", $childs);
						
						if($childs) {
							$restcats .= ",".$childs;
						}
					}
				}				
				$where[] = "catrel.category_id IN (".$restcats.")";
		}
		
		$manid = \JFactory::getApplication()->input->get("manufacturer_id", "");
		if($manid == "") {
			$manid = \JFactory::getApplication()->input->get("manufacturer", "");
		}
		if($manid != "") {
			if(!is_array($manid)) {
				$manid = Array($manid);
			}
			if(count($manid)) {
				$where[] = "prod.product_manufacturer_id IN(". implode(',', $manid) .")"; 
			}
		}
		
		if(count($where)) {
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		$is_search_results = \JFactory::getApplication()->input->get("extended");

		if($is_search_results) {
			$results = ExtendedFilterModel::getResults(false, true); 
			$result = Array();
			foreach($results as $item) {
				$result[] = $item->{"extra_field_".$char_id};
			}
		}
		else {
			$db->setQuery($query);	
			$result = JVERSION < 3 ? $db->loadResultArray() : $db->loadColumn();
		}
		
		$values = Array();
		if(count($result)) {
			foreach($result as $val) {
				if(!in_array($val, $values) && $val != "") {
					$values[] = (int)$val;
				}
			}			
			$char = modJShopExtendedFilterHelper::getCharacteristic($char_id);
			if(count($values) && $char->type == "1") { //text characteristic
				$tmp = Array();
				foreach($values as $val) {
					$value = new stdClass;
					$value->id = $val;
					$value->name = $val;
					$tmp[] = $value;
				}
				$values = $tmp;
			}
			else if(count($values)) {
				$lang = JFactory::getLanguage();
				$lang_name = "name_".$lang->getTag();

				$query = "SELECT `{$lang_name}` as name, id FROM #__jshopping_products_extra_field_values WHERE field_id = {$char_id}";
				$query .= " AND id IN (".implode(",", $values).")";
				$query .= " ORDER BY ordering ASC";
				
				$db->setQuery($query);	
				$values = $db->loadObjectList();
				
				if($is_search_results) {
					$values_all = modJShopExtendedFilterHelper::getCharValuesByCat($char_id, $restrict, $restcat, $restsub, $restmode);
					foreach($values_all as $value) {
						$value->hidden = 1;
						foreach($values as $val) {
							if($value->id == $val->id) {
								$value->hidden = 0;
							}
						}
					}
					return $values_all;
				}
			}
		}

		return $values;
	}
	
	public static function getCharValuesByCat($char_id, $restrict, $restcat, $restsub, $restmode) {
	
		$db = JFactory::getDBO();
		$query = "SELECT prod.extra_field_{$char_id} FROM #__jshopping_products AS prod";
		$query .= " LEFT JOIN #__jshopping_products_to_categories AS catrel ON prod.product_id = catrel.product_id";
		
		$where = Array();
		if($restrict == 1 && $restcat != '' || ($restmode == 1 && \JFactory::getApplication()->input->get("category"))) {
				$restcat = str_replace(" ", "", $restcat);
				
				if($restcat == "" && $restmode == 1 && \JFactory::getApplication()->input->get("category")) {
					$restcat = \JFactory::getApplication()->input->get("category");
				}
				
				if(!is_array($restcat)) {
					$restcat = explode(",", $restcat);
				}
								
				$restcats = '';
				foreach($restcat as $k=>$catid) {
					if($k == 0) {
						$restcats .= $catid;
					}
					else {
						$restcats .= ",".$catid;
					}
					
					if($restsub) {
						$childs = modJShopExtendedFilterHelper::getCategoryChildren($catid);
						$childs = implode(",", $childs);
						
						if($childs) {
							$restcats .= ",".$childs;
						}
					}
				}				
				$where[] = "catrel.category_id IN (".$restcats.")";
		}
		
		$manid = \JFactory::getApplication()->input->get("manufacturer_id", "");
		if($manid == "") {
			$manid = \JFactory::getApplication()->input->get("manufacturer", "");
		}
		if($manid != "") {
			if(!is_array($manid)) {
				$manid = Array($manid);
			}
			if(count($manid)) {
				$where[] = "prod.product_manufacturer_id IN(". implode(',', $manid) .")"; 
			}
		}
		
		if(count($where)) {
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		$db->setQuery($query);	
		$result = JVERSION < 3 ? $db->loadResultArray() : $db->loadColumn();
		
		$values = Array();
		if(count($result)) {
			foreach($result as $val) {
				if(!in_array($val, $values) && $val != "") {
					$values[] = (int)$val;
				}
			}
			
			if(count($values)) {
				$lang = JFactory::getLanguage();
				$lang_name = "name_".$lang->getTag();

				$query = "SELECT `{$lang_name}` as name, id FROM #__jshopping_products_extra_field_values WHERE field_id = {$char_id}";
				$query .= " AND id IN (".implode(",", $values).")";
				$query .= " ORDER BY ordering ASC";
				
				$db->setQuery($query);	
				$values = $db->loadObjectList();
			}
		}

		return $values;
	}

	public static function getLabels() {
		$db = JFactory::getDBO();
		$lang = JFactory::getLanguage();
		$lang_name = "name_".$lang->getTag();

		$query = "SELECT *, `{$lang_name}` as name  FROM #__jshopping_product_labels ORDER BY `{$lang_name}` ASC";
		$db->setQuery($query);
		$list = $db->loadObjectList();

		return $list;
	}

	public static function getAllManufacturers($restrict, $restcat, $restsub) {
		$db = JFactory::getDBO();
		$lang = JFactory::getLanguage();
		$lang_name = "name_".$lang->getTag();
		$lang_desc = "description_".$lang->getTag();
		$lang_short = "short_description_".$lang->getTag();

		$query = "SELECT manuf.*, manuf.`".$lang_name."` as name, manuf.`".$lang_desc."` as description, manuf.`".$lang_short."` as short_description";
		
		if($restrict == 1 && $restcat != '') {
			$query .= ", catrel.*";
		}
		
		$query .= " FROM `#__jshopping_manufacturers` AS manuf";
		
		if($restrict == 1 && $restcat != '') {
			$query .= " LEFT JOIN `#__jshopping_products` AS prod ON prod.product_manufacturer_id = manuf.manufacturer_id";
			$query .= " LEFT JOIN `#__jshopping_products_to_categories` AS catrel ON prod.product_id = catrel.product_id";
		
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
				
				if($restsub) {
					$childs = modJShopExtendedFilterHelper::getCategoryChildren($catid);
					$childs = implode(",", $childs);
					
					if($childs) {
						$restcats .= ",".$childs;
					}
				}
			}
		}	

		$query .= " HAVING manuf.`manufacturer_publish` = '1'";
		if($restrict == 1 && $restcat != '') {
			$query .= " AND catrel.category_id IN (".$restcats.")";
		}
		
		$query .= " ORDER BY manuf.ordering ASC";
		
		$db->setQuery($query);
		$list = $db->loadObjectList();

		if($restrict == 1 && $restcat != '') {
			if($list) {
				$newList = Array();
				foreach($list as $manuf) {
					$newList[$manuf->manufacturer_id] = $manuf;
				}
				$list = $newList;
			}
		}
		
		return $list;
	}

	public static function buildTreeCategory($restrict, $restcat, $restsub) {
		$db = JFactory::getDBO();
		$lang = JFactory::getLanguage();
		$lang_name = "name_".$lang->getTag();
		$user = JFactory::getUser();
		
		$where = array();

		$where[] = "category_publish = '1'";

		$groups = implode(',', $user->getAuthorisedViewLevels());
		$where[] = ' access IN ('.$groups.')';
		
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
				
				if($restsub) {
					$childs = modJShopExtendedFilterHelper::getCategoryChildren($catid);
					$childs = implode(",", $childs);
					
					if($childs) {
						$restcats .= ",".$childs;
					}
				}
			}
			
			$where[] = ' category_id IN ('.$restcats.')';
		}

		$add_where = "";
		if (count($where)){
			$add_where = " WHERE ".implode(" and ", $where);
		}

		$query = "SELECT `".$lang_name."` as name, category_id, category_parent_id, category_publish FROM `#__jshopping_categories`
				  ".$add_where." ORDER BY category_parent_id, ordering";
		$db->setQuery($query);
		$all_cats = $db->loadObjectList();

		$categories = array();
		if(count($all_cats)) {
			$root = 100;
			foreach($all_cats as $cat) {
				if($cat->category_parent_id < $root) {
					$root = $cat->category_parent_id;
				}
			}
			if($root == 100) {
				$root = 0;
			}
			
			foreach ($all_cats as $key => $value) {
				if($value->category_parent_id == $root){
					modJShopExtendedFilterHelper::recurseTree($value, 0, $all_cats, $categories);
				}
			}
		}
		return $categories;
	}
	
	public static function recurseTree($cat, $level, $all_cats, &$categories) {
		$probil = '';

		for ($i = 0; $i < $level; $i++) {
			$probil .= '-- ';
		}

		$cat->name = ($probil . $cat->name);
		$cat->level = $level;
		$categories[] = $cat;

		foreach ($all_cats as $categ) {
			if($categ->category_parent_id == $cat->category_id) {
				modJShopExtendedFilterHelper::recurseTree($categ, ++$level, $all_cats, $categories);
				$level--;
			}
		}
		return $categories;
	}
	
	public static function getCategoryChildren($catid) {
		$db = JFactory::getDBO();
		$arr = array();

		$query = "SELECT category_id FROM #__jshopping_categories WHERE category_parent_id = {$catid} 
					AND category_publish = 1";
		$db->setQuery($query);
		$res = $db->loadObjectList();
		if(count($res)) {
			foreach($res as $cat) {
				$arr[] = $cat->category_id;
				$query = "SELECT category_id FROM #__jshopping_categories WHERE category_parent_id = {$cat->category_id} 
							AND category_publish = 1";
				$db->setQuery($query);
				$res = $db->loadObjectList();
				if(count($res)) {
					foreach($res as $cat) {
						$arr[] = $cat->category_id;	
						$query = "SELECT category_id FROM #__jshopping_categories WHERE category_parent_id = {$cat->category_id} 
									AND category_publish = 1";
						$db->setQuery($query);
						$res = $db->loadObjectList();
						if(count($res)) {
							foreach($res as $cat) {
								$arr[] = $cat->category_id;		
							}
						}						
					}
				}
			}
		}
		
		return $arr;
	}
	
	public static function hasChildren($id) {
		$id = (int) $id;
		$db = JFactory::getDBO();
		$query = "SELECT category_id FROM #__jshopping_categories WHERE category_parent_id = {$id} AND category_publish = 1";
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();		
		return count($rows);
	}
	
	public static function getModuleParams($id) {
		$db = JFactory::getDBO();
		
		$query = "SELECT * FROM #__modules WHERE id = {$id}";

		$db->setQuery($query);
		$result = $db->loadObject();
		
		$moduleParams = json_decode($result->params);
		return $moduleParams;
	}
	
	public static function getPriceValue($direction, $restrict, $restcat, $restsub, $restmode) {
		$db = JFactory::getDBO();
		
		$jshopConfig = JSFactory::getConfig();
		$cur_currency = $jshopConfig->cur_currency;
		$query = "SELECT currency_value FROM #__jshopping_currencies WHERE currency_id = {$cur_currency}";
		$db->setQuery($query);
		$cur_currency_value = (float)$db->loadResult();
		
		$query = "SELECT prod.product_price FROM #__jshopping_products AS prod";
		$query .= " LEFT JOIN #__jshopping_products_to_categories AS catrel ON prod.product_id = catrel.product_id";
		
		$where = Array();
		if($restrict == 1 && $restcat != '' || ($restmode == 1 && \JFactory::getApplication()->input->get("category"))) {
				$restcat = str_replace(" ", "", $restcat);
				
				if($restcat == "" && $restmode == 1 && \JFactory::getApplication()->input->get("category")) {
					$restcat = \JFactory::getApplication()->input->get("category");
				}
				
				if(!is_array($restcat)) {
					$restcat = explode(",", $restcat);
				}
				
				$restcats = '';
				foreach($restcat as $k=>$catid) {
					if($k == 0) {
						$restcats .= $catid;
					}
					else {
						$restcats .= ",".$catid;
					}
					
					if($restsub) {
						$childs = modJShopExtendedFilterHelper::getCategoryChildren($catid);
						$childs = implode(",", $childs);
						
						if($childs) {
							$restcats .= ",".$childs;
						}
					}
				}
				
				$where[] = "catrel.category_id IN (".$restcats.")";
		}
				
		$manid = \JFactory::getApplication()->input->get("manufacturer_id", "");
		if($manid == "") {
			$manid = \JFactory::getApplication()->input->get("manufacturer", "");
		}
		if($manid != "") {
			if(!is_array($manid)) {
				$manid = Array($manid);
			}
			if(count($manid)) {
				$where[] = "prod.product_manufacturer_id IN(". implode(',', $manid) .")"; 
			}
		}
		
		if(count($where)) {
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}
		
		$db->setQuery($query);	
		$result = JVERSION < 3 ? $db->loadResultArray() : $db->loadColumn();

		if(count($result)) {
			if($direction == "min") {
				$min_value = (float)$result[0];
				foreach($result as $val) {
					if($val != '') {
						$val = (float)$val;
					}
					else {
						continue;
					}
					if($val < $min_value) {
						$min_value = $val;
					}
				}
				if($cur_currency_value != 1) {
					$min_value = $min_value * $cur_currency_value;
				}
				return $min_value;
			}
			else {
				$max_value = (float)$result[0];
				foreach($result as $val) {
					$val = (float)$val;
					if($val > $max_value) {
						$max_value = $val;
					}
				}
				if($cur_currency_value != 1) {
					$max_value = $max_value * $cur_currency_value;
				}
				return $max_value;				
			}
		}		
	}	
	
	public static function getCharValue($direction, $char_id, $restrict, $restcat, $restsub, $restmode) {
	
		$db = JFactory::getDBO();
		$query = "SELECT prod.extra_field_{$char_id} FROM #__jshopping_products AS prod";
		$query .= " LEFT JOIN #__jshopping_products_to_categories AS catrel ON prod.product_id = catrel.product_id";
		
		$where = Array();
		if($restrict == 1 && $restcat != '' || ($restmode == 1 && \JFactory::getApplication()->input->get("category"))) {
				$restcat = str_replace(" ", "", $restcat);
				
				if($restcat == "" && $restmode == 1 && \JFactory::getApplication()->input->get("category")) {
					$restcat = \JFactory::getApplication()->input->get("category");
				}
				
				if(!is_array($restcat)) {
					$restcat = explode(",", $restcat);
				}
								
				$restcats = '';
				foreach($restcat as $k=>$catid) {
					if($k == 0) {
						$restcats .= $catid;
					}
					else {
						$restcats .= ",".$catid;
					}
					
					if($restsub) {
						$childs = modJShopExtendedFilterHelper::getCategoryChildren($catid);
						$childs = implode(",", $childs);
						
						if($childs) {
							$restcats .= ",".$childs;
						}
					}
				}				
				$where[] = "catrel.category_id IN (".$restcats.")";
		}
		
		$manid = \JFactory::getApplication()->input->get("manufacturer_id", "");
		if($manid == "") {
			$manid = \JFactory::getApplication()->input->get("manufacturer", "");
		}
		if($manid != "") {
			if(!is_array($manid)) {
				$manid = Array($manid);
			}
			if(count($manid)) {
				$where[] = "prod.product_manufacturer_id IN(". implode(',', $manid) .")"; 
			}
		}
		
		if(count($where)) {
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}
		
		$db->setQuery($query);	
		$result = JVERSION < 3 ? $db->loadResultArray() : $db->loadColumn();

		if(count($result)) {
			if($direction == "min") {
				$min_value = (float)$result[0];
				foreach($result as $val) {
					if($val != '') {
						$val = (float)$val;
					}
					else {
						continue;
					}
					if($val < $min_value) {
						$min_value = $val;
					}
				}
				return $min_value;
			}
			else {
				$max_value = (float)$result[0];
				foreach($result as $val) {
					$val = (float)$val;
					if($val > $max_value) {
						$max_value = $val;
					}
				}
				return $max_value;				
			}
		}		
	}
	
	public static function getTitle($type, $id) {
		$db = JFactory::getDBO();
		
		$lang = JFactory::getLanguage();
		$lang_name = 'name_'.$lang->getTag();
		
		if($type == "characteristic") {
			$from = "#__jshopping_products_extra_fields";
			$where = "id";
		}
		else {
			$from = "#__jshopping_attr";
			$where = "attr_id";
		}
		
		$query = "SELECT `{$lang_name}` FROM {$from} WHERE {$where} = {$id}";
		$db->setQuery($query);
		
		return $db->loadResult();
	}

}

?>