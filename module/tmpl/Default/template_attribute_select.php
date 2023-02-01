<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="filter-field-attr-select">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<select name="attr<?php echo $filter->id; ?>">
			
			<option value=""><?php echo '--- '.$filter->title.' ---'; ?></option>
			
			<?php
			if($attr_vals) {
				foreach ($attr_vals as $value) {
					echo '<option value="'.$value->value_id.'" ';
					if (JFactory::getApplication()->input->get('attr'.$filter->id) == $value->value_id) {echo 'selected="selected"';}
					echo '>'.$value->name.'</option>';
				}
			}
			?>
			
		</select>
		
	</div>