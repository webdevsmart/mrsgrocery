<?php
class Template {
	private $adaptor;

  	public function __construct($adaptor) {
	    $class = 'Template\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class();

                global $journal2, $config, $registry;
			    $this->adaptor->journal2 = $journal2;
			    $this->adaptor->url = $registry->get('url');
			    $this->adaptor->config = $config;

			    if (version_compare(VERSION, '3', '>=')) {
			        $this->adaptor->set('journal2', $journal2);
			        $this->adaptor->set('url', $registry->get('url'));
			        $this->adaptor->set('config', $config);
			    }
            
		} else {
			throw new \Exception('Error: Could not load template adaptor ' . $adaptor . '!');
		}
	}

	public function set($key, $value) {
		$this->adaptor->set($key, $value);
	}

	public function render($template) {
		return $this->adaptor->render($template);
	}
}
