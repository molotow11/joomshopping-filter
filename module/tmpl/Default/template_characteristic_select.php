<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="filter-field-char-select">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<select name="char<?php echo $filter->id; ?>">
			
			<option value=""><?php echo '--- '.$filter->title.' ---'; ?></option>
			
			<?php
			if($char_vals) {
				foreach ($char_vals as $value) {
					echo '<option value="'.$value->id.'" ';
					if (JFactory::getApplication()->input->get('char'.$filter->id) == $value->id) {echo 'selected="selected"';}
					if($value->hidden) {
						echo " class='hidden-value' style='color: #999;'";
					}
					echo '>'.$value->name.'</option>';
				}
			}
			?>
			
		</select>
		
	</div>