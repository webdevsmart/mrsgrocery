<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
          <button style="display: none;" type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" id="delete-seller-import"><i class="fa fa-trash-o"></i></button>
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
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
   <?php if (isset($success) && $success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading; ?></h3>
      </div>
      <div class="panel-body">
		<div class="table-responsive">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
            <table class="list table table-bordered table-hover" id="list-seller-imports">
                <thead>
                <tr>
                    <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                    <td class="large"><?php echo $ms_catalog_imports_field_name; ?></td>
                    <td class="medium"><?php echo $ms_catalog_imports_field_seller; ?></td>
                    <td class="medium"><?php echo $ms_catalog_imports_field_date; ?></td>
                    <td class="medium"><?php echo $ms_catalog_imports_field_type; ?></td>
                    <td class="tiny"><?php echo $ms_catalog_imports_field_processed; ?></td>
                    <td class="tiny"><?php echo $ms_catalog_imports_field_added; ?></td>
                    <td class="tiny"><?php echo $ms_catalog_imports_field_updated; ?></td>
                    <td class="medium"><?php echo $ms_catalog_imports_field_errors; ?></td>
                    <td class="medium"><?php echo $ms_catalog_imports_field_actions; ?></td>
                </tr>
                <tr class="filter">
                    <td></td>
                    <td><input type="text"/></td>
                    <td><input type="text"/></td>
                    <td><input type="text" class="input-date-datepicker"/></td>
                    <td><input type="text"/></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </form>
		</div>
      </div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('#list-seller-imports').dataTable( {
		"sAjaxSource": "index.php?route=multimerch/import/getTableData&token=<?php echo $token; ?>",
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "name" },
			{ "mData": "seller" },
			{ "mData": "date" },
			{ "mData": "type" },
			{ "mData": "processed", "sClass": "text-center" },
			{ "mData": "added", "sClass": "text-center" },
			{ "mData": "updated", "sClass": "text-center" },
			{ "mData": "errors", "sClass": "text-center" },
			{ "mData": "actions", "bSortable": false}
		]
	});

	$(document).on('click', '.ms-delete-import', function() {
		return confirm("<?php echo $this->language->get('text_confirm'); ?>");
	});

	$(document).on('click', '#delete-seller-import', function(e) {
		e.preventDefault();
		var form = $('#form').serialize();
		if(form) {
			if(confirm('Are you sure?')) {
				$.ajax({
					url: 'index.php?route=multimerch/import/delete&token=<?php echo $token; ?>',
					data: form,
					type: 'post',
					dataType: 'json',
					complete: function(response) {
						console.log(response);
						window.location.reload();
					}
				});
			}
		}
	});

	$(document).on('click', '#list-seller-imports input:checkbox', function() {
		var selected_imports = $('#list-seller-imports').children('tbody').find('input:checkbox:checked');
		if(selected_imports.length > 0) {
			$('#delete-seller-import').show();
		} else {
			$('#delete-seller-import').hide();
		}
	});
});
</script>
<?php echo $footer; ?>