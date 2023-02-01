<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="filter-field-char-text">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<input class="inputbox" style="width: 200px; text-align: left;" name="char<?php echo $filter->id; ?>" type="text" <?php if (JFactory::getApplication()->input->get('char'.$filter->id)) echo ' value="'.JFactory::getApplication()->input->get('char'.$filter->id).'"'; ?> />
	</div>