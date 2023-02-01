<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="filter-field-text">
		<h3>
			<?php echo JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_TEXT_TITLE'); ?>
		</h3>
		
		<input class="inputbox" style="width: 200px; text-align: left;" name="text" type="text" <?php if (JFactory::getApplication()->input->get('text')) echo ' value="'.JFactory::getApplication()->input->get('text').'"'; ?> />
	</div>