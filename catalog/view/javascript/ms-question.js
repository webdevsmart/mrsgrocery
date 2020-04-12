// Get seller questions
$(function() {

	// Create pagination for questions
	setTimeout(function() {
		var questions = [];
		$.each($('.question') || $(), function (key, question) {
			questions.push($(question)[0].outerHTML);
		});

		if($('#questions-pag').length > 0)
			createPagination('#questions-pag', questions);

		setTimeout(function() {
			toggleAnswers();
		});
	}, 1500);

	function createPagination(name, sources) {
		var container = $(name);

		container.pagination({
			dataSource: sources,
			pageSize: 5,
			className: 'paginationjs-theme-blue paginationjs-small',
			hideWhenLessThanOnePage: true,
			showPrevious: false,
			showNext: false,
			callback: function(response){
				// script not entering here if less than one page, therefore double toggleAnswers() is needed
				container.prev().html(response);
				toggleAnswers();
			}
		});

		return container;
	}

	function toggleAnswers() {
		$.each($('.question'), function(key, item) {
			var $question = $(item);
			var $question_answers = $question.find('.question-answers');
			var question_id = $question.find('input[name="question_id"]').val();
			var total_answers = $question.find('input[name="total_question_answers"]').val();

			if(total_answers > 0) {
				$question_answers.load('index.php?route=multimerch/product_question/jxGetAnswers&question_id=' + parseInt(question_id));
			}
		});
	}

	$(document).on('click', "#addQuestion", function(e) {
		e.preventDefault();

		var product_id = $(document).find('input[name="product_id"]').val();

		var data = $('#question-form').serialize();
		data['product_id'] = product_id;

		$.ajax({
			url: 'index.php?route=multimerch/product_question/jxAddQuestion',
			type: 'post',
			data: data,
			beforeSend: function() {
				$('#error-holder').empty().hide();
				$('#success-holder').empty().hide();
			},
			success: function(json) {
				if(json.errors) {
					$('textarea[name="question"]').parent().addClass('has-error');

					$.each(json.errors, function(key, val) {
						$('#error-holder').append(val + '<br>');
					});

					$('#error-holder').show();
				} else if (json.success) {
					$('textarea[name="question"]').parent().removeClass('has-error').val('');
					$("#mm-questions").load("index.php?route=multimerch/product_question&product_id=" + product_id, function() {
                        $('#success-holder').append(json.success).show();

                        var questions = [];
                        $.each($('.question') || $(), function (key, question) {
                            questions.push($(question)[0].outerHTML);
                        });

                        if($('#questions-pag').length > 0)
                            createPagination('#questions-pag', questions);
                    });
				}
			},
			error: function(error) {
				console.log(error);
			}
		});
	});
});