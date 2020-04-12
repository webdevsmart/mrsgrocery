<?php

class ControllerMultimerchBase extends Controller {
	/** @var	array	$data	Array of all data that is passed by controllers to views. */
	public $data = array();

	/**
	 * ControllerMultimerchBase constructor.
	 *
	 * @param			$registry
	 */
	public function __construct($registry) {
		parent::__construct($registry);

		$this->registry = $registry;
		$this->data = array_merge($this->data, $this->load->language('multiseller/multiseller'));
		$this->data['token'] = $this->session->data['token'];

		$this->document->addStyle('view/stylesheet/multimerch/multiseller.css');
		$this->document->addStyle('view/javascript/multimerch/datatables/css/jquery.dataTables.css');
		$this->document->addScript('view/javascript/multimerch/datatables/js/jquery.dataTables.min.js');
		$this->document->addScript('view/javascript/multimerch/common.js');
	}

	/**
	 * Requests validation.
	 *
	 * @param	string	$action
	 * @param	string	$level
	 * @return	bool
	 */
	public function validate($action = '', $level = 'access') {
		// @todo: validation
		return true;
	}

	/**
	 * Generates warning message with number of affected items when admin tries to delete any MultiMerch element.
	 *
	 * @return	string			'Confirm delete' warning message.
	 */
	public function jxConfirmDelete() {
		$referrer = isset($this->request->get['referrer']) ? $this->request->get['referrer'] : '';

		if (isset($this->request->get[$referrer . '_id'])) $this->request->post['selected'][] = $this->request->get[$referrer . '_id'];
		$deleted_items_msg = sprintf($this->language->get('ms_delete_' . $referrer), count($this->request->post['selected']));

		$affected_items_msg = '';

		switch($referrer) {
			case 'attribute':
				// Related items: products
				$products_affected = array();
				foreach ($this->request->post['selected'] as $attribute_id) {
					$products_affected = array_merge($products_affected, $this->MsLoader->MsAttribute->getProductsByAttributeId($attribute_id));
				}
				$total_products_affected = count(array_unique($products_affected));

				$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_product'), $total_products_affected));

				break;

			case 'attribute_group':
				// Related items: attributes > products
				$total_attributes_affected = $total_products_affected = 0;
				$products_affected = array();

				foreach ($this->request->post['selected'] as $attribute_group_id) {
					$attributes_affected = $this->MsLoader->MsAttribute->getAttributes(array('attribute_group_id' => $attribute_group_id));
					$total_attributes_affected += !empty($attributes_affected) ? $attributes_affected[0]['total_rows'] : 0;

					foreach ($attributes_affected as $attribute) {
						$products_affected = array_merge($products_affected, $this->MsLoader->MsAttribute->getProductsByAttributeId($attribute['attribute_id']));
					}
				}

				$total_products_affected = count(array_unique($products_affected));

				$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_attribute'), $total_attributes_affected));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_product'), $total_products_affected));

				break;

			case 'badge':
				// Related items: sellers, seller groups > sellers
				$sellers_affected = $seller_groups_affected = array();
				foreach ($this->request->post['selected'] as $badge_id) {
					$sellers_affected = array_merge($sellers_affected, $this->MsLoader->MsBadge->getSellersByBadgeId($badge_id));
					$seller_groups_affected = array_merge($seller_groups_affected, $this->MsLoader->MsBadge->getSellerGroupsByBadgeId($badge_id));
				}
				$total_sellers_affected = count(array_unique($sellers_affected));
				$total_seller_groups_affected = count(array_unique($seller_groups_affected));

				$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_seller_group'), $total_seller_groups_affected));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_seller'), $total_sellers_affected));

				break;

			case 'category':
				// Related items: products
				$products_affected = $categories_affected = array();
				foreach ($this->request->post['selected'] as $category_id) {
					$products_affected = array_merge($products_affected, $this->MsLoader->MsCategory->getProductsByCategoryId($category_id));
					$categories_affected = array_merge($categories_affected, $this->MsLoader->MsCategory->getChildCategoriesByCategoryId($category_id));
				}

				$total_products_affected = count(array_unique($products_affected));
				$total_categories_affected = count(array_diff(array_unique($categories_affected), $this->request->post['selected']));

				$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_product'), $total_products_affected));

				if($total_categories_affected) {
					$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_child_category'), $total_categories_affected));
				}

				break;

			case 'custom_field':
				// Related items: products
				$products_affected = array();
				foreach ($this->request->post['selected'] as $custom_field_id) {
					$products_affected = array_merge($products_affected, $this->MsLoader->MsCustomField->getProductsByCFId($custom_field_id));
				}
				$total_products_affected = count(array_unique($products_affected));

				$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_product'), $total_products_affected));

				break;

			case 'custom_field_group':
				// Related items: custom fields > products
				$total_cfs_affected = $total_products_affected = 0;
				$products_affected = array();

				foreach ($this->request->post['selected'] as $custom_field_group_id) {
					$cfs_affected = $this->MsLoader->MsCustomField->getCustomFields(array('custom_field_group_id' => $custom_field_group_id));
					$total_cfs_affected += !empty($cfs_affected) ? $cfs_affected[0]['total_rows'] : 0;

					foreach ($cfs_affected as $cf) {
						$products_affected = array_merge($products_affected, $this->MsLoader->MsCustomField->getProductsByCFId($cf['custom_field_id']));
					}
				}

				$total_products_affected = count(array_unique($products_affected));

				$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_custom_field'), $total_cfs_affected));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_product'), $total_products_affected));

				break;

			case 'invoice':
				// Related items: products || sellers
				// @todo: Delete unpaid sellers/products ?
				/*$total_products_affected = $total_sellers_affected = 0;
				foreach ($this->request->post['selected'] as $invoice_id) {
					$invoice_type = $this->MsLoader->MsPgRequest->getRequestType($invoice_id);
					if ($invoice_type == MsPgRequest::TYPE_LISTING) {
						$total_products_affected += 1;
					} elseif ($invoice_type == MsPgRequest::TYPE_SIGNUP) {
						$total_sellers_affected += 1;
					}
				}

				if($total_products_affected || $total_sellers_affected) {
					$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				}

				if ($total_products_affected) {
					$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_product'), $total_products_affected));
				}

				if ($total_sellers_affected) {
					$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_seller'), $total_sellers_affected));
				}*/

				break;

			case 'product':
				// Related items: questions, reviews, unpaid invoices, conversations
				// @todo: Reviews are not deleted, because they are base for seller's ratings
				$total_questions_affected = $total_reviews_affected = $total_invoices_affected = $total_conversations_affected = 0;
				foreach ($this->request->post['selected'] as $product_id) {
					$questions_affected = $this->MsLoader->MsQuestion->getQuestions(array('product_id' => $product_id));
					$total_questions_affected += !empty($questions_affected) ? $questions_affected[0]['total_rows'] : 0;

					/*$reviews_affected = $this->MsLoader->MsReview->getReviews(array('product_id' => $product_id));
					$total_reviews_affected += !empty($reviews_affected) ? $reviews_affected[0]['total_rows'] : 0;*/

					$invoices_affected = $this->MsLoader->MsPgRequest->getRequests(array('product_id' => $product_id, 'request_status' => array(MsPgRequest::STATUS_UNPAID)));
					$total_invoices_affected += !empty($invoices_affected) ? $invoices_affected[0]['total_rows'] : 0;

					$conversations_affected = $this->MsLoader->MsConversation->getConversations(array('product_id' => $product_id));
					$total_conversations_affected += !empty($conversations_affected) ? $conversations_affected[0]['total_rows'] : 0;
				}

				if($total_questions_affected || $total_reviews_affected || $total_invoices_affected || $total_conversations_affected) {
					$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				}

				if ($total_questions_affected) {
					$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_question'), $total_questions_affected));
				}

				if ($total_reviews_affected) {
					$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_review'), $total_reviews_affected));
				}

				if ($total_invoices_affected) {
					$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_invoice'), $total_invoices_affected));
				}

				if ($total_conversations_affected) {
					$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_conversation'), $total_conversations_affected));
				}

				break;

			case 'seller':
				// Related items: products
				$total_products_affected = 0;
				foreach ($this->request->post['selected'] as $seller_id) {
					$total_products_affected += $this->MsLoader->MsProduct->getTotalProducts(array('seller_id' => $seller_id));
				}

				$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_product'), $total_products_affected));

				break;

			case 'seller_group':
				// Related items: sellers (changed to default_group_id that can't be deleted)
				$total_sellers_affected = 0;
				foreach ($this->request->post['selected'] as $seller_group_id) {
					$sellers_affected = $this->MsLoader->MsSeller->getSellers(array('seller_group_id' => $seller_group_id));
					$total_sellers_affected += !empty($sellers_affected) ? $sellers_affected[0]['total_rows'] : 0;
				}

				$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_seller'), $total_sellers_affected));

				break;

			case 'shipping_method':
				// Related items: products' shipping, sellers' combined shipping
				$total_product_shipping_rules_affected = $total_combined_shipping_rules_affected = 0;
				foreach ($this->request->post['selected'] as $shipping_method_id) {
					$total_product_shipping_rules_affected += $this->MsLoader->MsShippingMethod->getTotalProductShippingRulesByMethodId($shipping_method_id);
					$total_combined_shipping_rules_affected += $this->MsLoader->MsShippingMethod->getTotalCombinedShippingRulesByMethodId($shipping_method_id);
				}

				$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_shipping_product'), $total_product_shipping_rules_affected));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_shipping_combined'), $total_combined_shipping_rules_affected));

				break;

			case 'social_channel':
				// Related items: sellers' channels
				$total_sellers_affected = 0;
				foreach ($this->request->post['selected'] as $channel_id) {
					$total_sellers_affected += $this->MsLoader->MsSocialLink->getTotalSellersByChannelId($channel_id);
				}

				$affected_items_msg .= ("\n" . $this->language->get('ms_delete_affected'));
				$affected_items_msg .= ("\n" . sprintf($this->language->get('ms_delete_seller'), $total_sellers_affected));

				break;

			default:

				break;
		}

		$affected_items_msg .= ("\n" . $this->language->get('ms_delete_areyousure'));

		$confirm_msg = sprintf($this->language->get('ms_delete_template_confirm'), $deleted_items_msg, $affected_items_msg);

		$this->response->setOutput(json_encode($confirm_msg));
	}
}