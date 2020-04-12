<?php

// General
$_['ms_enabled'] = 'Enabled';
$_['ms_disabled'] = 'Disabled';
$_['ms_apply'] = 'Apply';
$_['ms_type'] = 'Type';
$_['ms_type_checkbox'] = 'Checkbox';
$_['ms_type_date'] = 'Date';
$_['ms_type_datetime'] = 'Date &amp; Time';
$_['ms_type_file'] = 'File';
$_['ms_type_image'] = 'Image';
$_['ms_type_radio'] = 'Radio';
$_['ms_type_select'] = 'Select';
$_['ms_type_text'] = 'Text';
$_['ms_type_textarea'] = 'Textarea';
$_['ms_type_time'] = 'Time';
$_['ms_type_choose'] = 'Choose';
$_['ms_type_input'] = 'Input';
$_['ms_store'] = 'Store';
$_['ms_store_default'] = 'Default';
$_['ms_id'] = '#';
$_['ms_login_as_vendor'] = '<a href="%s">Log in as vendor</a> to manage shipping information';
$_['ms_sort_order'] = 'Sort order';

$_['ms_default_select_value'] = '-- None --';

$_['ms_button_approve'] = 'Approve';
$_['ms_button_decline'] = 'Decline';

$_['ms_fixed_coupon_warning'] = "<b>Warning:</b> Fixed (whole cart) coupons can not be applied to multivendor shopping carts and will prevent vendor commissions from being calculated correctly! Percentage coupons will work as expected.";
$_['ms_voucher_warning'] = "<b>Warning:</b> Gift Vouchers can not be applied to multivendor shopping carts and will prevent vendor commissions from being calculated correctly!";
$_['ms_error_directory'] = "Warning: Could not create directory: %s. Please create it manually and make it server-writable before proceeding. <br />";
$_['ms_error_directory_notwritable'] = "Warning: Directory already exists and is not writable: %s. Please make sure it's empty and make it server-writable before proceeding. <br />";
$_['ms_error_directory_exists'] = "Warning: Directory already exists: %s. Please make sure it's empty before proceeding. <br />";
$_['ms_error_product_publish'] = 'Failed to publish some products: seller account not active.';
$_['ms_success_installed'] = 'Extension successfully installed';
$_['ms_success_product_status'] = 'Successfully changed product status.';

$_['ms_db_upgrade'] = ' Please <a href="%s">click here</a> to upgrade your MultiMerch Marketplace database to the latest version (%s -> %s) .';
$_['ms_files_upgrade'] = ' Warning: The version of your MultiMerch files (%s) is older than the version required by your database structure (%s). This can be caused by uploading older MultiMerch version on top of a newer one. Please update your MultiMerch files or reinstall MultiMerch';
$_['ms_db_success'] = 'Your MultiMerch Marketplace database is now up to date!';
$_['ms_db_latest'] = 'Your MultiMerch Marketplace database is already up to date!';
$_['ms_multimerch_not_installed'] = 'Warning: MultiMerch is not installed!';

$_['ms_error_php_version'] = ' MultiMerch requires PHP 5.4 or newer. Please contact your hosting provider to upgrade your PHP installation or change it via CPanel > Select PHP version';

$_['heading_title'] = '<b>[MultiMerch]</b> Digital Marketplace';
$_['text_no_results'] = 'No results';
$_['error_permission'] = 'Warning: You do not have permission to modify module!';

$_['ms_error_withdraw_response'] = 'Error: no response';
$_['ms_error_withdraw_status'] = 'Error: unsuccessful transaction';
$_['ms_success'] = 'Success';
$_['ms_success_transactions'] = 'Transactions successfully completed';
$_['ms_success_payment_deleted'] = 'Payment deleted';
$_['text_success']                 = 'Success: You have modified settings!';

$_['ms_none'] = 'None';
$_['ms_seller'] = 'Seller';
$_['ms_all_sellers'] = 'All sellers';
$_['ms_balance'] = 'Balance';
$_['ms_amount'] = 'Amount';
$_['ms_product'] = 'Product';
$_['ms_quantity'] = 'Quantity';
$_['ms_sales'] = 'Sales';
$_['ms_price'] = 'Price';
$_['ms_net_amount'] = 'Net amount';
$_['ms_from'] = 'From';
$_['ms_to'] = 'To';
$_['ms_paypal'] = 'PayPal';
$_['ms_date_created'] = 'Date created';
$_['ms_status'] = 'Status';
$_['ms_date_modified'] = 'Date modified';
$_['ms_date_paid'] = 'Date paid';
$_['ms_date_last_paid'] = 'Date last paid';
$_['ms_date'] = 'Date';
$_['ms_description'] = 'Description';
$_['ms_confirm'] = 'Confirm';
$_['ms_total'] = 'Total';
$_['ms_method'] = 'Method';

$_['ms_commission'] = 'Commission';
$_['ms_commissions_fees'] = 'Commissions & fees';

$_['ms_user_settings'] = 'User settings';
$_['ms_seller_full_name'] = "Full name";
$_['ms_seller_address1'] = "Address Line 1";
$_['ms_seller_address1_placeholder'] = 'Street address, P.O. box, company name, c/o';
$_['ms_seller_address2'] = "Address Line 2";
$_['ms_seller_address2_placeholder'] = 'Apartment, suite, unit, building, floor etc.';
$_['ms_seller_city'] = "City";
$_['ms_seller_state'] = "State/Province/Region";
$_['ms_seller_zip'] = "ZIP/Postal Code";
$_['ms_seller_country'] = "Country";
$_['ms_seller_company'] = 'Company';

$_['ms_catalog_sellerinfo_information'] = 'Information';
$_['ms_seller_website'] = 'Website';
$_['ms_seller_phone'] = 'Phone';
$_['ms_seller_error_deleting'] = 'Error deleting seller!';
$_['ms_seller_success_deleting'] = 'Success: Seller successfully deleted!';

$_['ms_commission_' . MsCommission::RATE_SALE] = 'Sale fee';
$_['ms_commission_' . MsCommission::RATE_LISTING] = 'Listing fee / method';
$_['ms_commission_' . MsCommission::RATE_SIGNUP] = 'Signup fee / method';

$_['ms_commission_short_' . MsCommission::RATE_SALE] = 'S';
$_['ms_commission_short_' . MsCommission::RATE_LISTING] = 'L';
$_['ms_commission_short_' . MsCommission::RATE_SIGNUP] = 'SU';
$_['ms_commission_actual'] = 'Actual fee rates';

$_['ms_name'] = 'Name';
$_['ms_config_width'] = 'Width';
$_['ms_config_height'] = 'Height';
$_['ms_description'] = 'Description';

$_['ms_enable'] = 'Enable';
$_['ms_disable'] = 'Disable';
$_['ms_create'] = 'Create';
$_['ms_delete'] = 'Delete';
$_['ms_view_in_store'] = 'View in store';
$_['ms_view'] = 'View';
$_['ms_add'] = 'Add';
$_['ms_back'] = 'Back';

$_['ms_button_pay'] = 'Pay';

$_['ms_logo'] = 'Logo';

// Menu
$_['ms_menu_multiseller'] = 'MultiMerch';
$_['ms_menu_dashboard'] = 'Dashboard';
$_['ms_menu_sellers'] = 'Sellers';
$_['ms_menu_seller_groups'] = 'Seller groups';
$_['ms_menu_catalog'] = 'Catalog';
$_['ms_menu_attributes'] = 'Attributes';
$_['ms_menu_categories'] = 'Categories';
$_['ms_menu_options'] = 'Options';
$_['ms_menu_products'] = 'Products';
$_['ms_menu_imports'] = 'Imports';
$_['ms_menu_custom_fields'] = 'Custom fields';
$_['ms_menu_orders'] = 'Orders';
$_['ms_menu_finances'] = 'Finances';
$_['ms_menu_payment'] = 'Payments';
$_['ms_menu_invoice_seller'] = 'Seller invoices';
$_['ms_menu_payment_request'] = 'Invoices';
$_['ms_menu_payment_gateway'] = 'Payment methods';
$_['ms_menu_payment_gateway_settings'] = 'Payment method settings';
$_['ms_menu_payout'] = 'Payouts';
$_['ms_menu_payout_generate'] = 'Generate';
$_['ms_menu_payout_view'] = 'View';
$_['ms_menu_transactions'] = 'Transactions';
$_['ms_menu_conversations'] = 'Conversations';
$_['ms_menu_reviews'] = 'Reviews';
$_['ms_menu_questions'] = 'Questions';
$_['ms_menu_shipping_method'] = 'Shipping methods';
$_['ms_menu_event'] = 'Activity';
$_['ms_menu_settings'] = 'Settings';
$_['ms_menu_install'] = 'Install';
$_['ms_menu_marketplace'] = 'Marketplace';
$_['ms_menu_system'] = 'System';
$_['ms_menu_coupon'] = 'Coupons';

$_['ms_menu_reports'] = 'Reports';
$_['ms_menu_reports_sales'] = 'Sales';
$_['ms_menu_reports_sales_list'] = 'List of sales';
$_['ms_menu_reports_sales_by_day'] = 'Sales by day';
$_['ms_menu_reports_sales_by_month'] = 'Sales by month';
$_['ms_menu_reports_sales_by_product'] = 'Sales by product';
$_['ms_menu_reports_sales_by_seller'] = 'Sales by seller';
$_['ms_menu_reports_sales_by_customer'] = 'Sales by customer';
$_['ms_menu_reports_finances'] = 'Finances';
$_['ms_menu_reports_finances_transactions'] = 'Transactions';
$_['ms_menu_reports_finances_seller'] = 'Seller finances';
$_['ms_menu_reports_finances_payouts'] = 'Payouts';
$_['ms_menu_reports_finances_payments'] = 'Payments';

// Settings
$_['ms_settings_heading'] = 'Settings';
$_['ms_settings_breadcrumbs'] = 'Settings';
$_['ms_config_seller_validation'] = 'Seller validation';
$_['ms_config_seller_validation_note'] = 'If enabled, new seller accounts will be created as inactive until approved by the admin';
$_['ms_config_seller_validation_none'] = 'No validation';
$_['ms_config_seller_validation_activation'] = 'Activation via email';
$_['ms_config_seller_validation_approval'] = 'Manual approval';

$_['ms_error_htaccess'] = 'MultiMerch SEO requires OpenCart .htaccess file to be enabled. Please rename '. $_SERVER['DOCUMENT_ROOT'] .'/.htaccess.txt to '. $_SERVER['DOCUMENT_ROOT'] .'/.htaccess in the root of your OpenCart setup and make sure your server is configured to support mod_rewrite.';
$_['ms_error_htaccess_txt'] = 'MultiMerch SEO requires OpenCart .htaccess file to be enabled. Please copy the htaccess.txt file from OpenCart archive over to your marketplace root '. $_SERVER['DOCUMENT_ROOT'] .', rename it to .htaccess and make sure your server is configured to support mod_rewrite.';

$_['ms_settings_error_vendor_shipping_methods'] = 'Please <a target="_blank" href="%s">create at least one vendor shipping method</a> for vendor shipping to work correctly!';
$_['ms_settings_error_vendor_shipping_times'] = 'Please create least one vendor delivery time for vendor shipping to work correctly!';
$_['ms_settings_error_vendor_duplicate_seo_slug'] = 'Please specify different base SEO keywords for sellers and products!';

$_['ms_config_general'] = 'General';
$_['ms_config_limits'] = 'Limits';
$_['ms_config_file_types'] = 'File types';
$_['ms_config_shipping'] = 'Shipping';
$_['ms_config_product_fields'] = 'Product form fields';

$_['ms_config_product_validation'] = 'Product validation';
$_['ms_config_product_validation_note'] = "'No validation' means new products created by sellers will be active straight off. 'Manual approval' means they will be inactive until approved by the admin";
$_['ms_config_product_validation_from_group_settings'] = 'From group settings';
$_['ms_config_product_validation_none'] = 'No validation';
$_['ms_config_product_validation_approval'] = 'Manual approval';


$_['ms_config_allow_free_products'] = 'Allow free products';

$_['ms_config_allow_digital_products'] = 'Allow digital products';
$_['ms_config_allow_digital_products_note'] = "If enabled, sellers will be able to create products that don't require shipping";

$_['ms_config_minmax_product_price'] = 'Minimum and maximum product price';
$_['ms_config_minmax_product_price_note'] = 'Minimum and maximum product price (0 for no limits)';

$_['ms_config_product_attributes_options'] = 'Attributes / options';
$_['ms_config_allow_attributes'] = 'Allow vendors to create attributes';
$_['ms_config_allow_attributes_note'] = 'Allow sellers to create their own product attributes in addition to the main marketplace attributes';
$_['ms_config_allow_options'] = 'Allow vendors to create options';
$_['ms_config_allow_options_note'] = 'Allow sellers to create their own product options in addition to the main marketplace options';
$_['ms_config_allowed_option_types'] = 'Allowed option types';
$_['ms_config_allowed_option_types_note'] = 'Allow sellers to create these specific product option types only';
$_['ms_config_option_type_select'] = 'Select';
$_['ms_config_option_type_radio'] = 'Radio';
$_['ms_config_option_type_checkbox'] = 'Checkbox';
$_['ms_config_option_type_text'] = 'Text';
$_['ms_config_option_type_textarea'] = 'Textarea';
$_['ms_config_option_type_file'] = 'File';
$_['ms_config_option_type_date'] = 'Date';
$_['ms_config_option_type_time'] = 'Time';
$_['ms_config_option_type_datetime'] = 'Datetime';

$_['ms_config_product_questions'] = 'Product questions';
$_['ms_config_allow_question'] = 'Enable questions';
$_['ms_config_allow_question_note'] = 'Allow customers to ask questions on product page';

$_['ms_config_allowed_image_types'] = 'Allowed image extensions';
$_['ms_config_allowed_image_types_note'] = 'Allowed image extensions';

$_['ms_config_images_limits'] = 'Product image limits';
$_['ms_config_images_limits_note'] = 'Minimum and maximum number of images (incl. thumbnail) required/allowed for products (0 = no limit)';

$_['ms_config_downloads_limits'] = 'Product download limits';
$_['ms_config_downloads_limits_note'] = 'Minimum and maximum number of downloads required/allowed for products (0 = no limit)';

$_['ms_config_allowed_download_types'] = 'Allowed download extensions';
$_['ms_config_allowed_download_types_note'] = 'Allowed download extensions';

$_['ms_config_paypal_sandbox'] = 'PayPal Sandbox mode';
$_['ms_config_paypal_sandbox_note'] = 'Use PayPal in Sandbox mode for testing and debugging';

$_['ms_config_paypal_address'] = 'PayPal address';
$_['ms_config_paypal_address_note'] = 'Your PayPal address for listing and signup fees';


$_['ms_config_product_categories'] = 'Categories';
$_['ms_config_allow_seller_categories'] = 'Enable seller categories';
$_['ms_config_allow_seller_categories_note'] = 'If enabled, sellers will be able to create their own product categories independent from the main marketplace categories';
$_['ms_config_allow_multiple_categories'] = 'Allow multiple categories';
$_['ms_config_allow_multiple_categories_note'] = 'Allow sellers to add products to multiple categories';
$_['ms_config_restrict_categories'] = 'Disallowed categories';
$_['ms_config_restrict_categories_note'] = '<u>Disallow</u> sellers to list products in these categories';

$_['ms_config_product_included_fields'] = 'Product form fields';
$_['ms_config_product_included_fields_note'] = 'Choose which product fields will be displayed to sellers in the product publishing form';


$_['ms_config_seller_terms_page'] = 'Seller account terms';
$_['ms_config_seller_terms_page_note'] = 'Sellers have to agree to the terms when creating a seller account.';


$_['ms_config_finances'] = 'Finances';
$_['ms_config_miscellaneous'] = 'Miscellaneous';
$_['ms_config_deprecated'] = 'Deprecated';
$_['ms_config_see_deprecated'] = 'See deprecated settings';

// MM > Settings > Seller accounts
$_['ms_config_tab_sellers'] = 'Seller accounts';

// MM > Settings > Product publishing
$_['ms_config_tab_products'] = 'Product publishing';


// MM > Settings > Updates and licensing
$_['ms_config_updates'] = 'Updates';
$_['ms_config_updates_license_info'] = 'MultiMerch license & updates';
$_['ms_config_updates_license_key'] = 'License key';
$_['ms_config_updates_license_key_note'] = "Please specify your MultiMerch license key which can be found inside your <a href='https://multimerch.com/' target='_blank'>MultiMerch account</a>";
$_['ms_config_updates_license_activate'] = 'Activate';
$_['ms_config_updates_updates'] = 'Updates';
$_['ms_config_updates_updates_check'] = 'Updates';
$_['ms_config_updates_updates_not_activated'] = 'Please activate your license to receive information about new MultiMerch updates!';
$_['ms_license_error_no_key'] = 'Error: License key is not specified!';
$_['ms_license_success_activated'] = 'Success: Your copy of MultiMerch is activated!';
$_['ms_update_error_license'] = 'Error: MultiMerch is not activated!';
$_['ms_update_success_no_updates'] = 'Your have the latest version of MultiMerch! (%s)';
$_['ms_update_success_available_update'] = 'Updates for your MultiMerch are available! (%s -> %s)';
$_['ms_api_error'] = 'Error: %s.';
$_['ms_api_error_license_generic'] = "Could not activate license key! Please get in touch with MultiMerch at <a href='https://multimerch.com/' target='_blank'>https://multimerch.com/</a> for support";
$_['ms_api_error_license_connection'] = "Could not connect to licensing server! Please make sure your server configuration is not blocking external requests";
$_['ms_api_error_license_invalid'] = 'License is invalid';
$_['ms_api_error_license_missing'] = 'License does not exist';
$_['ms_api_error_license_not_activable'] = 'License can not be activated';
$_['ms_api_error_license_revoked'] = 'License key revoked';
$_['ms_api_error_no_activations_left'] = 'No activations left';
$_['ms_api_error_license_expired'] = "This license has expired! Please get in touch with MultiMerch at <a href='https://multimerch.com/' target='_blank'>https://multimerch.com/</a> for help and renewals";
$_['ms_api_error_key_mismatch'] = 'License key mismatch';
$_['ms_api_error_item_id'] = 'Invalid license identifier';
$_['ms_api_error_item_name'] = 'Item name mismatch';
$_['ms_api_error_no_site'] = 'Domain name mismatch';
$_['ms_api_error_unrecognized'] = 'Unrecognized error type';
$_['ms_api_error_request'] = 'Error: An API error occurred - %s.';
$_['ms_api_error_incorrect_response'] = 'Error: Incorrect server response!';

// MM > Settings > Shipping
$_['ms_config_shipping'] = 'Shipping';
$_['ms_config_shipping_methods'] = 'Vendor shipping methods';
$_['ms_config_shipping_methods_manage'] = 'Manage vendor shipping methods';
$_['ms_config_shipping_type'] = 'Shipping type';
$_['ms_config_enable_store_shipping'] = 'Marketplace shipping';
$_['ms_config_enable_vendor_shipping'] = 'Vendor shipping';
$_['ms_config_disable_shipping'] = 'Disabled';
$_['ms_config_shipping_type_note'] = "Marketplace shipping will enable OpenCart's standard shipping system and default shipping extensions, vendors will be able to control shipping.\nVendor shipping will enable MultiMerch multivendor shipping system where vendors can specify their own shipping rates for their products.\nDisabled will disable shipping completely and only allow digital products in MultiMerch.";
$_['ms_config_shipping_delivery_times'] = 'Vendor delivery times';
$_['ms_config_shipping_delivery_time_add_btn'] = '+ Add delivery time';
$_['ms_config_shipping_delivery_time_comment'] = 'Double click in cell to edit';
$_['ms_config_shipping_delivery_times_note'] = 'Specify a set of delivery times that will be available to vendors to choose from when configuring multivendor shipping, e.g. 24H, 3-5 days etc.';

$_['ms_config_vendor_shipping_type'] = 'Vendor shipping type';
$_['ms_config_vendor_shipping_combined'] = 'Combined shipping';
$_['ms_config_vendor_shipping_per_product'] = 'Per-product shipping';
$_['ms_config_vendor_shipping_both'] = 'Both';
$_['ms_config_vendor_shipping_type_note'] = 'With \'Combined shipping\' option selected, seller will be able to only set combined shipping rules. With \'Per-product shipping\' option selected, he can set only fixed per-product shipping rules. \'Both\' option allows to set \'Combined\' as well as \'Per-Product\' shipping rules';

// MM > Settings > Orders
$_['ms_config_orders'] = "Orders";
$_['ms_config_order_states'] = "OpenCart <span class='ms-order-status-color'>customer order</span> states";
$_['ms_config_order_states_note'] = <<<EOT
This setting links OpenCart <span class='ms-order-status-color'>order statuses</span> to logical order states (e.g. unpaid, paid, processing, dispatched, cancelled) and controls how MultiMerch treats OpenCart <span class='ms-order-status-color'>customer orders</span> depending on their status.<BR><BR>
For example, assigning a status to a Pending state will treat orders bearing this status as unpaid (incomplete) to prevent sellers from dispatching products before the customer has completed the payment.
EOT;
$_['ms_config_order_status_autocomplete'] = '(Autocomplete)';
$_['ms_config_order_state_' . MsOrderData::STATE_PENDING] = 'Pending';
$_['ms_config_order_state_' . MsOrderData::STATE_PROCESSING] = 'Processing';
$_['ms_config_order_state_' . MsOrderData::STATE_COMPLETED] = 'Completed';
$_['ms_config_order_state_' . MsOrderData::STATE_FAILED] = 'Failed';
$_['ms_config_order_state_' . MsOrderData::STATE_CANCELLED] = 'Cancelled';
$_['ms_config_order_state_note_' . MsOrderData::STATE_PENDING] = "Pending state indicates orders that were created, but weren't paid for. This lets the marketplace owner (and the sellers) put the order on hold until the customer makes the payment.";
$_['ms_config_order_state_note_' . MsOrderData::STATE_PROCESSING] = "Processing state indicates orders that were created, but weren't paid for. This lets the marketplace owner (and the sellers) put the order on hold until the customer makes the payment.";
$_['ms_config_order_state_note_' . MsOrderData::STATE_COMPLETED] = "Completed state indicates orders that were fully paid for. This lets the marketplace owner (and the sellers) proceed with dispatching products.";
$_['ms_config_order_state_note_' . MsOrderData::STATE_FAILED] = "Failed OC state note";
$_['ms_config_order_state_note_' . MsOrderData::STATE_CANCELLED] = "Cancelled OC state note";

$_['ms_config_suborder_states'] = "MultiMerch <span class='ms-suborder-status-color'>seller order</span> states";
$_['ms_config_suborder_states_note'] = <<<EOT
This setting links MultiMerch <span class='ms-suborder-status-color'>seller order statuses</span> to logical order states (e.g. unpaid, paid, processing, dispatched, cancelled) and controls how MultiMerch treats <span class='ms-suborder-status-color'>seller orders (suborders)</span> depending on their status.<BR><BR>
For example, assigning a status to a Completed state will treat seller orders bearing this status as completed (dispatched) and let the customer leave a review.
EOT;
$_['ms_config_suborder_status_autocomplete'] = '(Autocomplete)';
$_['ms_config_suborder_state_' . MsSuborder::STATE_PENDING] = 'Pending';
$_['ms_config_suborder_state_' . MsSuborder::STATE_PROCESSING] = 'Processing';
$_['ms_config_suborder_state_' . MsSuborder::STATE_COMPLETED] = 'Complete';
$_['ms_config_suborder_state_' . MsSuborder::STATE_FAILED] = 'Failed';
$_['ms_config_suborder_state_' . MsSuborder::STATE_CANCELLED] = 'Cancelled';
$_['ms_config_suborder_state_note_' . MsSuborder::STATE_PENDING] = "Pending state indicates seller orders that were created, but weren't processed yet.";
$_['ms_config_suborder_state_note_' . MsSuborder::STATE_PROCESSING] = "Processing state indicates seller orders that are currently in process.";
$_['ms_config_suborder_state_note_' . MsSuborder::STATE_COMPLETED] = "Completed state indicates seller orders that are completed (dispatched).";
$_['ms_config_suborder_state_note_' . MsSuborder::STATE_FAILED] = "Failed MS state note";
$_['ms_config_suborder_state_note_' . MsSuborder::STATE_CANCELLED] = "Cancelled MS state note";

$_['ms_config_order_statuses'] = "Order status configuration";
$_['ms_config_suborder_statuses'] = "Seller order statuses";
$_['ms_config_suborder_status_default'] = "Default seller order status";
$_['ms_config_suborder_status_default_note'] = "This is the default MultiMerch <span class='ms-suborder-status-color'>seller order status</span> that all new <span class='ms-suborder-status-color'>seller orders</span> will be assigned to when an OpenCart <span class='ms-order-status-color'>customer order</span> is created.";
$_['ms_config_order_status_credit'] = "Balance credit statuses";
$_['ms_config_order_status_credit_note'] = <<<EOT
Choose OpenCart <span class='ms-order-status-color'>customer order statuses</span> and/or MultiMerch <span class='ms-suborder-status-color'>seller order statuses</span> that will <span class='ms-credit-status-color'>credit</span> seller balances with the income from products sold when the status of the order is changed.<BR><BR>
Adding OpenCart statuses here will automatically create transactions to all sellers that are part of the same customer order. Selecting MultiMerch statuses here will create transactions for individual sellers when their respective seller order status is changed.
EOT;
$_['ms_config_order_status_debit'] = "Balance refund statuses";
$_['ms_config_order_status_debit_note'] = <<<EOT
Choose OpenCart <span class='ms-order-status-color'>customer order statuses</span> and/or MultiMerch <span class='ms-suborder-status-color'>seller order statuses</span> that will <span class='ms-refund-status-color'>refund</span> the original transaction from seller balances when the status of the order is changed.<BR><BR>
Adding OpenCart statuses here will automatically refund transactions from all sellers that are part of the same customer order. Selecting MultiMerch statuses here will refund transactions for individual sellers when their respective seller order status is changed.
EOT;

// MM > Settings > Products
$_['ms_config_reviews'] = 'Product reviews';
$_['ms_config_reviews_enable'] = 'Enable reviews';
$_['ms_config_reviews_enable_note'] = "Allow customers to leave feedback on purchased products. Enabling this will disable OpenCart's default review system";

$_['ms_config_import'] = 'Mass product import from CSV';
$_['ms_config_import_enable'] = 'Enable CSV imports';
$_['ms_config_import_enable_note'] = 'Allow vendors to upload products in bulk from a CSV file';
$_['ms_config_import_category_type'] = 'Category input style';
$_['ms_config_import_category_type_note'] = 'This setting controls how MultiMerch accepts product categories in CSV files - either all category levels in a single cell via a separator or each category level in a different cell';
$_['ms_config_import_category_type_all_categories'] = 'All levels in a single cell (separator - |)';
$_['ms_config_import_category_type_categories_levels'] = 'Different levels in different cells';

$_['ms_config_product_categories_type'] = 'Type of product categories';
$_['ms_config_product_categories_type_note'] = 'Which type of categories vendor is allowed to use when listing his product';
$_['ms_config_product_category_store'] = 'Store';
$_['ms_config_product_category_seller'] = 'Vendor';
$_['ms_config_product_category_both'] = 'Both';

// Sales > Order > Info > Shipping
$_['ms_sale_order_shipping_cost'] = 'Shipping cost';


$_['ms_config_status'] = 'Status';
$_['ms_config_top'] = 'Content Top';
$_['ms_config_limit'] = 'Limit:';
$_['ms_config_image'] = 'Image (W x H):';

$_['ms_config_enable_rte'] = 'Enable Rich Text Editor for descriptions';
$_['ms_config_enable_rte_note'] = 'Enable Summernote Rich Text Editor for product and seller description fields.';

$_['ms_config_rte_whitelist'] = 'Tag whitelist';
$_['ms_config_rte_whitelist_note'] = 'Permitted tags in RTE (empty = all tags permitted)';

$_['ms_config_image_sizes'] = 'Image sizes';
$_['ms_config_seller_avatar_image_size'] = 'Avatar image size';
$_['ms_config_seller_avatar_image_size_seller_profile'] = 'Seller profile';
$_['ms_config_seller_avatar_image_size_seller_list'] = 'Seller list';
$_['ms_config_seller_avatar_image_size_product_page'] = 'Product page';
$_['ms_config_seller_avatar_image_size_seller_dashboard'] = 'Seller dashboard';
$_['ms_config_seller_banner_size'] = 'Seller banner size';

$_['ms_config_image_preview_size'] = 'Image preview size';
$_['ms_config_image_preview_size_seller_avatar'] = 'Seller avatar';
$_['ms_config_image_preview_size_product_image'] = 'Product image';

$_['ms_config_product_image_size'] = 'Product image size';
$_['ms_config_product_image_size_seller_profile'] = 'Seller profile';
$_['ms_config_product_image_size_seller_products_list'] = 'Catalog products';
$_['ms_config_product_image_size_seller_products_list_account'] = 'Account products';


$_['ms_config_uploaded_image_size'] = 'Image size limits';
$_['ms_config_uploaded_image_size_note'] = 'Define uploaded image dimension limits (W x H). Set 0 for no limits.';
$_['ms_config_max'] = 'Max.';
$_['ms_config_min'] = 'Min.';

$_['ms_config_seo'] = 'SEO';
$_['ms_config_enable_seo_urls_seller'] = 'Generate SEO URLs for new sellers';
$_['ms_config_enable_seller_generate_metatags'] = 'Generate meta tegs for sellers';
$_['ms_config_meta_for_seller_page'] = 'Seller page';
$_['ms_config_meta_for_seller_products_page'] = 'Seller products page';
$_['ms_config_meta_seller_title_template'] = 'Seller title template';
$_['ms_config_meta_seller_h1_template'] = 'Seller h1 template';
$_['ms_config_meta_seller_description_template'] = 'Seller description template';
$_['ms_config_meta_seller_keyword_template'] = 'Seller keywords template';

$_['ms_config_enable_seo_urls_seller_note'] = 'This option will generate SEO-friendly URLs for new sellers. SEO URLs need to be enabled in OpenCart to use this.';
$_['ms_config_enable_seo_urls_product'] = 'Generate SEO URLs for new products (experimental)';
$_['ms_config_enable_seo_urls_product_note'] = 'This option will generate SEO-friendly URLs for new product. SEO URLs need to be enabled in OpenCart to use this. Experimental, especially for non-English stores.';
$_['ms_config_enable_non_alphanumeric_seo'] = 'Allow UTF8 in SEO URLs (experimental)';
$_['ms_config_enable_non_alphanumeric_seo_note'] = 'This will not strip UTF8 symbols from SEO URLs. Use at your own risk.';
$_['ms_config_sellers_slug'] = 'Sellers SEO URL base keyword';
$_['ms_config_sellers_slug_'] = '/seller-name/';
$_['ms_config_sellers_slug_note'] = <<<EOT
Use this setting to specify the URL keyword that will be used as a base for seller profiles in your marketplace, <br />e.g. %ssellers/johndoe/store/<br>
WARNING: Modifying this on live marketplaces may break previous SEO URLs and affect your rankings.
EOT;
$_['ms_config_products_slug'] = 'Products SEO URL base keyword';
$_['ms_config_products_slug_note'] = <<<EOT
Use this setting to specify the URL keyword that will be used as a base for products in your marketplace, <br />e.g. %sproducts/iphone-5s/. Leave this field blank to disable base URL keyword.<br>
WARNING: Modifying this on live marketplaces may break previous SEO URLs and affect your rankings.
EOT;

$_['ms_config_sellers_map'] = 'Map of sellers';
$_['ms_config_sellers_map_api_key'] = 'Google Maps API key';
$_['ms_config_sellers_map_api_key_note'] = 'Please specify your <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">Google Maps API key</a> to enable a map view of sellers';

$_['ms_config_logging'] = 'Debug';
$_['ms_config_logging_level'] = 'Debug log level';
$_['ms_config_logging_level_note'] = 'Specify the debug logging level (error - log errors only, info - log some info, debug - log complete function backtraces (only enable this for short periods of debugging))';
$_['ms_config_logging_level_error'] = 'Error';
$_['ms_config_logging_level_debug'] = 'Debug';
$_['ms_config_logging_level_info'] = 'Info';
$_['ms_config_logging_filename'] = 'Log filename';
$_['ms_config_logging_filename_note'] = 'Specify the MultiMerch debug log filename';

$_['ms_config_seller'] = 'Sellers';

// Change Seller Group
$_['ms_config_change_group'] = 'Allow select group on signup';
$_['ms_config_change_group_note'] = 'Allow sellers to choose seller group on signup';

// Change Seller Nickname
$_['ms_config_seller_change_nickname'] = 'Allow nickname change';
$_['ms_config_seller_change_nickname_note'] = 'Allow sellers to change nickname/shop name';

// Seller Nickname Rules
$_['ms_config_nickname_rules'] = 'Seller nickname rules';
$_['ms_config_nickname_rules_note'] = 'Character sets allowed in seller nicknames';
$_['ms_config_nickname_rules_alnum'] = 'Alphanumeric';
$_['ms_config_nickname_rules_ext'] = 'Extended latin';
$_['ms_config_nickname_rules_utf'] = 'Full UTF-8';


$_['mxt_google_analytics'] = 'Google Analytics';
$_['mxt_google_analytics_enable'] = 'Enable Google Analytics';

$_['mxt_disqus_comments'] = 'Disqus Comments';
$_['mxt_disqus_comments_enable'] = 'Enable Disqus comments';
$_['mxt_disqus_comments_shortname'] = 'Disqus shortname';

$_['mmes_messaging'] = 'Private Messaging';
$_['mmess_config_enable'] = 'Enable private messaging for MultiMerch';
$_['ms_config_msg_allowed_file_types'] = 'Allowed file extensions';
$_['ms_config_msg_allowed_file_types_note'] = 'Allowed file extensions for uploading in messages';

$_['ms_config_coupon'] = 'Discount coupons';
$_['ms_config_coupon_allow'] = 'Allow sellers to create discount coupons';

//Marketplace Dashboard
$_['ms_dashboard_title'] = 'Dashboard';
$_['ms_dashboard_heading'] = 'Dashboard';

$_['ms_dashboard_total_sales'] = 'Total sales';
$_['ms_dashboard_total_orders'] = 'Total orders';
$_['ms_dashboard_total_customers'] = 'Total customers';
$_['ms_dashboard_total_customers_online'] = 'Customers online';
$_['ms_dashboard_total_sellers'] = 'Total sellers';
$_['ms_dashboard_total_sellers_balances'] = 'Total across seller balances';
$_['ms_dashboard_total_products'] = 'Total products';
$_['ms_dashboard_total_products_views'] = 'Total product views';
$_['ms_dashboard_gross_sales'] = 'Gross sales';

$_['ms_dashboard_sales_analytics'] = 'Sales analytics';
$_['ms_dashboard_top_products'] = 'Top selling products';
$_['ms_dashboard_top_sellers'] = 'Best performing sellers';
$_['ms_dashboard_top_customers'] = 'Most valuable customers';
$_['ms_dashboard_top_countries'] = 'Top countries';

$_['ms_dashboard_sales_analytics_no_results'] = "No orders yet.";
$_['ms_dashboard_top_products_no_results'] = "No data.";
$_['ms_dashboard_top_sellers_no_results'] = "No data.";
$_['ms_dashboard_top_customers_no_results'] = "No data.";
$_['ms_dashboard_top_countries_no_results'] = "No data.";

$_['ms_dashboard_marketplace_activity'] = 'Marketplace activity';
$_['ms_dashboard_latest_orders'] = 'Latest orders';

$_['ms_dashboard_marketplace_activity_no_results'] = 'No marketplace activity yet.';
$_['ms_dashboard_latest_orders_no_results'] = 'No orders yet.';

//SEO
$_['ms_seo'] = 'SEO';
$_['ms_seo_urls'] = 'SEO URLs';
$_['ms_use_seo_urls'] = 'Use MultiMerch SEO URLs';
$_['ms_use_seo_urls_note'] = <<<EOT
MultiMerch SEO system replaces OpenCart\'s standard and third party SEO controllers to make MultiMerch search engine optimized out of the box.<br>
WARNING: Enabling this on live OpenCart marketplaces with custom SEO URL structures may break existing URLs. Take care!<br>
If you intend to use third party SEO systems, you may need to disable this as well as the multimerch_core_seo.xml file manually (NOT RECOMMENDED).
EOT;

// Badges
$_['ms_menu_badge'] = 'Badges';
$_['ms_config_badge_title'] = 'Seller badges';
$_['ms_config_badge_manage'] = 'Manage badges';
$_['ms_config_badge_enable_note'] = 'Enable seller badge functionality that allows the marketplace administration to create and assign badges to sellers';
$_['ms_config_badge_size'] = 'Badge size';
$_['ms_catalog_badges_breadcrumbs'] = 'Badges';
$_['ms_catalog_badges_heading'] = 'Badges';
$_['ms_badges_column_id'] = 'ID';
$_['ms_badges_column_name'] = 'Name';
$_['ms_badges_image'] = 'Image';
$_['ms_badges_column_action'] = 'Action';
$_['ms_catalog_insert_badge_heading'] = 'Create badge';
$_['ms_catalog_edit_badge_heading'] = 'Edit badge';
$_['ms_success_badge_created'] = 'Badge created';
$_['ms_success_badge_updated'] = 'Badge updated';
$_['ms_error_badge_name'] = 'Please specify a name for the badge';
$_['ms_error_badge_image'] = 'Please select an image for the badge';

// Social Links
$_['ms_menu_social_links'] = 'Social links';
$_['ms_config_sl_title'] = 'Social media links';
$_['ms_config_sl_enable_note'] = 'Enable the social media link feature which allows sellers to display links to their social media accounts in their profiles';
$_['ms_sl_icon_size'] = 'Icon size';
$_['ms_sl'] = 'Social links';
$_['ms_sl_manage'] = 'Manage social media channels';
$_['ms_sl_create'] = 'New social channel';
$_['ms_sl_update'] = 'Update social channel';
$_['ms_sl_column_id'] = '#';
$_['ms_sl_column_name'] = 'Name';
$_['ms_sl_image'] = 'Image';
$_['ms_sl_column_action'] = 'Action';
$_['ms_success_channel_created'] = 'Social channel created';
$_['ms_success_channel_updated'] = 'Social channel updated';
$_['ms_error_channel_deleting'] = 'Error deleting social channel!';
$_['ms_success_channel_deleting'] = 'Success: Social channel successfully deleted!';
$_['ms_error_channel_name'] = 'Please specify a name for the social channel';

// Seller - List
$_['ms_catalog_sellers_heading'] = 'Sellers';
$_['ms_catalog_sellers_breadcrumbs'] = 'Sellers';
$_['ms_catalog_sellers_newseller'] = 'New seller';
$_['ms_catalog_sellers_create'] = 'Create new seller';
$_['ms_catalog_sellers_view_profile'] = 'View seller profile';

$_['ms_catalog_sellers_total_balance'] = 'Total amount on all balances: <b>%s</b> (active sellers: <b>%s</b>)';
$_['ms_catalog_sellers_email'] = 'Email';
$_['ms_catalog_sellers_total_products'] = 'Products';
$_['ms_catalog_sellers_total_sales'] = 'Sales';
$_['ms_catalog_sellers_current_balance'] = 'Balance';
$_['ms_catalog_sellers_status'] = 'Status';
$_['ms_catalog_sellers_date_created'] = 'Date created';

$_['ms_seller_status_' . MsSeller::STATUS_ACTIVE] = 'Active';
$_['ms_seller_status_' . MsSeller::STATUS_INACTIVE] = 'Inactive';
$_['ms_seller_status_' . MsSeller::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_status_' . MsSeller::STATUS_INCOMPLETE] = 'Incomplete';
$_['ms_seller_status_' . MsSeller::STATUS_DELETED] = 'Deleted';
$_['ms_seller_status_' . MsSeller::STATUS_UNPAID] = 'Unpaid signup fee';

// Customer-seller form
$_['ms_catalog_sellerinfo_heading'] = 'Seller';
$_['ms_catalog_sellerinfo_seller_data'] = 'Seller data';

$_['ms_catalog_sellerinfo_customer'] = 'User';
$_['ms_catalog_sellerinfo_customer_data'] = 'User data';
$_['ms_catalog_sellerinfo_customer_new'] = 'New user';
$_['ms_catalog_sellerinfo_customer_existing'] = 'Existing user';
$_['ms_catalog_sellerinfo_customer_create_new'] = 'Create a new user';
$_['ms_catalog_sellerinfo_customer_firstname'] = 'First Name';
$_['ms_catalog_sellerinfo_customer_lastname'] = 'Last Name';
$_['ms_catalog_sellerinfo_customer_email'] = 'Email';
$_['ms_catalog_sellerinfo_customer_password'] = 'Password';
$_['ms_catalog_sellerinfo_customer_password_confirm'] = 'Confirm password';

$_['ms_catalog_sellerinfo_nickname'] = 'Nickname';
$_['ms_catalog_sellerinfo_keyword'] = 'SEO keyword';
$_['ms_catalog_sellerinfo_description'] = 'Description';
$_['ms_catalog_sellerinfo_zone'] = 'Region / State';
$_['ms_catalog_sellerinfo_zone_select'] = 'Select region/state';
$_['ms_catalog_sellerinfo_zone_not_selected'] = 'No region/state selected';
$_['ms_catalog_sellerinfo_sellergroup'] = 'Seller group';

$_['ms_catalog_sellerinfo_avatar'] = 'Avatar';
$_['ms_catalog_sellerinfo_message'] = 'Message';
$_['ms_catalog_sellerinfo_message_note'] = 'Include this message in the notification email to the seller (optional)';
$_['ms_catalog_sellerinfo_notify'] = 'Notify seller';
$_['ms_catalog_sellerinfo_notify_note'] = 'Check this box to send an email to the seller indicating his account has been modified';
$_['ms_catalog_sellerinfo_product_validation'] = 'Product validation';
$_['ms_catalog_sellerinfo_product_validation_note'] = 'Product validation for this seller';

$_['ms_error_sellerinfo_nickname_empty'] = 'Nickname cannot be empty';
$_['ms_error_sellerinfo_nickname_alphanumeric'] = 'Nickname can only contain alphanumeric symbols';
$_['ms_error_sellerinfo_nickname_utf8'] = 'Nickname can only contain printable UTF-8 symbols';
$_['ms_error_sellerinfo_nickname_latin'] = 'Nickname can only contain alphanumeric symbols and diacritics';
$_['ms_error_sellerinfo_nickname_length'] = 'Nickname should be between 4 and 50 characters';
$_['ms_error_sellerinfo_nickname_taken'] = 'This nickname is already taken';

// Catalog - Products
$_['ms_catalog_products_heading'] = 'Products';
$_['ms_catalog_products_breadcrumbs'] = 'Products';
$_['ms_catalog_products_notify_sellers'] = 'Notify Sellers';
$_['ms_catalog_products_bulk'] = '--Change status--';
$_['ms_catalog_products_bulk_seller'] = '--Change seller--';
$_['ms_catalog_products_noseller'] = '--No seller--';
$_['ms_catalog_products_error_deleting'] = 'Error deleting product(s)!';
$_['ms_catalog_products_success_deleting'] = 'Success: Product successfully deleted!';

$_['ms_product_status_' . MsProduct::STATUS_ACTIVE] = 'Active';
$_['ms_product_status_' . MsProduct::STATUS_INACTIVE] = 'Inactive';
$_['ms_product_status_' . MsProduct::STATUS_DISABLED] = 'Disabled';
$_['ms_product_status_' . MsProduct::STATUS_DELETED] = 'Deleted';
$_['ms_product_status_' . MsProduct::STATUS_UNPAID] = 'Unpaid listing fee';
$_['ms_product_status_' . MsProduct::STATUS_IMPORTED] = 'Imported';

$_['ms_catalog_products_field_price'] = 'Price';
$_['ms_catalog_products_field_quantity'] = 'Quantity';
$_['ms_catalog_products_field_marketplace_category'] = 'Marketplace category';
$_['ms_catalog_products_field_tags'] = 'Tags';
$_['ms_catalog_products_field_attributes'] = 'Attributes';
$_['ms_catalog_products_field_options'] = 'Options';
$_['ms_catalog_products_field_special_prices'] = 'Special prices';
$_['ms_catalog_products_field_quantity_discounts'] = 'Quantity discounts';
$_['ms_catalog_products_field_images'] = 'Images';
$_['ms_catalog_products_field_files'] = 'Files';
$_['ms_catalog_products_field_meta_keyword'] 	 = 'Meta Keywords';
$_['ms_catalog_products_field_meta_description'] = 'Meta Description';
$_['ms_catalog_products_field_meta_title'] = 'Meta Title';
$_['ms_catalog_products_field_seo_url'] = 'SEO Keyword';
$_['ms_catalog_products_field_model']            = 'Model';
$_['ms_catalog_products_field_sku']              = 'SKU';
$_['ms_catalog_products_field_upc']              = 'UPC';
$_['ms_catalog_products_field_ean']              = 'EAN';
$_['ms_catalog_products_field_jan']              = 'JAN';
$_['ms_catalog_products_field_isbn']             = 'ISBN';
$_['ms_catalog_products_field_mpn']              = 'MPN';
$_['ms_catalog_products_field_manufacturer']     = 'Manufacturer';
$_['ms_catalog_products_field_date_available']   = 'Date Available';
$_['ms_catalog_products_field_stock_status']     = 'Out Of Stock Status';
$_['ms_catalog_products_field_tax_class']        = 'Tax Class';
$_['ms_catalog_products_field_subtract']         = 'Subtract Stock';
$_['ms_catalog_products_field_stores']         = 'Stores';
$_['ms_catalog_products_filters']         = 'Filters';
$_['ms_catalog_products_min_order_qty']         = 'Minimum Order Quantity';
$_['ms_catalog_products_related_products']         = 'Related Products';
$_['ms_catalog_products_dimensions']            = 'Dimensions';
$_['ms_catalog_products_weight']            = 'Weight';

// Catalog - Products - Custom fields
$_['ms_catalog_products_tab_custom_field'] = '[MM] Custom fields';
$_['ms_catalog_products_text_placeholder'] = 'Enter some text...';
$_['ms_catalog_products_textarea_placeholder'] = 'Enter some text...';
$_['ms_catalog_products_date_placeholder'] = 'Select date...';
$_['ms_catalog_products_button_upload'] = 'Upload';
$_['ms_catalog_products_success_file_uploaded'] = 'File was successfully uploaded!';
$_['ms_catalog_products_success_upload_removed'] = 'File was successfully removed!';
$_['ms_catalog_products_error_field_required'] = 'Field is required!';
$_['ms_catalog_products_error_field_validation'] = 'Field validation failed! Pattern: %s';

// Catalog - Imports
$_['ms_catalog_imports_heading'] = 'Imports';
$_['ms_catalog_imports_breadcrumbs'] = 'Imports';

$_['ms_catalog_imports_field_name'] = 'Name';
$_['ms_catalog_imports_field_seller'] = 'Seller';
$_['ms_catalog_imports_field_date'] = 'Date';
$_['ms_catalog_imports_field_type'] = 'Type';
$_['ms_catalog_imports_field_processed'] = 'Processed';
$_['ms_catalog_imports_field_added'] = 'Added';
$_['ms_catalog_imports_field_updated'] = 'Updated';
$_['ms_catalog_imports_field_errors'] = 'Errors';
$_['ms_catalog_imports_field_actions'] = 'Actions';

// Catalog - Seller Groups
$_['ms_catalog_seller_groups_heading'] = 'Seller groups';
$_['ms_catalog_seller_groups_breadcrumbs'] = 'Seller Groups';

$_['ms_seller_groups_column_id'] = 'ID';
$_['ms_seller_groups_column_name'] = 'Name';
$_['ms_seller_groups_column_action'] = 'Actions';

$_['ms_catalog_insert_seller_group_heading'] = 'New Seller Group';
$_['ms_catalog_edit_seller_group_heading'] = 'Edit Seller Group';

$_['ms_product_period'] = 'Product listing period in days (0 for unlimited)';
$_['ms_product_quantity'] = 'Product quantity (0 for no limit)';

$_['ms_error_seller_group_name'] = 'Error: Name must be between 3 and 32 symbols long';
$_['ms_error_seller_group_default'] = 'Error: Default seller group can not be deleted!';
$_['ms_success_seller_group_created'] = 'Seller group created';
$_['ms_success_seller_group_updated'] = 'Seller group updated';
$_['ms_error_seller_group_deleting'] = 'Error deleting seller group!';
$_['ms_success_seller_group_deleting'] = 'Success: Seller group successfully deleted!';

// Payments
$_['ms_payment_heading'] = 'Payments';
$_['ms_payment_breadcrumbs'] = 'Payments';
$_['ms_payment_payout_requests'] = 'Payout requests';
$_['ms_payment_payouts'] = 'Manual payouts';
$_['ms_payment_pending'] = 'Pending';
$_['ms_payment_new'] = 'New payment';
$_['ms_payment_paid'] = 'Paid';
$_['ms_payment_no_methods'] = 'No payment methods are available!';
$_['ms_payment_multiple_invoices_no_methods'] = 'Multiple payouts are available only through Paypal MassPay! Please, check Paypal is activated and enabled for payouts in <a href="%s">Payment methods</a>.';

$_['ms_success_payment_created'] = 'Payment successfully created';

// Shipping methods
$_['ms_shipping_method_heading'] = 'Shipping Methods';
$_['ms_shipping_method_breadcrumbs'] = 'Shipping Method';
$_['ms_shipping_method_status_' . MsShippingMethod::STATUS_ENABLED] = 'Enabled';
$_['ms_shipping_method_status_' . MsShippingMethod::STATUS_DISABLED] = 'Disabled';
$_['ms_shipping_method_add_heading'] = 'Add Shipping Method';
$_['ms_shipping_method_add_success'] = 'You have successfully added new shipping method!';
$_['ms_shipping_method_edit_heading'] = 'Edit Shipping Method';
$_['ms_shipping_method_edit_success'] = 'You have successfully modified shipping method!';
$_['ms_shipping_method_delete_success'] = 'You have deleted shipping method!';
$_['ms_shipping_method_delete_error'] = 'Error deleting shipping method!';
$_['ms_shipping_method_name_error'] = 'Error: Name must be between 3 and 32 symbols long';

//Suborders statuses
$_['ms_menu_suborders_statuses'] = 'Seller order statuses';
$_['ms_suborder_status_heading'] = 'Seller order statuses';
$_['ms_suborder_status_name'] = 'Name';
$_['ms_suborder_status_action'] = 'Action';
$_['ms_suborder_status_add_heading'] = 'Add seller order status';
$_['ms_suborder_status_add_success'] = 'You have successfully added new seller order status!';
$_['ms_suborder_status_edit_heading'] = 'Edit seller order status';
$_['ms_suborder_status_edit_success'] = 'You have successfully modified seller order status!';
$_['ms_suborder_status_breadcrumbs'] = 'Seller order statuses';
$_['ms_suborder_status_delete_success'] = 'You have deleted seller order status!';
$_['ms_suborder_status_name_error'] = 'Error: Name must be between 3 and 32 symbols long';
$_['ms_suborder_status_info_disabled_delete'] = "This status is linked to one of MultiMerch order states or belongs to one or more existing orders and therefore can not be deleted.";

// Events
$_['ms_event_heading'] = 'Marketplace activity';
$_['ms_event_breadcrumbs'] = 'Marketplace activity';
$_['ms_event_column_event'] = 'Event';
$_['ms_event_column_description'] = 'Description';
$_['ms_event_product'] = 'Product';
$_['ms_event_seller'] = 'Seller';
$_['ms_event_customer'] = 'Customer';
$_['ms_event_order'] = 'Order';

$_['ms_event_user_deleted'] = '*User deleted*';

// Product events
$_['ms_event_type_' . \MultiMerch\Event\Event::PRODUCT_CREATED] = 'Product created';
$_['ms_event_type_template_' . \MultiMerch\Event\Event::PRODUCT_CREATED] = 'New product <a href="%s" target="_blank" class="product">%s</a> has been created by seller <a href="%s" target="_blank" class="seller">%s</a>.';
$_['ms_event_type_' . \MultiMerch\Event\Event::PRODUCT_MODIFIED] = 'Product modified';
$_['ms_event_type_template_' . \MultiMerch\Event\Event::PRODUCT_MODIFIED] = 'Product <a href="%s" target="_blank" class="product">%s</a> has been modified by <a href="%s" target="_blank" class="seller">%s</a>.';

// Seller events
$_['ms_event_type_' . \MultiMerch\Event\Event::SELLER_CREATED] = 'Seller created';
$_['ms_event_type_template_' . \MultiMerch\Event\Event::SELLER_CREATED] = 'New seller <a href="%s" target="_blank" class="seller">%s</a> has signed up!';
$_['ms_event_type_' . \MultiMerch\Event\Event::SELLER_MODIFIED] = 'Seller modified';
$_['ms_event_type_template_' . \MultiMerch\Event\Event::SELLER_MODIFIED] = 'Seller <a href="%s" target="_blank" class="seller">%s</a> has modified their seller profile.';

// Customer events
$_['ms_event_type_' . \MultiMerch\Event\Event::CUSTOMER_CREATED] = 'Customer created';
$_['ms_event_type_template_' . \MultiMerch\Event\Event::CUSTOMER_CREATED] = 'New customer <a href="%s" target="_blank" class="customer">%s</a> has signed up!';
$_['ms_event_type_' . \MultiMerch\Event\Event::CUSTOMER_MODIFIED] = 'Customer modified';
$_['ms_event_type_template_' . \MultiMerch\Event\Event::CUSTOMER_MODIFIED] = 'Customer <a href="%s" target="_blank" class="customer">%s</a> has updated his information.';

// Order events
$_['ms_event_type_' . \MultiMerch\Event\Event::ORDER_CREATED] = 'Order created';
$_['ms_event_type_template_' . \MultiMerch\Event\Event::ORDER_CREATED] = 'New order <a href="%s" target="_blank" class="order">#%s</a> has been placed by <a href="%s" target="_blank" class="customer">%s</a>.';
$_['ms_event_type_template_' . \MultiMerch\Event\Event::ORDER_CREATED . '_guest'] = 'New order <a href="%s" target="_blank" class="order">#%s</a> has been placed by Guest.';

// Debug
$_['ms_debug_heading'] = 'Debug';
$_['ms_debug_breadcrumbs'] = 'Debug';
$_['ms_debug_info'] = 'MultiMerch debug information';
$_['ms_debug_sub_heading_title'] = 'System information';
$_['ms_debug_multimerchinfo_heading_title'] = 'Debug log';
$_['ms_debug_phpinfo_heading_title'] = 'PHP information';
$_['ms_debug_warning_vqmod_not_installed'] = 'VQMod is not installed!';
$_['ms_debug_warning_server_log_not_available'] = 'Server log is unavailable';
$_['ms_debug_warning_hash_file_invalid'] = 'Hash file have a bad structure';
$_['ms_debug_warning_hash_file_not_find'] = 'Hash file not found';


// Finances - Transactions
$_['ms_transactions_heading'] = 'Transactions';
$_['ms_transactions_breadcrumbs'] = 'Transactions';
$_['ms_transactions_new'] = 'New transaction';

$_['ms_error_transaction_fromto'] = 'Please specify at least the source or the destination seller';
$_['ms_error_transaction_fromto_same'] = 'Source and destination cannot be the same';
$_['ms_error_transaction_amount'] = 'Please specify a valid positive amount';
$_['ms_success_transaction_created'] = 'Transaction successfully created';

$_['button_cancel'] = 'Cancel';
$_['button_save'] = 'Save';
$_['ms_action'] = 'Action';


// Account - Conversations and Messages
$_['ms_account_conversations'] = 'Conversations';
$_['ms_account_messages'] = 'Messages';
$_['ms_sellercontact_success'] = 'Your message has been successfully sent';

$_['ms_account_conversations_heading'] = 'Your Conversations';
$_['ms_account_conversations_breadcrumbs'] = 'Your Conversations';

$_['ms_account_conversations_status'] = 'Status';
$_['ms_account_conversations_from'] = 'Conversation from';
$_['ms_account_conversations_from_admin_prefix'] = ' (administrator)';
$_['ms_account_conversations_to'] = 'Conversation to';
$_['ms_account_conversations_title'] = 'Title';
$_['ms_account_conversations_type'] = 'Conversation type';
$_['ms_account_conversations_date_added'] = 'Date added';

$_['ms_account_conversations_error_deleting'] = 'Error deleting conversation!';
$_['ms_account_conversations_success_deleting'] = 'Success: Conversation successfully deleted!';

$_['ms_account_conversations_sender_type_seller'] = 'seller';
$_['ms_account_conversations_sender_type_buyer'] = 'buyer';
$_['ms_account_conversations_sender_type_admin'] = 'admin';

$_['ms_conversation_title_product'] = 'Inquiry about product: %s';
$_['ms_conversation_title_order'] = 'Inquiry about order: %s';
$_['ms_conversation_title'] = 'Inquiry from %s';

$_['ms_account_conversations_read'] = 'Read';
$_['ms_account_conversations_unread'] = 'Unread';

$_['ms_account_messages_heading'] = 'Messages';
$_['ms_last_message'] = 'Last message';
$_['ms_message_text'] = 'Your message';
$_['ms_post_message'] = 'Send message';

$_['ms_customer_does_not_exist'] = 'Customer account deleted';
$_['ms_error_empty_message'] = 'Message cannot be left empty';

$_['ms_account_conversations_textarea_placeholder'] = 'Enter your message...';
$_['ms_account_conversations_upload'] = 'Upload file';
$_['ms_account_conversations_file_uploaded'] = 'Your file was successfully uploaded!';
$_['ms_error_file_extension'] = 'Invalid extension';

$_['ms_mail_subject_private_message'] = 'New private message received';
$_['ms_mail_private_message'] = <<<EOT
You have received a new private message from %s!

%s

%s

You can reply in the messaging area in your account.
EOT;

$_['ms_account_message'] = 'Message';
$_['ms_account_message_sender'] = 'Sender';
$_['ms_account_message_attachments'] = 'Attachments';

// Attributes
$_['ms_attribute_heading'] = 'Attributes';
$_['ms_attribute_breadcrumbs'] = 'Attributes';
$_['ms_attribute_create'] = 'New attribute';
$_['ms_attribute_edit'] = 'Edit attribute';
$_['ms_attribute_value'] = 'Attribute value';
$_['ms_error_attribute_name'] = 'Attribute name must be between 1 and 128 characters';
$_['ms_error_attribute_type'] = 'This attribute type requires attribute values';
$_['ms_error_attribute_value_name'] = 'Attribute value name must be between 1 and 128 characters';
$_['ms_success_attribute_created'] = 'Attribute successfully created';
$_['ms_success_attribute_updated'] = 'Attribute successfully updated';

$_['button_cancel'] = 'Cancel';
$_['button_save'] = 'Save';
$_['ms_action'] = 'Action';

// Mails
$_['ms_mail_greeting'] = "Hello %s,\n";
$_['ms_mail_greeting_no_name'] = "Hello,\n";
$_['ms_mail_ending'] = "\nRegards,\n%s";
$_['ms_mail_message'] = "\nMessage:\n%s";

$_['ms_mail_subject_seller_account_modified'] = 'Seller account modified';
$_['ms_mail_seller_account_modified'] = <<<EOT
Your seller account at %s has been modified by the administrator.

Account status: %s
EOT;

$_['ms_mail_subject_product_modified'] = 'Product modified';
$_['ms_mail_product_modified'] = <<<EOT
Your product %s at %s has been modified by the administrator.

Product status: %s
EOT;

$_['ms_mail_subject_product_purchased'] = 'New order';
$_['ms_mail_product_purchased'] = <<<EOT
Your product(s) have been purchased from %s.

Customer: %s (%s)

Products:
%s
Total: %s
EOT;

$_['ms_mail_product_purchased_no_email'] = <<<EOT
Your product(s) have been purchased from %s.

Customer: %s

Products:
%s
Total: %s
EOT;

$_['ms_mail_product_purchased_info'] = <<<EOT
\n
Delivery address:

%s %s
%s
%s
%s
%s %s
%s
%s
EOT;

$_['ms_mail_product_purchased_comment'] = 'Comment: %s';

$_['ms_mail_subject_product_reviewed'] = 'New product review';
$_['ms_mail_product_reviewed'] = <<<EOT
New review has been submitted for %s. 
Visit the following link to view it: <a href="%s">%s</a>
EOT;

// Catalog - Mail
// Attributes
$_['ms_mail_subject_attribute_status_changed'] = 'Your product attribute status updated';
$_['ms_mail_attribute_status_changed'] = <<<EOT
The status of your product attribute <strong>%s</strong> has been updated to: <strong>%s</strong>.
EOT;

$_['ms_mail_subject_attribute_seller_changed'] = 'Attribute owner changed';
$_['ms_mail_attribute_seller_attached'] = <<<EOT
Attribute %s has been assigned to your account.
EOT;
$_['ms_mail_attribute_seller_detached'] = <<<EOT
Attribute %s has been reassigned from your account.
EOT;

$_['ms_mail_subject_attribute_converted_to_global'] = 'Attribute converted';
$_['ms_mail_attribute_converted_to_global'] = <<<EOT
Your attribute "%s" has been converted to global.
EOT;

// Attribute groups
$_['ms_mail_subject_attribute_group_status_changed'] = 'Your attribute group status updated';
$_['ms_mail_attribute_group_status_changed'] = <<<EOT
The status of your attribute group <strong>%s</strong> has been updated to: <strong>%s</strong>.
EOT;

// Options
$_['ms_mail_subject_option_status_changed'] = 'Your option status updated';
$_['ms_mail_option_status_changed'] = <<<EOT
The status of your option <strong>%s</strong> has been updated to: <strong>%s</strong>.
EOT;

$_['ms_mail_subject_option_seller_changed'] = 'Option owner changed';
$_['ms_mail_option_seller_attached'] = <<<EOT
Option %s has been assigned to your account.
EOT;
$_['ms_mail_option_seller_detached'] = <<<EOT
Option %s has been reassigned from your account.
EOT;

$_['ms_mail_subject_option_converted_to_global'] = 'Option converted';
$_['ms_mail_option_converted_to_global'] = <<<EOT
Your option "%s" has been converted to global.
EOT;

// Categories
$_['ms_mail_subject_category_status_changed'] = 'Your category status updated';
$_['ms_mail_category_status_changed'] = <<<EOT
The status of your category <strong>%s</strong> has been updated to: <strong>%s</strong>.
EOT;

// Sales - Mail
$_['ms_transaction_order_created'] = 'Order created';
$_['ms_transaction_order'] = 'Sale: Order Id #%s';
$_['ms_transaction_sale'] = 'Sale: %s (-%s commission)';
$_['ms_transaction_refund'] = 'Refund: %s';
$_['ms_payment_method'] = 'Payment method';
$_['ms_payment_method_balance'] = 'Seller balance';
$_['ms_payment_royalty_payout'] = 'Royalty payout to %s at %s';
$_['ms_payment_completed'] = 'Payment completed';

// Payment methods
$_['ms_pg_manage'] = 'Manage payment methods';
$_['ms_pg_heading'] = 'Payment methods';
$_['ms_pg_install'] = 'Success: You have installed %s method!';
$_['ms_pg_uninstall'] = 'Success: You have uninstalled %s method!';
$_['ms_pg_modify'] = 'Success: You have modified %s method!';
$_['ms_pg_modify_error'] = 'Warning: You do not have permission to modify Payment Method extensions!';
$_['ms_pg_for_fee'] = 'Enable for fee:';
$_['ms_pg_for_payout'] = 'Enable for payout:';
$_['ms_pg_uninstall_warning'] = 'Warning!\nAll payment method settings of all sellers will be deleted.\n\nAre you sure you want to continue?';
$_['ms_pg_fee_payment_method_name'] = '[MM] Payment Methods';

// Payment requests
$_['ms_pg_request'] = 'Seller invoices';
$_['ms_pg_request_create'] = 'Create invoice';
$_['ms_pg_request_desc_payout'] = "Payout: %s";
$_['ms_pg_request_error_seller_notselected'] = 'Error: You must select at least one seller!';
$_['ms_pg_request_error_select_payment_request'] = 'Error: You must select at least one invoice!';
$_['ms_pg_request_error_empty'] = 'Error: Request is empty!';
$_['ms_pg_request_error_not_created'] = 'Error: Invoice for %s was not created!';
$_['ms_pg_request_error_type'] = 'Error: Unrecognized invoice type!';
$_['ms_pg_request_error_date_period'] = 'Error: Payout period is not set!';
$_['ms_pg_request_success_deleted'] = 'Success: Invoice successfully deleted!';

$_['ms_pg_request_type_' . MsPgRequest::TYPE_SIGNUP] = 'Signup fee';
$_['ms_pg_request_type_' . MsPgRequest::TYPE_LISTING] = 'Listing fee';
$_['ms_pg_request_type_' . MsPgRequest::TYPE_PAYOUT] = 'Payout';
$_['ms_pg_request_type_' . MsPgRequest::TYPE_PAYOUT_REQUEST] = 'Payout request';
$_['ms_pg_request_type_' . MsPgRequest::TYPE_RECURRING] = 'Recurring';
$_['ms_pg_request_type_' . MsPgRequest::TYPE_SALE] = 'Sale';

$_['ms_pg_request_status_' . MsPgRequest::STATUS_UNPAID] = 'Unpaid';
$_['ms_pg_request_status_' . MsPgRequest::STATUS_PAID] = 'Paid';
$_['ms_pg_request_status_' . MsPgRequest::STATUS_REFUND_REQUESTED] = 'Refund requested';
$_['ms_pg_request_status_' . MsPgRequest::STATUS_REFUNDED] = 'Refunded';

// Payments
$_['ms_pg_payment_number'] = 'Payment #';
$_['ms_pg_payment_type_' . MsPgPayment::TYPE_PAID_REQUESTS] = 'Paid invoices';
$_['ms_pg_payment_type_' . MsPgPayment::TYPE_SALE] = 'Sales';

$_['ms_pg_payment_status_' . MsPgPayment::STATUS_INCOMPLETE] = '<p style="color: red">Incomplete</p>';
$_['ms_pg_payment_status_' . MsPgPayment::STATUS_COMPLETE] = '<p style="color: green">Complete</p>';
$_['ms_pg_payment_status_' . MsPgPayment::STATUS_WAITING_CONFIRMATION] = '<p style="color: blue">Waiting for confirmation</p>';

$_['ms_pg_payment_error_no_method'] = 'Error: You must select payment method!';
$_['ms_pg_payment_error_no_methods'] = 'You must select at least one payment!';
$_['ms_pg_payment_error_no_requests'] = 'Error: You must select invoice!';
$_['ms_pg_payment_error_payment'] = 'Error: Can\'t create payment!';
$_['ms_pg_payment_error_sender_data'] = 'Error: Admin has not specified needed information!';
$_['ms_pg_payment_error_receiver_data'] = 'Error: One or many sellers have not specified needed information!';

// Payouts
$_['ms_payout_heading'] = 'Payouts';
$_['ms_payout_payout'] = 'Payout';
$_['ms_payout_all_payouts'] = 'Past payouts';
$_['ms_payout_invoice'] = 'Invoice';

$_['ms_payout_seller_list_info'] = 'Sellers with zero or negative balances can not be paid and are therefore not displayed.';
$_['ms_payout_seller_list_generate'] = 'Begin a new payout to sellers';
$_['ms_payout_seller_list_refresh'] = 'Refresh';
$_['ms_payout_seller_list_pending'] = 'Pending';
$_['ms_payout_seller_list_payout_name'] = 'Payout name';

$_['ms_payout_view_heading'] = 'Invoices in Payout #%s';

$_['ms_payout_confirm'] = 'Confirm payout';
$_['ms_payout_selected_sellers'] = 'Selected sellers';
$_['ms_payout_date_payout_period'] = 'Payout period';
$_['ms_payout_date_payout_period_until'] = 'Till %s';
$_['ms_payout_error_no_sellers'] = 'Error: No sellers selected!';
$_['ms_payout_success_payout_created'] = 'Success: Payout #%s has been successfully created!';

// Validation messages
$_['ms_validate_default'] = 'The \'%s\' field is invalid';
$_['ms_validate_required'] = 'The \'%s\' field is required';
$_['ms_validate_alpha_numeric'] = 'The \'%s\' field may only contain alpha-numeric characters';
$_['ms_validate_max_len'] = 'The \'%s\' field needs to be \'%s\' or shorter in length';
$_['ms_validate_min_len'] = 'The \'%s\' field needs to be \'%s\' or longer in length';
$_['ms_validate_phone_number'] = 'The \'%s\' field is not a phone number';
$_['ms_validate_numeric'] = 'The \'%s\' field may only contain numeric characters';

// Seller group settings
$_['ms_seller_group_product_number_limit'] = 'Max product number';

// Category-based and product-based fees
$_['ms_fees_heading'] = 'MultiMerch fees';
$_['ms_config_fee_priority'] = 'Fee priority';
$_['ms_config_fee_priority_catalog'] = 'Catalog';
$_['ms_config_fee_priority_vendor'] = 'Vendor';
$_['ms_config_fee_priority_note'] = 'With \'Catalog\' option selected, category/product listing and sale fees will have priority over the vendor / vendor group fee settings (vice-versa with \'Vendor\' option selected)';

// Seller attributes
$_['ms_global_attribute'] = '--Global--';
$_['ms_catalog_attribute_attach_to_seller'] = 'Attach to seller';
$_['ms_catalog_attribute_all_sellers'] = '--All sellers--';
$_['ms_seller_attribute'] = 'Attribute';
$_['ms_seller_attribute_group'] = 'Attribute group';
$_['ms_seller_attribute_manage'] = 'Manage attributes';

$_['ms_seller_attribute_tab_ocattribute'] = 'Marketplace attributes';
$_['ms_seller_attribute_tab_msattribute'] = 'Seller attributes';

$_['ms_seller_attribute_updated'] = 'Success: Attribute(s) updated!';
$_['ms_seller_attribute_deleted'] = 'Success: Attribute(s) deleted!';
$_['ms_seller_attribute_group_updated'] = 'Success: Attribute group(s) updated!';
$_['ms_seller_attribute_group_deleted'] = 'Success: Attribute group(s) deleted!';

$_['ms_seller_attribute_error_creating'] = 'Error creating attribute!';
$_['ms_seller_attribute_error_updating'] = 'Error updating attribute!';
$_['ms_seller_attribute_error_deleting'] = 'Error deleting attribute!';
$_['ms_seller_attribute_error_assigned'] = 'Warning: Attribute `%s` cannot be deleted as it is currently assigned to %s products!';
$_['ms_seller_attribute_error_not_selected'] = 'You must select at least one attribute!';

$_['ms_seller_attribute_group_error_creating'] = 'Error creating attribute group!';
$_['ms_seller_attribute_group_error_updating'] = 'Error updating attribute group!';
$_['ms_seller_attribute_group_error_deleting'] = 'Error deleting attribute group!';
$_['ms_seller_attribute_group_error_assigned'] = 'Warning: Attribute group `%s` cannot be deleted as it is currently assigned to %s attributes!';
$_['ms_seller_attribute_group_error_not_selected'] = 'You must select at least one attribute group!';

$_['ms_seller_attribute_status_' . MsAttribute::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_attribute_status_' . MsAttribute::STATUS_APPROVED] = 'Approved';
$_['ms_seller_attribute_status_' . MsAttribute::STATUS_ACTIVE] = 'Active';
$_['ms_seller_attribute_status_' . MsAttribute::STATUS_INACTIVE] = 'Inactive';

// Seller options
$_['ms_global_option'] = '--Global--';
$_['ms_catalog_option_attach_to_seller'] = 'Attach to seller';
$_['ms_catalog_option_all_sellers'] = '--All sellers--';
$_['ms_seller_option_heading'] = 'Options';
$_['ms_seller_option_breadcrumbs'] = 'Options';
$_['ms_seller_option'] = 'Option';
$_['ms_seller_option_type'] = 'Type';
$_['ms_seller_option_values'] = 'Option values';
$_['ms_seller_option_manage'] = 'Manage options';

$_['ms_seller_option_tab_ocoptions'] = 'Marketplace options';
$_['ms_seller_option_tab_msoptions'] = 'Seller options';

$_['ms_seller_option_updated'] = 'Success: Option(s) updated!';
$_['ms_seller_option_deleted'] = 'Success: Option(s) deleted!';

$_['ms_seller_option_error_creating'] = 'Error creating option!';
$_['ms_seller_option_error_updating'] = 'Error updating option!';
$_['ms_seller_option_error_deleting'] = 'Error deleting option!';
$_['ms_seller_option_error_assigned'] = 'Warning: Option `%s` cannot be deleted as it is currently assigned to %s products!';
$_['ms_seller_option_error_not_selected'] = 'You must select at least one option!';

$_['ms_seller_option_status_' . MsOption::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_option_status_' . MsOption::STATUS_APPROVED] = 'Approved';
$_['ms_seller_option_status_' . MsOption::STATUS_ACTIVE] = 'Active';
$_['ms_seller_option_status_' . MsOption::STATUS_INACTIVE] = 'Inactive';

// Seller categories
$_['ms_global_category'] = '--Global--';
$_['ms_catalog_category_attach_to_seller'] = 'Attach to seller';
$_['ms_catalog_category_all_sellers'] = '--All sellers--';
$_['ms_seller_category_heading'] = 'Categories';
$_['ms_seller_category_breadcrumbs'] = 'Categories';
$_['ms_seller_category'] = 'Category';
$_['ms_seller_category_manage'] = 'Manage categories';
$_['ms_categories_tab_occategories'] = 'Marketplace categories';
$_['ms_categories_tab_mscategories'] = 'Seller categories';


$_['ms_categories_button_add_occategory'] = 'Add new marketplace category';
$_['ms_categories_button_add_mscategory'] = 'Add new seller category';

$_['ms_seller_newcategory_heading'] = 'Add new seller category';
$_['ms_seller_editcategory_heading'] = 'Edit seller category';
$_['ms_seller_category_general'] = 'General';
$_['ms_seller_category_name'] = 'Name';
$_['ms_seller_category_description'] = 'Description';
$_['ms_seller_category_meta_title'] = 'Meta title';
$_['ms_seller_category_meta_description'] = 'Meta description';
$_['ms_seller_category_meta_keyword'] = 'Meta keywords';
$_['ms_seller_category_data'] = 'Data';
$_['ms_seller_category_seller'] = 'Seller';
$_['ms_seller_category_parent'] = 'Parent';
$_['ms_seller_category_filter'] = 'Filters';
$_['ms_seller_category_store'] = 'Stores';
$_['ms_seller_category_keyword'] = 'SEO URL';
$_['ms_seller_category_image'] = 'Image';
$_['ms_seller_category_sort_order'] = 'Sort order';
$_['ms_seller_category_status'] = 'Status';

$_['ms_seller_category_created'] = 'Success: Category created!';
$_['ms_seller_category_updated'] = 'Success: Category updated!';
$_['ms_seller_category_deleted'] = 'Success: Category deleted!';

$_['ms_seller_category_error_creating'] = 'Error creating category!';
$_['ms_seller_category_error_updating'] = 'Error updating category!';
$_['ms_seller_category_error_deleting'] = 'Error deleting category!';
$_['ms_seller_category_error_assigned'] = 'Warning: This category cannot be deleted as it is currently assigned to %s products!';
$_['ms_seller_category_error_no_sellers'] = 'No sellers available';
$_['ms_seller_category_error_not_selected'] = 'You must select at least one category!';

$_['ms_seller_category_status_' . MsCategory::STATUS_DISABLED] = 'Disabled';
$_['ms_seller_category_status_' . MsCategory::STATUS_ACTIVE] = 'Active';
$_['ms_seller_category_status_' . MsCategory::STATUS_INACTIVE] = 'Inactive';

// Sale > Order > Info
$_['ms_order_details_by_seller'] = 'Order details by seller';
$_['ms_order_products_by'] = 'Seller:';
$_['ms_order_id'] = "Seller's unique order number:";
$_['ms_order_current_status'] = "Seller's current order status:";

$_['ms_order_transactions'] = "Seller's balance transactions for this order";
$_['ms_order_transactions_amount'] = 'Amount';
$_['ms_order_transactions_description'] = 'Description';
$_['ms_order_date_created'] = 'Date created';
$_['ms_order_notransactions'] = 'Seller has not yet received any balance transactions for this order';

$_['ms_order_status_initial'] = 'Order created';
$_['ms_order_history'] = "Seller's order status history";

// Reviews
$_['ms_review_heading'] = 'Reviews';
$_['ms_review_breadcrumbs'] = 'Reviews';
$_['ms_review_manage'] = 'Manage reviews';
$_['ms_review_column_product'] = 'Product';
$_['ms_review_column_customer'] = 'Customer';
$_['ms_review_column_seller'] = 'Seller';
$_['ms_review_column_order'] = 'Order';
$_['ms_review_column_rating'] = 'Rating';
$_['ms_review_column_comment'] = 'Comment';
$_['ms_review_column_date_added'] = 'Submitted';

$_['ms_review_general'] = 'Review';
$_['ms_review_edit_heading'] = "Customer's feedback";
$_['ms_review_edit_product'] = 'Product';
$_['ms_review_edit_order'] = 'Order ID';
$_['ms_review_edit_customer'] = 'Customer';
$_['ms_review_edit_review'] = 'Review';
$_['ms_review_edit_seller_response'] = "Seller's response";
$_['ms_review_edit_response'] = 'Response';
$_['ms_review_edit_rating'] = 'Rating';
$_['ms_review_edit_customer_images'] = "Customer's images";
$_['ms_review_edit_images'] = 'Images';

$_['ms_success_review_updated'] = 'Review updated!';
$_['ms_success_review_deleted'] = 'Review deleted!';
$_['ms_error_review_deleting'] = 'Error deleting review!';
$_['ms_error_review_id'] = 'Some error appeared while processing your request!';

$_['ms_review_comments_success_added'] = 'Your comment was successfully submitted!';
$_['ms_review_comments_error_signin'] = 'Please sign in to post a comment!';
$_['ms_review_comments_error_review_id'] = 'Error: no review id specified!';
$_['ms_review_comments_error_notext'] = 'Error: You must enter message!';
$_['ms_review_comments_textarea_placeholder'] = 'Enter your message...';
$_['ms_review_comments_post_message'] = 'Submit comment';
$_['ms_review_no_comments'] = 'Seller has not yet responded to this review.';

$_['ms_review_status_' . MsReview::STATUS_ACTIVE] = 'Active';
$_['ms_review_status_' . MsReview::STATUS_INACTIVE] = 'Inactive';

// Questions
$_['ms_question_heading'] = 'Questions';
$_['ms_question_breadcrumbs'] = 'Questions';
$_['ms_question_manage'] = 'Manage questions';
$_['ms_question_column_product'] = 'Product';
$_['ms_question_column_customer'] = 'Customer';
$_['ms_question_column_answer'] = 'Answer';
$_['ms_question_column_date_added'] = 'Submitted';

$_['ms_question_general'] = 'Question';
$_['ms_question_edit_heading'] = 'Product question';
$_['ms_question_edit_product'] = 'Product';
$_['ms_question_edit_customer'] = 'Author';
$_['ms_question_edit_question'] = 'Question';
$_['ms_question_edit_seller_answer'] = "Seller's answer";
$_['ms_question_edit_answer'] = 'Answer';
$_['ms_question_no_answers'] = 'Seller has not answered this question yet!';
$_['ms_questions_customer_deleted'] = '*Customer deleted*';

$_['ms_success_question_updated'] = 'Question updated!';
$_['ms_success_question_deleted'] = 'Question deleted!';
$_['ms_error_question_deleting'] = 'Error deleting question!';
$_['ms_error_question_id'] = 'Some error appeared while processing your request!';
$_['ms_error_answer_id'] = 'Some error appeared while processing your request!';

// Reports
$_['ms_report_guest_checkout'] = 'Guest';
$_['ms_report_column_order'] = 'Order';
$_['ms_report_column_product'] = 'Product';
$_['ms_report_column_seller'] = 'Seller';
$_['ms_report_column_customer'] = 'Customer';
$_['ms_report_column_email'] = 'Email';
$_['ms_report_column_gross'] = 'Gross';
$_['ms_report_column_discount'] = 'Discount';
$_['ms_report_column_net_total'] = 'Net total';
$_['ms_report_column_net_marketplace'] = 'Net marketplace';
$_['ms_report_column_net_seller'] = 'Net seller';
$_['ms_report_column_tax'] = 'Tax';
$_['ms_report_column_shipping'] = 'Shipping';
$_['ms_report_column_total'] = 'Total';
$_['ms_report_column_date'] = 'Date';
$_['ms_report_column_date_month'] = 'Month';
$_['ms_report_column_total_sales'] = 'Total sales';
$_['ms_report_column_total_orders'] = 'Total orders';
$_['ms_report_column_transaction'] = 'Transaction';
$_['ms_report_column_description'] = 'Description';
$_['ms_report_column_payment'] = 'Payment';
$_['ms_report_column_method'] = 'Method';
$_['ms_report_column_payout'] = 'Payout';
$_['ms_report_column_payer'] = 'Payer';
$_['ms_report_column_balance_in'] = 'Balance in';
$_['ms_report_column_balance_out'] = 'Balance out';
$_['ms_report_column_balance_current'] = 'Current balance';
$_['ms_report_column_marketplace_earnings'] = 'Marketplace earnings';
$_['ms_report_column_seller_earnings'] = 'Seller earnings';
$_['ms_report_column_payments_received'] = 'Payments received';
$_['ms_report_column_payouts_paid'] = 'Payouts paid';
$_['ms_report_column_status'] = 'Status';
$_['ms_report_column_amount'] = 'Amount';
$_['ms_report_column_country'] = 'Country';
$_['ms_report_column_period'] = 'Period';

$_['ms_report_manage_orders'] = 'Manage orders';
$_['ms_report_manage_sellers'] = 'Manage sellers';
$_['ms_report_manage_customers'] = 'Manage customers';
$_['ms_report_manage_products'] = 'Manage products';

$_['ms_report_date_range_today'] = 'Today';
$_['ms_report_date_range_yesterday'] = 'Yesterday';
$_['ms_report_date_range_last7days'] = 'Last 7 days';
$_['ms_report_date_range_last30days'] = 'Last 30 days';
$_['ms_report_date_range_thismonth'] = 'This month';
$_['ms_report_date_range_lastmonth'] = 'Last month';
$_['ms_report_date_range_custom'] = 'Custom range';
$_['ms_report_date_range_apply'] = 'Apply';
$_['ms_report_date_range_cancel'] = 'Cancel';

$_['ms_report_date_range_day_mo'] = 'Mo';
$_['ms_report_date_range_day_tu'] = 'Tu';
$_['ms_report_date_range_day_we'] = 'We';
$_['ms_report_date_range_day_th'] = 'Th';
$_['ms_report_date_range_day_fr'] = 'Fr';
$_['ms_report_date_range_day_sa'] = 'Sa';
$_['ms_report_date_range_day_su'] = 'Su';

$_['ms_report_date_range_month_jan'] = 'January';
$_['ms_report_date_range_month_feb'] = 'February';
$_['ms_report_date_range_month_mar'] = 'March';
$_['ms_report_date_range_month_apr'] = 'April';
$_['ms_report_date_range_month_may'] = 'May';
$_['ms_report_date_range_month_jun'] = 'June';
$_['ms_report_date_range_month_jul'] = 'July';
$_['ms_report_date_range_month_aug'] = 'August';
$_['ms_report_date_range_month_sep'] = 'September';
$_['ms_report_date_range_month_oct'] = 'October';
$_['ms_report_date_range_month_nov'] = 'November';
$_['ms_report_date_range_month_dec'] = 'December';

// Custom fields and field groups common
$_['ms_custom_field_heading'] = 'Custom field';
$_['ms_custom_field_manage'] = 'Manage custom fields';
$_['ms_custom_field_general'] = 'General';
$_['ms_custom_field_name'] = 'Name';
$_['ms_custom_field_location'] = 'Location';
$_['ms_custom_field_value'] = 'Value';
$_['ms_custom_field_sort_order'] = 'Sort order';
$_['ms_custom_field_validation'] = 'Validation';
$_['ms_custom_field_validation_tooltip'] = 'Use regex. E.g: /^[a-zA-Z\d+ ]{2,30}$/';
$_['ms_custom_field_cf_count'] = 'Custom fields attached';
$_['ms_custom_field_status'] = 'Status';
$_['ms_custom_field_required'] = 'Required';
$_['ms_custom_field_type'] = 'Type';
$_['ms_custom_field_note'] = 'Note';
$_['ms_custom_field_location_' . MsCustomField::LOCATION_PRODUCT] = 'Product';
$_['ms_custom_field_status_' . MsCustomField::STATUS_ACTIVE] = 'Active';
$_['ms_custom_field_status_' . MsCustomField::STATUS_DISABLED] = 'Disabled';

// Custom fields
$_['ms_custom_field'] = 'Custom field';
$_['ms_custom_field_breadcrumbs'] = 'Custom field';
$_['ms_custom_field_new_heading'] = 'New custom field';
$_['ms_custom_field_edit_heading'] = 'Edit custom field';
$_['ms_custom_field_create'] = 'Create custom field';
$_['ms_custom_field_confirm_delete'] = 'WARNING: You are about to delete custom field(s). All related product information stored in these custom fields will also be deleted. This operation cannot be undone. Delete custom field(s)?';
$_['ms_custom_field_error_deleting'] = 'Error: unable to delete custom field!';
$_['ms_custom_field_error_not_selected'] = 'You must select at least one custom field!';
$_['ms_custom_field_error_values'] = 'You must create at least one custom field value!';
$_['ms_custom_field_success_created'] = 'Success: Custom field successfully created!';
$_['ms_custom_field_success_updated'] = 'Success: Custom field successfully updated!';
$_['ms_custom_field_success_deleted'] = 'Success: Custom field successfully deleted!';

// Custom field groups
$_['ms_custom_field_group'] = 'Custom field group';
$_['ms_custom_field_group_breadcrumbs'] = 'Custom field group';
$_['ms_custom_field_group_new_heading'] = 'New custom field group';
$_['ms_custom_field_group_edit_heading'] = 'Edit custom field group';
$_['ms_custom_field_group_create'] = 'Create custom field group';
$_['ms_custom_field_group_confirm_delete'] = 'WARNING: You are about to delete custom field group(s). All custom fields that belong to these groups will also be deleted along with all related product information stored in these custom fields. This operation cannot be undone. Delete custom field group(s)?';
$_['ms_custom_field_group_error_deleting'] = 'Error: unable to delete custom field group!';
$_['ms_custom_field_group_error_locations'] = 'You must select at least one location for custom field group!';
$_['ms_custom_field_group_error_not_selected'] = 'You must select at least one custom field group!';
$_['ms_custom_field_group_success_created'] = 'Success: Custom field group successfully created!';
$_['ms_custom_field_group_success_updated'] = 'Success: Custom field group successfully updated!';
$_['ms_custom_field_group_success_deleted'] = 'Success: Custom field group successfully deleted!';

// Orders
$_['ms_order_heading'] = 'Orders';
$_['ms_order_breadcrumbs'] = 'Orders';
$_['ms_order_tab_orders'] = 'Orders';
$_['ms_order_tab_suborders'] = 'Seller orders';
$_['ms_order_column_date_added'] = 'Date Added';
$_['ms_order_column_date_modified'] = 'Date Modified';
$_['ms_order_column_order_id'] = 'Order #';
$_['ms_order_column_suborder_id'] = 'Seller order #';
$_['ms_order_column_order_status'] = 'Status';
$_['ms_order_column_order_total'] = 'Total';
$_['ms_order_column_order_customer'] = 'Customer';
$_['ms_order_column_order_vendor'] = 'Seller';
$_['ms_order_column_vendor_statuses'] = 'Seller order statuses';
$_['ms_order_column_action'] = 'Action';

// Deletion of MultiMerch various items
$_['ms_delete_template_confirm'] = "WARNING: You are about to delete %s. %s";
$_['ms_delete_affected'] = 'This action will affect:';
$_['ms_delete_areyousure'] = 'Are you sure?';
$_['ms_delete_attribute'] = '%s attribute(s)';
$_['ms_delete_attribute_group'] = '%s attribute group(s)';
$_['ms_delete_badge'] = '%s badge(s)';
$_['ms_delete_category'] = '%s category(ies)';
$_['ms_delete_occategory'] = '%s category(ies)';
$_['ms_delete_child_category'] = '%s child category(ies)';
$_['ms_delete_conversation'] = '%s conversation(s)';
$_['ms_delete_custom_field'] = '%s custom field(s)';
$_['ms_delete_custom_field_group'] = '%s custom field group(s)';
$_['ms_delete_coupon'] = '%s coupon(s)';
$_['ms_delete_invoice'] = '%s invoice(s)';
$_['ms_delete_option'] = '%s option(s)';
$_['ms_delete_payment'] = '%s payment(s)';
$_['ms_delete_product'] = '%s product(s)';
$_['ms_delete_question'] = '%s question(s)';
$_['ms_delete_review'] = '%s review(s)';
$_['ms_delete_seller'] = '%s seller(s)';
$_['ms_delete_seller_group'] = '%s seller group(s)';
$_['ms_delete_shipping_method'] = '%s shipping method(s)';
$_['ms_delete_shipping_combined'] = '%s combined shipping rule(s)';
$_['ms_delete_shipping_product'] = '%s product shipping rule(s)';
$_['ms_delete_social_channel'] = '%s social channel(s)';

// Coupons
$_['ms_coupon_heading'] = "Discount coupons";
$_['ms_coupon_breadcrumbs'] = "Coupons";
$_['ms_coupon_edit_info'] = "Seller coupons can be created and modified through seller interfaces.";
$_['ms_coupon_code'] = "Code";
$_['ms_coupon_value'] = "Value";
$_['ms_coupon_uses'] = "Times used";
$_['ms_coupon_date_start'] = "Date start";
$_['ms_coupon_date_end'] = "Date end";
$_['ms_coupon_error_deleting'] = "Error deleting product(s)!";
$_['ms_coupon_success_deleted'] = 'Success: Coupon successfully deleted!';
