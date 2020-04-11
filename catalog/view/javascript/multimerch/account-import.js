$(document).ready(function(){

	$('#step1').load('index.php?route=seller/account-import/step2', function() {
		initUploader('ms-import-dragndrop', 'index.php?route=seller/account-import/jxUploadFile', 'large', false, true);
	});



	// $(document).on('click', '#next_step1', function() {
	// 	var type = $('#type_blocks .active').data('type_id');
	// 	var config_id = $('#import_config').val();
	// 	$.ajax({
	// 		type: 'post',
	// 		url: 'index.php?route=seller/account-import/step1',
	// 		data: 'type='+type+'&config_id='+config_id,
	// 		dataType: 'json',
	// 		success: function(json) {
	// 			if(json.errors) {
	// 				error_text = '';
	// 				for (error in json.errors) {
	// 					error_text+= json.errors[error] + '<BR>';
	// 				}
	// 				$('#error-holder').empty().append(error_text).show();
	// 			} else {
	// 				$('#error-holder').hide();
	// 				$('#step2').load('index.php?route=seller/account-import/step2');
	// 				nextImportStep(2);
	// 			}
	// 		}
	// 	});
	// });

	$(document).on('click', '#next_step2', function() {
		var attachment_code = $('#attachment_code').val();
		var file_encoding = $('#file_encoding').val();
		$.ajax({
			type: 'post',
			url: 'index.php?route=seller/account-import/step2',
			data: $('#step2_data input, #step2_data select'),
			dataType: 'json',
			success: function(json) {
				if(json.errors) {
					error_text = '';
					for (error in json.errors) {
						error_text+= json.errors[error] + '<BR>';
					}
					$('#error-holder').empty().append(error_text).show();
					//window.scrollTo(0,0);
				} else {
					$('#error-holder').hide();
					$('#step2').load('index.php?route=seller/account-import/step3');
					nextImportStep(2);
				}
			}
		});
	});

	$(document).on('click', '#next_step3', function() {
		var mapping_data = [];
		$('#import_fields select option:selected').each(function(i,elem) {
			if($(this).val() != "0"){
				mapping_data.push(parseInt($(this).data('col_num'))+'-'+$(this).val());
			}
		});
		console.log(mapping_data);
		$.ajax({
			type: 'post',
			url: 'index.php?route=seller/account-import/step3',
			data: 'mapping_data='+JSON.stringify(mapping_data),
			dataType: 'json',
			success: function(json) {
				if(json.errors) {
					error_text = '';
					for (error in json.errors) {
						error_text+= json.errors[error] + '<BR>';
					}
					$('#error-holder').empty().append(error_text).show();
					//window.scrollTo(0,0);
				} else {
					$('#error-holder').hide();
					$('#step3').load('index.php?route=seller/account-import/step4');
					nextImportStep(3);
				}
				window.scrollTo(0,0);
			}
		});
	});

	// $('body').on('click', '#save_import_config, #update_import_config', function(){
	// 	var config_id = $(this).data('config_id');
	// 	if (config_id){
	// 		var url = 'index.php?route=seller/account-import/jxSaveConfig&config_id=' + config_id;
	// 	}else{
	// 		var url = 'index.php?route=seller/account-import/jxSaveConfig';
	// 	}
	// 	$.ajax({
	// 		url: url,
	// 		type: 'post',
	// 		dataType: 'json',
	// 		data: $('#import_config input, #ms-import-form select'),
	// 		beforeSend: function() {
	// 		},
	// 		complete: function() {
	// 		},
	// 		success: function(json) {
	// 			$('.text-danger').remove();
	// 			if (json['error']) {
	// 				$('#error-holder').empty().append(json['error']).show();
	// 				window.scrollTo(0, 0);
	// 			}else{
	// 				$('#error-holder').hide();
	// 			}
	// 			if (json['success']) {
	// 				alert(json['success']);
	// 			}
	// 		},
	// 		error: function(xhr, ajaxOptions, thrownError) {
	// 			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
	// 		}
	// 	});
	// });

	$('body').on('click', '.tabslide .tabs .active, .prev_step', function(e){
		var next_id = $(this).data('next_id');
		nextImportStep(next_id);
		window.scrollTo(0,0);
	});

	$('body').on('click', '.tabslide .tabs .tab', function(e){
		e.preventDefault();
	});
});


function nextImportStep(id) {
	$('.tabslide .tabs .tab').removeClass('active');
	$('.tabslide .tabs .tab').eq( id-1 ).addClass('active');

	$('#error-holder').hide();
	var tab_width = $('.tabs').children('.tab:first').width();
	var slider_width = $('#slider').width();

	$('#pointer').stop().animate({'left':((id - 1)*tab_width)+'px'});
	$('#slider').stop().animate({'left':-((id - 1)*slider_width)+'px'});
	$('.alert-success').hide();
}

function startImport() {
	$.ajax({
		type: 'post',
		url: 'index.php?route=seller/account-import/validate_import_data',
		//data: $('#step2_data input, #step2_data select'),
		dataType: 'json',
		success: function(json) {
			if(json.errors) {
				error_text = '';
				for (error in json.errors) {
					error_text+= json.errors[error] + '<BR>';
				}
				$('#error-holder').empty().append(error_text).show();
				window.scrollTo(0,0);
			} else {
				$('#error-holder').hide();
				$('#ms-import-form').submit();
			}
		}
	});
};

$(document).on('click', '.file-holder .ms-remove', function() {
	$(this).parent().remove();
	$('#ms-import-dragndrop').show();
});