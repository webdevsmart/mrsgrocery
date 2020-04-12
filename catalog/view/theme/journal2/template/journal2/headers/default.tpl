<header class="journal-header-default">
    <div class="header">
    <div class="journal-top-header j-min"></div>

    <div id="header" class="journal-header">
        <div class="journal-logo xs-100 sm-100 md-33 lg-25 xl-25">
            <?php if ($logo) { ?>
                <div id="logo">
                    <a href="<?php echo str_replace('index.php?route=common/home', '', $home); ?>">
                        <?php echo Journal2Utils::getLogo($this->config); ?>
                    </a>
                </div>
            <?php } ?>
        </div>
        <div class="header-assets xs-100 sm-100 md-66 lg-75 xl-75">

            <div class="journal-links j-min xs-100 sm-100 md-100 lg-66 xl-66">
                <div class="links j-min">
                    <ul class="top-menu">
                    <?php echo $this->journal2->settings->get('config_primary_menu'); ?>
                    </ul>
                </div>
            </div>
            <div class="journal-cart j-min xs-100 sm-50 md-50 lg-33 xl-33">
                <?php echo $cart; ?>
            </div>
            <div class="journal-login j-min xs-100 sm-100 md-100 lg-66 xl-66">
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
                <div class="journal-secondary">
                    <ul class="top-menu">
                    <?php echo $this->journal2->settings->get('config_secondary_menu'); ?>
                    </ul>
                </div>
            </div>

            <div class="journal-search j-min xs-100 sm-50 md-50 lg-33 xl-33">
                <?php if (version_compare(VERSION, '2', '>=')): ?>
                    <?php echo $search; ?>
                <?php else: ?>
                <div>
                    <div id="search" class="j-min">
                        <div class="button-search j-min"><i></i></div>
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
        <div class="journal-menu j-min xs-100 sm-100 md-100 lg-75 xl-75">
            <?php echo $this->journal2->settings->get('config_mega_menu'); ?>
        </div>
    </div>
    </div>
</header>