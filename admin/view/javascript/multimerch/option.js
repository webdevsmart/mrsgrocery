$(function() {
	// Options
	$('#list-ms-options').dataTable({
		"sAjaxSource": "index.php?route=multimerch/option/getMsTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "name" },
			{ "mData": "type" },
			{ "mData": "values", "bSortable": false },
			{ "mData": "status" },
			{ "mData": "seller" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right"}
		],
		"initComplete": function(settings, json) {
			var api = this.api();
			var statusColumn = api.column('#ms_status_column');

			$('#ms_status_select').change(function() {
				statusColumn.search($(this).val()).draw();
			});
		}
	});

	$('#list-oc-options').dataTable({
		"sAjaxSource": "index.php?route=multimerch/option/getOcTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "name" },
			{ "mData": "type" },
			{ "mData": "values", "bSortable": false },
			{ "mData": "status" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right"}
		],
		"initComplete": function(settings, json) {
			var api = this.api();
			var statusColumn = api.column('#oc_status_column');

			$('#oc_status_select').change(function() {
				statusColumn.search($(this).val()).draw();
			});
		}
	});

	// Global actions with options
	$(document).on('click', '#ms-opts-approve', function(e) {
		e.preventDefault();

		var button = $(this);

		var selected_options = [];
		$.map($('#list-ms-options input[name="selected[]"]:checked'), function(item) {
			selected_options.push(parseInt($(item).val()));
		});

		if(selected_options.length > 0) {
			button.find('i').switchClass( "fa-check", "fa-spinner fa-spin", 0, "linear" );

			$.ajax({
				url: 'index.php?route=multimerch/option/jxUpdateOption&option_status=' + msGlobals.status_active + '&token=' + msGlobals.token,
				type: 'post',
				data: {selected_options: selected_options},
				dataType: 'json',
				success: function (json) {
					if (json.option_status) {
						button.find('i').switchClass("fa-spinner fa-spin", "fa-check", 0, "linear");

						for (var opt_id in json.option_status) {
							var option_row = $('#list-ms-options').find('input[name="selected[]"][value="' + opt_id + '"]').closest('tr');

							option_row.find('td:nth-child(5)').html(json.option_status[opt_id]).effect("highlight", {color: '#BBDF8D'}, 2000);
							option_row.find('.ms-change-status').remove();
						}
					} else if (json.error) {
						$('.alert-danger').text(json.error).show();
					}
				}
			});
		} else {
			$('.alert-danger').text(msGlobals.error_not_selected).show();
		}
	});

	var url = document.location.toString();
	if (url.match('#')) {
		$('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
		window.scrollTo(0, 0);
	}

	$(document).on('click', '#list-ms-options input:checkbox', function() {
		var selected_options = $('#list-ms-options').children('tbody').find('input:checkbox:checked');
		if(selected_options.length > 0) {
			$('#ms-opts-approve').show();
			$('#ms-opts-delete').show();
		} else {
			$('#ms-opts-approve').hide();
			$('#ms-opts-delete').hide();
		}
	});
});