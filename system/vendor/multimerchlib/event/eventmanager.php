<?php

namespace MultiMerch\Event;

use MultiMerch\ServiceLocator\ServiceLocatorAwareTrait;
use MultiMerch\ServiceLocator\ServiceLocatorAwareInterface;
use MultiMerch\Event\Event as MsEvent;

class EventManager implements ServiceLocatorAwareInterface
{
	use ServiceLocatorAwareTrait;

	protected $registry;

	public function __construct()
	{
		$this->registry = \MsLoader::getInstance()->getRegistry();
	}

	public function __get($name)
	{
		return $this->registry->get($name);
	}

	/**
	 * Get a list of events from database.
	 *
	 * @param array $data
	 * @param array $sort
	 * @return array
	 */
	public function getEvents($data = array(), $sort = array())
	{
		$filters = '';
		if(isset($sort['filters'])) {
			foreach($sort['filters'] as $k => $v) {
				$filters .= " AND {$k} LIKE '%" . $this->db->escape($v) . "%'";
			}
		}

		$sql = "SELECT
					SQL_CALC_FOUND_ROWS
					*
				FROM `" . DB_PREFIX . "ms_event`
				WHERE 1 = 1"

			. (isset($data['event_id']) ? " AND event_id = '" . (int)$data['event_id'] . "'" : "")
			. (isset($data['admin_id']) ? " AND admin_id = '" . (int)$data['admin_id'] . "'" : "")
			. (isset($data['customer_id']) ? " AND customer_id = '" . (int)$data['customer_id'] . "'" : "")
			. (isset($data['seller_id']) ? " AND seller_id = '" . (int)$data['seller_id'] . "'" : "")
			. (isset($data['event_type']) ? " AND event_type = '" . (int)$data['event_type'] . "'" : "")
			. (isset($data['data']) ? " AND `data` LIKE '%" . $this->db->escape($data['data']) . "%'" : "")

			. $filters

			. (isset($sort['order_by']) ? " ORDER BY {$sort['order_by']} {$sort['order_way']}" : '')
			. (isset($sort['limit']) ? " LIMIT ".(int)$sort['offset'].', '.(int)($sort['limit']) : '');

		$res = $this->db->query($sql);

		$total = $this->db->query("SELECT FOUND_ROWS() as total");
		if ($res->num_rows) {
			if(isset($data['single'])) {
				$res->row['total_rows'] = $total->row['total'];
			} else {
				$res->rows[0]['total_rows'] = $total->row['total'];
			}
		}

		return ($res->num_rows && isset($data['single'])) ? $res->row : $res->rows;
	}

	public function getEventDescription($data)
	{
		$this->load->model('customer/customer');
		$event_data = (array)json_decode($data['data']);
		$event_description = $data['body'];

		switch($data['event_type']) {
			case MsEvent::PRODUCT_CREATED:
			case MsEvent::PRODUCT_MODIFIED:
				if(isset($event_data['product_id'])) {
					$product = $this->MsLoader->MsProduct->getProduct($event_data['product_id']);
					$event_description = sprintf($this->language->get('ms_event_type_template_' . $data['event_type']),
						$this->url->link('catalog/product/edit', 'product_id=' . $event_data['product_id'] . '&token=' . $this->session->data['token']),
						$product['languages'][$this->config->get('config_language_id')]['name'],
						$this->url->link('multimerch/seller/update', 'seller_id=' . $data['seller_id'] . '&token=' . $this->session->data['token']),
						$this->MsLoader->MsSeller->getSellerNickname($data['seller_id'])
					);
				}
				break;

			case MsEvent::SELLER_CREATED:
			case MsEvent::SELLER_MODIFIED:
				$seller_name = $this->MsLoader->MsSeller->getSellerNickname($data['seller_id']) ?: $this->language->get('ms_event_user_deleted');
				$event_description = sprintf($this->language->get('ms_event_type_template_' . $data['event_type']),
					$this->url->link('multimerch/seller/update', 'seller_id=' . $data['seller_id'] . '&token=' . $this->session->data['token']),
					$seller_name
				);
				break;

			case MsEvent::CUSTOMER_CREATED:
			case MsEvent::CUSTOMER_MODIFIED:
				$customer = $this->model_customer_customer->getCustomer($data['customer_id']);

				$customer_name = !empty($customer) ? $customer['firstname'] . ' ' . $customer['lastname'] : $this->language->get('ms_event_user_deleted');

				$event_description = sprintf($this->language->get('ms_event_type_template_' . $data['event_type']),
					$this->url->link('customer/customer/edit', 'customer_id=' . $data['customer_id'] . '&token=' . $this->session->data['token']),
					$customer_name
				);
				break;

			case MsEvent::ORDER_CREATED:
				if(isset($data['customer_id'])) {
					$customer = $this->model_customer_customer->getCustomer($data['customer_id']);

					$customer_name = !empty($customer) ? $customer['firstname'] . ' ' . $customer['lastname'] : $this->language->get('ms_event_user_deleted');

					$event_description = sprintf($this->language->get('ms_event_type_template_' . $data['event_type']),
						$this->url->link('sale/order/info', 'order_id=' . $event_data['order_id'] . '&token=' . $this->session->data['token']),
						$event_data['order_id'],
						$this->url->link('customer/customer/edit', 'customer_id=' . $data['customer_id'] . '&token=' . $this->session->data['token']),
						$customer_name
					);
				} else {
					$event_description = sprintf($this->language->get('ms_event_type_template_' . $data['event_type'] . '_guest'),
						$this->url->link('sale/order/info', 'order_id=' . $event_data['order_id'] . '&token=' . $this->session->data['token']),
						$event_data['order_id']
					);
				}
				break;

			default:

				break;
		}

		return $event_description;
	}

	/**
	 * Create events records in database.
	 *
	 * @param EventCollection $events
	 */
	public function create(EventCollection $events)
	{
		foreach ($events->getList() as $event) {
			$data = $event->getData();

			$this->db->query("INSERT INTO " . DB_PREFIX . "ms_event
				SET event_type = " . (int)$data['event_type'] . ",
					data = '" . $this->db->escape(json_encode($data['data'])) . "',
					date_created = NOW()"
				. (isset($data['admin_id']) ? ", admin_id = " . (int)$data['admin_id'] : "")
				. (isset($data['customer_id']) ? ", customer_id = " . (int)$data['customer_id'] : "")
				. (isset($data['seller_id']) ? ", seller_id = " . (int)$data['seller_id'] : "")
				. (isset($data['body']) ? ", body = '" . $this->db->escape($data['body']) . "'" : "")
			);

			$this->ms_events->remove($event);
		}
	}
}