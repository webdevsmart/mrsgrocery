$(function() {
	$.fn.dataTableExt.sErrMode = 'throw';

	if (typeof msGlobals.config_language != 'undefined') {
		$.extend($.fn.dataTable.defaults, {
			"oLanguage": {
				"sUrl": msGlobals.config_language
			}
		});
	}

	$.extend($.fn.dataTable.defaults, {
		"bProcessing": true,
		"bSortCellsTop": true,
		"bServerSide": true,
		"sPaginationType": "full_numbers",
		"aaSorting": [],
		"bAutoWidth": false,
		"bLengthChange": false,
		"iDisplayLength": 50
		//"iDisplayLength": msGlobals.config_admin_limit
		/*
		"fnDrawCallback":function(){
			if ( $('.dataTables_paginate span span.paginate_button').size()) {
				$('.dataTables_paginate')[0].style.display = "block";
			} else {
				$('.dataTables_paginate')[0].style.display = "none";
			}
		}*/
	});
	
	
	$("body").delegate(".dataTable .filter input[type='text']", "keyup",  function() {
		$(this).parents(".dataTable").dataTable().fnFilter(this.value, $(this).parents(".dataTable").find("thead tr.filter td").index($(this).parent("td")));
	});

	$(document).ready(function() {
		$('.input-date-datepicker').datetimepicker({
			format: 'YYYY-MM-DD',
			pickTime: false,
			useCurrent: false,
			focusOnShow: false,
			showClear: true
		})
		.on('dp.change', function() {
			$(this).trigger('keyup');
		})
		.attr('readonly', 'readonly');
	});

	// Fix colspan issues for multiple Datatables on the same page
	setTimeout(function() {
		$.map($('table.dataTable'), function(item) {
			if($(item).find('tbody tr:first td').length == 1) {
				$(item).find('tbody tr:first td').attr('colspan', '100%'); // @todo 8.14: check on older browsers versions
			}
		});
	}, 500);

	$(document).on('click', '.ms-delete', function() {
		var $this = $(this);

		// Get request referrer
		var referrer = $this.attr('data-referrer');

		// Delete link
		var url = 'index.php?token=' + msGlobals.token + '&route=';

		// Multiple items selected for deletion
		var selected = [];
		$.map($('input[name="selected[]"]:checked'), function(item) {
			selected.push(parseInt($(item).val()));
		});

		switch (referrer) {
			case 'attribute':
				url += 'multimerch/attribute/jxDeleteAttribute';
				break;

			case 'attribute_group':
				url += 'multimerch/attribute/jxDeleteAttributeGroup';
				break;

			case 'badge':
				url += 'multimerch/badge/delete';
				break;

			case 'category':
				url += 'multimerch/category/jxDeleteCategory';
				break;

			case 'coupon':
				url += 'multimerch/coupon/jxDeleteCoupon';
				break;

			case 'occategory':
				url += 'multimerch/category/jxDeleteOcCategory';
				break;

			case 'conversation':
				url += 'multimerch/conversation/delete';
				break;

			case 'coupon':
				url += 'multimerch/coupon/jxDeleteCoupon';
				break;

			case 'custom_field':
				url += 'multimerch/custom-field/jxDeleteCF';
				break;

			case 'custom_field_group':
				url += 'multimerch/custom-field/jxDeleteCFG';
				break;

			case 'invoice':
				url += 'multimerch/payment-request/jxDelete';
				break;

			case 'option':
				url += 'multimerch/option/jxDeleteOption';
				break;

			case 'product':
				url += 'multimerch/product/delete';
				break;

			case 'question':
				url += 'multimerch/question/delete';
				break;

			case 'review':
				url += 'multimerch/review/delete';
				break;

			case 'seller':
				url += 'multimerch/seller/delete';
				break;

			case 'seller_group':
				url += 'multimerch/seller-group/delete';
				break;

			case 'shipping_method':
				url += 'multimerch/shipping-method/delete';
				break;

			case 'social_channel':
				url += 'multimerch/social_link/delete';
				break;

			default:
				alert('Error deleting element(s)!');
				return;
		}

		if(typeof($this.data('id')) != 'undefined') {
			selected = [];
			selected.push($this.data('id'));
		}

		$.ajax({
			type: "post",
			dataType: "json",
			url: 'index.php?route=multimerch/base/jxConfirmDelete&token=' + msGlobals.token + '&referrer=' + encodeURIComponent(referrer),
			data: {selected: selected},
			success: function(confirm_msg) {
				if(confirm(confirm_msg)) {
					$.ajax({
						type: "post",
						dataType: "json",
						url: url,
						data: {selected: selected},
						success: function(json) {
							if(json.redirect) {
								window.location = json.redirect.replace('&amp;', '&');
								window.location.reload();
							} else if (json.errors) {
								var errors_html = '';
								$.map(json.errors, function(item) {
									errors_html += item + '<br/>';
								});

								$('.alert-danger').html(errors_html).show();
							}
						}
					});
				}
			}
		});
	});
});