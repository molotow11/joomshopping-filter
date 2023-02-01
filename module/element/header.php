<?php
/**
 * @version     $Id: header.php 1647 2012-09-26 16:30:16Z lefteris.kavadas $
 * @package     K2
 * @author      JoomlaWorks http://www.joomlaworks.net
 * @copyright   Copyright (c) 2006 - 2012 JoomlaWorks Ltd. All rights reserved.
 * @license     GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die ;

class JFormFieldHeader extends JFormField {

	function getInput(){
		return JFormFieldHeader::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	}

    public function fetchElement($name, $value, &$node, $control_name)
    {

        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::root(true).'/modules/mod_jshopping_extended_filter/assets/css/filter.css');

        return '<div class="paramHeaderContainer"><div class="paramHeaderContent">'.JText::_($value).'</div><div class="clear"></div></div>';
    }

    public function fetchTooltip($label, $description, &$node, $control_name, $name)
    {
        return NULL;
    }

}