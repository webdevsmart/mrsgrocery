<?php echo $header; ?>
<div class="container">
	<?php if (isset($success) && $success) { ?>
	<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
	<?php } ?>

	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
			<h3>
				<?php echo $heading_title; ?>
				<div class="pull-right">
					<a href="<?php echo $prepare; ?>" class="btn btn-primary"><?php echo $ms_import_text_start_new_import; ?></a>
				</div>
			</h3>
			<br />
			<div class="table-responsive">
					<table class="list table table-bordered table-hover">
						<thead>
						<tr>
							<td class="large"><?php echo $ms_import_text_name; ?></td>
							<td class="medium"><?php echo $ms_import_text_date; ?></td>
							<td class="medium"><?php echo $ms_import_text_type; ?></td>
							<td class="tiny"><?php echo $ms_import_text_processed; ?></td>
							<td class="tiny"><?php echo $ms_import_text_added; ?></td>
							<td class="tiny"><?php echo $ms_import_text_errors; ?></td>
							<td class="medium"><?php echo $ms_import_text_actions; ?></td>
						</tr>
						<tr class="filter">
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
						</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<?php echo $footer; ?>