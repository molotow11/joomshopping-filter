<?php
/**
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

 use \Joomla\Component\Jshopping\Site\Helper;

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class joomlacomponentjshoppingsitecontrollersearchViewjsfilter extends JViewLegacy {

	function display($tpl = null) {	
				$mainframe = JFactory::getApplication();
				JSFactory::loadLanguageFile();
				$jshopConfig = JSFactory::getConfig();

				require_once (JPATH_SITE.DS.'modules'.DS.'mod_jshopping_extended_filter'.DS.'helper.php');
				$moduleId = JFactory::getApplication()->input->get("moduleId");
				$moduleParams = modJShopExtendedFilterHelper::getModuleParams($moduleId);
				
				
				if (!defined('JPATH_ROOT')) {
				   define('JPATH_ROOT', JPath::clean(JPATH_SITE));
				}
	
				$pluginPath = JPATH_BASE.DS.'plugins'.DS.'system'.DS.'jsfilter'.DS.'jsfilter';
	
				require_once($pluginPath.DS.'models'.DS.'search.php');
				
				echo ExtendedFilterModel::getResults(true);
				exit;
				
	}

}

?>