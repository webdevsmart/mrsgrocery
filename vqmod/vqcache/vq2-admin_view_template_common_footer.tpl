<footer id="footer"><?php echo $text_footer; ?><br /><?php echo $text_version; ?></footer></div>

				<?php if(MsLoader::getInstance()->MsHelper->isInstalled()) { ?>
					<script type="text/javascript">
						if (typeof msGlobals.config_language === "undefined") {
							<?php
								$lang = "view/javascript/multimerch/datatables/lang/" . $this->config->get('config_admin_language') . ".lng";
								$default_lang = "view/javascript/multimerch/datatables/lang/en-gb.lng";
								$dt_language = file_exists(DIR_APPLICATION . $lang) ? "'$lang'" : "'$default_lang'";
							?>
							msGlobals.config_language = <?php echo $dt_language; ?>;
						}
						if (typeof msGlobals.config_limit_admin === "undefined") {
							msGlobals.config_limit_admin = '<?php echo $this->config->get('config_limit_admin'); ?>';
						}
					</script>
				<?php } ?>
				
</body></html>
