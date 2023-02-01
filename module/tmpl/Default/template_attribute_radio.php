<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('a.uncheck_filter<?php echo $filter->id; ?>').click(function () {
			jQuery('input[name=attr<?php echo $filter->id; ?>]').removeAttr('checked');
			return false;
		});
	});
</script>
	
	<div class="filter-field-attr-radio">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<div class="values-container">
		
			<?php
			
				foreach ($attr_vals as $value) {
					echo '<input name="attr'.$filter->id.'" type="radio" value="'.$value->value_id.'" id="'.$value->name.$value->value_id.'"';
					
					if (JFactory::getApplication()->input->get('attr'.$filter->id) == $value->value_id) echo 'checked="checked"';
					
					echo ' /><label for="'.$value->name.$value->value_id.'">';
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