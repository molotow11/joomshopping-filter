<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="filter-field-title-text">
		<h3>
			<?php echo JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_TITLE_TITLE'); ?>
		</h3>
		
		<input class="inputbox" style="width: 200px; text-align: left;" name="title" type="text" <?php if (JFactory::getApplication()->input->get('title')) echo ' value="'.JFactory::getApplication()->input->get('title').'"'; ?> />
	</div>