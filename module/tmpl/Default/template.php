<?php

/*
// Joomshopping Extended Filter and Search module by Andrey M
// molotow11@gmail.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('jquery.framework');

$path = JURI::root(true) . '/modules/mod_jshopping_extended_filter/assets/';
$input = JFactory::getApplication()->input;

?>

<link type="text/css" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script type="text/javascript" src="<?php echo $path; ?>js/jquery.multiselect.js"></script>
<link type="text/css" href="<?php echo $path; ?>css/jquery.multiselect.css" rel="stylesheet" />	
<script type="text/javascript" src="<?php echo $path; ?>js/jquery.multiselect.filter.js"></script>
<link type="text/css" href="<?php echo $path; ?>css/jquery.multiselect.filter.css" rel="stylesheet" />

<script type="text/javascript">		
	jQuery(document).ready(function() {
		var isClear = 0;
	
		jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form").submit(function() {
			jQuery(this).find("input, select").each(function() {
				if(jQuery(this).val() == '') {
					jQuery(this).attr("name", "");
				}
			});
		});
		
		<?php if($auto_submit) : ?>
		jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> select, #ExtendedFilterContainer<?php echo $module->id; ?> input[type=checkbox]").change(function() {
			if(isClear == 1) {
				return false;
			}
			submit_form_<?php echo $module->id; ?>();
		});
		<?php endif; ?>
		
		<?php if($auto_counter) : ?>
		jQuery("body").append('<div id="auto_counter<?php echo $module->id; ?>" class="auto_counter"><div class="info"><?php echo JText::_('MOD_JSHOP_EFILTER_AUTO_COUNTER_INFO_TEXT'); ?>: <span></span></div><button type="button" class="ui-widget ui-state-default ui-corner-all ui-state-hover"><?php echo JText::_('MOD_JSHOP_EFILTER_BUTTON_SHOW'); ?></button></div>');
		
		jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form").change(function(event) {
			var counter_block = jQuery("#auto_counter<?php echo $module->id; ?>");
			var filter_block = jQuery("#ExtendedFilterContainer<?php echo $module->id; ?>");
			var filter_left = filter_block.offset().left;
			var filter_right = jQuery("body").width() - filter_left - filter_block.outerWidth();
			
			//set left offset
			if(filter_left < filter_right) {
				counter_block.css("left", filter_left + filter_block.outerWidth() + 20);
			}
			else {
				counter_block.css("left", filter_left - counter_block.outerWidth() - 20);
			}
			
			//set top offset
			counter_block.css("top", jQuery(event.target).offset().top);
			
			get_count<?php echo $module->id; ?>();
		});
		jQuery("#auto_counter<?php echo $module->id; ?> button").click(function() {
			submit_form_<?php echo $module->id; ?>();
		});
		<?php endif; ?>
		
		//multi select box
		jQuery(".filter-field-char-multi select, .filter-field-attr-multi select").multiselect({
			selectedList: 4,
			checkAllText: '<?php echo JText::_("MOD_JSHOP_EFILTER_CHECK_ALL_TEXT"); ?>',
			uncheckAllText: '<?php echo JText::_("MOD_JSHOP_EFILTER_UNCHECK_ALL_TEXT"); ?>',
			noneSelectedText: '<?php echo JText::_("MOD_JSHOP_EFILTER_SELECT_OPTIONS_TEXT"); ?>'
		}).multiselectfilter({
			label: '<?php echo JText::_("Filter"); ?>', 
			placeholder: '<?php echo JText::_("Keyword"); ?>'
		});
		
		jQuery(".filter-field-category-multi select").multiselect({
			selectedList: 4,
			checkAllText: '<?php echo JText::_("MOD_JSHOP_EFILTER_CHECK_ALL_TEXT"); ?>',
			uncheckAllText: '<?php echo JText::_("MOD_JSHOP_EFILTER_UNCHECK_ALL_TEXT"); ?>',
			noneSelectedText: '<?php echo JText::_("MOD_JSHOP_EFILTER_SELECT_CATEGORY_OPTIONS_TEXT"); ?>'
		}).multiselectfilter({
			label: '<?php echo JText::_("Filter"); ?>', 
			placeholder: '<?php echo JText::_("Keyword"); ?>'
		});
		
		jQuery(".filter-field-manufacturer-multi select").multiselect({
			selectedList: 4,
			checkAllText: '<?php echo JText::_("MOD_JSHOP_EFILTER_CHECK_ALL_TEXT"); ?>',
			uncheckAllText: '<?php echo JText::_("MOD_JSHOP_EFILTER_UNCHECK_ALL_TEXT"); ?>',
			noneSelectedText: '<?php echo JText::_("MOD_JSHOP_EFILTER_SELECT_MANUFACTURERS_OPTIONS_TEXT"); ?>'
		}).multiselectfilter({
			label: '<?php echo JText::_("Filter"); ?>', 
			placeholder: '<?php echo JText::_("Keyword"); ?>'
		});
		
		jQuery(".filter-field-label-multi select").multiselect({
			selectedList: 4,
			checkAllText: '<?php echo JText::_("MOD_JSHOP_EFILTER_CHECK_ALL_TEXT"); ?>',
			uncheckAllText: '<?php echo JText::_("MOD_JSHOP_EFILTER_UNCHECK_ALL_TEXT"); ?>',
			noneSelectedText: '<?php echo JText::_("MOD_JSHOP_EFILTER_SELECT_LABEL_OPTIONS_TEXT"); ?>'
		}).multiselectfilter({
			label: '<?php echo JText::_("Filter"); ?>', 
			placeholder: '<?php echo JText::_("Keyword"); ?>'
		});
	});
	
	function submit_form_<?php echo $module->id; ?>() {
		<?php if($ajax_results == 1) : ?>
		ajax_results<?php echo $module->id; ?>();
		return false;
		<?php endif; ?>	
		jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form").submit();
	}
	
	<?php if($auto_counter) : ?>
	function get_count<?php echo $module->id; ?>() {
		var auto_counter = jQuery("#auto_counter<?php echo $module->id; ?> .info");

		auto_counter.parent().clearQueue();
		auto_counter.parent().stop();

		jQuery.ajax({
			data: jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form").find("input[value!=''], select[value!='']").serialize() + "&tmpl=count",
			type: jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form").attr('method'),
			url: jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form").attr('action'),
			success: function(response) {	
				auto_counter.find("span").html(response);
				auto_counter.parent().show();
				auto_counter.parent().animate({
					display: "block"
				}, 5000 );
				auto_counter.parent().queue(function() {
					jQuery(this).hide();
				});
			}
		});			
	}
	<?php endif; ?>
</script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

<div id="ExtendedFilterContainer<?php echo $module->id; ?>" class="ExtendedFilterContainer">
	
	<form name="ExtendedFilter<?php echo $module->id; ?>" action="<?php echo JRoute::_('index.php?option=com_jshopping&controller=search&task=result'); ?>" method="get">
	
  		<?php $app =& JFactory::getApplication(); if (!$app->getCfg('sef')): ?>
		<input type="hidden" name="option" value="com_jshopping" />
		<input type="hidden" name="controller" value="search" />
		<input type="hidden" name="task" value="result" />
		<?php endif; ?>
	
		<?php
	
			$cell_style = '';
			if($cols > 1) {
				$width = number_format(94 / $cols, 0);
				$cell_style = " style='float: left; width: ".$width."%;'";
			}
			
		?>
		
		<?php $counter = 0; ?>
		<?php foreach($list as $k=>$filter) : ?>
			
			<div class="filter-cell filter-cell<?php echo $k; ?>"<?php echo $cell_style; ?>>
			
			<?php
			
			switch($filter->type) {
				case "price" :
					if(@$filter->slider == 1) {
						require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_price_slider'));
					}
					else {
						require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_price'));
					}
				break;
				
				case "title_text" :
					require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_title_text'));
				break;
				
				case "title_az" :
					require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_title_az'));
				break;
				
				case "text" :
					require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_text'));
				break;
				
				case "code" :
					require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_code'));
				break;
				
				case "stock" :
					require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_stock'));
				break;
				
				case "categories" :
					$categories = modJShopExtendedFilterHelper::buildTreeCategory($restrict, $restcat, $restsub);
					require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_category_multi'));
				break;
				
				case "manufacturer" :
					$manufacturers = modJShopExtendedFilterHelper::getAllManufacturers($restrict, $restcat, $restsub);
					require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_manufacturers_multi'));
				break;
				
				case "labels" :
					$labels = modJShopExtendedFilterHelper::getLabels();
					require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_labels_multi'));
				break;
				
				case "date" :
					require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_date'));
				break;
				
				case "characteristic" :
					$characteristic = $filter->characteristic;
					
					if(@$filter->slider == 1) {
						require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_slider'));
						break;
					}
					
					if($filter->field_type) {
						if($dyno_chars_vals || $characteristic->type == "1") {
							$char_vals = modJShopExtendedFilterHelper::getCharValuesAuto($filter->id, $restrict, $restcat, $restsub, $restmode);
						}
						else {
							$char_vals = modJShopExtendedFilterHelper::getCharacteristicValues($filter->id);
						}
						switch($filter->field_type) {
							case "select" :
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_select'));
							break;
							case "select_multiple" :
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_multi'));
							break;
							case "radio" :
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_radio'));
							break;
							case "checkbox" :
								if($characteristic->type == "1") {
									require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_checkbox_text'));
								}
								else {
									require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_checkbox'));
								}
							break;
							case "text" :
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_text'));
							break;
							case "text_range" :
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_text_range'));
							break;
							case "text_date" :
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_text_date'));
							break;
						}
					}
					else {
						if($characteristic->type == 1) { // text
							if(@$filter->slider == 1) {
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_slider'));
							}
							else {
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_text'));
							}
						}
						else {
							if($dyno_chars_vals) {
								$char_vals = modJShopExtendedFilterHelper::getCharValuesAuto($filter->id, $restrict, $restcat, $restsub, $restmode);
							}
							else {
								$char_vals = modJShopExtendedFilterHelper::getCharacteristicValues($filter->id);
							}
							require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_characteristic_multi'));
						}
					}
				break;
				
				case "attribute" :
					$attribute = $filter->attribute;
					$attr_vals = $filter->attr_vals;
					if($attribs_only_actual_vals) {
						foreach($attr_vals as $k=>$val) {
							if(array_key_exists($filter->id, $attributes_active)) {
								if(array_key_exists($val->value_id, $attributes_active[$filter->id])) {
									$val->values_count = $attributes_active[$filter->id][$val->value_id];
								}
								else {
									unset($attr_vals[$k]);
								}
							}
							else {
								unset($attr_vals[$k]);
							}
						}
					}
					
					if($filter->field_type) {
						switch($filter->field_type) {
							case "select" :
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_attribute_select'));
							break;
							case "select_multiple" :
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_attribute_multi'));
							break;
							case "radio" :
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_attribute_radio'));
							break;
							case "checkbox" :
								require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_attribute_checkbox'));
							break;
						}
					}
					else {					
						if($attribute->attr_type == 1) {
							require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_attribute_multi'));
						}
						else if($attribute->attr_type == 2) {
							require (JModuleHelper::getLayoutPath('mod_jshopping_extended_filter', $getTemplate.DS.'template_attribute_radio'));
						}
					}
						
				break;

			}
			
			?>
			
			</div>
			
			<?php 
			
				if($cols > 1) {
					if((($counter+1) % $cols == 0) && (($counter+1) != $k)) {
						echo "<div class='clear'></div>";
					}
				}
				$counter++;
			
			?>
			
		<?php endforeach; ?>
		
		<div class='clear'></div>
		
		<?php if($button || $clear_btn) : ?>
		<p></p>
		<div class="filter-cell filter-cell-submit">
			<?php if($button) : ?>
				<input type="submit" value="<?php echo $button_text; ?>" class="button submit <?php echo $moduleclass_sfx; ?>" <?php if($ajax_results) : ?>onclick="ajax_results<?php echo $module->id; ?>(); return false;"<?php endif;?>/>
			<?php endif; ?>

			<?php if ($clear_btn):?>
				<script type="text/javascript">
					<!--
					function addCommas(nStr)
					{
						nStr += '';
						x = nStr.split('.');
						x1 = x[0];
						x2 = x.length > 1 ? '.' + x[1] : '';
						var rgx = /(\d+)(\d{3})/;
						while (rgx.test(x1)) {
							x1 = x1.replace(rgx, '$1' + '.' + '$2');
						}
						return x1 + x2;
					}
					
					function clearSearch_<?php echo $module->id; ?>() {
						isClear = 1;
						jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form select").each(function () {
							jQuery(this).val(-1);
						});
						
						jQuery(".filter-field-attr-multi select").each(function() {
							jQuery(this).multiselect("uncheckAll");
						});	
						jQuery(".filter-field-char-multi select").each(function() {
							jQuery(this).multiselect("uncheckAll");
						});		
						
						jQuery(".filter-field-category-multi select").multiselect("uncheckAll");
						
						jQuery(".filter-field-manufacturer-multi select").multiselect("uncheckAll");
						
						jQuery(".filter-field-label-multi select").multiselect("uncheckAll");
									
						jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form input.inputbox").each(function () {
							jQuery(this).val("");
						});		

						jQuery(".filter-field-price-slider").each(function() {
							var slider_min = jQuery(this).find('.ui-slider').slider("option", "min");
							var slider_max = jQuery(this).find('.ui-slider').slider("option", "max");
							jQuery(this).find('.ui-slider').slider("values", 0, slider_min);
							jQuery(this).find('.ui-slider').slider("values", 1, slider_max);
							
							jQuery(this).find(".filter-slider-amount").val(addCommas(slider_min) + " - " + addCommas(slider_max));
						});
						
						jQuery(".filter-field-char-slider").each(function() {
							var slider_min = jQuery(this).find('.ui-slider').slider("option", "min");
							var slider_max = jQuery(this).find('.ui-slider').slider("option", "max");
							jQuery(this).find('.ui-slider').slider("values", 0, slider_min);
							jQuery(this).find('.ui-slider').slider("values", 1, slider_max);
							
							jQuery(this).find(".filter-slider-amount").val(addCommas(slider_min) + " - " + addCommas(slider_max));
						});
						
						jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form input.slider_val").each(function () {
							jQuery(this).val("");
						});
								
						jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form input[type=checkbox]").each(function () {
							jQuery(this).removeAttr('checked');
						});						
						
						jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form input[type=radio]").each(function () {
							jQuery(this).removeAttr('checked');
						});	
						
						isClear = 0;
						
						<?php if($ajax_results) : ?>
						jQuery("<?php echo $ajax_container; ?>").html("");
						<?php endif; ?>
						
						<?php if($auto_submit) : ?>
						submit_form_<?php echo $module->id; ?>();
						<?php endif; ?>
						
						return false;
					}
					//-->
				</script>	

				<input type="button" value="<?php echo JText::_('MOD_JSHOP_EFILTER_BUTTON_CLEAR'); ?>" class="clear button submit <?php echo $moduleclass_sfx; ?>" onclick="clearSearch_<?php echo $module->id; ?>()" />
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		<input name="extended" value="1" type="hidden" />

		<?php if($restrict == 1 && $restmode == 1) : ?>
		<input name="restcata" value="<?php echo $restcat; ?>" type="hidden" />
		<?php endif; ?>
		
		<input name="orderby" value="<?php echo $input->get('orderby'); ?>" type="hidden" />
		<input name="orderto" value="<?php echo $input->get('orderto'); ?>" type="hidden" />
		<input name="limit" value="<?php echo $input->get('limit'); ?>" type="hidden" />
		
		<?php if($input->get("category_id") != "") : ?>
		<input name="category_id" value="<?php echo $input->get("category_id"); ?>" type="hidden" />
		<?php endif; ?>
		<input name="moduleId" value="<?php echo $module->id; ?>" type="hidden" />
		<input name="Itemid" value="<?php echo $itemid; ?>" type="hidden" />
	
	</form>
	
	<div class="clear"></div>
	
	<?php if($ajax_results) : ?>
	<?php $jshopConfig = JSFactory::getConfig(); ?>
	<script type="text/javascript">
		function ajax_results<?php echo $module->id; ?>() {
			jQuery("<?php echo $ajax_container; ?>").html("<p><img src='<?php echo JURI::root(); ?>modules/mod_jshopping_extended_filter/assets/loading.gif' /></p>");

			var module_pos = jQuery("<?php echo $ajax_container; ?>").offset();
			window.scrollTo(module_pos.left, module_pos.top - 100);
			
			data = jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form").find("input[value!=''], select[value!='']").serialize() + "&format=raw";
			
			jQuery.ajax({
				data: data,
				type: jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form").attr('method'),
				url: jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> form").attr('action'),
				success: function(response) {
					response = jQuery(response);
					response.find('div.pagination a').each(function() {
						jQuery(this).bind("click", function() {
							ajax_pagination<?php echo $module->id; ?>(jQuery(this));
							return false;
						});
					});
					jQuery("<?php echo $ajax_container; ?>").html(response);
				}
			});			
		}
		
		function ajax_pagination<?php echo $module->id; ?>(el) {
			jQuery("<?php echo $ajax_container; ?>").html("<p><img src='<?php echo JURI::root(); ?>modules/mod_jshopping_extended_filter/assets/loading.gif' /></p>");
						
			var module_pos = jQuery("<?php echo $ajax_container; ?>").offset();
			window.scrollTo(module_pos.left, module_pos.top - 70);
						
			jQuery.ajax({
				type: "GET",
				url: el.attr('href') + "&format=raw",
				success: function(response) {
					response = jQuery(response);
					response.find('div.pagination a').each(function() {
						jQuery(this).bind("click", function() {
							ajax_pagination<?php echo $module->id; ?>(jQuery(this));
							return false;
						});
					});
					jQuery("<?php echo $ajax_container; ?>").html(response);
				}
			});
		}
		
		function submitListProductFilterSortDirection() {
			var orderto = jQuery('#ExtendedFilterContainer<?php echo $module->id; ?> input[name=orderto]');
			if(orderto.val() == undefined || orderto.val() == '') {
				orderto.val("<?php echo $jshopConfig->product_sorting_direction; ?>");
			}
			orderto.val(orderto.val() ^ 1);
			submit_form_<?php echo $module->id; ?>();
			return false;
		}
	</script>
	<?php endif;?>
	
	<?php if($ajax_results && $ajax_container == "#ajax_container") : ?>
	<div id="ajax_container"></div>
	<?php endif; ?>	
</div>