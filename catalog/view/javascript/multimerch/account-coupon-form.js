$(function() {
	function initSelectize(field_type) {
		var $initialized = false;

		var $this = $('#coupon_' + field_type);
		var delimiter = '~';

		var valueField = '',
			labelField = '',
			searchField = '',
			jxUrl = '';

		switch(field_type) {
			case 'products':
				valueField = 'product_id';
				labelField = searchField = 'name';
				jxUrl = 'index.php?route=seller/account-coupon/jxGetProducts';
				break;

			case 'customers':
				valueField = 'customer_id';
				labelField = searchField = 'name';
				jxUrl = 'index.php?route=seller/account-coupon/jxGetCustomers';
				break;

			case 'oc_categories':
				valueField = 'category_id';
				labelField = searchField = 'name';
				jxUrl = 'index.php?route=seller/account-coupon/jxGetOcCategories';
				break;

			case 'ms_categories':
				valueField = 'category_id';
				labelField = searchField = 'name';
				jxUrl = 'index.php?route=seller/account-coupon/jxGetMsCategories';
				break;

			default:
				console.error('Unable to initialize Selectize: Wrong field type (' + field_type + ')!')
				return;
		}

		// Flag indicating items should be included or excluded
		var flag = $('select[name="coupon[flag_include_' + field_type + ']"]').val();

		$(document).on('change', 'select[name="coupon[flag_include_' + field_type + ']"]', function() {
			var $this_flag = $(this);
			var input_fields = $(document).find('input[name="coupon[' + field_type + '][' + flag + '][]"]');

			if (input_fields.length > 0) {
				$.map(input_fields, function(item) {
					$(item).attr('name', 'coupon[' + field_type + '][' + $this_flag.val() + '][]');
				});
			}

			flag = $this_flag.val();
		});

		// Initial values
		var initial_items = [];
		$.map($('input[name^="coupon[' + field_type + '][' + flag + '][]"'), function(initial_item) {
			initial_items.push($(initial_item).data('name'));
		});

		$this.selectize({
			plugins: ['remove_button'],
			valueField: valueField,
			labelField: labelField,
			searchField: searchField,
			maxOptions: 50,
			preload: true,
			delimiter: delimiter,
			create: false,
			createOnBlur: false,
			selectOnTab: true,
			render: {
				option: function (item, escape) {
					return '<div>' + escape(item.name) + '</div>';
				}
			},
			load: function (query, callback) {
				$.ajax({
					url: jxUrl + '&name=' + query,
					type: 'GET',
					dataType: 'json',
					error: function () {
						callback();
					},
					success: function (res) {
						callback(res[field_type]);
					}
				});
			},
			onItemAdd: function(value, item) {
				if ($('input[type="hidden"][name="coupon[' + field_type + '][' + flag + '][]"][value="' + value + '"]').length === 0)
					$this.after('<input type="hidden" name="coupon[' + field_type + '][' + flag + '][]" value="' + value + '" />');

				// Change categories placeholder on products select
				if (field_type == 'products') {
					var selectize_categories = $('#coupon_ms_categories').length > 0 ? $('#coupon_ms_categories')[0].selectize : $('#coupon_oc_categories')[0].selectize;
					selectize_categories.settings.placeholder = msGlobals.ms_seller_account_coupon_categories_placeholder_products_specified;
					selectize_categories.updatePlaceholder();
				}
			},
			onItemRemove: function(value) {
				$this.siblings('input[type="hidden"][name="coupon[' + field_type + '][' + flag + '][]"][value="' + value + '"]').remove();

				// Change categories placeholder on products select
				if (field_type == 'products') {
					var selectize_products = $this[0].selectize;
					var selectize_categories = $('#coupon_ms_categories').length > 0 ? $('#coupon_ms_categories')[0].selectize : $('#coupon_oc_categories')[0].selectize;
					selectize_categories.settings.placeholder = selectize_products.items.length > 0 ? msGlobals.ms_seller_account_coupon_categories_placeholder_products_specified : msGlobals.ms_seller_account_coupon_categories_placeholder;
					selectize_categories.updatePlaceholder();
				}
			},
			onLoad: function(data) {
				if(!$initialized) {
					var selectize = $this[0].selectize;

					$.each(initial_items, function(key, item) {
						selectize.addItem(selectize.search(item).items[0].id);
					});

					$initialized = true;
				}
			}
		});
	}

	initSelectize('products');
	initSelectize(msGlobals.msconf_allow_seller_categories == 1 ? 'ms_categories' : 'oc_categories');

	$(document).on('click', '#ms-submit-button', function() {
		var button = $(this);

		$.ajax({
			type: "POST",
			dataType: "json",
			url: $('base').attr('href') + 'index.php?route=seller/account-coupon/jxSaveCoupon',
			data: $("form#ms-new-coupon").serialize(),
			beforeSend: function () {
				$('.error').html('');
				$('div').removeClass('has-error');
				$('#error-holder').hide();
			},
			complete: function (jqXHR, textStatus) {
				if (textStatus != 'success') {
					$('#error-holder').empty().text(msGlobals.formError).show();
					window.scrollTo(0, 0);
				}
			},
			error: function () {
				$('#error-holder').empty().text(msGlobals.formError).show();
				window.scrollTo(0, 0);
			},
			success: function (json) {
				if (json.errors) {
					button.button('reset');

					$('#error-holder').empty();
					$('form#ms-new-coupon').find('.text-danger').remove();
					$('form#ms-new-coupon').find('div.has-error').removeClass('has-error');

					for (error in json.errors) {
						var skip_input_error_note = ['coupon[value]'];
						if ($('[name^="' + error + '"]').length > 0) {
							$('[name^="' + error + '"]').closest('div').addClass('has-error');

							if ($.inArray(error, skip_input_error_note) == -1)
								$('[name^="' + error + '"]').parents('div:first').append('<p class="error" id="error_' + error + '">' + json.errors[error] + '</p>');
						}

						$('#error-holder').append(json.errors[error] + '<BR>').show();
					}

					window.scrollTo(0, 0);
				} else {
					window.location = json.redirect;
				}
			}
		});
	});

	$("body").delegate(".date", "focusin", function(){
		$(this).datetimepicker({pickTime: false});
	});

	// Toggle currency symbol or percent on coupon type change
	$(document).on('change', '.ms-coupon-type-select', changeCouponTypeSymbol);
	changeCouponTypeSymbol();

	function changeCouponTypeSymbol() {
		var type = $('.ms-coupon-type-select').val();
		var value_input = $('input[name="coupon[value]"]');

		if (type == msGlobals.ms_coupon_type_pct) {
			// Percentage
			$('.ms-coupon-type-pct').removeClass('hidden-important').addClass('table-cell-important');
			$('.ms-coupon-type-fixed').removeClass('table-cell-important').addClass('hidden-important');
			value_input.removeClass('border-radius-left border-radius-right').addClass('border-radius-left');
		} else if (type == msGlobals.ms_coupon_type_fixed) {
			// Fixed
			$('.ms-coupon-type-pct').removeClass('table-cell-important').addClass('hidden-important');

			$.map($('.ms-coupon-type-fixed'), function(item) {
				if ($(item).text()) {
					$(item).removeClass('hidden-important').addClass('table-cell-important');

					if ($(item).hasClass('left-symbol')) {
						value_input.removeClass('border-radius-left border-radius-right').addClass('border-radius-right');
					} else if ($(item).hasClass('right-symbol')) {
						value_input.removeClass('border-radius-left border-radius-right').addClass('border-radius-left');
					}
				}
			});
		}
	}
});