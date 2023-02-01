<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JFactory::getApplication()->input->get("category");

if(!is_array($checked)) {
	$checked = Array($checked);
}

?>
	
	<div class="filter-field-category-multi multiple-select">
		<h3>
			<?php echo JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_CATEGORY_TITLE'); ?>
		</h3>
		
		<div class="values-container">
		<?php 
		if($categories) {
			foreach($categories as $category) {
			
			echo '<input name="category[]" type="checkbox" value="'.$category->category_id.'" id="'.$category->name.$category->category_id.'"';
			
			if($checked) {
				foreach ($checked as $check) {
					if ($check == $category->category_id) echo ' checked="checked"';
				}
			}
			
			echo ' /><label for="'.$category->name.$category->category_id.'">'.$category->name.'</label>';
			echo '<br />';
			
			}
		}
		?>
		</div>	
	</div>