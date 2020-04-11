<?php if ($items): ?>
    <?php foreach ($items as $item): ?>
        <li>
            <?php if ($item['href']): ?>
                <a href="<?php echo $item['href']; ?>" <?php echo $item['class']; ?><?php echo $item['target']; ?>><?php echo $item['icon_left']; ?>
                    <span class="top-menu-link"><?php echo $item['name']; ?></span><?php echo $item['icon_right']; ?>
                </a>
            <?php else: ?>
                <span <?php echo $item['class']; ?><?php echo $item['target']; ?>><?php echo $item['icon_left']; ?><span><?php echo $item['name']; ?></span><?php echo $item['icon_right']; ?></span>
            <?php endif; ?>
            <?php if (isset($item['items']) && count($item['items'])): ?>
                <ul class="top-dropdown">
                    <?php foreach ($item['items'] as $item): ?>
                        <li>
                            <?php if ($item['href']): ?>
                                <a href="<?php echo $item['href']; ?>" <?php echo $item['class']; ?><?php echo $item['target']; ?>><?php echo $item['icon_left']; ?>
                                    <span class="top-menu-link"><?php echo $item['name']; ?></span><?php echo $item['icon_right']; ?>
                                </a>
                            <?php else: ?>
                                <span <?php echo $item['class']; ?><?php echo $item['target']; ?>><?php echo $item['icon_left']; ?><?php echo $item['name']; ?><?php echo $item['icon_right']; ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
<?php endif; ?>
