<div class="container">
	<div class="row">
		<div class="col-md-8">
			<div class="col-sm-12 step_header">
				<div class="col-md-4"></div>
				<div class="col-md-4 text-center step_header_text"><?php echo $ms_import_text_title_step1; ?></div>
				<div class="col-md-4 text-right step_header_steps"><?php echo $ms_imports_text_import_steps2;?></div>
			</div>
			<div class="col-md-12">
				<form id="step2_data" class="form-horizontal">
                    <fieldset>
                        <div class="alert alert-danger" style="display: none;"></div>
                        <div class="dragndrop" id="ms-import-dragndrop">
                            <p class="mm_drophere"><?php echo $ms_drag_drop_here; ?></p>
                            <p class="mm_or"><?php echo $ms_or; ?></p>
                            <a class="btn btn-default" href="#" id="ms-import"><span><?php echo $ms_import_text_select_file; ?></span></a>
                            <p class="mm_allowed"><?php echo $ms_import_text_upload_file_note; ?></p>
                        </div>
                        <div id="import_file_result" class="col-md-12 text-center" style="display: none;"></div>
                        <div class="ms-note pull-right">
                            <a href="<?php echo $example_url; ?>"><?php echo $ms_import_text_example_url; ?></a>
                        </div>
                        <div class="ms-progress progress"></div>
                    </fieldset>

                    <div class="form-group" style="display: none">
						<div class="col-sm-5">
							<?php if($filename){ ?>
							<b id="attachment_filename_result"><?php echo $ms_import_text_upload_file; ?> <span id="attachment_filename"><?php echo $filename; ?></span></b>
							<?php }else{ ?>
							<b id="attachment_filename_result" style="display:none;"><?php echo $ms_import_text_upload_file; ?> <span id="attachment_filename"></span></b>
							<?php } ?>
						</div>
					</div>

					<div class="form-group" style="display: none">
						<label class="col-sm-3 control-label"><?php echo $ms_import_text_file_encoding; ?></label>
						<div class="col-sm-5">
							<select name="file_encoding" class="form-control">
								<?php foreach ($file_encodings as $file_encoding_key=>$file_encoding_value) { ?>
									<?php if($file_encoding_key == $file_encoding){ ?>
										<option selected value="<?php echo $file_encoding_key; ?>"><?php echo $file_encoding_value; ?></option>
									<?php }else{ ?>
										<option value="<?php echo $file_encoding_key; ?>"><?php echo $file_encoding_value; ?></option>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group" style="display: none">
						<label class="col-sm-3 control-label"><?php echo $ms_import_text_rows_limits; ?></label>
						<label class="col-sm-2 control-label text-left"><?php echo $ms_import_text_start_row; ?>:</label>
						<div class="col-sm-1 control-inline">
							<input name="start_row" class="form-control" type="text" value="<?php echo $start_row; ?>" size="2"/>
						</div>
						<label class="col-sm-2 control-label"><?php echo $ms_import_text_finish_row; ?>:</label>
						<div class="col-sm-1 control-inline">
							<input name="finish_row" class="form-control" type="text" value="<?php echo $finish_row; ?>" size="2"/>
						</div>
					</div>
					<div class="form-group" style="display: none">
						<label class="col-sm-3 control-label"><?php echo $ms_import_text_separators; ?></label>
						<label class="col-sm-2 control-label text-left"><?php echo $ms_import_text_cell_separator; ?></label>
						<div class="col-sm-1 control-inline">
							<input name="cell_separator" class="form-control" type="text" value="<?php echo $cell_separator; ?>" size="2"/>
						</div>
						<label class="col-sm-2 control-label"><?php echo $ms_import_text_cell_container; ?></label>
						<div class="col-sm-1 control-inline">
							<input name="cell_container" class="form-control" type="text" value="<?php echo $cell_container; ?>" size="2	"/>
						</div>
					</div>
					<input type="hidden" value="<?php echo $attachment_code; ?>" id="attachment_code" name="attachment_code" />
				</form>
			</div>
			<div class="col-sm-12 text-right step_footer">
				<button id="next_step2" class="btn btn-primary"><?php echo $ms_imports_text_import_continue; ?></button>
			</div>
		</div>
	</div>
</div>
