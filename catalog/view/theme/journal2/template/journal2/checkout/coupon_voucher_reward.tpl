<div class="checkout-content coupon-voucher">
    <h2 class="secondary-title"><?php echo $this->journal2->settings->get('one_page_lang_coupon_voucher', 'Do you Have a Coupon or Voucher?'); ?></h2>
    <div class="cvr">
    <?php if ($coupon_status): ?>
    <div class="panel-body checkout-coupon">
        <label class="col-sm-2 control-label" for="input-coupon"><?php echo $this->journal2->settings->get('one_page_lang_coupon_placeholder', 'Enter coupon code'); ?></label>
        <div class="input-group">
            <input type="text" name="coupon" value="<?php echo $coupon; ?>" placeholder="<?php echo $this->journal2->settings->get('one_page_lang_coupon_placeholder', 'Enter coupon code'); ?>" id="input-coupon" class="form-control" />
            <span class="input-group-btn">
                <input type="button" value="<?php echo $this->journal2->settings->get('one_page_lang_coupon_button', 'Apply Coupon'); ?>" id="button-coupon" data-loading-text="<?php echo $text_loading; ?>"  class="btn-primary button" />
            </span>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($voucher_status): ?>
    <div class="panel-body checkout-voucher">
        <label class="col-sm-2 control-label" for="input-voucher"><?php echo $this->journal2->settings->get('one_page_lang_voucher_placeholder', 'Enter voucher code'); ?></label>
        <div class="input-group">
            <input type="text" name="voucher" value="<?php echo $voucher; ?>" placeholder="<?php echo $this->journal2->settings->get('one_page_lang_voucher_placeholder', 'Enter voucher code'); ?>" id="input-voucher" class="form-control" />
            <span class="input-group-btn">
                <input type="button" value="<?php echo $this->journal2->settings->get('one_page_lang_voucher_button', 'Apply Voucher'); ?>" id="button-voucher" data-loading-text="<?php echo $text_loading; ?>"  class="btn-primary button" />
            </span>
        </div>
    </div>
    <?php endif; ?>
    <?php if ($reward_status): ?>
        <div class="panel-body checkout-reward">
            <label class="col-sm-2 control-label" for="input-reward"><?php echo $this->journal2->settings->get('one_page_lang_reward_placeholder', 'Enter reward points'); ?></label>
            <div class="reward-stats">
                <span class="reward-total"><?php echo $reward_total; ?></span>
                <span class="reward-use"><?php echo $reward_use; ?></span>
            </div>
            <div class="input-group">
                <input type="text" name="reward" value="<?php echo $reward; ?>" placeholder="<?php echo $this->journal2->settings->get('one_page_lang_reward_placeholder', 'Enter reward points'); ?>" id="input-reward" class="form-control" />
            <span class="input-group-btn">
                <input type="button" value="<?php echo $this->journal2->settings->get('one_page_lang_reward_button', 'Apply Points'); ?>" id="button-reward" data-loading-text="<?php echo $text_loading; ?>"  class="btn-primary button" />
            </span>
            </div>
        </div>
    <?php endif; ?>
    </div>
</div>
