<?php

/*------------------------------------------------------------------------
# jsfilter - Extended Filter for Joomshopping
# ------------------------------------------------------------------------
# author    Andrey Miasoedov
# copyright Copyright (C) 2012 Joomcar.net All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://joomcar.net
# Technical Support: http://joomcar.net
-------------------------------------------------------------------------*/

use Joomla\Component\Jshopping\Site\Controller;

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('joomla.html.parameter');
jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

if(!class_exists("JshoppingControllerBase") 
	&& JFactory::getApplication()->input->get("extended") == 1 
	&& JFactory::getApplication()->input->get("option") == "com_jshopping" 
	&& JFile::exists(JPATH_COMPONENT_SITE.'/controllers/base.php')
) { //fix JS 4.11 compatibility
	if (!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR);
	if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT',	JPATH_BASE.DS.'components'.DS.'com_jshopping');
	if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_jshopping');
	if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jshopping');
	
	jimport('joomla.application.component.model');
	JModelLegacy::addIncludePath(JPATH_COMPONENT_SITE.'/models');
	require_once(JPATH_COMPONENT_SITE."/lib/factory.php");
	require_once(JPATH_COMPONENT_SITE.'/controllers/base.php');
	$controller = getJsFrontRequestController();
	require(JPATH_COMPONENT_SITE."/loadparams.php");
}

class plgSystemJSFilter extends JPlugin {
	function onAfterDispatch() {
			if(!JFile::exists(JPATH_ROOT."/components/com_jshopping/Lib/JSFactory.php")) {
				echo "Joomshopping does not installed. ";
				return;
			}
			
			ini_set("memory_limit", "400M");
			ini_set("max_execution_time", "300"); 
			ini_set("display_errors", "On");
			error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
			
			// Define the DS constant under Joomla! 3.0
			if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
			
			$option = JFactory::getApplication()->input->get("option");
			$controller = JFactory::getApplication()->input->get("controller");
			$task = JFactory::getApplication()->input->get("task");
						
			if($option == "com_jshopping" && $controller == "search" && $task == "result" && JFactory::getApplication()->input->get("extended") == 1) {
					
				if (!defined('JPATH_COMPONENT')) define( 'JPATH_COMPONENT',	JPATH_BASE.DS.'components'.DS.'com_jshopping');
				if (!defined('JPATH_COMPONENT_SITE')) define( 'JPATH_COMPONENT_SITE', JPATH_SITE.DS.'components'.DS.'com_jshopping');
				if (!defined('JPATH_COMPONENT_ADMINISTRATOR')) define( 'JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jshopping');
				
				$pluginPath = JPATH_BASE.DS.'plugins'.DS.'system'.DS.'jsfilter'.DS.'jsfilter';
				
				$controller = new Joomla\Component\Jshopping\Site\Controller\SearchController;	

				$config['name'] =  "search";
				$config['default_task'] =  "display";
				$config['base_path'] =  $pluginPath;
				$config['model_path'] =  $pluginPath.DS."models";
				$config['view_path'] =  $pluginPath.DS."views";
				$config['template_path'] = JPATH_COMPONENT."/templates/".$this->getJShopTemplate()."/category";
					
				$controller->__construct($config);
				
				$format = JFactory::getApplication()->input->get("tmpl", "");
				switch($format) {
					case "count" :
						$view = $controller->getView("jsfilter", "count", "", $config);
					break;
					
					default :
						$view = $controller->getView("jsfilter", "html", "", $config);
				}

				ob_start();
					$view->display();
				$result = ob_get_contents();
				ob_end_clean();

				$doc = \JFactory::getDocument();
				if($_REQUEST['tmpl'] === 'raw') {
					echo $result;
					return;
				}
				else {
					$doc->setBuffer($result, "component");
				}
			}
			
	} // onAfterRoute
	
	function onBeforeDisplayProductListView($view) {
		if($view->results_template == "category") {
			$filterLang = JFactory::getLanguage();
			$filterLang->load("mod_jshopping_extended_filter");
			
			require_once (JPATH_SITE.DS.'modules'.DS.'mod_jshopping_extended_filter'.DS.'helper.php');
			$moduleId = JFactory::getApplication()->input->get("moduleId");
			$moduleParams = modJShopExtendedFilterHelper::getModuleParams($moduleId);
			
			if (count($view->rows)) {
				echo "<div class='results-text'><p>" . $moduleParams->search_results_text . "(" . $view->results_total . ") :</p></div>";
			}
			else {
				echo "<div class='results-text'><p>" . $moduleParams->text_no_results . "</p></div>";
			}
		}
	}
	
	function onBeforeQueryGetProductList($view, &$adv_result, &$adv_from, &$adv_query, &$order_query, &$filters) {
		if(JFactory::getApplication()->input->get("extended") == 1) {
			$adv_query = '';
		}
	}
	
	function getJShopTemplate() {
		$db = JFactory::getDBO();
		$query = "SELECT value FROM #__jshopping_configs WHERE `key` = 'template'";
		$db->setQuery($query);
		
		$result = $db->loadResult();
		if($result == "") {
			return "default";
		}
		return $result;
	}

} // class

?>