<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('a.uncheck_filter<?php echo $filter->id; ?>').click(function () {
			jQuery('input[name=char<?php echo $filter->id; ?>]').removeAttr('checked');
			return false;
		});
	});
</script>
	
	<div class="filter-field-char-radio">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<div class="values-container">
		
			<?php

				foreach ($char_vals as $value) {
					echo '<input name="char'.$filter->id.'" type="radio" value="'.$value->id.'" id="'.$value->name.$value->id.'"';
					
					if (JFactory::getApplication()->input->get('char'.$filter->id) == $value->id) echo 'checked="checked"';
					
					echo ' /><label for="'.$value->name.$value->id.'"';
					if($value->hidden) {
						echo " class='hidden-value' style='color: #999;'";
					}					
					echo '>';
					if($value->image) {
						echo '<img src="'.JURI::root().'components/com_jshopping/files/img_attributes/'.$value->image.'" />';
					}
					echo $value->name.'</label>';
					echo '<br />';
				}			
			?>
		
		</div>
		
		<p></p>
		<p>
			<a href="#" class="button uncheck uncheck_filter<?php echo $filter->id; ?>"><?php echo JText::_("MOD_JSHOP_EFILTER_UNCHECK"); ?></a>
		</p>	
		
	</div>