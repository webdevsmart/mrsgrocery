<?php

namespace MultiMerch\Core;

class Hook
{
	protected $registry;

	protected $actions = array();
	protected $filters = array();

	public function setRegistry(\Registry $registry)
	{
		$this->registry = $registry;
		return $this;
	}

	public function getRegistry()
	{
		return $this->registry;
	}

	/**
	 * Attaches a function or a method to a specific action.
	 *
	 * @param 	string			$hook_name		The name of the hook.
	 * @param	array			$callback		Function to be called.
	 * @param	int				$priority		Optional. Used to specify the order in which functions are executed.
	 * 											Default 100.
	 */
	public function attachAction($hook_name, $callback, $priority = 100)
	{
		$this->attach($this->actions, $hook_name, $callback, $priority);
	}

	/**
	 * Attaches a function or a method to a specific filter.
	 *
	 * @param 	string			$hook_name		The name of the hook.
	 * @param	array			$callback		Function to be called.
	 * @param	int				$priority		Optional. Used to specify the order in which functions are executed.
	 * 											Default 100.
	 */
	public function attachFilter($hook_name, $callback, $priority = 100)
	{
		$this->attach($this->filters, $hook_name, $callback, $priority);
	}

	/**
	 * Attaches an action or a filter to a corresponding collection.
	 *
	 * @param 	array			$hooks			Collection of filters or actions passed by reference.
	 * @param 	string			$hook_name		The name of the hook.
	 * @param	array			$callback		Function to be called.
	 * @param	int				$priority		Optional. Used to specify the order in which functions are executed.
	 * 											Default 100.
	 */
	protected function attach(&$hooks, $hook_name, $callback, $priority = 100)
	{
		$hooks[] = array(
			'name' => $hook_name,
			'callback' => $callback,
			'priority' => $priority
		);
	}

	/**
	 * Detaches action from actions collection.
	 *
	 * @param	string			$hook_name		The name of the hook.
	 */
	public function detachAction($hook_name)
	{
		foreach ($this->actions as $key => $registered_action) {
			if ($hook_name === $registered_action['name'])
				unset($this->actions[$key]);
		}
	}

	/**
	 * Detaches filter from filters collection.
	 *
	 * @param	string			$hook_name		The name of the hook.
	 */
	public function detachFilter($hook_name)
	{
		foreach ($this->filters as $key => $registered_filter) {
			if ($hook_name === $registered_filter['name'])
				unset($this->filters[$key]);
		}
	}

	/**
	 * Calls the callback functions attached to an action hook.
	 *
	 * @param $hook_name
	 * @param $args
	 * @return mixed
	 */
	public function triggerAction($hook_name, $args = array())
	{
		$this->trigger($this->actions, $hook_name, $args);
	}

	/**
	 * Calls the callback functions attached to a filter hook.
	 *
	 * @param $hook_name
	 * @param array $args
	 * @return mixed
	 */
	public function triggerFilter($hook_name, $args = array())
	{
		return $this->trigger($this->filters, $hook_name, $args);
	}

	/**
	 * Calls the callback(s) for the corresponding hook.
	 *
	 * @param	array					$hooks			Collection of filters or actions.
	 * @param	string					$hook_name		The name of the hook.
	 * @param	array					$args			The arguments for the callback(s).
	 * @return	bool|\Exception|string					Returns false if no hooks exist. Returns Exception if callback
	 * 													is invalid. Returns rendered html if filter is triggered.
	 */
	protected function trigger($hooks, $hook_name, $args)
	{
		if (!$hooks)
			return false;

		// Rendered result
		$result = '';

		$callbacks_by_priority = $this->getCallbacks($hooks, $hook_name);

		foreach ($callbacks_by_priority as $priority => $callbacks) {
			foreach ($callbacks as $callback) {
				$route = isset($callback[0]) ? $callback[0] : '';

				$file = DIR_APPLICATION . $route . '.php';
				$class = preg_replace('/[^a-zA-Z0-9]/', '', $route);

				if (is_file($file)) {
					include_once(\VQMod::modCheck(modification($file), $file));

					$controller = new $class($this->registry);
					$reflection = new \ReflectionClass($class);

					if (isset($callback[1]) && $reflection->hasMethod($callback[1]) && $reflection->getMethod($callback[1])->getNumberOfRequiredParameters() <= count($args))
						$result .= call_user_func_array(array($controller, $callback[1]), $args);
				} else {
					return new \Exception('Error: Could not call ' . $callback[0] . '/' . $callback[1] . '!');
				}
			}
		}

		return $result;
	}

	/**
	 * Returns callbacks for specified hook.
	 *
	 * Callbacks are sorted by priority. If priority is the same for some callbacks, they are called by the order they
	 * were added to the corresponding collection.
	 *
	 * @param	array			$hooks			Collection of filters or actions.
	 * @param	string			$hook_name		The name of the hook.
	 * @return	array							Array of callbacks for a hook.
	 */
	protected function getCallbacks($hooks, $hook_name)
	{
		$callbacks = array();

		foreach ($hooks as $key => $hook) {
			if (empty($hook['callback']) || (string)$hook_name !== (string)$hook['name'])
				continue;

			$callbacks[$hook['priority']][] = $hook['callback'];
		}

		ksort($callbacks, SORT_ASC);

		return $callbacks;
	}

	/**
	 * Attaches initial actions and filters to the default MultiMerch hooks.
	 */
	public function registerDefaultHooks()
	{
		/**
		 * Overall structure:
		 * - Attaching action: $this->MsHooks->attachAction(<hook_name>, array(<route>, <method/function>), <priority (optional)>);
		 * - Triggering action: $this->MsHooks->triggerAction(<hook_name>, <data (optional)>);
		 * - Attaching filter: $this->MsHooks->attachFilter(<hook_name>, array(<route>, <method/function>), <priority (optional)>);
		 * - Triggering filter: $this->MsHooks->triggerFilter(<hook_name>, <data (optional)>);
		 *
		 * Example of attaching actions in controllers/models/views:
		 *
		 * $this->MsHooks->attachAction('test_hook_for_actions', array('controller/common/home', 'testMsAction'));
		 * $this->MsHooks->attachAction('test_hook_for_actions', array('controller/common/home', 'testMsActionWithPriority'), 1);
		 *
		 * Example of triggering action in controllers/models/views:
		 *
		 * $this->MsHooks->triggerAction('test_hook_for_actions');
		 * $this->MsHooks->triggerAction('test_hook_for_actions', $data);
		 *
		 *
		 * Example of attaching filter in controllers/models/views:
		 *
		 * $this->MsHooks->attachFilter('test_hook_for_filters', array('controller/common/home', 'testMsFilter'));
		 * $this->MsHooks->attachFilter('test_hook_for_filters', array('controller/common/home', 'testMsFilterWithPriority'), 1);
		 *
		 * Example of triggering filter in controllers/models/views:
		 *
		 * echo $this->MsHooks->triggerFilter('test_hook_for_filters');
		 * echo $this->MsHooks->triggerFilter('test_hook_for_filters', $data);
		 *
		 */
	}
}