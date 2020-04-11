<h3><?php echo $heading_title; ?></h3>
<div class="row module catalog-seller">
    <?php foreach ($sellers as $seller) { ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 one-seller-panel">
        <div class="panel panel-default mm-one-seller">
            <div class="panel-heading" style="background-image: url('<?php echo $seller['banner']; ?>');"></div>
            <div class="panel-body text-center">
                <a href="<?php echo $seller['href']; ?>"><img class="panel-profile-img" src="<?php echo $seller['thumb']; ?>" title="<?php echo $seller['nickname']; ?>" alt="<?php echo $seller['nickname']; ?>"></a>
                <h5 class="panel-title"><a href="<?php echo $seller['href']; ?>"><?php echo $seller['nickname']; ?></a></h5>
                <ul class="list-unstyled">
                    <li class="seller-country"><?php echo trim($seller['settings']['slr_city'] . ', ' . $seller['settings']['slr_country'], ',') ;?></li>
                    <li class="seller-website"><a target="_blank" href="<?php echo $seller['settings']['slr_website'] ;?>"><?php echo $seller['settings']['slr_website'] ;?></a></li>
                    <li class="seller-total-products"><a href="<?php echo $seller['products_href']; ?>"><?php echo $seller['total_products']; ?> <?php echo mb_strtolower($ms_account_products);?></a></li>
                </ul>
                <div class="mm-one-seller-products">
                    <?php foreach($seller['products'] as $product) :?>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <a href="<?php echo $product['href'] ;?>"><img class="img-thumbnail img-responsive" src="<?php echo $product['p.image'] ? $product['p.image'] : '' ;?>"></a>
                    </div>
                    <?php endforeach ;?>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>