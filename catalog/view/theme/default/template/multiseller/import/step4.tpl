<div class="container">
	<div class="row">
		<div class="col-sm-8">
			<div class="col-sm-12 step_header">
				<div class="col-md-4"></div>
				<div class="col-md-4 text-center step_header_text"><?php echo $ms_import_text_title_step3; ?></div>
				<div class="col-md-4 text-right step_header_steps"><?php echo $ms_imports_text_import_steps4;?></div>
			</div>
			<form style="display:none" method="post" id="ms-import-form" action="<?php echo $import; ?>" class="ms-form form-horizontal">
				<div class="col-sm-6">
					<table class="table">
						<tr>
							<td class="text-right"><?php echo $ms_imports_text_update_field; ?>:</td>
							<td>sku
								<input type="hidden" name="update_key_id" value="2" />
								<select style="display: none;">
									<?php foreach ($fields as $fields_key=>$field) { ?>
										<?php if($field['update_key']) { ?>
											<?php if($field['oc_field_id'] == $import_data['update_key_id']){ ?>
												<option selected value="<?php echo $field['oc_field_id']; ?>"><?php echo $field['oc_field_name']; ?></option>
											<?php }else{ ?>
												<option value="<?php echo $field['oc_field_id']; ?>"><?php echo $field['oc_field_name']; ?></option>
											<?php } ?>
                                        <?php } ?>
									<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_type; ?>:</td>
							<td><?php echo $type; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_imports_text_import_filename; ?>:</td>
							<td><?php echo $filename; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_file_encoding; ?>:</td>
							<td><?php echo $file_encoding; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_start_row; ?>:</td>
							<td><?php echo $import_data['start_row']; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_finish_row; ?>:</td>
							<td><?php echo $import_data['finish_row']; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_cell_container; ?></td>
							<td><?php echo $import_data['cell_container']; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_cell_separator; ?></td>
							<td><?php echo $import_data['cell_separator']; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_default_quantity; ?>:</td>
							<td><?php echo $import_data['default_quantity']; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_default_product_status; ?>:</td>
							<td><?php echo $import_data['default_product_status']; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_delimiter_category; ?>:</td>
							<td><?php echo $import_data['delimiter_category']; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_fill_category; ?>:</td>
							<td><?php echo $import_data['fill_category']; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_stock_status; ?>:</td>
							<td><?php echo $import_data['stock_status']; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_product_approved; ?>:</td>
							<td><?php echo $import_data['product_approved']; ?></td>
						</tr>
						<tr>
							<td class="text-right"><?php echo $ms_import_text_images_path; ?>:</td>
							<td><?php echo $import_data['images_path']; ?></td>
						</tr>
					</table>
				</div>
			</form>
			<div class="col-sm-6" style="display: none;">
				<table class="table">
					<?php foreach ($fields as $fields_key=>$field) { ?>
					<tr>
						<td class="text-right"><?php echo $ms_imports_text_file_column; ?> <?php echo ($fields_key + 1); ?>:</td>
						<td><?php echo $field['oc_field_name']; ?></td>
					</tr>
					<?php } ?>
				</table>
			</div>

			<div class="col-md-12 table-responsive">
				<table class="table table-bordered">
					<thead>
					<tr class="active">
						<th><?php echo $ms_imports_text_column_label; ?></th>
						<th><?php echo $ms_imports_text_preview_information; ?></th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ($fields as $field) { ?>
					<tr>
						<td><?php echo $field['oc_field_name']; ?></td>
						<td class="preview_information">
							<?php echo $field['sample_data']; ?>
						</td>
					</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>

			<div class="col-sm-12" style="height: 300px; overflow: scroll; margin-bottom: 20px; display: none;">
				<table class="table table-bordered">
					<head>
						<tr>
							<th></th>
							<?php foreach ($fields as $field) { ?>
							<th><?php echo $field['oc_field_name']; ?></th>
							<?php } ?>
						</tr>
					</head>
					<tbody>
					<?php foreach ($samples as $row_num=>$row) { ?>
					<tr>
						<td><?php echo $row_num; ?></td>
						<?php foreach ($row as $col) { ?>
						<td><?php echo $col; ?></td>
						<?php } ?>
					</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>

            <div class="col-sm-12 step_footer">
                <div class="pull-right">
                    <button onclick="startImport();" class="btn btn-primary"><?php echo $ms_import_text_start_new_import; ?></button>
                    <input type="hidden" name="import_type" value="<?php echo $type; ?>" />
                    <?php foreach ($this->session->data['import_data'] as $data_name=>$data_value) { ?>
                    <?php if($data_name == 'update_key_id') continue; ?>
                    <?php if($data_name == 'mapping') { ?>
                    <input type="hidden" name="<?php echo $data_name; ?>" value='<?php echo serialize($data_value); ?>' />
                    <?php continue; } ?>
                    <input type="hidden" name="<?php echo $data_name; ?>" value="<?php echo htmlspecialchars($data_value); ?>" />
                    <?php } ?>
                </div>
                <button class="btn btn-default prev_step" data-next_id="2"><?php echo $ms_imports_text_import_back; ?></button>
            </div>

            <div id="import_config" class="pull-left" style="display: none;">
				<?php if($config_id) { ?>
				<button id="update_import_config" data-config_id="<?php echo $config_id; ?>" class="btn btn-primary"><?php echo $ms_imports_text_import_update_config; ?></button>
				<?php } ?>
				<button id="save_import_config" class="btn btn-primary"><?php echo $ms_imports_text_import_save_config; ?></button>
				<input name="config_name" type="text" placeholder="<?php echo $ms_imports_text_config_name; ?>" />
			</div>
		</div>
	</div>
</div>