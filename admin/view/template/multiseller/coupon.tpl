<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a style="display: none;" id="ms-coupon-delete" data-toggle="tooltip" title="<?php echo $ms_delete; ?>" class="btn btn-danger ms-delete" data-referrer="coupon"><i class="fa fa-trash-o"></i></a>
			</div>

			<h1><?php echo $heading; ?></h1>

			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="container-fluid">
		<div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <?php echo $ms_coupon_edit_info; ?></div>

		<div class="alert alert-danger" style="display: <?php echo (isset($error_warning) && $error_warning) ? 'block' : 'none'; ?>;"><i class="fa fa-exclamation-circle"></i><?php if (isset($error_warning) && $error_warning) { echo $error_warning; } ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>

		<?php if (isset($success) && $success) { ?>
			<div class="alert alert-success" style="display: <?php echo (isset($success) && $success) ? 'block' : 'none'; ?>;"><i class="fa fa-check-circle"></i> <?php if(isset($success) && $success) { echo $success; } ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
			</div>
		<?php } ?>

		<div class="panel panel-default">
			<div class="panel-heading"></div>
			<div class="panel-body">
				<div class="table-responsive">
					<form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form-coupons">
						<table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-coupons">
							<thead>
								<tr>
									<td width="1" style="text-align: center;"><input type="checkbox" onclick="$(document).find('#list-coupons input[name*=\'selected\']').prop('checked', $(this).prop('checked'));" /></td>
									<td class="small"><?php echo $ms_date_created; ?></td>
									<td class="medium"><?php echo $ms_name; ?></td>
									<td class="medium"><?php echo $ms_coupon_code; ?></td>
									<td class="medium"><?php echo $ms_seller; ?></td>
									<td class="small"><?php echo $ms_coupon_value; ?></td>
									<td class="small"><?php echo $ms_coupon_uses; ?></td>
									<td class="small"><?php echo $ms_coupon_date_start; ?></td>
									<td class="small"><?php echo $ms_coupon_date_end; ?></td>
									<td class="small"><?php echo $ms_status; ?></td>
									<td class="small"><?php echo $ms_action; ?></td>
								</tr>
								<tr class="filter">
									<td></td>
									<td></td>
									<td><input type="text"/></td>
									<td><input type="text"/></td>
									<td><input type="text"/></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</form>
				</div>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
	var msGlobals = {
		token: '<?php echo $token; ?>',
	};
</script>
<?php echo $footer; ?>