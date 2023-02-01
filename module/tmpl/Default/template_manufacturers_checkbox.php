<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JFactory::getApplication()->input->get("manufacturer");
if(!$checked) {
	if(JFactory::getApplication()->input->get("controller") == "manufacturer") {
		$checked = JFactory::getApplication()->input->get("manufacturer_id");
	}
}
if(!is_array($checked)) {
	$checked = Array($checked);
}
			
?>
	
	<div class="filter-field-manuf-multi">
		<h3>
			<?php echo JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_MANUFACTURER_TITLE'); ?>
		</h3>
		
		<div class="values-container">
		<?php 
		if($manufacturers) {
			foreach($manufacturers as $manufacturer) {
			
			echo '<input name="manufacturer[]" type="checkbox" value="'.$manufacturer->manufacturer_id.'" id="'.$manufacturer->name.$manufacturer->manufacturer_id.'"';
			
			if($checked) {
				foreach ($checked as $check) {
					if ($check == $manufacturer->manufacturer_id) echo ' checked="checked"';
				}
			}
			
			echo ' /><label for="'.$manufacturer->name.$manufacturer->manufacturer_id.'">'.$manufacturer->name.'</label>';
			echo '<br />';
			
			}
		}
		?>
		</div>
		
	</div>