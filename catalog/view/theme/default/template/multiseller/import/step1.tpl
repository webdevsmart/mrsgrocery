<style>
	#type_blocks{
		margin: 0 auto 10px auto;
	}
	#type_blocks .type_block{
		cursor: pointer;
		height: 100px;
		border: 1px solid #808080;
		margin: 10px;
		border-radius: 5px;

	}
	#type_blocks .type_block .name{
		margin-top: 50px;
	}
	#type_blocks .active{
		font-weight: bold;
	}
</style>
<div class="container">
	<div class="row">
		<div id="content" class="col-sm-8">
			<h3>
				<div class="pull-right">
					<button id="next_step1" class="btn btn-primary"><?php echo $ms_imports_text_import_continue; ?></button>
				</div>
				<?php echo $ms_import_text_title_step1; ?>
			</h3>
			<br />
			<div id="type_blocks" class="col-sm-12 ">
				<?php foreach($types as $type) { ?>
					<div data-type_id="<?php echo $type['id']; ?>" class="col-sm-3 type_block text-center">
						<span class="name"><?php echo $type['name']; ?></span>
					</div>
				<?php } ?>
			</div>
			<form class="form-horizontal">
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo $ms_import_text_select_config; ?></label>
					<div class="col-sm-10">
						<select id="import_config" class="form-control">
							<option selecte value="0"><?php echo $ms_import_text_select_you_config; ?></option>
							<?php foreach ($configs as $config) { ?>
								<?php if($config['config_name']) { ?>
								<option value="<?php echo $config['config_id']; ?>"><?php echo $config['config_name']; ?></option>
								<?php }else{ ?>
								<option value="<?php echo $config['config_id']; ?>"><?php echo $config['import_type']; ?> - <?php echo $config['date_added']; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>