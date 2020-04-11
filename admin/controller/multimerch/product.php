<?php

class ControllerMultimerchProduct extends ControllerMultimerchBase {
	public function getTableData() {
		$colMap = array(
			'id' => 'product_id',
			'name' => 'pd.name',
			'status' => 'mp.product_status',
			'price' => 'p.price',
			'quantity' => 'p.quantity',
			'seller' => 'ms.nickname',
			'date_added' => 'p.date_added',
			'date_modified' => 'p.date_modified'
		);

		$sorts = array('name', 'seller', 'price', 'quantity', 'date_added', 'date_modified', 'status');
		$filters = array_diff($sorts, array('price', 'quantity'));

		list($sortCol, $sortDir) = $this->MsLoader->MsHelper->getSortParams($sorts, $colMap);
		$filterParams = $this->MsLoader->MsHelper->getFilterParams($filters, $colMap);

		$results = $this->MsLoader->MsProduct->getProducts(
			array(),
			array(
				'order_by'  => $sortCol,
				'order_way' => $sortDir,
				'filters' => $filterParams,
				'offset' => $this->request->get['iDisplayStart'],
				'limit' => $this->request->get['iDisplayLength']
			),
			array(
				'product_sales' => 1
			)
		);

		$total = isset($results[0]) ? $results[0]['total_rows'] : 0;

		$this->load->model('tool/image');

		$columns = array();
		foreach ($results as $result) {
			$shop_url = HTTP_CATALOG . "index.php?route=product/product&product_id=" . $result['product_id'];
			// actions
			$actions = "";
			$actions .= "<a class='btn btn-info' target='_blank' class='ms-button' href='" . $shop_url . "' title='".$this->language->get('ms_view_in_store')."'><i class='fa fa-search'></i></a>";
			$actions .= "<a class='btn btn-primary' href='" . $this->url->link('catalog/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'], 'SSL') . "' title='".$this->language->get('button_edit')."'><i class='fa fa-pencil''></i></a>";
			$actions .= "<a class='btn btn-danger ms-delete' title='".$this->language->get('ms_delete')."' data-id='" . $result['product_id'] . "' data-referrer='product'><i class='fa fa-trash-o'></i></a>";

			$image = $this->model_tool_image->resize($result['p.image'] && is_file(DIR_IMAGE . $result['p.image']) ? $result['p.image'] : 'no_image.png', '40', '40');

			$columns[] = array_merge(
				$result,
				array(
					'checkbox' => "<input type='checkbox' name='selected[]' value='{$result['product_id']}' />",
					'image' => '<img src="' . $image . '" class="ms-list-image-thumb" />',
					'name' => $result['pd.name'],
					'seller' => '<a target="_blank" href="' . $this->url->link('multimerch/seller/update', 'token=' . $this->session->data['token'] . '&seller_id=' . $result['seller_id'], 'SSL') . '">' . $this->MsLoader->MsSeller->getSellerNickname($result['seller_id']) . '</a>',
					'price' => $this->currency->format($result['p.price'], $this->config->get('config_currency')),
					'quantity' => $result['p.quantity'],
					'status' => $result['mp.product_status'] ? $this->language->get('ms_product_status_' . $result['mp.product_status']) : '',
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['p.date_added'])),
					'date_modified' => date($this->language->get('date_format_short'), strtotime($result['p.date_modified'])),
					'actions' => $actions
				)
			);
		}

		$this->response->setOutput(json_encode(array(
			'iTotalRecords' => $total,
			'iTotalDisplayRecords' => $total,
			'aaData' => $columns
		)));
	}
	
	public function index() {
		$this->document->addScript('//code.jquery.com/ui/1.11.2/jquery-ui.min.js');
		$this->validate(__FUNCTION__);
		$this->data['add'] = $this->url->link('catalog/product/add', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}		

		$this->data['token'] = $this->session->data['token'];		
		$this->data['heading'] = $this->language->get('ms_catalog_products_heading');
		$this->document->setTitle($this->language->get('ms_catalog_products_heading'));

		$this->data['breadcrumbs'] = $this->MsLoader->MsHelper->admSetBreadcrumbs(array(
			array(
				'text' => $this->language->get('ms_menu_multiseller'),
				'href' => $this->url->link('multimerch/dashboard', '', 'SSL'),
			),
			array(
				'text' => $this->language->get('ms_catalog_products_breadcrumbs'),
				'href' => $this->url->link('multimerch/product', '', 'SSL'),
			)
		));
		$this->data['sellers'] = $this->MsLoader->MsSeller->getSellers(
			array(
				'seller_status' => array(MsSeller::STATUS_ACTIVE, MsSeller::STATUS_INACTIVE)
			),
			array(
				'order_by'  => 'ms.nickname',
				'order_way' => 'ASC'
			)
		);
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');
		$this->data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('multiseller/product.tpl', $this->data));
	}	
	
	public function jxProductStatus() {
		$this->validate(__FUNCTION__);
		$serviceLocator = $this->MsLoader->load('\MultiMerch\Module\MultiMerch')->getServiceLocator();
		$mailTransport = $serviceLocator->get('MailTransport');
		$mails = new \MultiMerch\Mail\Message\MessageCollection();
		$defaultLanguageId = $this->config->get('config_language_id');

		if (isset($this->request->post['selected'])) {
			foreach ($this->request->post['selected'] as $product_id) {
				$seller = $this->MsLoader->MsSeller->getSeller($this->MsLoader->MsProduct->getSellerId($product_id));
				
				if ((int)$this->request->post['bulk_product_status'] > 0) {
					$this->MsLoader->MsProduct->createRecord($product_id, array());
					switch ($this->request->post['bulk_product_status']) {
						case MsProduct::STATUS_ACTIVE:
							if ($seller['ms.seller_status'] != MsSeller::STATUS_ACTIVE) {
								$this->session->data['error'] = $this->language->get('ms_error_product_publish');
							} else {
								$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_ACTIVE);
								$this->MsLoader->MsProduct->approve($product_id);
							}
							break;
						case MsProduct::STATUS_INACTIVE:
							$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_INACTIVE);
							$this->MsLoader->MsProduct->disapprove($product_id);
							break;
						case MsProduct::STATUS_DISABLED:
							$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_DISABLED);
							$this->MsLoader->MsProduct->disapprove($product_id);
							break;
						case MsProduct::STATUS_DELETED:
							$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_DELETED);
							$this->MsLoader->MsProduct->disapprove($product_id);
							break;
					}
					
					if (!isset($this->session->data['error']))
						$this->session->data['success'] = $this->language->get('ms_success_product_status');
				}
				
				if ($seller['ms.seller_status'] == MsSeller::STATUS_ACTIVE) {

					$productArray = $this->MsLoader->MsProduct->getProduct($product_id);

					$MailProductModified = $serviceLocator->get('MailProductModified', false)
						->setTo($seller['c.email'])
						->setData(array(
							'addressee' => $seller['ms.nickname'],
							'product_name' => $productArray['languages'][$defaultLanguageId]['name'],
							'product_status' => $productArray['product_status'],
							'message' => isset($this->request->post['product_message']) ? $this->request->post['product_message'] : '',
						));
					$mails->add($MailProductModified);
				}
			}
			
			if (isset($this->request->post['bulk_mail'])) {
				$mailTransport->sendMails($mails);
			}
		} else {
			//$this->session->data['error'] = 'Error changing product status.';
		}
	}
	
	public function jxProductSeller() {
		$json = array();
		
		$this->validate(__FUNCTION__);
		if(isset($this->request->get['product_id'])){
			$products = array($this->request->get['product_id']);
		}
		else if(isset($this->request->post['selected'])){
			$products = $this->request->post['selected'];
		}
		$seller = $this->MsLoader->MsSeller->getSeller($this->request->get['seller_id']);
		foreach ($products as $product_id) {
			$this->MsLoader->MsProduct->createRecord($product_id, array('seller_id' => $this->request->get['seller_id']));
			$this->MsLoader->MsProduct->changeSeller($product_id, $this->request->get['seller_id']);
			$json['product_status'] = $this->language->get('ms_product_status_' . $seller['ms.seller_status']);
			switch($seller['ms.seller_status']) {
				case MsSeller::STATUS_ACTIVE:
					$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_ACTIVE);
					$this->MsLoader->MsProduct->approve($product_id);
					break;
				case MsSeller::STATUS_INACTIVE:
				case MsSeller::STATUS_UNPAID:
					$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_INACTIVE);
					$this->MsLoader->MsProduct->disapprove($product_id);
					$json['product_status'] = $this->language->get('ms_product_status_' . MsProduct::STATUS_INACTIVE);
					break;
				case MsSeller::STATUS_DISABLED:
				case MsSeller::STATUS_INCOMPLETE:
					$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_DISABLED);
					$this->MsLoader->MsProduct->disapprove($product_id);
					break;
				case MsSeller::STATUS_DELETED:
					$this->MsLoader->MsProduct->changeStatus($product_id, MsProduct::STATUS_DELETED);
					$this->MsLoader->MsProduct->disapprove($product_id);
					break;
				default:
					$product = $this->MsLoader->MsProduct->getProduct($product_id);
					$json['product_status'] = $this->language->get('ms_product_status_' . $product['mp.product_status']);
					break;
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function delete() {
		$json = array();

		if(!isset($this->request->get['product_id']) && !isset($this->request->post['selected'])) {
			$json['errors'][] = $this->language->get('ms_catalog_products_error_deleting');
		}

		if(!isset($json['errors'])) {
			$product_ids = isset($this->request->get['product_id']) ?
				array($this->request->get['product_id']) :
				(isset($this->request->post['selected']) ? $this->request->post['selected'] : array());

			foreach ($product_ids as $product_id) {
				$this->MsLoader->MsProduct->deleteProduct($product_id);

				$this->session->data['success'] =  $this->language->get('ms_catalog_products_success_deleting');
				$json['redirect'] = $this->url->link('multimerch/product', 'token=' . $this->session->data['token'], true);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>
