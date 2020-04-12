$(function() {


	// Custom field groups - CFG


	$('#list-custom-field-groups').dataTable({
		"sAjaxSource": "index.php?route=multimerch/custom-field/getCFGTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "name" },
			{ "mData": "cf_count" },
			{ "mData": "status" },
			{ "mData": "sort_order" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right"}
		],
		"initComplete": function(settings, json) {
			var api = this.api();
			var statusColumn = api.column('#cfg_status_column');

			$('#cfg_status_select').change(function() {
				statusColumn.search($(this).val()).draw();
			});
		}
	});

	$(document).on('click', '#ms-cfg-create', function(e) {
		window.location.href = $('base').attr('href') + "index.php?route=multimerch/custom-field/createCFG&token=" + msGlobals.token;
	});

	$(document).on('click', '#list-custom-field-groups input:checkbox', function() {
		var selected_cfgs = $('#list-custom-field-groups').children('tbody').find('input:checkbox:checked');
		if(selected_cfgs.length > 0) {
			$('#ms-cfg-delete').show();
		} else {
			$('#ms-cfg-delete').hide();
		}
	});


	// Custom field - CF


	$('#list-custom-fields').dataTable({
		"sAjaxSource": "index.php?route=multimerch/custom-field/getCFTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "name" },
			{ "mData": "group_name" },
			{ "mData": "type", "bSortable": false },
			{ "mData": "status" },
			{ "mData": "sort_order" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right"}
		],
		"initComplete": function(settings, json) {
			var api = this.api();
			var statusColumn = api.column('#cf_status_column');

			$('#cf_status_select').change(function() {
				statusColumn.search($(this).val()).draw();
			});
		}
	});

	$(document).on('click', '#ms-cf-create', function(e) {
		window.location.href = $('base').attr('href') + "index.php?route=multimerch/custom-field/createCF&token=" + msGlobals.token;
	});

	$(document).on('click', '#list-custom-fields input:checkbox', function() {
		var selected_cfs = $('#list-custom-fields').children('tbody').find('input:checkbox:checked');
		if(selected_cfs.length > 0) {
			$('#ms-cf-delete').show();
		} else {
			$('#ms-cf-delete').hide();
		}
	});


	// General


	var url = document.location.toString();
	if (url.match('#')) {
		$('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
		window.scrollTo(0, 0);

		// This duplicate is needed for working 'back from form' button
		var buttons = $('.page-header .pull-right a');
		if(url.split('#')[1] == 'tab-cfg') {
			$.map(buttons, function(item) {
				var new_id = $(item).attr('id').replace('cf-', 'cfg-');
				$(item).attr('id', new_id);
				$(item).attr('data-referrer', 'custom_field_group');
			});
		}

		if(url.split('#')[1] == 'tab-cf') {
			$.map(buttons, function(item) {
				var new_id = $(item).attr('id').replace('cfg-', 'cf-');
				$(item).attr('id', new_id);
				$(item).attr('data-referrer', 'custom_field');
			});
		}
	}

	$(document).on('click', '.ms-custom-fields-topbar li a', function() {
		var buttons = $('.page-header .pull-right a');

		$('.alert-danger').hide();

		$.map($('input[name^="selected"]:checked'), function(item) {
			$(item).attr('checked', false);
		});

		if($(this).attr('href') == '#tab-cfg') {
			$.map(buttons, function(item) {
				var new_id = $(item).attr('id').replace('cf-', 'cfg-');
				$(item).attr('id', new_id);
				$(item).attr('data-referrer', 'custom_field_group');
			});

			$('#ms-cfg-create').attr('title', msGlobals.ms_custom_field_group_create).tooltip('fixTitle');
		}

		if($(this).attr('href') == '#tab-cf') {
			$.map(buttons, function(item) {
				var new_id = $(item).attr('id').replace('cfg-', 'cf-');
				$(item).attr('id', new_id);
				$(item).attr('data-referrer', 'custom_field');
			});

			$('#ms-cf-create').attr('title', msGlobals.ms_custom_field_create).tooltip('fixTitle');
		}

		$('#ms-cfg-delete').hide();
		$('#ms-cf-delete').hide();
	});
});