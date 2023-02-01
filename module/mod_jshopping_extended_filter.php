<?php 

/*------------------------------------------------------------------------
# mod_jshopping_extended_filter - Extended Filter for Joomshopping
# ------------------------------------------------------------------------
# author    Andrey Miasoedov
# copyright Copyright (C) 2012 Joomcar.net. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomcar.net
# Technical Support: http://joomcar.net
-------------------------------------------------------------------------*/

use Joomla\Component\Jshopping\Site\Model;
use Joomla\CMS\Factory;

// no direct access
defined('_JEXEC') or die('Restricted access');

$input = JFactory::getApplication()->input;
require_once (JPATH_SITE . '/modules/mod_jshopping_extended_filter/helper.php');

// Define the DS constant under Joomla! 3.0
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

\JSFactory::loadLanguageFile();

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root(true).'/modules/mod_jshopping_extended_filter/assets/css/filter.css');

// Main params
$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$getTemplate = $params->get('getTemplate', 'Default');

// Category restriction
$restrict = $params->get('restrict', 0);
$restmode = $params->get('restmode', 0);
if($restmode == 0) {
	$restcat = $params->get('restcat', '');
}
else {
	$restcat = $input->get("category_id", "");
	if($restcat == "") {
		$restcat = $input->get("restcata", "");
	}
}
$restsub = $params->get('restsub', 1);
$button = $params->get('button', 1);
$button_text = $params->get('button_text', 'Search');

$clear_btn = $params->get('clear_btn', 0);

$auto_submit = $params->get('auto_submit', 0);

$auto_counter = $params->get('auto_counter', 0);

$auto_fetch_attribs = $params->get('auto_fetch_attribs', 0);
$attribs_only_actual_vals = $params->get('attribs_only_actual_vals', 0);

$auto_fetch_chars = $params->get('auto_fetch_chars', 0);
$dyno_chars_vals = $params->get('dyno_chars_vals', 0);

$cols = $params->get('cols', '1');

$filters = $params->get('filters', 0);

$slider_fields = $params->get('slider_fields', 0);

$ajax_results = $params->get('ajax_results', 0);
$ajax_container = $params->get('ajax_container', "#ajax_container");

$itemid = $params->get('itemid', $input->get("Itemid", "101"));

if($auto_fetch_attribs) {
	require_once (JPATH_SITE.DS.'plugins'.DS.'system'.DS.'jsfilter'.DS.'jsfilter'.DS.'models'.DS.'search.php');
	$_REQUEST["moduleId"] = $module->id;
	if($restrict == 1 && $restmode == 1) {
		$_REQUEST["restcata"] = $restcat;
	}
	$items = ExtendedFilterModel::getResults();
	
	$attributes_list = Array();
	if(count($items)) {
		foreach($items as $item) {
			$js_product = JTable::getInstance('product', 'jshop');
			$js_product->load($item->product_id);
			$attributesDatas = $js_product->getAttributesDatas();
			$attributes = $js_product->getBuildSelectAttributes($attributesDatas['attributeValues'], $attributesDatas['attributeSelected']);
			if(count($attributes)) {
				foreach($attributes as $attr) {
					$attributes_list[$attr->attr_id] = $attr->attr_name;
				}
			}
		}
	}
	if(count($attributes_list)) {
		foreach($attributes_list as $k=>$val) {
			$filters .= "\r\nattribute:{$val}:{$k}";
		}
	}
}

if($attribs_only_actual_vals) {
	require_once (JPATH_SITE.DS.'plugins'.DS.'system'.DS.'jsfilter'.DS.'jsfilter'.DS.'models'.DS.'search.php');
	$_REQUEST["moduleId"] = $module->id;
	if($restrict == 1 && $restmode == 1) {
		$_REQUEST["restcata"] = $restcat;
	}
	$items = ExtendedFilterModel::getResults(false, true);
	$attributes_active = Array();
	if(count($items)) {
		foreach($items as $product) {
			$js_product = JTable::getInstance('product', 'jshop');
			$js_product->load($product->product_id);
			$attributesDatas = $js_product->getAttributesDatas();
			if(count($attributesDatas["attributeValues"])){
				foreach($attributesDatas["attributeValues"] as $attr_id=>$values_array) {
					if(count($values_array)) {
						foreach($values_array as $val) {
							$attributes_active[$attr_id][$val->val_id]++;
						}
					}
				}
			}
		}
	}
}

if($auto_fetch_chars) {
	require_once (JPATH_SITE.DS.'plugins'.DS.'system'.DS.'jsfilter'.DS.'jsfilter'.DS.'models'.DS.'search.php');
	$_REQUEST["moduleId"] = $module->id;
	if($restrict == 1 && $restmode == 1) {
		$_REQUEST["restcata"] = $restcat;
	}
	$items = ExtendedFilterModel::getResults();
	
	$chars_list = Array();
	if(count($items)) {
		foreach($items as $item) {
			$js_product = JTable::getInstance('product', 'jshop');
			$js_product->load($item->product_id);
			$chars = $js_product->getExtraFields();
			if(count($chars)) {
				foreach($chars as $char) {
					$chars_list[$char['id']] = $char['name'];
				}
			}
		}
	}
	if(count($chars_list)) {
		foreach($chars_list as $k=>$val) {
			$filters .= "\r\ncharacteristic:{$val}:{$k}";
		}
	}
}

if($filters) {
	$filters = explode("\r\n", $filters);	
	$list = Array();
	
	foreach($filters as $filter) {
		$tmp = new JObject;
		$filter = explode(":", $filter);
		// Filter_type:::Title:::Id:::Field_type
		
		$tmp->type = $filter[0];		
		$tmp->title = $filter[1];
		
		$id = @$filter[2];
		if($id) {
			$tmp->id = $id;
			$tmp->title = modJShopExtendedFilterHelper::getTitle($tmp->type, $id);
		}
		
		if($filter[0] == "characteristic") {
			$characteristic = modJShopExtendedFilterHelper::getCharacteristic($id);

			if($restrict == 1) {
				if(!$characteristic->allcats == 1) {
					$cats = Array();
					$temps = explode("s", $characteristic->cats);
					foreach($temps as $tmps) {
						$catid = explode("\"", $tmps);
						if(count($catid) > 1) {
							$catid = $catid[1];
							$cats[] = $catid;
						}
					}
							
					$checker = 0;
					$restcats = explode(",", $restcat);
								
					foreach($restcats as $catid) {
						if(in_array($catid, $cats)) {
							$checker = 1;
						}
					}
								
					if($checker == 0) {
						continue;
					}
				}
			}
			$tmp->characteristic = $characteristic;
			$tmp->field_type = @$filter[3];
		}
		
		if($filter[0] == "attribute") {
			$attribute = modJShopExtendedFilterHelper::getAttribute($id);
				
			$attr_vals = modJShopExtendedFilterHelper::getAttributeValues($id);
			
			if($restrict == 1) {
				if(!$attribute->allcats == 1) {
					$cats = Array();
					$temps = explode("s", $attribute->cats);
					foreach($temps as $tmps) {
						$catid = explode("\"", $tmps);
						if(count($catid) > 1) {
							$catid = $catid[1];
							$cats[] = $catid;
						}
					}
							
					$checker = 0;
					$restcats = explode(",", $restcat);
								
					foreach($restcats as $catid) {
						if(in_array($catid, $cats)) {
							$checker = 1;
						}
					}
								
					if($checker == 0) {
						continue;
					}
				}
			}
			$tmp->attribute = $attribute;
			$tmp->attr_vals = $attr_vals;
			$tmp->field_type = @$filter[3];
		}
		
		$list[] = $tmp;
	}
}

if($filters && $slider_fields) {
	$sliders = explode("\r\n", $slider_fields);
	$sliders_list = Array();
	
	foreach($sliders as $slider) {
		$tmp = new JObject;
		list($name, $range) = explode("=>", $slider);
		$tmp->title = $name;
		$tmp->range = $range;
		$sliders_list[] = $tmp;
	}
	
	foreach($list as $filter) {
		foreach($sliders_list as $slider) {
			if($filter->title == $slider->title) {
				$filter->slider = 1;
				list($slider_from, $slider_to) = explode("-", $slider->range);
				
				if($slider_from == "") {
					if($filter->type == "price") {
						$slider_from = floor(modJShopExtendedFilterHelper::getPriceValue($direction="min", $restrict, $restcat, $restsub, $restmode));
					}
					if($filter->type == "characteristic") {
						$slider_from = modJShopExtendedFilterHelper::getCharValue($direction="min", $filter->id, $restrict, $restcat, $restsub, $restmode);
					}
				}
				
				if($slider_to == "") {
					if($filter->type == "price") {
						$slider_to = ceil(modJShopExtendedFilterHelper::getPriceValue($direction="max", $restrict, $restcat, $restsub, $restmode));
					}
					if($filter->type == "characteristic") {
						$slider_to = modJShopExtendedFilterHelper::getCharValue($direction="max", $filter->id, $restrict, $restcat, $restsub, $restmode);
					}
				}

				$filter->slider_from = $slider_from;
				$filter->slider_to = $slider_to;
			}
		}
	}
}

if($filters) {
	require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template'));
}
else {
	echo "Please, adjust the module params.<br />";
}
	
if(!JPluginHelper::isEnabled('system', 'jsfilter')) {
	echo "<p>JC Jshopping Extended Filter plugin is not published.</p>";
}

?>