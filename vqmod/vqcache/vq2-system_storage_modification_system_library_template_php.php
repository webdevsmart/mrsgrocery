<?php
namespace Template;
final class PHP {

				public function __get($key) {
					return \MsLoader::getInstance()->getRegistry()->get($key);
				}

				public function __set($key, $value) {
					\MsLoader::getInstance()->getRegistry()->set($key, $value);
				}
			
	private $data = array();
	
	public function set($key, $value) {
		$this->data[$key] = $value;
	}
	
	public function render($template) {
		$file = DIR_TEMPLATE . $template;

		if (is_file($file)) {
			extract($this->data);

			ob_start();

			require(\VQMod::modCheck(modification($file), $file));

			return ob_get_clean();
		}

		trigger_error('Error: Could not load template ' . $file . '!');
		exit();
	}	
}
