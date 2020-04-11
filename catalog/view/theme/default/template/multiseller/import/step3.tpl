<div class="container">
	<div class="row">
		<div class="col-md-8">
			<div class="col-sm-12 step_header">
				<div class="col-md-4"></div>
				<div class="col-md-4 text-center step_header_text"><?php echo $ms_import_text_title_step2; ?></div>
				<div class="col-md-4 text-right step_header_steps"><?php echo $ms_imports_text_import_steps3;?></div>
			</div>
			<div class="col-md-12 table-responsive">
				<form class="form-horizontal" id="import_fields">
					<fieldset>
					<?php if ($mapping) { ?>
						<table class="table table-bordered">
							<thead>
							<tr>
								<th><?php echo $ms_imports_text_column_label; ?></th>
								<th class="preview_information"><?php echo $ms_imports_text_preview_information; ?></th>
								<th><?php echo $ms_imports_text_product_property; ?></th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($field_captions as $field_key=>$field_caption) { ?>
							<tr>
								<td><?php echo $field_key; ?></td>
								<td class="preview_information"><?php echo $simples_fields[$field_caption]; ?></td>
								<td>
									<select class="property_selector">
										<option data-col_num="0" value="0">-</option>
										<?php foreach ($oc_field_types as $field_type_key=>$oc_field_type) { ?>
										<option data-col_num="<?php echo $field_caption; ?>" <?php if(isset($mapping[$field_caption]) AND $mapping[$field_caption] == $field_type_key) { ?>selected <?php } ?> value="<?php echo $field_type_key; ?>"><?php echo $oc_field_type['oc_field_name']; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<?php } ?>
							</tbody>
						</table>
					<?php }else{ ?>
						<table class="table table-bordered">
							<thead>
							<tr class="active">
								<th><?php echo $ms_imports_text_column_label; ?></th>
								<th><?php echo $ms_imports_text_preview_information; ?></th>
								<th><?php echo $ms_imports_text_product_property; ?></th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($field_captions as $field_key=>$field_caption) { ?>
							<tr>
								<td><?php echo $field_key; ?></td>
								<td class="preview_information">
									<?php if (isset($simples_fields[$field_caption])) { ?>
									<?php echo $simples_fields[$field_caption]; ?>
									<?php } ?>
								</td>
								<td>
									<?php $categories_keys = array(11,12,13,18,19,20); ?>
                                    <select class="property_selector">
										<option data-col_num="0" value="0">-</option>
										<?php foreach ($oc_field_types as $field_type_key=>$oc_field_type) { ?>
										<option data-col_num="<?php echo $field_caption; ?>" <?php if($field_key==$oc_field_type['csv_col_name'] AND !in_array($field_type_key, $categories_keys)) { ?>selected <?php } ?> value="<?php echo $field_type_key; ?>"><?php echo $oc_field_type['oc_field_name']; ?></option>
										<?php } ?>
									</select>
								</td>
							</tr>
							<?php } ?>
							</tbody>
						</table>
					<?php } ?>
					</fieldset>
				</form>
			</div>
            <div class="col-sm-12 step_footer">
                <div class="pull-right">
                    <button id="next_step3" class="btn btn-primary"><?php echo $ms_imports_text_import_continue; ?></button>
                </div>
                <button class="btn btn-default prev_step" data-next_id="1"><?php echo $ms_imports_text_import_back; ?></button>
            </div>
		</div>
	</div>
</div>
<script>
	(function () {

		//delete selected options for other fields
		$('#import_fields .property_selector').each(function(){
			if (this.value != 0){
				$(".property_selector option[value='"+this.value+"']").attr("disabled","disabled");
				$(".property_selector option[value='"+this.value+"']").attr("hidden","hidden");
			}
		});

		//if option is selected - delete this option for other fields
		var previous;
		$('#import_fields .property_selector').on('focus', function () {
			$('#import_fields .property_selector').removeClass('active');
			previous = this.value;
			$(".property_selector option[value='"+previous+"']").removeAttr("disabled");
			$(".property_selector option[value='"+previous+"']").removeAttr("hidden");
		}).on('change click',function() {
            $(".property_selector option[value='"+previous+"']").removeAttr("disabled");
			$(".property_selector option[value='"+previous+"']").removeAttr("hidden");
			$(this).addClass('active');
			var current_value = $(this).val();
			if (current_value != "0"){
				$(".property_selector:not(.active) option[value='"+current_value+"']").attr("disabled","disabled");
				$(".property_selector:not(.active) option[value='"+current_value+"']").attr("hidden","hidden");
			}
            previous = $(this).val();
		});
	})();
</script>