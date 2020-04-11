<?php
    if (!function_exists('renderMultiLevelMenu2')) {
        function renderMultiLevelMenu2($menu, $show_total) {
            $html = '';
            foreach ($menu['subcategories'] as $menu_item) {
                $submenu = renderMultiLevelMenu2($menu_item, $show_total);
                $html .= '<li>';
                if ($menu_item['class'] === 'active') {
                    $span = $submenu ? ' <i><span>-</span></i>' : '';
                } else {
                    $span = $submenu ? ' <i><span>+</span></i>' : '';
                }
                $total = $show_total ? ' <span class="product-count">(' . $menu_item['total'] . ')</span>' : '';
                if ($menu_item['href']) {
                    $html .= '<a class="' . $menu_item['class'] . '" href="' . $menu_item['href'] . '"><span class="category-name">' . $menu_item['name'] . $total . '</span>' . $span . '</a>';
                } else {
                    $html .= '<a class="' . $menu_item['class'] . '">' . $menu_item['name'] . $span . '</a>';
                }
                $html .= $submenu;
                $html .= '</li>';
            }
            return $html ? '<ul>' . $html . '</ul>' : '';
        }
    }
?>
<div id="journal-side-category-<?php echo $module; ?>" class="journal-side-category-<?php echo $module_id; ?> box side-category <?php echo $class; ?>">
    <div class="box-heading"><?php echo $heading_title; ?></div>
    <div class="box-category">
        <ul>
        <?php foreach ($top_items as $item): ?>
            <li>
                <?php if ($item['type'] === 'custom'): ?>
                <?php if ($item['href']): ?>
                <a href="<?php echo $item['href']; ?>" <?php echo $item['target']; ?> class="<?php echo $item['class']; ?>"><?php echo $item['name']; ?></a>
                <?php else: ?>
                <a><?php echo $item['name']; ?></a>
                <?php endif; ?>
                <?php else: ?>
                <?php $menu = renderMultiLevelMenu2($item, $show_total); ?>
                <a href="<?php echo $item['href']; ?>" class="<?php echo $item['class']; ?>">
                    <span class="category-name"><?php echo $item['name']; ?>
                        <?php if ($show_total): ?>
                        <span class="product-count">(<?php echo $item['total']; ?>)</span>
                        <?php endif; ?>
                    </span>
                    <?php if ($menu): ?>
                    <?php if ($item['class'] === 'active'): ?>
                    <i><span>-</span></i>
                    <?php else: ?>
                    <i><span>+</span></i>
                    <?php endif; ?>
                    <?php endif; ?>
                </a>
                <?php echo $menu; ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        <?php foreach ($categories as $category): ?>
            <li>
                <?php $menu = renderMultiLevelMenu2($category, $show_total); ?>
                <a href="<?php echo $category['href']; ?>" class="<?php echo $category['class']; ?>">
                    <span class="category-name"><?php echo $category['name']; ?>
                        <?php if ($show_total): ?>
                        <span class="product-count">(<?php echo $category['total']; ?>)</span>
                        <?php endif; ?>
                    </span>
                    <?php if ($menu): ?>
                    <?php if ($category['class'] === 'active'): ?>
                    <i><span>-</span></i>
                    <?php else: ?>
                    <i><span>+</span></i>
                    <?php endif; ?>
                    <?php endif; ?>
                </a>
                <?php echo $menu; ?>
            </li>
        <?php endforeach; ?>
        <?php foreach ($bottom_items as $item): ?>
            <li>
                <?php if ($item['type'] === 'custom'): ?>
                <?php if ($item['href']): ?>
                <a href="<?php echo $item['href']; ?>" <?php echo $item['target']; ?> class="<?php echo $item['class']; ?>"><?php echo $item['name']; ?></a>
                <?php else: ?>
                <a><?php echo $item['name']; ?></a>
                <?php endif; ?>
                <?php else: ?>
                <?php $menu = renderMultiLevelMenu2($item, $show_total); ?>
                <a href="<?php echo $item['href']; ?>" class="<?php echo $item['class']; ?>">
                    <span class="category-name"><?php echo $item['name']; ?>
                        <?php if ($show_total): ?>
                        <span class="product-count">(<?php echo $item['total']; ?>)</span>
                        <?php endif; ?>
                    </span>
                    <?php if ($menu): ?>
                    <?php if ($item['class'] === 'active'): ?>
                    <i><span>-</span></i>
                    <?php else: ?>
                    <i><span>+</span></i>
                    <?php endif; ?>
                    <?php endif; ?>
                </a>
                <?php echo $menu; ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <?php if ($type === 'accordion'): ?>
    <script>
        $('#journal-side-category-<?php echo $module; ?> .box-category a i').click(function(e, first){
            e.preventDefault();
            $('+ ul', $(this).parent()).slideToggle(first ? 0 : 400);
            $(this).parent().toggleClass('active');
            $(this).html($(this).parent().hasClass('active') ? "<span>-</span>" : "<span>+</span>");
            return false;
        });
        $('#journal-side-category-<?php echo $module; ?> .is-active i').trigger('click', true);
    </script>
    <?php endif; ?>
</div>
