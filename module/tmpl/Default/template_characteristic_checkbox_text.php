<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JFactory::getApplication()->input->get("char".$filter->id);
			
?>
	
	<div class="filter-field-char-multi">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<div class="values-container">
		<?php 
		if($char_vals) {
			foreach($char_vals as $k=>$value) {		
				echo '<input name="char'.$filter->id.'[]" type="checkbox" value="'.$value->name.'" id="'.$value->name.$filter->id.$k.'"';
				
				if($checked) {
					foreach ($checked as $check) {
						if ($check == $value) echo ' checked="checked"';
					}
				}
				echo ' /><label for="'.$value->name.$filter->id.$k.'"';
				echo '>'.$value->name.'</label>';
				echo '<br />';
			}
		}
		?>
		</div>
		
	</div>