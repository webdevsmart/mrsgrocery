$(function() {


	// Latest Questions list

	$('#list-questions').dataTable( {
		"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-question/getTableData",
		"aoColumns": [
			{ "mData": "product_name" },
			{ "mData": "customer" },
			{ "mData": "answer", "bSortable": false },
			{ "mData": "date_created" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right" }
		],
		"aaSorting": [[3, 'desc']]
	});

	$(document).on('click', '.icon-remove.ms_remove_question', function(e) {
		e.preventDefault();
		var url = $(this).attr('href');

		if(confirm('Are you sure?')) {
			$.ajax({
				type: "get",
				dataType: "json",
				url: url,
				beforeSend: function () {
					if ($('.alert-success').length > 0)
						$('.alert-success').text('').hide();
				},
				success: function (json) {
					if (json.error) {
						$('#error-holder').text('').append(json.error + '<BR>').show();
					} else {
						window.location.reload();
					}
				}
			})
		}
	});

	// Question edit form
	if($('input[name="question_id"]').length > 0) {
		var question_id = $('input[name="question_id"]').val();
		var $question_answers = $('.question-answers');

		$question_answers.load('index.php?route=multimerch/product_question/jxGetAnswers&question_id=' + parseInt(question_id));

		$(document).on('click', '#answer-btn', function(e) {
			e.preventDefault();

			var $form = $(this).closest('form');

			$.ajax({
				type: 'post',
				dataType: 'json',
				url: "index.php?route=multimerch/product_question/jxAddAnswer",
				data: {question_id: question_id, text: $form.find('textarea[name="text"]').val()},
				beforeSend: function() {
					$('#error-holder').empty().hide();
					$('#success-holder').empty().hide();
				},
				success: function (json) {
					if(json.errors) {
						for(var i = 0, length = json.errors.length; i < length; i++) {
							console.error(json.errors[i]);
						}
					} else if(json.success) {
						$question_answers.load('index.php?route=multimerch/product_question/jxGetAnswers&question_id=' + parseInt(question_id), function() {
						    console.log($('#success-holder'))
                            $('#success-holder').append(json.success).show();
                        });
					}
				}
			});
		});
	}
});