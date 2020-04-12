<div class="checkout-content checkout-payment-methods">
<?php if ($error_warning) { ?>
    <div class="alert alert-warning warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    </div>
<?php } ?>
<?php if ($payment_methods) { ?>
    <h2 class="secondary-title"><?php echo $this->journal2->settings->get('one_page_lang_payment_method', 'Payment Method'); ?></h2>
    <?php foreach ($payment_methods as $payment_method) { ?>
        <div class="radio">
            <label>
                <?php if ($payment_method['code'] == $code || !$code) { ?>
                    <?php $code = $payment_method['code']; ?>
                    <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>"
                           checked="checked"/>
                <?php } else { ?>
                    <input type="radio" name="payment_method" value="<?php echo $payment_method['code']; ?>"/>
                <?php } ?>
                <?php echo $payment_method['title']; ?>
                <?php if (isset($payment_method['terms']) && $payment_method['terms']) { ?>
                    (<?php echo $payment_method['terms']; ?>)
                <?php } ?>
            </label>
        </div>
    <?php } ?>
<?php } ?>
</div>