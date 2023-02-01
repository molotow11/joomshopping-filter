
jQuery.noConflict();

jQuery(document).ready(function() {

	var filtersVal = jQuery("#FiltersListVal");
	if(filtersVal.val() != '') {
		var filterValues = filtersVal.val().split("\n");
		for(var i = 0; i < filterValues.length; i++) {
			var title = filterValues[i].split(":")[1];
			
			// adds type select for chars and attrs
			var type_select = '';
			if(filterValues[i].split(":")[0] == "characteristic" || filterValues[i].split(":")[0] == "attribute") {
				var selected = filterValues[i].split(":")[3];
				var values = ["select", "select_multiple", "radio", "checkbox"];
				if(filterValues[i].split(":")[0] == "characteristic") {
					values.push("text", "text_range", "text_date");
				}
					type_select = "<select class='field_type_select'>";
					type_select += "<option value=''>" + MOD_JSHOP_EFILTER_ADMIN_SELECT_FIELD_TYPE + "</option>";	
					for(var j=0; j<values.length; j++) {
						switch(values[j]) {
							case "select" :
								type_select += "<option value='select'";
								if(selected == "select") {
									type_select += " selected='selected'";
								}
								type_select += ">Select box</option>";		
							break;
							case "select_multiple" :
								type_select += "<option value='select_multiple'";
								if(selected == "select_multiple") {
									type_select += " selected='selected'";
								}
								type_select += ">Multiple select box</option>";		
							break;
							case "radio" :
								type_select += "<option value='radio'";
								if(selected == "radio") {
									type_select += " selected='selected'";
								}
								type_select += ">Radio</option>";		
							break;
							case "checkbox" :
								type_select += "<option value='checkbox'";
								if(selected == "checkbox") {
									type_select += " selected='selected'";
								}
								type_select += ">Checkbox</option>";		
							break;
							case "text" :
								type_select += "<option value='text'";
								if(selected == "text") {
									type_select += " selected='selected'";
								}
								type_select += ">Text</option>";		
							break;
							case "text_range" :
								type_select += "<option value='text_range'";
								if(selected == "text_range") {
									type_select += " selected='selected'";
								}
								type_select += ">Text range</option>";		
							break;
							case "text_date" :
								type_select += "<option value='text_date'";
								if(selected == "text_date") {
									type_select += " selected='selected'";
								}
								type_select += ">Text date range</option>";		
							break;
						}
					}
				type_select += "</select>";
			}
			jQuery("#sortableFields").append("<li><span class='val' rel='"+filterValues[i]+"'>" + 
			title + "</span><span class='sortableRightBlock'>" + type_select + "<span class='deleteFilter'>x</span></span></li>");
		}
	}
	
	jQuery("#sortableFields").sortable({update: updateFiltersVal});
	
	jQuery("#sortableFields .deleteFilter").live('click', function() {
		jQuery(this).parent().parent().remove();
		updateFiltersVal();
	});
	
	jQuery("#sortableFields .field_type_select").live('change', function() {
		var selected = jQuery(this).find("option:selected").val();
		var value = jQuery(this).parent().siblings(".val").attr("rel").split(":");
		jQuery(this).parent().siblings(".val").attr("rel", value[0] + ":" + value[1] + ":" + value[2] + ":" + selected)
		updateFiltersVal();
	});
	
	jQuery('.FilterSelect').change(function() {
	
		var selected = jQuery(this).find('option:selected');
		
		if(selected.val() != '' && selected.val() != 0) {
		
			// add type select for chars and attrs
			var type_select = '';
			if(selected.val().split(":")[0] == "characteristic" || selected.val().split(":")[0] == "attribute") {
				type_select = 
				"<select class='field_type_select'>" + 
					"<option value=''>" + MOD_JSHOP_EFILTER_ADMIN_SELECT_FIELD_TYPE + "</option>" +			
					"<option value='select'>Select box</option>" +			
					"<option value='select_multiple'>Multiple select box</option>" +			
					"<option value='radio'>Radio</option>" +			
					"<option value='checkbox'>Checkbox</option>";
				if(selected.val().split(":")[0] == "characteristic") {			
					type_select += "<option value='text'>Text</option>" +			
					"<option value='text_range'>Text range</option>" +
					"<option value='text_date'>Text date range</option>";
				}
				type_select += "</select>";
			}
		
			jQuery("#sortableFields").append("<li><span class='val' rel='"+selected.val()+"'>"+ 
			selected.val().split(":")[1] +"</span><span class='sortableRightBlock'>" + type_select + "<span class='deleteFilter'>x</span></span></li>");
			
			updateFiltersVal();
		}
		
		jQuery('.FilterSelect').val(0).trigger('liszt:updated');
		
	});
	
});

function updateFiltersVal() {
	var FiltersVal = '';
	jQuery("#sortableFields li span.val").each(function(count) {
		if(count > 0) {
			FiltersVal = FiltersVal + "\r\n";
		}
		FiltersVal = FiltersVal + jQuery(this).attr("rel");
	});
	jQuery("#FiltersListVal").val(FiltersVal);
}