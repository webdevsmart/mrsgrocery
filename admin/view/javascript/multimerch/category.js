$(function() {
	// Categories
	$('#list-mscategories').dataTable({
		"sAjaxSource": "index.php?route=multimerch/category/getMsCategoryTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "name" },
			{ "mData": "seller" },
			{ "mData": "status" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right"}
		],
		"initComplete": function(settings, json) {
			var api = this.api();
			var statusColumn = api.column('#status_column');

			$('#status_select').change(function() {
				statusColumn.search($(this).val()).draw();
			});
		}
	});

	$('#list-occategories').dataTable({
		"sAjaxSource": "index.php?route=multimerch/category/getOcCategoryTableData&token=" + msGlobals.token,
		"aoColumns": [
			{ "mData": "name" },
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

	$(document).on('click', '.ms-cat-change-status, .ms-cat-assign-seller', function(e) {
		e.preventDefault();

		var button = $(this);
		var category_id = button.closest('tr').children('td:first').find('input:checkbox').val();
		var url = 'index.php?route=multimerch/category';

		if (button.hasClass('ms-cat-change-status')) {
			var status_id = button.data('status');
			url += '/jxChangeStatus&category_status=' + status_id;
		} else if (button.hasClass('ms-cat-assign-seller')) {
			var seller_id = button.siblings('select').val();
			url += '/jxChangeSeller&seller_id=' + seller_id + (seller_id == 0 ? '&category_status=' + msGlobals.status_active : '');
		}

		url += '&category_id=' + category_id;

		button.find('i').switchClass("fa-check", "fa-spinner fa-spin", 0, "linear");

		$.ajax({
			type: "get",
			dataType: "json",
			url: url + '&token=' + msGlobals.token,
			success: function(json) {
				if (json.category_status) {
					button.find('i').switchClass("fa-spinner fa-spin", "fa-check", 0, "linear");

					for(var cat_id in json.category_status) {
						button.closest('tr').find('td:nth-child(4)').html(json.category_status[cat_id]).effect("highlight", {color: '#BBDF8D'}, 2000);
					}

					button.closest('tr').find('.ms-cat-change-status').remove();
				} else if (json.error){
					$('.alert-danger').text(json.error).show();
				}
			}
		});
	});

	// Global actions
	$(document).on('click', '#ms-cats-approve', function(e) {
		e.preventDefault();

		var button = $(this);

		var selected_categories = [];
		$.map($('#list-mscategories input[name="selected[]"]:checked'), function(item) {
			selected_categories.push(parseInt($(item).val()));
		});

		if(selected_categories.length > 0) {
			button.find('i').switchClass( "fa-check", "fa-spinner fa-spin", 0, "linear" );

			$.ajax({
				url: 'index.php?route=multimerch/category/jxChangeStatus&category_status=' + msGlobals.status_active + '&token=' + msGlobals.token,
				type: 'post',
				data: {selected_categories: selected_categories},
				dataType: 'json',
				success: function (json) {
					if (json.category_status) {
						button.find('i').switchClass("fa-spinner fa-spin", "fa-check", 0, "linear");

						for (var cate_id in json.category_status) {
							var category_row = $(document).find('#list-mscategories input[name="selected[]"][value="' + cate_id + '"]').closest('tr');

							category_row.find('td:nth-child(4)').html(json.category_status[cate_id]).effect("highlight", {color: '#BBDF8D'}, 2000);
							category_row.find('.ms-cat-change-status').remove();
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

	// General
	var url = document.location.toString();
	if (url.match('#')) {
		$('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
		window.scrollTo(0, 0);
	}

	$(document).on('click', '#list-mscategories input:checkbox', function() {
		var selected_categories = $('#list-mscategories').children('tbody').find('input:checkbox:checked');
		if(selected_categories.length > 0) {
			$('#ms-cats-approve').show();
			$('#ms-cats-delete').show();
		} else {
			$('#ms-cats-approve').hide();
			$('#ms-cats-delete').hide();
		}
	});
});