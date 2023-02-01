<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
	<div class="filter-field-manufacturer-select">
		<h3>
			<?php echo JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_MANUFACTURER_TITLE'); ?>
		</h3>
		
		<select name="manufacturer">
			<option value=''><?php echo JText::_('MOD_JSHOP_EFILTER_SEARCH_ALL_MANUFACTURERS'); ?></option>
		
		<?php
			if($manufacturers) {
				foreach($manufacturers as $manufacturer) {
					$selected = '';
					if(JFactory::getApplication()->input->get("manufacturer") == $manufacturer->manufacturer_id) {
						$selected = ' selected=selected';
					}
					echo "<option value='".$manufacturer->manufacturer_id."'".$selected.">".$manufacturer->name."</option>";
				}
			}
		?>		
		</select>
		
	</div>