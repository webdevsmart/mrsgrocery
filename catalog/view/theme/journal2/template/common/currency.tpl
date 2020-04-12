<?php $type = $this->journal2->settings->get(Journal2Utils::getDevice() === 'desktop' ? 'currency_display' : 'currency_display_mobile', 'symbol'); ?>
<?php if (count($currencies) > 1): ?>
<?php
    $current_currency = '';
    foreach ($currencies as $currency) {
        if ($currency['code'] == $code) {
            switch ($type) {
                case 'symbol':
                    $current_currency = $currency['symbol_left'] ? "<span class='currency-symbol'>{$currency['symbol_left']}</span>" : "<span class='currency-symbol'>{$currency['symbol_right']}</span>";
                    break;
                case 'text':
                    $current_currency = "{$currency['title']}";
                    break;
                case 'code':
                    $current_currency = "{$currency['code']}";
                    break;
                case 'full':
                    $current_currency = $currency['symbol_left'] ? "<span class='currency-symbol'>{$currency['symbol_left']}</span> <span class='currency-text'>{$currency['title']}</span>" : "<span class='currency-text'>{$currency['title']}</span> <span class='currency-symbol'>{$currency['symbol_right']}</span>";
                    break;
            }
        }
    }
?>
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
    <div id="currency" class="<?php echo $this->journal2->settings->get('currency_display', 'never') === 'full' ? 'full-text' : ''; ?>">
        <div class="btn-group">
            <button class="dropdown-toggle" type="button" data-hover="dropdown">
                <div><?php echo $current_currency; ?></div>
            </button>
            <ul class="dropdown-menu">
                <?php foreach ($currencies as $currency): ?>
                    <?php if ($currency['symbol_left']): ?>
                        <?php if ($type === 'symbol'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $currency['code']; ?>'); $(this).closest('form').submit();"><?php echo $currency['symbol_left']; ?></a></li>
                        <?php endif; ?>
                        <?php if ($type === 'text'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $currency['code']; ?>'); $(this).closest('form').submit();"><?php echo $currency['title']; ?></a></li>
                        <?php endif; ?>
                        <?php if ($type === 'code'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $currency['code']; ?>'); $(this).closest('form').submit();"><?php echo $currency['code']; ?></a></li>
                        <?php endif; ?>
                        <?php if ($type === 'full'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $currency['code']; ?>'); $(this).closest('form').submit();"><?php echo $currency['symbol_left'];?><span class="currency-text"><?php echo $currency['title']; ?></span></a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if ($type === 'symbol'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $currency['code']; ?>'); $(this).closest('form').submit();"><?php echo $currency['symbol_right'];?></a></li>
                        <?php endif; ?>
                        <?php if ($type === 'text'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $currency['code']; ?>'); $(this).closest('form').submit();"><?php echo $currency['title']; ?></a></li>
                        <?php endif; ?>
                        <?php if ($type === 'code'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $currency['code']; ?>'); $(this).closest('form').submit();"><?php echo $currency['code']; ?></a></li>
                        <?php endif; ?>
                        <?php if ($type === 'full'): ?>
                        <li><a onclick="$(this).closest('form').find('input[name=\'code\']').val('<?php echo $currency['code']; ?>'); $(this).closest('form').submit();"><span class="currency-text"><?php echo $currency['title']; ?></span> <?php echo $currency['symbol_right']; ?></a></li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <input type="hidden" name="code" value="" />
        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
    </div>
</form>
<?php endif; ?>
