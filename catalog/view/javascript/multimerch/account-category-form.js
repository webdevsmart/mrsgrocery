$(function() {


    // Categories form

    var lang_inputs = $('.lang-select-field');
    var current_language = msGlobals.config_language;
    for (var i = 0; i < lang_inputs.length; i++) {
        if ($(lang_inputs[i]).data('lang') != current_language) {
            $(lang_inputs[i]).hide();
        } else {
            $(lang_inputs[i]).show();
        }
    }

    $(".select-input-lang").on("click", function () {
        var selectedLang = $(this).data('lang');
        $('.lang-select-field').each(function () {
            if ($(this).data('lang') == selectedLang) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        $('.lang-chooser img').each(function () {
            if ($(this).data('lang') == selectedLang) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    });

	// Category parent
	function initSelectize(dom_element_id) {
		var $this = $('#' + dom_element_id);
		var $initialized = false;

		var initial_item = $('input[name="parent_id"]').data('name');
		var exclude_category_id = $('input[name="category_id"]').val();

		$this.selectize({
			valueField: 'category_id',
			labelField: 'name',
			searchField: 'name',
			maxItems: 1,
			maxOptions: 1000,
			preload: true,
			delimiter: '~',
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
					url: 'index.php?route=seller/account-category/jxAutocompleteCategories' + (exclude_category_id ? '&exclude_category_id=' + exclude_category_id : ''),
					type: 'GET',
					dataType: 'json',
					error: function () {
						callback();
					},
					success: function (res) {
						callback(res);
					}
				});
			},
			onItemAdd: function(value, item) {
				$('input[name="parent_id"]').val(value);
			},
			onItemRemove: function(value) {
				$('input[name="parent_id"]').val();
			},
			onLoad: function(data) {
				if(!$initialized) {
					var selectize = $this[0].selectize;

					if (initial_item) {
						selectize.addItem(selectize.search(initial_item).items[0].id);
					}

					$initialized = true;
				}
			}
		});
	}

	initSelectize('category_parent');

    // Category filters
    $('input[name="filter"]').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: 'index.php?route=catalog/filter/autocomplete&filter_name=' + encodeURIComponent(request),
                dataType: 'json',
                success: function (json) {
                    response($.map(json, function (item) {
                        return {
                            label: item['name'],
                            value: item['filter_id']
                        }
                    }));
                }
            });
        },
        'select': function (item) {
            $('input[name="filter"]').val('');
            $('#category-filter' + item['value']).remove();
            $('#category-filter').append('<div id="category-filter' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="category_filter[]" value="' + item['value'] + '" /></div>');
        }
    });

    $('#category-filter').delegate('.fa-minus-circle', 'click', function () {
        $(this).parent().remove();
    });

    $("#ms-submit-button").click(function () {
        var button = $(this);

        if (msGlobals.config_enable_rte == 1) {
            for (var instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        }

        $.ajax({
            type: "POST",
            dataType: "json",
            url: $('base').attr('href') + 'index.php?route=seller/account-category/jxSaveCategory',
            data: $("form#ms-new-category").serialize(),
            beforeSend: function () {
                $('.error').html('');
                $('#error-holder').hide();
                $('div.has-error').removeClass('has-error');
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
                    $('form#ms-new-category').find('.text-danger').remove();
                    $('form#ms-new-category').find('div.has-error').removeClass('has-error');

                    for (error in json.errors) {
                        if ($('[name^="' + error + '"]').length > 0) {
                            $('[name^="' + error + '"]').closest('div').addClass('has-error');
                            $('[name^="' + error + '"]').parents('div:first').append('<p class="error" id="error_' + error + '">' + json.errors[error] + '</p>');
                        }

                        $('#error-holder').append(json.errors[error] + '<BR>').show();
                    }

                    window.scrollTo(0,0);
                } else {
                    window.location = json.redirect;
                }
            }
        });
    });
});