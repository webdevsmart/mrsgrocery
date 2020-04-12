<div id="cart" class="btn-group btn-block">
  <button type="button" data-toggle="dropdown" class="btn btn-inverse btn-block btn-lg dropdown-toggle heading"><a><span id="cart-total" data-loading-text="<?php echo $text_loading; ?>&nbsp;&nbsp;"><?php echo $text_items; ?></span> <i></i></a></button>
  <div class="content">
    <ul class="cart-wrapper">
    <?php if ($products || $vouchers) { ?>
    <li class="mini-cart-info">
      <table class="table table-striped">
        <?php foreach ($products as $product) { ?>
        <tr>
          <td class="text-center image"><?php if ($product['thumb']) { ?>
            <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
            <?php } ?></td>
          <td class="text-left name"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
          <div>
              <?php if ($product['option']) { ?>
              <?php foreach ($product['option'] as $option) { ?>
              <br />
              - <small><?php echo $option['name']; ?> <?php echo $option['value']; ?></small>
              <?php } ?>
              <?php } ?>
              <?php if ($product['recurring']) { ?>
              <br />
              - <small><?php echo $text_recurring; ?> <?php echo $product['recurring']; ?></small>
              <?php } ?></div>
          </td>
          <td class="text-right quantity">x <?php echo $product['quantity']; ?></td>
          <td class="text-right total"><?php echo $product['total']; ?></td>
          <td class="text-center remove"><button type="button" onclick="cart.remove('<?php echo $product[version_compare(VERSION, '2.1', '<') ? 'key' : 'cart_id']; ?>');" title="<?php echo $button_remove; ?>" class=""><i class=""></i></button></td>
        </tr>
        <?php } ?>
        <?php foreach ($vouchers as $voucher) { ?>
        <tr>
          <td class="text-center"></td>
          <td class="text-left name"><?php echo $voucher['description']; ?></td>
          <td class="text-right quantity">x&nbsp;1</td>
          <td class="text-right total"><?php echo $voucher['amount']; ?></td>
          <td class="text-center remove"><button type="button" onclick="voucher.remove('<?php echo $voucher['key']; ?>');" title="<?php echo $button_remove; ?>" class=""><i class=""></i></button></td>
        </tr>
        <?php } ?>
      </table>
    </li>
    <li>
      <div class="mini-cart-total">
        <table class="table table-bordered">
          <?php foreach ($totals as $total) { ?>
          <tr>
            <td class="text-right right"><strong><?php echo $total['title']; ?></strong></td>
            <td class="text-right right"><?php echo $total['text']; ?></td>
          </tr>
          <?php } ?>
        </table>
        <p class="text-right checkout"><a class="button" href="<?php echo $cart; ?>"><?php echo $text_cart; ?></a>&nbsp;<a class="button" href="<?php echo $checkout; ?>"><?php echo $text_checkout; ?></a></p>
      </div>
    </li>
    <?php } else { ?>
    <li>
      <p class="text-center empty"><?php echo $text_empty; ?></p>
    </li>
    <?php } ?>
    </ul>
  </div>
</div>
