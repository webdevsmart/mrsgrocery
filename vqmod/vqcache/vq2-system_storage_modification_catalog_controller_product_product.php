<?php
class ControllerProductProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('product/product');


			if(MsLoader::getInstance()->MsHelper->isInstalled()) {
				if (isset($this->session->data['error_warning'])) {
					$data['error_warning'] = $this->session->data['error_warning'];
					unset($this->session->data['error_warning']);
				} else {
					$data['error_warning'] = '';
				}
			}
			
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->load->model('catalog/category');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path)
					);
				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
				);
			}
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

                $this->load->model('journal2/product');
            

		$product_info = $this->model_catalog_product->getProduct($product_id);

            if(MsLoader::getInstance()->MsHelper->isInstalled()) {
            	if($this->config->get('msconf_allow_questions')) {
					$this->MsLoader->MsHelper->addStyle('pagination');
					$this->document->addScript('catalog/view/javascript/pagination.min.js');
					$this->document->addScript('catalog/view/javascript/ms-question.js');
				}

            	$questions = MsLoader::getInstance()->MsQuestion->getQuestions(array('product_id' => $product_id));
				$data['ms_total_questions'] = (!empty($questions)) ? $questions[0]['total_rows'] : 0;
			}
            

		if ($product_info) {

			if(MsLoader::getInstance()->MsHelper->isInstalled()) {
				$this->document->addStyle('catalog/view/theme/default/stylesheet/multiseller.css');
				$this->document->addScript('catalog/view/javascript/dialog-sellercontact.js');
				$data = array_merge($data, MsLoader::getInstance()->getRegistry()->get('load')->language('multiseller/multiseller'));
				MsLoader::getInstance()->getRegistry()->get('load')->model('localisation/country');
				MsLoader::getInstance()->getRegistry()->get('load')->model('localisation/zone');
				MsLoader::getInstance()->getRegistry()->get('load')->model('tool/image');

				$seller_id = MsLoader::getInstance()->MsProduct->getSellerId($this->request->get['product_id']);
				$seller = MsLoader::getInstance()->MsSeller->getSeller($seller_id);

				if (!$seller) {
					$data['seller'] = NULL;
				} else {
					$data['seller'] = array();
					if (!empty($seller['ms.avatar'])) {
						$data['seller']['thumb'] = MsLoader::getInstance()->MsFile->resizeImage($seller['ms.avatar'], $this->config->get('msconf_seller_avatar_product_page_image_width'), $this->config->get('msconf_seller_avatar_product_page_image_height'));
					} else {
						$data['seller']['thumb'] = MsLoader::getInstance()->MsFile->resizeImage('ms_no_image.jpg', $this->config->get('msconf_seller_avatar_product_page_image_width'), $this->config->get('msconf_seller_avatar_product_page_image_height'));
					}

					$settings = MsLoader::getInstance()->MsSetting->getSellerSettings(array("seller_id" => $seller['seller_id']));
					$default_settings = MsLoader::getInstance()->MsSetting->getSellerDefaults();

					$settings = array_merge($default_settings, $settings);
					$country = $this->model_localisation_country->getCountry($settings['slr_country']);
					$settings['slr_country'] = (isset($country['name']) ? $country['name'] : '');

					$data['seller']['settings'] = $settings;

					$data['seller']['nickname'] = $seller['ms.nickname'];
					$data['seller']['seller_id'] = $seller['seller_id'];

					$data['seller']['href'] = $this->url->link('seller/catalog-seller/profile', 'seller_id=' . $seller['seller_id']);

					// social links
					if ($this->config->get('msconf_sl_status')) {
						$this->MsLoader->MsHelper->addStyle('multimerch_social_links');
						$data['seller']['social_links'] = $this->MsLoader->MsSocialLink->getSellerChannels($seller['seller_id']);
						foreach ($data['seller']['social_links'] as &$link) {
							if($this->MsLoader->MsHelper->isValidUrl($link['channel_value'])) {
								$link['image'] = $this->model_tool_image->resize($link['image'], $this->config->get('msconf_sl_icon_width'), $this->config->get('msconf_sl_icon_height'));
							} else {
								unset($link);
							}
						}
					}

					// badges
					$badges = array_unique(array_merge(
						$this->MsLoader->MsBadge->getBadges(array('seller_id' => $seller['seller_id'])),
						$this->MsLoader->MsBadge->getBadges(array('seller_group_id' => $seller['ms.seller_group'])),
						$this->MsLoader->MsBadge->getBadges(array('seller_group_id' => $this->config->get('msconf_default_seller_group_id')))
					), SORT_REGULAR);

					foreach ($badges as &$badge) {
						$badge['image'] = $this->model_tool_image->resize($badge['image'], $this->config->get('msconf_badge_width'), $this->config->get('msconf_badge_height'));
					}
					$data['seller']['badges'] = $badges;

					// load disqus data
					$data = array_merge($data, $this->load->language('module/multimerch_disqus'));
					$data['disqus_identifier'] = 'pid' . $this->request->get['product_id'];
					$data['disqus_url'] = $this->url->link('product/product', 'product_id=' . $this->request->get['product_id'], 'SSL');

					//$data['seller']['total_products'] = MsLoader::getInstance()->MsSeller->getTotalProducts(array(
					//	'seller_id' => $seller['seller_id'],
					//	'product_status' => array(MsProduct::STATUS_ACTIVE)
					//));

					$data['contactForm'] = $this->MsLoader->MsHelper->renderPmDialog($data);
				}
			}
			
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			);

			$this->document->setTitle($product_info['meta_title']);
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');

			$data['heading_title'] = $product_info['name'];

			$data['text_select'] = $this->language->get('text_select');
			$data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$data['text_model'] = $this->language->get('text_model');
			$data['text_reward'] = $this->language->get('text_reward');
			$data['text_points'] = $this->language->get('text_points');
			$data['text_stock'] = $this->language->get('text_stock');
			$data['text_discount'] = $this->language->get('text_discount');
			$data['text_tax'] = $this->language->get('text_tax');
			$data['text_option'] = $this->language->get('text_option');
			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_write'] = $this->language->get('text_write');
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
			$data['text_note'] = $this->language->get('text_note');
			$data['text_tags'] = $this->language->get('text_tags');
			$data['text_related'] = $this->language->get('text_related');
			$data['text_payment_recurring'] = $this->language->get('text_payment_recurring');
			$data['text_loading'] = $this->language->get('text_loading');

			$data['entry_qty'] = $this->language->get('entry_qty');
			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_review'] = $this->language->get('entry_review');
			$data['entry_rating'] = $this->language->get('entry_rating');
			$data['entry_good'] = $this->language->get('entry_good');
			$data['entry_bad'] = $this->language->get('entry_bad');

			$data['button_cart'] = $this->language->get('button_cart');
			$data['button_wishlist'] = $this->language->get('button_wishlist');
			$data['button_compare'] = $this->language->get('button_compare');
			$data['button_upload'] = $this->language->get('button_upload');
			$data['button_continue'] = $this->language->get('button_continue');

			$this->load->model('catalog/review');

			$data['tab_description'] = $this->language->get('tab_description');
			$data['tab_attribute'] = $this->language->get('tab_attribute');
			
            if(MsLoader::getInstance()->MsHelper->isInstalled()) {

				$reviews = MsLoader::getInstance()->MsReview->getReviews(array('product_id' => $this->request->get['product_id']));
				$total_reviews = (!empty($reviews)) ? $reviews[0]['total_rows'] : 0;
				$avg_rating = 0;

				$data['avg_rating'] = 0;
				$data['total_reviews'] = $total_reviews;

				if($total_reviews > 0) {
					foreach ($reviews as $key => $review) {
						$avg_rating += $review['rating'];
					}
					$data['avg_rating'] = round($avg_rating / $total_reviews, 1);
				}

				$data['ms_catalog_seller_profile_total_reviews'] = sprintf($data['ms_catalog_seller_profile_total_reviews'], $total_reviews, $total_reviews == 1 ? $this->language->get('mm_review_rating_review') : $this->language->get('mm_review_rating_reviews'));

				$data['tab_review'] = sprintf($this->language->get('tab_review'), $this->config->get('msconf_reviews_enable') ? (int)$total_reviews : (int)$product_info['reviews']);
			} else {
                $data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);
			}
            

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];

			if (strpos($this->config->get('config_template'), 'journal2') === 0) {
			    $this->load->model('catalog/manufacturer');
			    $data['text_manufacturer'] = $this->language->get('text_manufacturer');
                $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
                if ($manufacturer_info && $manufacturer_info['image'] && $this->journal2->settings->get('manufacturer_image', '0') == '1') {
                    $this->journal2->settings->set('manufacturer_image', 'on');
                    $data['manufacturer_image_width'] = $this->journal2->settings->get('manufacturer_image_width', 100);
                    $data['manufacturer_image_height'] = $this->journal2->settings->get('manufacturer_image_height', 100);
                    $data['manufacturer_image'] = Journal2Utils::resizeImage($this->model_tool_image, $manufacturer_info['image'], $data['manufacturer_image_width'], $data['manufacturer_image_height']);
                    switch ($this->journal2->settings->get('manufacturer_image_additional_text', 'none')) {
                        case 'brand':
                            $data['manufacturer_image_name'] = $product_info['manufacturer'];
                            break;
                        case 'custom':
                            $data['manufacturer_image_name'] = $this->journal2->settings->get('manufacturer_image_custom_text');
                            break;
                    }
                }
			}
            
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

			if(MsLoader::getInstance()->MsHelper->isInstalled()) {
				$data['shippableByOC'] = MsLoader::getInstance()->MsProduct->isShippableByOC($product_id);
				$data['product_is_owned_by_customer'] = MsLoader::getInstance()->MsProduct->productOwnedBySeller($product_id, $this->customer->getId());
			}
			


                if (true && $product_info['quantity'] <= 0) {
                    $data['stock_status'] = 'outofstock';
                }
                if (true && $product_info['quantity'] > 0) {
                    $data['stock_status'] = 'instock';
                }
                $data['labels'] = $this->model_journal2_product->getLabels($product_info['product_id']);
            
			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			$this->load->model('tool/image');

			if ($product_info['image']) {
$data['popup_fixed'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
$data['original'] = strpos($this->config->get('config_template'), 'journal2') === 0 ? Journal2Utils::resizeImage($this->model_tool_image, $product_info['image']) : '';
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height'));
			} else {
				$data['popup'] = $this->model_tool_image->resize('no_image.png', $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
			}

			if ($product_info['image']) {
$data['thumb_fixed'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'));
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get($this->config->get('config_theme') . '_image_thumb_width'), $this->config->get($this->config->get('config_theme') . '_image_thumb_height'));
			} else {
				$data['thumb'] = $this->model_tool_image->resize('no_image.png', $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
			}

			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

			foreach ($results as $result) {
				$data['images'][] = array(
'original' => strpos($this->config->get('config_template'), 'journal2') === 0 ? Journal2Utils::resizeImage($this->model_tool_image, $result['image']) : '',
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_popup_width'), $this->config->get($this->config->get('config_theme') . '_image_popup_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_additional_width'), $this->config->get($this->config->get('config_theme') . '_image_additional_height'))
				);
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {

                if (strpos($this->config->get('config_template'), 'journal2') === 0 && $this->journal2->settings->get('show_countdown_product_page', 'on') == 'on') {
                    $this->load->model('journal2/product');
                    $date_end = $this->model_journal2_product->getSpecialCountdown($this->request->get['product_id']);
                    if ($date_end === '0000-00-00') {
                        $date_end = false;
                    }
                    $data['date_end'] = $date_end;
                }
            
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['special'] = false;
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => strpos($this->config->get('config_template'), 'journal2') === 0 && $option_value['image'] && is_file(DIR_IMAGE . $option_value['image']) ? Journal2Utils::resizeImage($this->model_tool_image, $option_value['image'], $this->journal2->settings->get('product_page_options_push_image_width', 30), $this->journal2->settings->get('product_page_options_push_image_height', 30), 'crop') : $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}

			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['review_status'] = $this->config->get('config_review_status');

			if(MsLoader::getInstance()->MsHelper->isInstalled()) {
				if($this->config->get('msconf_reviews_enable')) {
					$this->MsLoader->MsHelper->addStyle('pagination');
					$this->document->addScript('catalog/view/javascript/pagination.min.js');
					$this->document->addScript('catalog/view/javascript/ms-review.js');
				}
			}
            

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			
			if(MsLoader::getInstance()->MsHelper->isInstalled()) {
				$data['reviews'] = sprintf($this->language->get('text_reviews'), $this->config->get('msconf_reviews_enable') ? (int)$data['total_reviews'] : (int)$product_info['reviews']);
			} else {
				$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			}
            
			$data['rating'] = (int)$product_info['rating'];

			// Captcha
			if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			if(MsLoader::getInstance()->MsHelper->isInstalled()) {
				$ms_product_custom_fields_data = $this->MsLoader->MsCustomField->getProductCustomFields(array(
					'product_id' => $product_id
				));

				$ms_product_custom_fields = array();
				foreach($ms_product_custom_fields_data as $ms_cf) {
					$ms_field_type = $this->MsLoader->MsCustomField->getCustomFieldType($ms_cf['custom_field_id']);
					$values = isset($ms_cf['value']) ? (array)json_decode($ms_cf['value']) : FALSE;

					// check if values are for correct type of field
					if(!isset($values[$ms_field_type])) continue;

					$ms_cf_value = '';
					foreach($values as $type => $vals) {
						if(in_array($type, array('text', 'textarea', 'date', 'time', 'datetime'))) {
							// straight assign because actual values are stored
							$ms_cf_value = $vals[0];
						} elseif ($type == 'file') {
							$ms_cf_value = '<ul>';

							// get value name because id is stored
							foreach($vals as $val) {
								$download_info = $this->MsLoader->MsHelper->getOcDownload($val);

								if($download_info) {
									$ms_cf_value .= '<li><a href="' . $this->url->link('product/product/msDownload', 'download_id=' . $val) . '">' . $download_info['name'] . '</a></li>';
								}
							}

							$ms_cf_value .= '</ul>';
						} else {
							// get value name because id is stored
							foreach($vals as &$val) {
								$val = $this->MsLoader->MsCustomField->getCustomFieldValueName($val, $this->config->get('config_language_id'));
							}

							$ms_cf_value = implode(', ', $vals);
						}
					}

					if($ms_cf_value) {
						$ms_product_custom_fields[$ms_cf['custom_field_group_name']][] = array(
							'name' => $ms_cf['custom_field_name'],
							'value' => $ms_cf_value
						);
					}
				}


				$data['ms_product_custom_fields'] = $ms_product_custom_fields;
			}
			

			$data['products'] = array();

			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);

			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get($this->config->get('config_theme') . '_image_related_width'), $this->config->get($this->config->get('config_theme') . '_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get($this->config->get('config_theme') . '_image_related_width'), $this->config->get($this->config->get('config_theme') . '_image_related_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}


                $date_end = false;
                if (strpos($this->config->get('config_template'), 'journal2') === 0 && $special && $this->journal2->settings->get('show_countdown', 'never') !== 'never') {
                    $this->load->model('journal2/product');
                    $date_end = $this->model_journal2_product->getSpecialCountdown($result['product_id']);
                    if ($date_end === '0000-00-00') {
                        $date_end = false;
                    }
                }
            

                $additional_images = $this->model_catalog_product->getProductImages($result['product_id']);

                $image2 = false;

                if (count($additional_images) > 0) {
                    $image2 = $this->model_tool_image->resize($additional_images[0]['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
                }
            
				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,

                'thumb2'       => $image2,
            

                'labels'        => $this->model_journal2_product->getLabels($result['product_id']),
            
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,

                'date_end'       => $date_end,
            
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/product', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function review() {
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		$data['text_no_reviews'] = $this->language->get('text_no_reviews');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		$this->response->setOutput($this->load->view('product/review', $data));
	}


				public function msDownload() {
					$download_id = isset($this->request->get['download_id']) ? $this->request->get['download_id'] : 0;
					$download_info = $this->MsLoader->MsHelper->getOcDownload($download_id);

					if ($download_info) {
						$file = DIR_DOWNLOAD . $download_info['filename'];
						$mask = basename($download_info['mask']);

						if (!headers_sent()) {
							if (file_exists($file)) {
								header('Content-Type: application/octet-stream');
								header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
								header('Expires: 0');
								header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
								header('Pragma: public');
								header('Content-Length: ' . filesize($file));

								if (ob_get_level()) {
									ob_end_clean();
								}

								readfile($file, 'rb');

								exit();
							} else {
								exit('Error: Could not find file ' . $file . '!');
							}
						} else {
							exit('Error: Headers already sent out!');
						}
					}
				}
			
	public function write() {
		$this->load->language('product/product');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get($this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);

            if(MsLoader::getInstance()->MsHelper->isInstalled()) {
            	if($this->config->get('msconf_allow_questions')) {
					$this->MsLoader->MsHelper->addStyle('pagination');
					$this->document->addScript('catalog/view/javascript/pagination.min.js');
					$this->document->addScript('catalog/view/javascript/ms-question.js');
				}

            	$questions = MsLoader::getInstance()->MsQuestion->getQuestions(array('product_id' => $product_id));
				$data['ms_total_questions'] = (!empty($questions)) ? $questions[0]['total_rows'] : 0;
			}
            
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
