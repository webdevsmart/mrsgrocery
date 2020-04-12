<?php

class ControllerMultimerchDebug extends ControllerMultimerchBase {
	private $error = array();


	public function __construct($registry) {
		parent::__construct($registry);

		$this->load->model('extension/extension');
		$this->load->model('extension/module');
		$this->load->model('extension/modification');
	}

	public function index() {
		$this->_validate();

		$this->document->setTitle($this->language->get('ms_debug_heading'));

		$this->data['token'] = $this->session->data['token'];
		$this->data['heading_title'] = $this->language->get('ms_debug_heading');
		$this->data['sub_heading_title'] = $this->language->get('ms_debug_sub_heading_title');
		$this->data['phpinfo_heading_title'] = $this->language->get('ms_debug_phpinfo_heading_title');

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_debug_breadcrumbs'),
				'href' => $this->url->link('multimerch/debug', '', 'SSL'),
			)
		));

		$this->data['extensions'] = $this->_getExtensions();
		$this->data['modifications'] = $this->_getModifications();
		$this->data['vqmod_files'] = $this->_getVqmodFiles();
		$this->data['error_log'] = $this->_getErrorLog();
		$this->data['server_log'] = $this->_getServerLog();
		$this->data['vqmod_log'] = $this->_getVQModLog();
		$this->data['mslogger_log'] = $this->_getMsLoggerLog();
		$this->data['phpinfo'] = $this->_getPHPInfo();
		$this->data['modified_files'] = $this->_getModifiedFiles();

		$this->data['active_theme'] = $this->config->get('BurnEngine_theme') ? $this->config->get('BurnEngine_theme')['name'] : MsLoader::getInstance()->load('\MultiMerch\Module\MultiMerch')->getViewTheme();

		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('multiseller/debug.tpl', $this->data));
	}


	/**
	 * Gets all modified MultiMerch Core files
	 *
	 * @return array
	 */
	private function _getModifiedFiles() {
		$result = array(
			'modified' => array(),
			'deleted' => array(),
			'warning' => array()
		);
		$json_file_path = DIR_SYSTEM . 'vendor/multimerchlib/module/config/hash_file.json';
		if (file_exists($json_file_path)){
			$core_files = json_decode(file_get_contents($json_file_path));
			foreach ($core_files as $core_file){
				if (file_exists(DIR_APPLICATION . '../'.$core_file[0])){
					if (isset($core_file[1])){
						if (hash_file('crc32b', DIR_APPLICATION . '../'.$core_file[0]) != $core_file[1]){
							$result['modified'][] = trim($core_file[0],'/');
						}
					}else{
						$result['warning'][] = $this->language->get('ms_debug_warning_hash_file_invalid');
						break;
					}
				}else{
					$result['deleted'][] = trim($core_file[0],'/');
				}
			}
		}else{
			$result['warning'][] = $this->language->get('ms_debug_warning_hash_file_not_find');
		}

		return $result;
	}

	/**
	 * Gets all extensions existing in current OC installation
	 *
	 * @return array
	 */
	private function _getExtensions() {
		$extensions = array(
			'installed' => array(),
			'other' => array()
		);

		$installed = $this->model_extension_extension->getInstalled('module');

		$files_path_23 = DIR_APPLICATION . 'controller/extension/module';
		$files_path_pre23 = DIR_APPLICATION . 'controller/module';

		$files = array_merge(glob($files_path_23 . '/*.php'), glob($files_path_pre23 . '/*.php'));

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				$this->load->language((defined('VERSION') && VERSION >= '2.3.0.0' ? 'extension/' : '') . 'module/' . $extension);

				$version = '';
				if (strpos($extension, 'multimerch_') !== FALSE) {
					$f = file($file);
					foreach ($f as $line) {
						if (strpos($line, 'version') !== false) {
							if (preg_match("/['\"](.*?)['\"]/", $line, $matches)) $version = $matches[1];
							break;
						}
					}
				}

				$key = in_array($extension, $installed) ? 'installed' : 'other';
				$extensions[$key][strtolower(strip_tags($this->language->get('heading_title')))] = array(
					'name' => $this->language->get('heading_title'),
					'version' => $version
				);
			}
		}

		ksort($extensions['installed']);
		ksort($extensions['other']);
		return $extensions;
	}

	/**
	 * Gets all modifications existing in current OC installation
	 *
	 * @return array
	 */
	private function _getModifications() {
		$result = array(
			'installed' => array(),
			'xml' => array(),
			'other' => array()
		);

		$modifications = $this->model_extension_modification->getModifications();
		foreach ($modifications as $modification){
			if ($modification['status']){
				$result['installed'][] = $modification['name'];
			}else{
				$result['other'][] = $modification['name'];
			}
		}

		$files = glob(DIR_SYSTEM . '*.ocmod.xml');
		if ($files) {
			foreach($files as $file){
				$result['xml'][] = basename($file);
			}
		}

		return $result;
	}

	/**
	 * Gets xml files
	 *
	 * @return array|string
	 */
	private function _getVqmodFiles() {
		if(class_exists('VQMod')){
			$result = array(
				'installed' => array(),
				'other' => array()
			);
			$vqmod_files = scandir(DIR_APPLICATION . '../' . VQmod::$logFolder . '../xml/');
			$exceptions = array('.','..');
			foreach ($vqmod_files as $key=>$vqmod_file){
				if (!in_array($vqmod_file,$exceptions)){
					$ext = explode(".", $vqmod_file);
					$ext = end($ext);
					if ($ext == 'xml'){
						$result['installed'][] = $vqmod_file;
					}else{
						$result['other'][] = $vqmod_file;
					}
				}
			}
			return $result;
		}else{
			return $this->language->get('ms_debug_warning_vqmod_not_installed');
		}
	}

	/**
	 * Gets content of OC error log file
	 *
	 * @return string
	 */
	private function _getErrorLog() {
		$error_log = '';
		$file = DIR_LOGS . $this->config->get('config_error_filename');

		if (file_exists($file)) {
			$error_log = $this->_readLog($file, 50);
		}

		return $error_log;
	}

	/**
	 * Gets content of latest Vqmod error log file
	 *
	 * @return string
	 */
	private function _getVQModLog() {
		if(!class_exists('VQMod')) return $this->language->get('ms_debug_warning_vqmod_not_installed');

		$vqmod_log = '';
		$files = glob(DIR_APPLICATION . '../' . VQmod::$logFolder . '*.log');

		if ($files) {
			usort($files, create_function('$a,$b', 'return filemtime($b) - filemtime($a);'));
			$log = array_shift($files);
			$vqmod_log = $this->_readLog($log, 150);
		}

		return $vqmod_log;
	}

	/**
	 * Gets content of Server error log file
	 *
	 * @return string
	 */
	private function _getServerLog() {
		$file = @ini_get('error_log');

		if($file) {
			$l = $this->_readLog($file, 50);
			$server_log = ($l !== FALSE ? $l : $this->language->get('ms_debug_warning_server_log_not_available') . "($file)");
		} else {
			$server_log = $this->language->get('ms_debug_warning_server_log_not_available');
		}

		return $server_log;
	}

	/**
	 * Gets content of MsLogger log file
	 *
	 * @return string
	 */
	private function _getMsLoggerLog() {
		$mslogger_log = array(
			'full' => '',
			'short' => ''
		);
		$file = DIR_LOGS . $this->config->get('msconf_logging_filename');

		if (file_exists($file)) {
			$mslogger_log['full'] = $this->_readLog($file, 1000);
			$mslogger_log['short'] = $this->_readLog($file, 50);
		}

		return $mslogger_log;
	}

	/**
	 * Gets phpinfo() and fixes it's styles
	 *
	 * @return string
	 */
	private function _getPHPInfo() {
		ob_start();
		phpinfo();

		preg_match ('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);

		# $matches [1]; # Style information
		# $matches [2]; # Body information

		$html = "<div class='phpinfodisplay'><style type='text/css'>\n";
		$html .= join( "\n",
			array_map(
				create_function(
					'$i',
					'return ".phpinfodisplay " . preg_replace( "/,/", ",.phpinfodisplay ", $i );'
				),
				preg_split( '/\n/', $matches[1] )
			)
		);
		$html .= "</style>\n";
		$html .= $matches[2];
		$html .= "\n</div>\n";

		return $html;
	}

	/**
	 * Gets `$lines` lines from `$file`
	 *
	 * @param $file
	 * @param int $lines
	 * @param bool $adaptive
	 * @return bool|string
	 */
	private function _readLog($file, $lines, $adaptive = true) {
		$f = @fopen($file, "rb");
		if ($f === false) return false;

		if (!$adaptive) $buffer = 4096;
		else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

		fseek($f, -1, SEEK_END);
		if (fread($f, 1) != "\n") $lines -= 1;
		$output = '';
		$chunk = '';
		while (ftell($f) > 0 && $lines >= 0) {
			$seek = min(ftell($f), $buffer);
			fseek($f, -$seek, SEEK_CUR);
			$output = ($chunk = fread($f, $seek)) . $output;
			fseek($f, -utf8_strlen($chunk, '8bit'), SEEK_CUR);
			$lines -= substr_count($chunk, "\n");
		}

		while ($lines++ < 0) $output = substr($output, strpos($output, "\n") + 1);
		fclose($f);

		return trim($output);
	}

	private function _validate() {
		if (!$this->user->hasPermission('access', 'multimerch/debug')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}