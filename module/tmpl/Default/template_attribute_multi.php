<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JFactory::getApplication()->input->get("attr".$filter->id);

?>
	
	<div class="filter-field-attr-multi multiple-select">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<select name="attr<?php echo $filter->id; ?>[]" multiple="multiple">
		<?php
		if($attr_vals) {
			foreach ($attr_vals as $value) {
				$selected = '';
				if($checked) {
					foreach ($checked as $check) {
						if ($check == $value->value_id) $selected = ' selected="selected"';
					}
				}
				echo "<option value='".$value->value_id."'".$selected.">".$value->name."</option>";
			}
		}
		?>			
		</select>	
	</div>