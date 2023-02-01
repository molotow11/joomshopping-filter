<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JFactory::getApplication()->input->get("category");
if(!$checked) {
	if(JFactory::getApplication()->input->get("controller") == "category") {
		$checked = JFactory::getApplication()->input->get("category_id");
	}
}
if(!is_array($checked)) {
	$checked = Array($checked);
}

?>
	
	<div class="filter-field-category-multi multiple-select">
		<h3>
			<?php echo JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_CATEGORY_TITLE'); ?>
		</h3>
		
		<select name="category[]" multiple="multiple">		
		<?php
			if($categories) {
				foreach($categories as $category) {
					$selected = '';
					if($checked) {
						foreach ($checked as $check) {
							if ($check == $category->category_id) $selected = ' selected="selected"';
						}
					}
					echo "<option value='".$category->category_id."'".$selected.">".$category->name."</option>";
				}
			}
		?>		
		</select>		
	</div>