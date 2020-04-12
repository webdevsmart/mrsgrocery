/**
 * Initializes pluploader
 * @param {String} id
 * @param {String} url
 * @param {String} size
 * @param {Boolean} multiple
 * @param {Boolean} isFile
 * @param {String} customFieldsLocation		Location is passed if uploader is used in multiple custom fields ('product_cf', 'checkout_cf' etc.)
 * @return {Number} paramsId
 */
function initUploader(id, url, size, multiple, isFile, customFieldsLocation) {
	// Id of the DOM element or DOM element itself to use as a drop zone for Drag-n-Drop
	var drop_element = (multiple ? (id + '-dragndrop') : id);
	var target = $('#' + drop_element);

	// A set of file type filters
	var filters = {
		title: (isFile ? "Files" : "Images")
	};

	// Add base url
	url = $('base').attr('href') + url;

	var uploader = new plupload.Uploader({
		browse_button : id,
		url: url,
		drop_element : drop_element,
		file_data_name: id,
		filters: filters,
		multi_selection: multiple,
		runtimes : 'html5',
		image_counter: 0,
		file_counter: 0
	});

	uploader.bind('Init', function(up, params) {
		if (uploader.features.dragdrop) {
			var style = (size == 'mini') ? 'dragndropmini' : 'dragndrop';

			target.ondragover = function(event) {
				event.dataTransfer.dropEffect = "copy";
				this.className = style + " dragover";
			};

			target.ondragenter = function() {
				this.className = style + " dragover";
			};

			target.ondragleave = function() {
				this.className = style;
			};

			target.ondrop = function() {
				this.className = style;
			};
		}
	});

	uploader.init();

	uploader.bind('BeforeUpload', function(up, file) {
		if(isFile) {
			up.settings.file_counter = $('#' + id).closest('div').siblings('.ms-image').find('.file-holder').length + 1;
			up.settings.multipart_params = {'fileCount': up.settings.file_counter}
		} else {
			up.settings.image_counter = $('#' + id).closest('div').siblings('.ms-image').find('.image-holder').length + 1;
			up.settings.multipart_params = {'imageCount': up.settings.image_counter}
		}
	});

	// Hide error messages and start upload immediately
	var error_holder = $(target).siblings('.alert').length > 0 ? $(target).siblings('.alert') : $(target).closest('form').find('.alert');
	uploader.bind('FilesAdded', function(up, files) {
		error_holder.hide();
		setTimeout(up.start(), 1); // "detach" from the main thread
	});

	// Show and hide progress bar on uploader state changes
	var progressBar = $(target).siblings('.progress').length > 0 ? $(target).siblings('.progress') : $(target).closest('form').find('.progress');
	uploader.bind('StateChanged', function(up) {
		if (up.state == plupload.STOPPED) {
			progressBar.fadeOut(300, function() { /*$(this).html("").hide();*/ });
		} else {
			progressBar.show();
		}
	});

	// Change progress bar values on upload progress
	uploader.bind('UploadProgress', function(up, file) {
		// For some reason file.percent is always set to 100, therefore this workaround is needed
		var percent = file.size > 0 ? Math.ceil(file.loaded / file.size * 100) : 100;

		progressBar.attr("aria-valuenow", percent);
		progressBar.css('width', percent + '%');
		progressBar.html(percent + '%');
	});

	// Fires on any errors
	uploader.bind("Error", function(up, err) {
		var err_message = '';
		var file = err.file;

		if (file) {
			var debug_message = err.message;
			if (err.details) {
				debug_message += " (" + err.details + ")";
			}

			if (err.code == plupload.FILE_SIZE_ERROR) {
				err_message = "Error: File too large:" + " " + file.name;
			}

			if (err.code == plupload.FILE_EXTENSION_ERROR) {
				err_message = "Error: Invalid file extension:" + " " + file.name;
			}
		}

		if (err.code === plupload.INIT_ERROR) {
			setTimeout(function() {
				uploader.destroy();
			}, 1);
			err_message = "Error: Plupload init error";
		}

		error_holder.text(err_message).show();

		up.refresh();
	});

	// Post upload actions
	uploader.bind('FileUploaded', function(up, file, data) {
		data = $.parseJSON(data.response);

		// Show back-end errors if any
		if(data.errors.length > 0) {
			error_holder.empty();
			for(error in data.errors) {
				error_holder.append(data.errors[error] + '<BR>');
			}
			error_holder.show();
		} else {
			error_holder.hide();

			var image = $(target).siblings('.ms-image').length > 0 ? $(target).siblings('.ms-image') : $(target).closest('form').find('.ms-image');

			if(multiple) {
				var holder_type = isFile ? 'file-holder' : 'image-holder';
				var field_name = isFile ? 'files[][filename]' : 'images[]';

				for(var i = 0; i < data.files.length; i++) {
					var field_value = isFile ? data.files[i].fileName : data.files[i].name;

					if(customFieldsLocation) {
						field_name = customFieldsLocation + '[' + data.files[i].custom_field_id + '][value][]';
						field_value = data.files[i].download_id;
					}

					var html = '';
					html += '<div class="' + holder_type + '">';
					html += isFile ? '<i class="fa fa-file"></i>' : '';
					html += '<input type="hidden" name="' + field_name + '" value="' + field_value + '"/>';
					html += !isFile ? '<img src="' + data.files[i].thumb + '"/>' : '';
					html += '<span class="ms-remove"><i class="fa fa-times"></i></span>';
					html += isFile ? '<span class="file-name">' + data.files[i].fileMask + '</span>' : '';
					html += '</div>';

					image.append(html);
				}
			} else if(id == 'ms-import-dragndrop') {
				$(target).hide();
				var html = '';
				html += '<div class="ms-image" id="file-holder">';
				html += '<div class="file-holder"><i class="fa fa-file"></i>';
				html += '<span class="ms-remove"><i class="fa fa-times"></i></span>';
				html += '<span class="file-name">' + data.files[0].fileMask + '</span>';
				html += '</div></div>';
				$('#attachment_code').val(data.files[0].code);
				$('#import_file_result').html(html).show();
			} else {
				image.children().hide().fadeIn(2000);
				$(target).hide();
				image.find('input').val(data.files[0].name);
				image.find('img').attr('src', data.files[0].thumb);
			}
			image.removeClass('hidden');
		}
	});

	return uploader;
}

$(document).ajaxSuccess(function() {
	setTimeout(function() {
		$(".ms-spinner" ).button('reset');
	}, 1000);
});

$(document).ajaxStart(function() {
	$(".ms-spinner").attr("data-loading-text","<i class='fa fa-spinner fa-spin '></i>")
	$(".ms-spinner").button('loading');
});

$(document).on('blur', '.ms-autocomplete', function() {
	var $this = $(this);

	if(!$this.val() || ($this.val() && $this.siblings('input[type="hidden"]').val() == 0 && $this.val() !== msLanguageDefaults.ms_account_product_shipping_elsewhere)) {
        $(this).val("");
    }
});

$(document).ready(function(){
	$('.ms-sellers-map-link').click(function(e){
		e.preventDefault();
		var current_url = document.location.toString();
		document.location.href = $(this).attr('href') + '#map-view';
	})
});