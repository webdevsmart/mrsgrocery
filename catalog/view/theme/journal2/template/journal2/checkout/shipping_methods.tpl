<div class="checkout-content checkout-shipping-methods">
<?php if ($error_warning) { ?>
    <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
<?php } ?>
<?php if ($shipping_methods) { ?>
    <h2 class="secondary-title"><?php echo $this->journal2->settings->get('one_page_lang_ship_method', 'Shipping Method'); ?></h2>
    <?php foreach ($shipping_methods as $shipping_method) { ?>
        <p><strong><?php echo $shipping_method['title']; ?></strong></p>
        <?php if (!$shipping_method['error']) { ?>
            <?php foreach ($shipping_method['quote'] as $quote) { ?>
                <div class="radio">
                    <label>
                        <?php if ($quote['code'] == $code || !$code) { ?>
                            <?php $code = $quote['code']; ?>
                            <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>"
                                   checked="checked"/>
                        <?php } else { ?>
                            <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>"/>
                        <?php } ?>
                        <?php echo $quote['title']; ?> - <?php echo $quote['text']; ?></label>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="alert alert-danger"><?php echo $shipping_method['error']; ?></div>
        <?php } ?>
    <?php } ?>
<?php } ?>
</div>
