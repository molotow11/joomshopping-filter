<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="filter-field-char-text-range">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<input class="inputbox" style="width: 44%; max-width: 100px;" name="char<?php echo $filter->id; ?>-from" type="text" <?php if (JFactory::getApplication()->input->get('char'.$filter->id."-from")) echo ' value="'.JFactory::getApplication()->input->get('char'.$filter->id."-from").'"'; ?> />
		-
		<input class="inputbox" style="width: 44%; max-width: 100px;" name="char<?php echo $filter->id; ?>-to" type="text" <?php if (JFactory::getApplication()->input->get('char'.$filter->id."-to")) echo ' value="'.JFactory::getApplication()->input->get('char'.$filter->id."-to").'"'; ?> />
	</div>