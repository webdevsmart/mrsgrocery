<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<h1><?php echo $heading_title; ?></h1>

			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<div class="panel with-nav-tabs panel-default">
			<div class="panel-heading">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab-info" data-toggle="tab"><h3 class="panel-title"><i class="fa fa-gear"></i> <?php echo $sub_heading_title; ?></h3></a></li>
					<li><a href="#tab-phpinfo" data-toggle="tab"><h3 class="panel-title"><i class="fa fa-code"></i> <?php echo $phpinfo_heading_title; ?></h3></a></li>
					<li><a href="#tab-multimerch" data-toggle="tab"><h3 class="panel-title"><i class="fa fa-bug"></i> <?php echo $ms_debug_multimerchinfo_heading_title; ?></h3></a></li>
				</ul>
			</div>
			<div class="panel-body">
				<div class="tab-content">
					<div id="tab-info" class="tab-pane fade in active">
						<textarea wrap="soft" rows="30" readonly class="form-control">
							Version information:
							====================
							PHP: <?php echo phpversion(); ?>

							OpenCart: <?php echo VERSION; ?>

							<?php if(class_exists('VQMod')) { ?>
							VQMod: <?php echo VQMod::$_vqversion; ?>

							<?php } ?>
							Theme: <?php echo $active_theme . (isset($active_theme_version) ? ' ' . $active_theme_version : ''); ?> <?php echo $this->MsLoader->MsHelper->getIntegrationPackVersion() ? (' + ' . $this->MsLoader->MsHelper->getIntegrationPackVersion()) : '. No integration pack installed'; ?>

							MultiMerch: <?php echo $this->MsLoader->appVer; ?> / <?php echo $this->MsLoader->dbVer; ?>

							<?php echo ($this->config->get('msconf_license_key') && $this->config->get('msconf_license_activated')) ? 'LICENSE ACTIVATED' : 'LICENSE NOT ACTIVATED'; ?>


							Installed extensions:
							====================
							<?php foreach($extensions['installed'] as $e) { ?>
								+ <?php echo strip_tags($e['name']); ?> <?php echo $e['version']; ?>

							<?php } ?>
							<?php foreach($extensions['other'] as $e) { ?>
								<?php echo strip_tags($e['name']); ?> <?php echo $e['version']; ?>

							<?php } ?>

							OCMOD Modifications:
							====================
							<?php foreach($modifications['installed'] as $modification) { ?>
							+<?php echo $modification; ?>

							<?php } ?>
							<?php foreach($modifications['xml'] as $modification) { ?>
							~<?php echo $modification; ?>

							<?php } ?>
							<?php foreach($modifications['other'] as $modification) { ?>
							<?php echo $modification; ?>

							<?php } ?>

							VQMOD files:
							====================
							<?php if(is_array($vqmod_files) AND $vqmod_files) { ?>
								<?php foreach($vqmod_files['installed'] as $vqmod_file) { ?>
								+<?php echo $vqmod_file; ?>

								<?php } ?>
								<?php foreach($vqmod_files['other'] as $vqmod_file) { ?>
								<?php echo $vqmod_file; ?>

								<?php } ?>
							<?php }else{ ?>
								<?php echo $vqmod_files; ?>

							<?php } ?>

							File state:
							====================
							<?php if($modified_files['warning']) { ?>
							<?php foreach($modified_files['warning'] as $e) { ?>
							! <?php echo $e; ?>

							<?php } ?>
							<?php } ?>
							<?php if($modified_files['modified']) { ?>
							<?php foreach($modified_files['modified'] as $e) { ?>
							~ <?php echo $e; ?>

							<?php } ?>
							<?php } ?>
							<?php if($modified_files['deleted']) { ?>
							<?php foreach($modified_files['deleted'] as $e) { ?>
							- <?php echo $e; ?>

							<?php } ?>
							<?php } ?>

							OpenCart log (last 50 lines):
							====================
							<?php echo $error_log; ?>

							====================

							Server log (last 50 lines):
							====================
							<?php echo $server_log; ?>

							====================

							<?php if(class_exists('VQMod')) { ?>
							VQMod log (last 150 lines):
							====================
							<?php echo $vqmod_log; ?>

							<?php } ?>

							====================

							MultiMerch Debug Log (last 50 lines):
							====================
							<?php echo $mslogger_log['short']; ?>

						</textarea>
					</div>

					<div id="tab-multimerch" class="tab-pane fade">
						<textarea wrap="soft" rows="30" readonly class="form-control">
							MultiMerch Debug Log (last 1000 lines):
							====================
							<?php echo $mslogger_log['full']; ?>

						</textarea>
					</div>

					<div id="tab-phpinfo" class="tab-pane fade">
						<?php echo $phpinfo; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>