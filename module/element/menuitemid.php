<?php

defined('_JEXEC') or die ;

class JFormFieldMenuItemid extends JFormField {

	var $_name = 'menuitemid';

	var	$type = 'menuitemid';

	function getInput(){
		return JFormFieldMenuItemid::fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	}
	
	function fetchElement($name, $value, &$node, $control_name) {
		$input = JFactory::getApplication()->input;
		$db = JFactory::getDbo();

		$where = ' WHERE 1';

		$query = 'SELECT menutype, title' . ' FROM #__menu_types' . ' ORDER BY title';
		$db->setQuery($query);
		$menuTypes = $db->loadObjectList();

		if ($state = $node->attributes('state'))
		{
			$where .= ' AND published = ' . (int) $state;
		}

		// load the list of menu items
		// TODO: move query to model
		$query = 'SELECT id, parent_id, title, menutype, type' . ' FROM #__menu' . $where . ' ORDER BY menutype, parent_id';

		$db->setQuery($query);
		$menuItems = $db->loadObjectList();

		// Establish the hierarchy of the menu
		// TODO: use node model
		$children = array();

		if ($menuItems)
		{
			// First pass - collect children
			foreach ($menuItems as $v)
			{
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}

		// Second pass - get an indent list of the items
		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

		// Assemble into menutype groups
		$n = count($list);
		$groupedList = array();
		foreach ($list as $k => $v)
		{
			$groupedList[$v->menutype][] = &$list[$k];
		}

		// Assemble menu items to the array
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('JOPTION_SELECT_MENU_ITEM'));

		foreach ($menuTypes as $type)
		{
			if ($menuType == '')
			{
				$options[] = JHtml::_('select.option', '0', '&#160;', 'value', 'text', true);
				$options[] = JHtml::_('select.option', $type->menutype, $type->title . ' - ' . JText::_('JGLOBAL_TOP'), 'value', 'text', true);
			}
			if (isset($groupedList[$type->menutype]))
			{
				$n = count($groupedList[$type->menutype]);
				for ($i = 0; $i < $n; $i++)
				{
					$item = &$groupedList[$type->menutype][$i];

					// If menutype is changed but item is not saved yet, use the new type in the list
					if ($input->get('option', '') == 'com_menus')
					{
						$currentItemArray = $input->get('cid', array(0));
						$currentItemId = (int) $currentItemArray[0];
						$currentItemType = $input->get('type', $item->type);
						if ($currentItemId == $item->id && $currentItemType != $item->type)
						{
							$item->type = $currentItemType;
						}
					}

					$disable = strpos($node->attributes('disable'), $item->type) !== false ? true : false;
					$options[] = JHtml::_('select.option', $item->id, '&#160;&#160;&#160;' . $item->treename, 'value', 'text', $disable);

				}
			}
		}

		$fieldName = $name;
			
		return JHTML::_('select.genericlist', $options, $fieldName, 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
	}
}
