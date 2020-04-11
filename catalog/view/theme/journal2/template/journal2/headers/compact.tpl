<header class="journal-header-compact">
    <div class="header">
    <div id="header" class="journal-header">

        <div class="journal-logo">
            <?php if ($logo) { ?>
                <div id="logo">
                    <a href="<?php echo str_replace('index.php?route=common/home', '', $home); ?>">
                        <?php echo Journal2Utils::getLogo($this->config); ?>
                    </a>
                </div>
            <?php } ?>
        </div>

            <div class="journal-links">
                <div class="links">
                    <ul class="top-menu">
                        <?php echo $this->journal2->settings->get('config_primary_menu'); ?>
                        <?php echo $this->journal2->settings->get('config_secondary_menu'); ?>
                    </ul>
                </div>
            </div>

            <div class="journal-cart">
                <?php echo $cart; ?>
            </div>

            <?php if ($language || $currency): ?>
            <div class="lang-curr">
                <?php if ($language): ?>
                <div class="journal-language">
                    <?php echo $language; ?>
                </div>
                <?php endif; ?>
                <?php if ($currency): ?>
                <div class="journal-currency">
                    <?php echo $currency; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <div class="divider"></div>
            <div class="journal-menu">
                <?php echo $this->journal2->settings->get('config_mega_menu'); ?>
            </div>
            <div class="journal-search">
            <?php if (version_compare(VERSION, '2', '>=')): ?>
                <?php echo $search; ?>
            <?php else: ?>
                <div>
                    <div id="search">
                        <div class="button-search"><i></i></div>
                        <?php if (isset($filter_name)): /* v1541 compatibility */ ?>
                            <?php if ($filter_name) { ?>
                                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" autocomplete="off" />
                            <?php } else { ?>
                                <input type="text" name="filter_name" value="<?php echo $text_search; ?>" autocomplete="off" onclick="this.value = '';" onkeydown="this.style.color = '#000000';" />
                            <?php } ?>
                        <?php else: ?>
                            <input type="text" name="search" placeholder="<?php echo $this->journal2->settings->get('search_placeholder_text'); ?>" value="<?php echo $search; ?>" autocomplete="off" />
                        <?php endif; /* end v1541 compatibility */ ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        </div>
    </div>
</header>