$(function() {

	$('#list-msattributes').dataTable({
		"sAjaxSource": "index.php?route=multimerch/attribute/getMsAttributeTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "name" },
			{ "mData": "group_name" },
			{ "mData": "status" },
			{ "mData": "seller" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right"}
		],
		"initComplete": function(settings, json) {
			var api = this.api();
			var statusColumn = api.column('#attr_status_column');

			$('#attr_status_select').change(function() {
				statusColumn.search($(this).val()).draw();
			});
		}
	});

	$('#list-ocattributes').dataTable({
		"sAjaxSource": "index.php?route=multimerch/attribute/getOcAttributeTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "name" },
			{ "mData": "group_name" },
			{ "mData": "status" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right"}
		],
		"initComplete": function(settings, json) {
			var api = this.api();
			var statusColumn = api.column('#oc_attr_status_column');

			$('#oc_attr_status_select').change(function() {
				statusColumn.search($(this).val()).draw();
			});
		}
	});

	// Global actions with attributes
	$(document).on('click', '#ms-attrs-approve', function(e) {
		e.preventDefault();

		var button = $(this);

		var selected_attributes = [];
		$.map($('#list-msattributes input[name="selected[]"]:checked'), function(item) {
			selected_attributes.push(parseInt($(item).val()));
		});

		if(selected_attributes.length > 0) {
			button.find('i').switchClass( "fa-check", "fa-spinner fa-spin", 0, "linear" );

			$.ajax({
				url: 'index.php?route=multimerch/attribute/jxUpdateAttribute&attribute_status=' + msGlobals.status_active + '&token=' + msGlobals.token,
				type: 'post',
				data: {selected_attributes: selected_attributes},
				dataType: 'json',
				success: function (json) {
					if (json.attribute_status) {
						button.find('i').switchClass("fa-spinner fa-spin", "fa-check", 0, "linear");

						for(var attr_id in json.attribute_status) {
							var attribute_row = $('#list-msattributes').find('input[name="selected[]"][value="' + attr_id + '"]').closest('tr');

							attribute_row.find('td:nth-child(4)').html(json.attribute_status[attr_id]).effect("highlight", {color: '#BBDF8D'}, 2000);
							attribute_row.find('.ms-attr-change-status').remove();
						}
					} else if (json.error) {
						$('.alert-danger').text(json.error).show();
					}
				}
			});
		} else {
			$('.alert-danger').text(msGlobals.error_attr_not_selected).show();
		}
	});

	// General

	var url = document.location.toString();
	if (url.match('#')) {
		$('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
		window.scrollTo(0, 0);
	}

	$(document).on('click', '.ms-attributes-topbar li a', function() {
		var buttons = $('.page-header .pull-right a');

		$.map($('input[name="selected[]"]:checked'), function(item) {
			$(item).attr('checked', false);
		});

		$('.alert-danger').hide();

		if($(this).attr('href') == '#tab-attribute') {
			$.map(buttons, function(item) {
				var new_id = $(item).attr('id').replace('attr-grs', 'attrs')
				$(item).attr('id', new_id);
				$(item).attr('data-referrer', 'attribute');
			});
		}

		if($(this).attr('href') == '#tab-attribute-group') {
			$.map(buttons, function(item) {
				var new_id = $(item).attr('id').replace('attrs', 'attr-grs')
				$(item).attr('id', new_id);
				$(item).attr('data-referrer', 'attribute_group');
			});
		}

		$('#ms-attrs-approve').hide();
		$('#ms-attrs-delete').hide();
	});

	$(document).on('click', '#list-msattributes input:checkbox', function() {
		var selected_attribute_groups = $('#list-msattributes').children('tbody').find('input:checkbox:checked');
		if(selected_attribute_groups.length > 0) {
			$('#ms-attrs-approve').show();
			$('#ms-attrs-delete').show();
		} else {
			$('#ms-attrs-approve').hide();
			$('#ms-attrs-delete').hide();
		}
	});
});