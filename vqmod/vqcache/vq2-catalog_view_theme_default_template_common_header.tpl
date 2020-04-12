<!DOCTYPE html>
<!--[if IE]><![endif]-->
<!--[if IE 8 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie8"><![endif]-->
<!--[if IE 9 ]><html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" class="ie9"><![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content= "<?php echo $keywords; ?>" />
<?php } ?>
<script src="catalog/view/javascript/jquery/jquery-2.1.1.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
<script src="catalog/view/javascript/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<link href="catalog/view/javascript/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="//fonts.googleapis.com/css?family=Open+Sans:400,400i,300,700" rel="stylesheet" type="text/css" />
<link href="catalog/view/theme/default/stylesheet/stylesheet.css" rel="stylesheet">
<?php foreach ($styles as $style) { ?>
<link href="<?php echo $style['href']; ?>" type="text/css" rel="<?php echo $style['rel']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script src="catalog/view/javascript/common.js" type="text/javascript"></script>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>

            <?php if(MsLoader::getInstance()->MsHelper->isInstalled()) { ?>
				<script type="text/javascript"> if (!window.console) console = {log: function() {}}; var config_language = <?php echo $dt_language; ?>; var msLanguageDefaults = { ms_account_product_shipping_elsewhere: "<?php echo $this->language->get('ms_account_product_shipping_elsewhere'); ?>"} </script>
			<?php } ?>
			
<?php foreach ($scripts as $script) { ?>
<script src="<?php echo $script; ?>" type="text/javascript"></script>
<?php } ?>
<?php foreach ($analytics as $analytic) { ?>
<?php echo $analytic; ?>
<?php } ?>

           	<?php if (MsLoader::getInstance()->MsHelper->isInstalled() && $this->config->get('msconf_config_seo_url_enable')) { ?>
           	<?php echo $this->load->controller('startup/multimerch_seo_microdata'); ?>
			<?php } ?>
			
</head>
<body class="<?php echo $class; ?>">
<nav id="top">
  <div class="container">
    <?php echo $currency; ?>
    <?php echo $language; ?>
    <div id="top-links" class="nav pull-right">
      <ul class="list-inline">

			    <!-- mm header menu start -->
			
        <li><a href="<?php echo $contact; ?>"><i class="fa fa-phone"></i></a> <span class="hidden-xs hidden-sm hidden-md"><?php echo $telephone; ?></span></li>
        <li class="dropdown"><a href="<?php echo $account; ?>" title="<?php echo $text_account; ?>" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_account; ?></span> <span class="caret"></span></a>
          <ul class="dropdown-menu dropdown-menu-right">
            <?php if ($logged) { ?>
            <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>

			<?php if(MsLoader::getInstance()->MsHelper->isInstalled()) { ?>
                <li role="separator" class="divider"></li>
                <li class='menu-header'><?php echo $ms_account_orders_customer ;?></li>
			<?php } ?>
			
            <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
            <li><a href="<?php echo $transaction; ?>"><?php echo $text_transaction; ?></a></li>
            <li><a href="<?php echo $download; ?>"><?php echo $text_download; ?></a></li>

			<?php if(MsLoader::getInstance()->MsHelper->isInstalled()) { ?>
                <li role="separator" class="divider"></li>
                <li class='menu-header'><?php echo $ms_seller ;?></li>
				<?php if ($ms_seller_created && $this->MsLoader->MsSeller->getStatus($this->customer->getId()) == MsSeller::STATUS_ACTIVE) { ?>
					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-dashboard', '', 'SSL'); ?>"><?php echo $ms_account_dashboard; ?></a></li>
					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-order', '', 'SSL'); ?>"><?php echo $ms_account_orders; ?></a></li>
					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-product', '', 'SSL'); ?>"><?php echo $ms_account_products; ?></a></li>
					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-transaction', '', 'SSL'); ?>"><?php echo $ms_account_transactions; ?></a></li>
					<!--<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-payment', '', 'SSL'); ?>"><?php echo $ms_account_payments; ?></a></li>-->
					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-payment-request', '', 'SSL'); ?>"><?php echo $ms_account_payment_requests; ?></a></li>

					<?php if ($this->config->get('msconf_allow_seller_coupons')) { ?>
						<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-coupon', '', 'SSL'); ?>"><?php echo $ms_seller_account_coupon; ?></a></li>
					<?php } ?>

					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-profile', '', 'SSL'); ?>"><?php echo $ms_account_sellerinfo; ?></a></li>

					<?php if ($this->config->get('msconf_reviews_enable')) { ?>
						<li><a href="<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-review', '', 'SSL'); ?>"><?php echo $ms_account_reviews; ?></a></li>
					<?php } ?>

					<?php if ($this->config->get('msconf_allow_questions')) { ?>
						<li><a href="<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-question', '', 'SSL'); ?>"><?php echo $ms_account_questions; ?></a></li>
					<?php } ?>

					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-setting', '', 'SSL'); ?>"><?php echo $ms_account_settings; ?></a></li>
				<?php } else if ($ms_seller_created && $this->MsLoader->MsSeller->getStatus($this->customer->getId()) == MsSeller::STATUS_UNPAID) { ?>
					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-payment-request', '', 'SSL'); ?>"><?php echo $ms_account_payment_requests; ?></a></li>
					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-profile', '', 'SSL'); ?>"><?php echo $ms_account_sellerinfo; ?></a></li>
					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-setting', '', 'SSL'); ?>"><?php echo $ms_account_settings; ?></a></li>
				<?php } else { ?>
					<li><a href= "<?php echo MsLoader::getInstance()->getRegistry()->get('url')->link('seller/account-profile', '', 'SSL'); ?>"><?php echo $ms_account_sellerinfo_new_short; ?></a></li>
				<?php } ?>
                <li role="separator" class="divider"></li>
			<?php } ?>
			
            <li><a href="<?php echo $logout; ?>"><?php echo $text_logout; ?></a></li>
            <?php } else { ?>
            <li><a href="<?php echo $register; ?>"><?php echo $text_register; ?></a></li>
            <li><a href="<?php echo $login; ?>"><?php echo $text_login; ?></a></li>
            <?php } ?>
          </ul>
        </li>
        <li><a href="<?php echo $wishlist; ?>" id="wishlist-total" title="<?php echo $text_wishlist; ?>"><i class="fa fa-heart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_wishlist; ?></span></a></li>
        <li><a href="<?php echo $shopping_cart; ?>" title="<?php echo $text_shopping_cart; ?>"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_shopping_cart; ?></span></a></li>
        <li><a href="<?php echo $checkout; ?>" title="<?php echo $text_checkout; ?>"><i class="fa fa-share"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_checkout; ?></span></a></li>

			    <!-- mm header menu end -->
			
      </ul>
    </div>
  </div>
</nav>
<header>
  <div class="container">
    <div class="row">
      <div class="col-sm-4">
        <div id="logo">
          <?php if ($logo) { ?>
          <a href="<?php echo $home; ?>"><img src="<?php echo $logo; ?>" title="<?php echo $name; ?>" alt="<?php echo $name; ?>" class="img-responsive" /></a>
          <?php } else { ?>
          <h1><a href="<?php echo $home; ?>"><?php echo $name; ?></a></h1>
          <?php } ?>
        </div>
      </div>
      <div class="col-sm-5"><?php echo $search; ?>
      </div>
      <div class="col-sm-3"><?php echo $cart; ?></div>
    </div>
  </div>
</header>
<?php if ($categories) { ?>
<div class="container">
  <nav id="menu" class="navbar">
    <div class="navbar-header"><span id="category" class="visible-xs"><?php echo $text_category; ?></span>
      <button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"><i class="fa fa-bars"></i></button>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
      <ul class="nav navbar-nav">
        <?php foreach ($categories as $category) { ?>
        <?php if ($category['children']) { ?>
        <li class="dropdown"><a href="<?php echo $category['href']; ?>" class="dropdown-toggle" data-toggle="dropdown"><?php echo $category['name']; ?></a>
          <div class="dropdown-menu">
            <div class="dropdown-inner">
              <?php foreach (array_chunk($category['children'], ceil(count($category['children']) / $category['column'])) as $children) { ?>
              <ul class="list-unstyled">
                <?php foreach ($children as $child) { ?>
                <li><a href="<?php echo $child['href']; ?>"><?php echo $child['name']; ?></a></li>
                <?php } ?>
              </ul>
              <?php } ?>
            </div>
            <a href="<?php echo $category['href']; ?>" class="see-all"><?php echo $text_all; ?> <?php echo $category['name']; ?></a> </div>
        </li>
        <?php } else { ?>
        <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
        <?php } ?>
        <?php } ?>
      </ul>
    </div>
  </nav>
</div>
<?php } ?>
