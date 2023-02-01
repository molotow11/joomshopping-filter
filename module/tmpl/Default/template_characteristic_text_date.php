<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery("input.datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
	});
</script>
	
	<div class="filter-field-char-text-date-range">
		<h3>
			<?php echo $filter->title; ?>
		</h3>
		
		<input class="datepicker inputbox" style="width: 44%; max-width: 100px;"  name="char<?php echo $filter->id; ?>-date-from" type="text" <?php if (JFactory::getApplication()->input->get('char'.$filter->id."-date-from")) echo ' value="'.JFactory::getApplication()->input->get('char'.$filter->id."-date-from").'"'; ?> />
		-
		<input class="datepicker inputbox" style="width: 44%; max-width: 100px;"  name="char<?php echo $filter->id; ?>-date-to" type="text" <?php if (JFactory::getApplication()->input->get('char'.$filter->id."-date-to")) echo ' value="'.JFactory::getApplication()->input->get('char'.$filter->id."-date-to").'"'; ?> />
	</div>