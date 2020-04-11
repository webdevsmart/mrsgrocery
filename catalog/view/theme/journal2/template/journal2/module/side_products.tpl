<div class="side-products-<?php echo $module_id; ?> box oc-module side-products">
    <?php if ($heading_title): ?>
    <div class="box-heading"><?php echo $heading_title; ?></div>
    <?php endif; ?>
    <div class="box-content">
        <div class="box-product">
            <?php foreach ($products as $product) { ?>
            <div class="product-grid-item <?php echo $this->journal2->settings->get('product_grid_classes'); ?> display-<?php echo $this->journal2->settings->get('product_grid_wishlist_icon_display');?> <?php echo $this->journal2->settings->get('product_grid_button_block_button');?>">
                <div class="product-wrapper">
                    <?php if ($product['thumb']) { ?>
                    <div class="image">
                        <a href="<?php echo $product['href']; ?>">
                            <img width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" />
                        </a>
                    </div>
                    <?php } ?>
                    <div class="product-details">
                        <div class="name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></div>
                        <?php if ($product['price']) { ?>
                        <div class="price">
                            <?php if (!$product['special']) { ?>
                            <?php echo $product['price']; ?>
                            <?php } else { ?>
                            <span class="price-old"><?php echo $product['price']; ?></span> <span class="price-new"><?php echo $product['special']; ?></span>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        <?php if ($product['rating']) { ?>
                        <div class="rating">
                            <?php for ($i = 1; $i <= 5; $i++) { ?>
                            <?php if ($product['rating'] < $i) { ?>
                            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
                            <?php } else { ?>
                            <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
                            <?php } ?>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>