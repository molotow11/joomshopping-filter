<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

$checked = JFactory::getApplication()->input->get("char".$filter->id);

?>
	
	<div class="filter-field-char-multi multiple-select">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<select name="char<?php echo $filter->id; ?>[]" multiple="multiple">			
		<?php
		if($char_vals) {
			foreach ($char_vals as $value) {
				if($value->hidden) {
					continue;
				}
				$selected = '';
				if($checked) {
					foreach ($checked as $check) {
						if ($check == $value->id) $selected = ' selected="selected"';
					}
				}
				echo "<option value='".$value->id."'".$selected.">".$value->name."</option>";
			}
		}
		?>			
		</select>	
	</div>