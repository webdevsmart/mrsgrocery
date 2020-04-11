<?php echo $header; ?>
<div id="container" class="container j-container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a href="<?php echo $breadcrumb['href']; ?>" itemprop="url"><span itemprop="title"><?php echo $breadcrumb['text']; ?></span></a></li>
    <?php } ?>
  </ul>
  <?php if ($success) { ?>
  <div class="alert alert-success success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
  <?php } ?>
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?><?php echo $column_right; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>">
      <?php echo $content_top; ?>
      <div class="row login-content">
        <div class="col-sm-6 left">
          <div class="well">
            <h2 class="secondary-title"><?php echo $text_new_customer; ?></h2>
            <div class="login-wrap">
            <p><strong><?php echo $text_register; ?></strong></p>
            <p><?php echo $text_register_account; ?></p>
            </div>
            <hr/>
            <a href="<?php echo $register; ?>" class="btn btn-primary button"><?php echo $button_continue; ?></a></div>


			<?php if(MsLoader::getInstance()->MsHelper->isInstalled()) { ?>
				<?php $msLng = MsLoader::getInstance()->getRegistry()->get('language')->load('multiseller/multiseller'); ?>
				<div class="well">
					<h2><?php echo $msLng['ms_account_register_new']; ?></h2>
					<p><strong><?php echo $msLng['ms_account_register_seller']; ?></strong></p>
					<p><?php echo $msLng['ms_account_register_seller_note']; ?></p>
					
				<a href="<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('account/register-seller', '', 'SSL'); ?>" class="btn btn-primary button"><?php echo $button_continue; ?></a>
			
				</div>
			<?php } ?>
			
				<!-- mm register form end -->
			
        </div>
        <div class="col-sm-6 right">
          <div class="well">
            <h2 class="secondary-title"><?php echo $text_returning_customer; ?></h2>
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
              <div class="login-wrap">
                <p><?php echo $text_i_am_returning_customer; ?></p>
              <div class="form-group">
                <label class="control-label" for="input-email"><?php echo $entry_email; ?></label>
                <input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-password"><?php echo $entry_password; ?></label>
                <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control" />
                <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a></div>
                </div>
              <hr/>
              <input type="submit" value="<?php echo $button_login; ?>" class="btn btn-primary button" />
              <?php if ($redirect) { ?>
              <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
              <?php } ?>
            </form>
          </div>
        </div>
      </div>
      <?php echo $content_bottom; ?></div>
    </div>
</div>
<?php echo $footer; ?>
