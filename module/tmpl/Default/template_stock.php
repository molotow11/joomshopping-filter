<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
$selected = JFactory::getApplication()->input->get("stock", "-1");
?>
	
	<div class="filter-field-stock-select">
		<h3>
			<?php echo JText::_("MOD_JSHOP_EFILTER_FIELDS_PRODUCTS_IN_STOCK_TITLE"); ?>
		</h3>
		
		<select name="stock">
			<option value=""><?php echo '--- '.JText::_("MOD_JSHOP_EFILTER_FIELDS_PRODUCTS_IN_STOCK_TITLE").' ---'; ?></option>
			<option value="0"<?php if($selected == 0) echo " selected=selected"; ?>>> 0</option>
			<option value="5"<?php if($selected == 5) echo " selected=selected"; ?>>> 5</option>
			<option value="10"<?php if($selected == 10) echo " selected=selected"; ?>>> 10</option>
		</select>		
	</div>