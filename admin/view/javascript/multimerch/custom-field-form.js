$(function() {
	// Show only selected language's fields
	var lang_inputs = $('.lang-select-field');
	var current_language = msGlobals.current_language;
	for(var i = 0; i < lang_inputs.length; i++) {
		if($(lang_inputs[i]).data('lang') != current_language) {
			$(lang_inputs[i]).hide();
			$(lang_inputs[i]).siblings('.lang-img-icon-input').hide();
		} else {
			$(lang_inputs[i]).show();
			$(lang_inputs[i]).siblings('.lang-img-icon-input').show();
		}
	}

	// Language select
	$(".select-input-lang").on("click", function() {
		var selectedLang = $(this).data('lang');

		$('.lang-select-field').each(function() {
			if ($(this).data('lang') == selectedLang) {
				$(this).show();
				$(this).siblings('.lang-img-icon-input').show();
			} else {
				$(this).hide();
				$(this).siblings('.lang-img-icon-input').hide();
			}
		});

		$('.lang-chooser img').each(function() {
			if ($(this).data('lang') == selectedLang) {
				$(this).addClass('active');
			} else {
				$(this).removeClass('active');
			}
		});
	});

	$(document).on('click', '#ms-submit-cfg-button', function() {
		$.ajax({
			type: 'post',
			url: 'index.php?route=multimerch/custom-field/jxSaveCFG&token=' + msGlobals.token,
			data: $('#ms-cfg-form').serialize(),
			dataType: 'json',
			success: function(json) {
				if(json.errors) {
					$('#error-holder').empty();
					$('#ms-cfg-form').find('.text-danger').remove();
					$('#ms-cfg-form').find('div.has-error').removeClass('has-error');

					for (error in json.errors) {
						if ($('[name^="' + error + '"]').length > 0) {
							$('[name^="' + error + '"]').closest('div').addClass('has-error');
							$('[name^="' + error + '"]').parents('div:first').append('<div class="text-danger" id="error_' + error + '">' + json.errors[error] + '</div>');
						}

						$('#error-holder').append(json.errors[error] + '<BR>').show();
					}

					window.scrollTo(0,0);
				} else {
					window.location = json.redirect.replace('&amp;', '&');
				}
			}
		});
	});

	$(document).on('click', '#ms-submit-cf-button', function() {
		$.ajax({
			type: 'post',
			url: 'index.php?route=multimerch/custom-field/jxSaveCF&token=' + msGlobals.token,
			data: $('#ms-cf-form').serialize(),
			dataType: 'json',
			success: function(json) {
				if(json.errors) {
					$('#error-holder').empty();
					$('#ms-cf-form').find('.text-danger').remove();
					$('#ms-cf-form').find('div.has-error').removeClass('has-error');

					for (error in json.errors) {
						if ($('[name^="' + error + '"]').length > 0) {
							$('[name^="' + error + '"]').closest('div').addClass('has-error');
							$('[name^="' + error + '"]').parents('div:first').append('<div class="text-danger" id="error_' + error + '">' + json.errors[error] + '</div>');
						}

						$('#error-holder').append(json.errors[error] + '<BR>').show();
					}

					window.scrollTo(0,0);
				} else {
					window.location = json.redirect.replace('&amp;', '&');
				}
			}
		});
	});

	var initial_required_field_value = $('#cf-required').find('input[type="checkbox"]').prop('checked');

	$('select[name=\'type\']').on('change', function() {
		if (this.value == 'select' || this.value == 'radio' || this.value == 'checkbox' || this.value == 'image') {
			$('#cf-values').show();
		} else {
			$('#cf-values').hide();
		}

		if(this.value == 'text' || this.value == 'textarea') {
			$('#cf-validation').show();
		} else {
			$('#cf-validation').hide();
		}

		if(this.value == 'radio') {
			$('#cf-required').hide();
			$('#cf-required').find('input[type="checkbox"]').prop('checked', true);
		} else {
			$('#cf-required').show();
			$('#cf-required').find('input[type="checkbox"]').prop('checked', initial_required_field_value);
		}
	});

	$('select[name=\'type\']').trigger('change');

	$(document).on('click', '.ms_remove_cf_value', function() {
		$(this).closest('tr').remove();
	});

	$(document).on('click', '.ms_add_cf_value', function() {
		var lastRow = $(this).closest('#cf-values').find('tbody tr.cf_value:last input:last').attr('name');
		if (typeof lastRow == "undefined") {
			var newRowNum = 1;
		} else {
			var newRowNum = parseInt(lastRow.match(/[0-9]+/g).pop()) + 1;
		}
		var newVal = $(this).closest('#cf-values').find('tbody tr.ffSample').clone();

		newVal.find('input,select,textarea').attr('name', function(i,name) {
			if(name !== undefined) return name.replace('cf_value[0]','cf_value[' + newRowNum + ']');
		});

		$(this).closest('#cf-values').find('tbody').append(newVal.removeClass('ffSample'));

		$(this).val(0);
	});
});