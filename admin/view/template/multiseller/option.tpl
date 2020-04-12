<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $ms_seller_option_manage; ?></h1>

            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <div class="alert alert-danger" style="display: <?php echo (isset($error_warning) && $error_warning) ? 'block' : 'none'; ?>;"><i class="fa fa-exclamation-circle"></i><?php if (isset($error_warning) && $error_warning) { echo $error_warning; } ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>

        <?php if (isset($success) && $success) { ?>
        <div class="alert alert-success" style="display: <?php echo (isset($success) && $success) ? 'block' : 'none'; ?>;"><i class="fa fa-check-circle"></i> <?php if(isset($success) && $success) { echo $success; } ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-body tab-content">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab-ocoptions" data-toggle="tab"><?php echo $ms_seller_option_tab_ocoptions; ?></a></li>
                    <li><a href="#tab-msoptions" data-toggle="tab"><?php echo $ms_seller_option_tab_msoptions; ?></a></li>
                </ul>
                <div class="tab-pane active" id="tab-ocoptions">
                    <div class="table-responsive">
                        <div class="ms-tab-pane-actions">
                            <div class="pull-right">
                                <a href="<?php echo $this->url->link('catalog/option/add', 'token=' . $this->session->data['token'], true); ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                            </div>
                        </div>
                        <form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form-oc-options">
                            <table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-oc-options">
                                <thead>
                                <tr>
                                    <td class="large"><?php echo $ms_seller_option; ?></td>
                                    <td class="small"><?php echo $ms_seller_option_type; ?></td>
                                    <td class="large"><?php echo $ms_seller_option_values; ?></td>
                                    <td class="small" id="oc_status_column"><?php echo $ms_status; ?></td>
                                    <td class="small"><?php echo $ms_action; ?></td>
                                </tr>
                                <tr class="filter">
                                    <td><input type="text"/></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <select id="oc_status_select">
                                            <option></option>
                                            <?php $msOption = new ReflectionClass('MsOption'); ?>
                                            <?php foreach ($msOption->getConstants() as $cname => $cval) { ?>
                                            <?php if (strpos($cname, 'STATUS_') !== FALSE) { ?>
                                            <option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_seller_option_status_' . $cval); ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="tab-pane" id="tab-msoptions">
                    <div class="table-responsive">
                        <div class="ms-tab-pane-actions">
                            <div class="pull-right">
                                <a href="<?php echo $this->url->link('catalog/option/add', 'token=' . $this->session->data['token'], true); ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                                <a style="display: none;" id="ms-opts-approve" data-toggle="tooltip" title="<?php echo $ms_button_approve; ?>" class="btn btn-success"><i class="fa fa-check"></i></a>
                                <a style="display: none;" id="ms-opts-delete" data-toggle="tooltip" title="<?php echo $ms_delete; ?>" class="btn btn-danger ms-delete" data-referrer="option"><i class="fa fa-trash-o"></i></a>
                            </div>
                        </div>
                        <form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form-ms-options">
                            <table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-ms-options">
                                <thead>
                                <tr>
                                    <td width="1" style="text-align: center;"><input type="checkbox" onclick="$(document).find('#list-ms-options input[name*=\'selected\']').prop('checked', $(this).prop('checked'));" /></td>
                                    <td class="large"><?php echo $ms_seller_option; ?></td>
                                    <td class="small"><?php echo $ms_seller_option_type; ?></td>
                                    <td class="large"><?php echo $ms_seller_option_values; ?></td>
                                    <td class="small" id="ms_status_column"><?php echo $ms_status; ?></td>
                                    <td class="large"><?php echo $ms_seller; ?></td>
                                    <td class="small"><?php echo $ms_action; ?></td>
                                </tr>
                                <tr class="filter">
                                    <td></td>
                                    <td><input type="text"/></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <select id="ms_status_select">
                                            <option></option>
                                            <?php $msOption = new ReflectionClass('MsOption'); ?>
                                            <?php foreach ($msOption->getConstants() as $cname => $cval) { ?>
                                            <?php if (strpos($cname, 'STATUS_') !== FALSE) { ?>
                                            <option value="<?php echo $cval; ?>"><?php echo $this->language->get('ms_seller_option_status_' . $cval); ?></option>
                                            <?php } ?>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td><input type="text"/></td>
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

    </div>
</div>
<script type="text/javascript">
    var msGlobals = {
        token: '<?php echo $token; ?>',
        status_active: '<?php echo MsOption::STATUS_ACTIVE; ?>',
        error_not_selected: '<?php echo $ms_seller_option_error_not_selected; ?>'
    };
</script>
<?php echo $footer; ?>