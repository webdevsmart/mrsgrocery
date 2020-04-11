
            <?php
				$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
				$ckeditor = isset($_GET['CKEditorFuncNum']) ? $_GET['CKEditorFuncNum'] : null;
				$imgpath = HTTP_CATALOG . 'image/';
				$base = $_SERVER['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER;
				$params = http_build_query(array(
					'CKEditor' 			=> isset($_GET['CKEditor']) ? $_GET['CKEditor'] : null,
					'CKEditorFuncNum' 	=> isset($_GET['CKEditorFuncNum']) ? $_GET['CKEditor'] : null,
					'langCode'			=> isset($_GET['langCode']) ? $_GET['langCode'] : null
				));
			?>
            <?php if (!$is_ajax): ?>
            <!DOCTYPE html>
            <html dir="ltr" lang="en">
            <head>
                <meta charset="UTF-8" />
                <title>Image Manager</title>
                <base href="<?php echo $base; ?>" />
                <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
                <script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
                <script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
                <link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" />
                <link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
                <link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="screen" />
                <script src="view/javascript/common.js" type="text/javascript"></script>
                <?php if ($ckeditor): ?>
                <script>
                var ckeditor = <?php echo $ckeditor; ?>;
                var imgpath = '<?php echo $imgpath; ?>';
				var ckparams = '&<?php echo $params; ?>';
                </script>
                <?php endif; ?>
                <style>
                #modal-image .modal-dialog{
                    width:100%;
                    margin: 0 auto;
                }
                .modal-header .close{
                display:none;
                }
                </style>
            </head>
            <body>
            <div id="modal-image">
            <?php endif; ?>
            
<div id="filemanager" class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title"><?php echo $heading_title; ?></h4>
    </div>
    <div class="modal-body">
      <div class="row">
        <div class="col-sm-5"><a href="<?php echo $parent; ?>" data-toggle="tooltip" title="<?php echo $button_parent; ?>" id="button-parent" class="btn btn-default"><i class="fa fa-level-up"></i></a> <a href="<?php echo $refresh; ?>" data-toggle="tooltip" title="<?php echo $button_refresh; ?>" id="button-refresh" class="btn btn-default"><i class="fa fa-refresh"></i></a>
          <button type="button" data-toggle="tooltip" title="<?php echo $button_upload; ?>" id="button-upload" class="btn btn-primary"><i class="fa fa-upload"></i></button>
          <button type="button" data-toggle="tooltip" title="<?php echo $button_folder; ?>" id="button-folder" class="btn btn-default"><i class="fa fa-folder"></i></button>
          <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" id="button-delete" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
        </div>
        <div class="col-sm-7">
          <div class="input-group">
            <input type="text" name="search" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_search; ?>" class="form-control">
            <span class="input-group-btn">
            <button type="button" data-toggle="tooltip" title="<?php echo $button_search; ?>" id="button-search" class="btn btn-primary"><i class="fa fa-search"></i></button>
            </span></div>
        </div>
      </div>
      <hr />
      <?php foreach (array_chunk($images, 4) as $image) { ?>
      <div class="row">
        <?php foreach ($image as $image) { ?>
        <div class="col-sm-3 col-xs-6 text-center">
          <?php if ($image['type'] == 'directory') { ?>
          <div class="text-center"><a href="<?php echo $image['href']; ?>" class="directory" style="vertical-align: middle;"><i class="fa fa-folder fa-5x"></i></a></div>
          <label>
            <input type="checkbox" name="path[]" value="<?php echo $image['path']; ?>" />
            <?php echo $image['name']; ?></label>
          <?php } ?>
          <?php if ($image['type'] == 'image') { ?>
          <a href="<?php echo $image['href']; ?>" class="thumbnail"><img src="<?php echo $image['thumb']; ?>" alt="<?php echo $image['name']; ?>" title="<?php echo $image['name']; ?>" /></a>
          <label>
            <input type="checkbox" name="path[]" value="<?php echo $image['path']; ?>" />
            <?php echo $image['name']; ?></label>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
      <br />
      <?php } ?>
    </div>
    <div class="modal-footer"><?php echo $pagination; ?></div>
  </div>
</div>
<script type="text/javascript"><!--
<?php if ($target) { ?>
$('a.thumbnail').on('click', function(e) {
	e.preventDefault();

	<?php if ($thumb) { ?>
	$('#<?php echo $thumb; ?>').find('img').attr('src', $(this).find('img').attr('src'));
	<?php } ?>

	$('#<?php echo $target; ?>').val($(this).parent().find('input').val());

	$('#modal-image').modal('hide');
});
<?php } ?>


            if (typeof (ckeditor) !== 'undefined') {
                $('a.thumbnail').off('click').on('click', function(e) {
                    e.preventDefault();

                    window.opener.CKEDITOR.tools.callFunction(ckeditor, imgpath + $(this).parent().find('input').attr('value'));

                    self.close();
                });
            }
            
$('a.directory').on('click', function(e) {
	e.preventDefault();

	$('#modal-image').load($(this).attr('href'));
});

$('.pagination a').on('click', function(e) {
	e.preventDefault();

	$('#modal-image').load($(this).attr('href'));
});

$('#button-parent').on('click', function(e) {
	e.preventDefault();

	$('#modal-image').load($(this).attr('href'));
});

$('#button-refresh').on('click', function(e) {
	e.preventDefault();

	$('#modal-image').load($(this).attr('href'));
});

$('input[name=\'search\']').on('keydown', function(e) {
	if (e.which == 13) {
		$('#button-search').trigger('click');
	}
});

$('#button-search').on('click', function(e) {
	var url = 'index.php?route=common/filemanager&token=<?php echo $token; ?>&directory=<?php echo $directory; ?>';

	var filter_name = $('input[name=\'search\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	<?php if ($thumb) { ?>
	url += '&thumb=' + '<?php echo $thumb; ?>';
	<?php } ?>

	<?php if ($target) { ?>
	url += '&target=' + '<?php echo $target; ?>';
	<?php } ?>

	$('#modal-image').load(url);
});
//--></script>
<script type="text/javascript"><!--
$('#button-upload').on('click', function() {
	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file[]" value="" multiple="multiple" /></form>');

	$('#form-upload input[name=\'file[]\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file[]\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=common/filemanager/upload&token=<?php echo $token; ?>&directory=<?php echo $directory; ?>',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$('#button-upload i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
					$('#button-upload').prop('disabled', true);
				},
				complete: function() {
					$('#button-upload i').replaceWith('<i class="fa fa-upload"></i>');
					$('#button-upload').prop('disabled', false);
				},
				success: function(json) {
					if (json['error']) {
						alert(json['error']);
					}

					if (json['success']) {
						alert(json['success']);

						$('#button-refresh').trigger('click');
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});

$('#button-folder').popover({
	html: true,
	placement: 'bottom',
	trigger: 'click',
	title: '<?php echo $entry_folder; ?>',
	content: function() {
		html  = '<div class="input-group">';
		html += '  <input type="text" name="folder" value="" placeholder="<?php echo $entry_folder; ?>" class="form-control">';
		html += '  <span class="input-group-btn"><button type="button" title="<?php echo $button_folder; ?>" id="button-create" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></span>';
		html += '</div>';

		return html;
	}
});

$('#button-folder').on('shown.bs.popover', function() {
	$('#button-create').on('click', function() {
		$.ajax({
			url: 'index.php?route=common/filemanager/folder&token=<?php echo $token; ?>&directory=<?php echo $directory; ?>',
			type: 'post',
			dataType: 'json',
			data: 'folder=' + encodeURIComponent($('input[name=\'folder\']').val()),
			beforeSend: function() {
				$('#button-create').prop('disabled', true);
			},
			complete: function() {
				$('#button-create').prop('disabled', false);
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
					alert(json['success']);

					$('#button-refresh').trigger('click');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
});

$('#modal-image #button-delete').on('click', function(e) {
	if (confirm('<?php echo $text_confirm; ?>')) {
		$.ajax({
			url: 'index.php?route=common/filemanager/delete&token=<?php echo $token; ?>',
			type: 'post',
			dataType: 'json',
			data: $('input[name^=\'path\']:checked'),
			beforeSend: function() {
				$('#button-delete').prop('disabled', true);
			},
			complete: function() {
				$('#button-delete').prop('disabled', false);
			},
			success: function(json) {
				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
					alert(json['success']);

					$('#button-refresh').trigger('click');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
});
//--></script>

            <?php if (!$is_ajax): ?>
                </div>
            </body>
            </html>
            <?php endif; ?>
            
