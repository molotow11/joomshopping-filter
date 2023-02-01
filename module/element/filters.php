<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

// Define the DS constant under Joomla! 3.0
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

class JFormFieldFilters extends JFormField {	
	var $_name = 'filters';

	var	$type = 'filters';

	function getInput(){
		return JFormFieldFilters::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	}
	
	function fetchElement($name, $value, &$node, $control_name) {
			
			$db = JFactory::getDBO();
			$lang = JFactory::getLanguage();
			$lang_name = 'name_'.$lang->getTag();
			
			$mitems[] = JHTML::_('select.option',  0, "-- ".JText::_('MOD_JSHOP_EFILTER_SELECT_FIELDS')." -- ");
			
			$mitems[] = JHTML::_('select.option',  'price:'.JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_PRICE'), JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_PRICE'));
			
			$mitems[] = JHTML::_('select.option',  'title_text:'.JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_TITLE'), JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_TITLE') );
			
			$mitems[] = JHTML::_('select.option',  'title_az:'.JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_TITLE_AZ'), JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_TITLE_AZ') );
			
			$mitems[] = JHTML::_('select.option',  'text:'.JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_TEXT'), JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_TEXT') );
			
			$mitems[] = JHTML::_('select.option',  'code:'.JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_CODE'), JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_CODE') );
			
			$mitems[] = JHTML::_('select.option',  'stock:'.JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCTS_IN_STOCK'), JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCTS_IN_STOCK') );
			
			$mitems[] = JHTML::_('select.option',  'categories:'.JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_CATEGORY'), JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_CATEGORY') );
			
			$mitems[] = JHTML::_('select.option',  'manufacturer:'.JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_MANUFACTURER'), JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_MANUFACTURER') );
			
			$mitems[] = JHTML::_('select.option',  'labels:'.JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_LABEL'), JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_LABEL') );
			
			$mitems[] = JHTML::_('select.option',  'date:'.JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_DATE'), JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_DATE') );
			
			//characteristics
			$mitems[] = JHTML::_('select.option',  '', '------ ' . JText::_('MOD_JSHOP_EFILTER_FIELDS_CHARACTERISTICS') .' ------' );
			
			$query = "SELECT t.*, g.`{$lang_name}` AS group_name ";
			$query .= "FROM #__jshopping_products_extra_fields AS t ";
			$query .= "LEFT JOIN #__jshopping_products_extra_field_groups AS g ON g.id = t.group ";
			$query .= "ORDER BY group_name, t.ordering";
			
			$db->setQuery($query);
			$list = $db->loadObjectList();
			
			if($list) {
				$group = $list[0]->group_name;
				array_splice( $list, 0, 0, $group );
							
				for($i = 1; $i < count($list); $i++) {
					$new_group = $list[$i]->group_name;
					if($new_group != $group) {
						array_splice( $list, $i, 0, $new_group );
						$group = $new_group;
					}
				}

				foreach ( $list as $item ) {
					if(is_object($item)) {
						$mitems[] = JHTML::_('select.option',  'characteristic:'.$item->$lang_name.':'.$item->id, $item->$lang_name );
					}
					else {
						$mitems[] = JHTML::_('select.option',  '', '   --- '.$item.' ---' );
					}
				}
			}
			
			//attributes
			$mitems[] = JHTML::_('select.option',  '', '------ ' . JText::_('MOD_JSHOP_EFILTER_FIELDS_ATTRIBUTES') .' ------' );

			$query = "SELECT t.* ";
			$query .= "FROM #__jshopping_attr AS t ";
			$query .= "ORDER BY t.attr_ordering";
			
			$db->setQuery($query);
			$attr_list = $db->loadObjectList();
			
			if($attr_list) {
				foreach ( $attr_list as $item ) {
					$mitems[] = JHTML::_('select.option',  'attribute:'.$item->$lang_name.':'.$item->attr_id, $item->$lang_name );				
				}
			}
			
			//other...
			
			/////

			$output = JHTML::_('select.genericlist',  $mitems, '', 'class="FilterSelect inputbox"', 'value', 'text', '0');		
			$output .= "<div class='clear'></div><ul id='sortableFields'></ul>";
			$output .= "<div class='clear'></div>";
			$output .= "<textarea style='display: none;' name='".$name."' id='FiltersListVal'>".$value."</textarea>";
			$output .= "
			
			<script type='text/javascript'>
				
				var FilterPath = '".JURI::root(true)."/modules/mod_jshopping_extended_filter/assets/';
				var MOD_JSHOP_EFILTER_ADMIN_SELECT_FIELD_TYPE = '".JText::_("MOD_JSHOP_EFILTER_ADMIN_SELECT_FIELD_TYPE")."';
				
				if(typeof jQuery == 'undefined') {
					var script = document.createElement('script');
					script.type = 'text/javascript';
					script.src = '".JURI::root(true)."/modules/mod_jshopping_extended_filter/assets/js/jquery-1.10.2.min.js';
					document.getElementsByTagName('head')[0].appendChild(script);
				   
					if (script.readyState) { //IE
						script.onreadystatechange = function () {
							if (script.readyState == 'loaded' || script.readyState == 'complete') {
								script.onreadystatechange = null;
								load_ui();
							}
						};
					} else { //Others
						script.onload = function () {
							load_ui();
						};
					}
				}
				else {
					load_ui();
				}
				
				function load_ui() {				
					if(typeof jQuery.ui == 'undefined') {
					   var script = document.createElement('script');
					   script.type = 'text/javascript';
					   script.src = FilterPath+'js/jquery-ui-1.10.4.custom.min.js';
					   document.getElementsByTagName('head')[0].appendChild(script);
										   
					   var style = document.createElement('link');
					   style.rel = 'stylesheet';
					   style.type = 'text/css';
					   style.href = FilterPath+'css/smoothness/jquery-ui-1.10.4.custom.min.css';
					   document.getElementsByTagName('head')[0].appendChild(style);
					   
						if (script.readyState) { //IE
							script.onreadystatechange = function () {
								if (script.readyState == 'loaded' || script.readyState == 'complete') {
									script.onreadystatechange = null;
									load_base();
								}
							};
						} else { //Others
							script.onload = function () {
								load_base();
							};
						}		   
					}
					else {
						load_base();
					}
				}
				
				function load_base() {
					var migrate_script = document.createElement('script');
					migrate_script.type = 'text/javascript';
					migrate_script.src = FilterPath+'js/jquery-migrate-1.2.1.min.js';
					document.getElementsByTagName('head')[0].appendChild(migrate_script);
				
					var base_script = document.createElement('script');
					base_script.type = 'text/javascript';
					base_script.src = FilterPath+'js/filter.admin.js';
					document.getElementsByTagName('head')[0].appendChild(base_script);					
				}
			</script>
			
			";

			return $output;
	}
}

?>