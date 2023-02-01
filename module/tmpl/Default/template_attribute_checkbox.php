<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JFactory::getApplication()->input->get("attr".$filter->id);
			
?>
	
	<div class="filter-field-attr-checkbox">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<div class="values-container">
		<?php 
		if($attr_vals) {
			foreach($attr_vals as $value) {
			
			echo '<input name="attr'.$filter->id.'[]" type="checkbox" value="'.$value->value_id.'" id="'.$value->name.$value->value_id.'"';
			
			if($checked) {
				foreach ($checked as $check) {
					if ($check == $value->value_id) echo ' checked="checked"';
				}
			}
			
			echo ' /><label for="'.$value->name.$value->value_id.'">';
			if($value->image) {
				echo '<img src="'.JURI::root().'components/com_jshopping/files/img_attributes/'.$value->image.'" />';
			}
			echo $value->name.'</label>';
			echo '<br />';
			
			}
		}
		?>
		</div>
		
	</div>