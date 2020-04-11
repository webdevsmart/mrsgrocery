<?php
class ControllerStartupMaintenance extends Controller {
	public function index() {
		if ($this->config->get('config_maintenance')) {
			// Route
			if (isset($this->request->get['route']) && $this->request->get['route'] != 'startup/router') {
				$route = $this->request->get['route'];
			} else {
				$route = $this->config->get('action_default');
			}			
			
			$ignore = array(
				'common/language/language',
				'common/currency/currency'
			);
			
			// Show site if logged in as admin
			$this->user = new Cart\User($this->registry);

			
                $is_j2_assets = isset($this->request->get['route']) && strpos($this->request->get['route'], 'journal2/assets') === 0;

                if ((substr($route, 0, 7) != 'payment' && substr($route, 0, 3) != 'api' && !$is_j2_assets) && !in_array($route, $ignore) && !$this->user->isLogged()) {
            
				return new Action('common/maintenance');
			}
		}
	}
}
