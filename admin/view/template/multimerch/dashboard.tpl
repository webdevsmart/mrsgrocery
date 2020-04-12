<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-dashboard">
  <div class="page-header">
	<div class="container-fluid">
	  <h1><?php echo $heading_title; ?></h1>
	  <ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	  </ul>
	</div>
  </div>
  <div class="container-fluid">
	  <div class="row">
		  <div class="col-lg-3 col-md-6 col-sm-6">
			  <div class="ms-dashboard-widget total sales">
				  <div class="heading">
					  <a href="<?php echo $this->url->link('multimerch/report/sales', 'token=' . $this->session->data['token'], 'SSL'); ?>"><?php echo $ms_dashboard_total_sales; ?></a>
				  </div>
				  <div class="body">
					  <i class="fa fa-money" aria-hidden="true"></i>
					  <p class="pull-right"><?php echo $sales_count; ?></p>
				  </div>
			  </div>
		  </div>
		  <div class="col-lg-3 col-md-6 col-sm-6">
			  <div class="ms-dashboard-widget total orders">
				  <div class="heading">
					  <a href="<?php echo $this->url->link('multimerch/order', 'token=' . $this->session->data['token'], 'SSL'); ?>"><?php echo $ms_dashboard_total_orders; ?></a>
				  </div>
				  <div class="body">
					  <i class="fa fa-shopping-cart" aria-hidden="true"></i>
					  <p class="pull-right"><?php echo $orders_count; ?></p>
				  </div>
			  </div>
		  </div>
		  <div class="col-lg-3 col-md-6 col-sm-6">
			  <div class="ms-dashboard-widget total customers">
				  <div class="heading">
					  <a href="<?php echo $this->url->link('customer/customer', 'token=' . $this->session->data['token'], 'SSL'); ?>"><?php echo $ms_dashboard_total_customers; ?></a>
				  </div>
				  <div class="body">
					  <i class="fa fa-user" aria-hidden="true"></i>
					  <p class="pull-right"><?php echo $customers_count; ?></p>
				  </div>
			  </div>
		  </div>
		  <div class="col-lg-3 col-md-6 col-sm-6">
			  <div class="ms-dashboard-widget total online">
				  <div class="heading"><?php echo $ms_dashboard_total_customers_online; ?></div>
				  <div class="body">
					  <i class="fa fa-users" aria-hidden="true"></i>
					  <p class="pull-right"><?php echo $customers_online_count; ?></p>
				  </div>
			  </div>
		  </div>
	  </div>

	  <div class="row">
		  <div class="col-lg-3 col-md-6 col-sm-6">
			  <div class="ms-dashboard-widget total balances">
				  <div class="heading"><?php echo $ms_dashboard_total_sellers_balances; ?></div>
				  <div class="body">
					  <i class="fa fa-credit-card" aria-hidden="true"></i>
					  <p class="pull-right"><?php echo $balances_count; ?></p>
				  </div>
			  </div>
		  </div>
		  <div class="col-lg-3 col-md-6 col-sm-6">
			  <div class="ms-dashboard-widget total sellers">
				  <div class="heading">
					  <a href="<?php echo $this->url->link('multimerch/seller', 'token=' . $this->session->data['token'], 'SSL'); ?>"><?php echo $ms_dashboard_total_sellers; ?></a>
				  </div>
				  <div class="body">
					  <i class="fa fa-user-secret" aria-hidden="true"></i>
					  <p class="pull-right"><?php echo $sellers_count; ?></p>
				  </div>
			  </div>
		  </div>
		  <div class="col-lg-3 col-md-6 col-sm-6">
			  <div class="ms-dashboard-widget total products">
				  <div class="heading">
					  <a href="<?php echo $this->url->link('catalog/product', 'token=' . $this->session->data['token'], 'SSL'); ?>"><?php echo $ms_dashboard_total_products; ?></a>
				  </div>
				  <div class="body">
					  <i class="fa fa-briefcase" aria-hidden="true"></i>
					  <p class="pull-right"><?php echo $products_count; ?></p>
				  </div>
			  </div>
		  </div>
		  <div class="col-lg-3 col-md-6 col-sm-6">
			  <div class="ms-dashboard-widget total views">
				  <div class="heading"><?php echo $ms_dashboard_total_products_views; ?></div>
				  <div class="body">
					  <i class="fa fa-eye" aria-hidden="true"></i>
					  <p class="pull-right"><?php echo $products_view_count; ?></p>
				  </div>
			  </div>
		  </div>
	  </div>

	  <div class="row">
		  <div class="col-lg-12 col-md-12 col-sm-12">
			  <div class="ms-dashboard-widget chart">
				  <div class="heading">
					  <h3><?php echo $ms_dashboard_sales_analytics; ?></h3>
				  </div>
				  <div class="body">
					  <canvas id="ms_sales_analytics_chart" width="3" height="1" data-type="sales_analytics"></canvas>
				  </div>
			  </div>
		  </div>
	  </div>

	  <div class="row">
		  <div class="col-lg-6 col-md-6 col-sm-12">
			  <div class="ms-dashboard-widget chart">
				  <div class="heading">
					  <h3><?php echo $ms_dashboard_top_sellers; ?></h3>
				  </div>
				  <div class="body">
					  <canvas id="ms_top_sellers_chart" width="3" height="1" data-type="top_sellers"></canvas>
				  </div>
			  </div>
		  </div>
		  <div class="col-lg-6 col-md-6 col-sm-12">
			  <div class="ms-dashboard-widget chart">
				  <div class="heading">
					  <h3><?php echo $ms_dashboard_top_products; ?></h3>
				  </div>
				  <div class="body">
					  <canvas id="ms_top_products_chart" width="3" height="1" data-type="top_products"></canvas>
				  </div>
			  </div>
		  </div>
	  </div>

	  <div class="row">
		  <div class="col-lg-6 col-md-6 col-sm-12">
			  <div class="ms-dashboard-widget chart">
				  <div class="heading">
					  <h3><?php echo $ms_dashboard_top_customers; ?></h3>
				  </div>
				  <div class="body">
					  <canvas id="ms_top_customers_chart" width="3" height="1" data-type="top_customers"></canvas>
				  </div>
			  </div>
		  </div>
		  <div class="col-lg-6 col-md-6 col-sm-12">
			  <div class="ms-dashboard-widget chart">
				  <div class="heading">
					  <h3><?php echo $ms_dashboard_top_countries; ?></h3>
				  </div>
				  <div class="body">
					  <canvas id="ms_top_countries_chart" width="3" height="1" data-type="top_countries"></canvas>
				  </div>
			  </div>
		  </div>
	  </div>

	  <div class="row">
		  <div class="col-lg-6 col-md-6 col-sm-12">
			  <div class="ms-dashboard-widget list">
				  <div class="heading">
					  <h3><?php echo $ms_dashboard_marketplace_activity; ?></h3>
				  </div>
				  <div class="body table-responsive <?php echo empty($marketplace_activity) ? 'empty' : ''; ?>">
					  <?php if (!empty($marketplace_activity)) { ?>
						  <table class="table">
							  <thead>
								  <tr>
									  <td><?php echo $ms_report_column_date; ?></td>
									  <td><?php echo $ms_event_column_event; ?></td>
								  </tr>
							  </thead>
							  <tbody>
								  <?php foreach ($marketplace_activity as $activity) { ?>
									  <tr>
										  <td><?php echo $activity['date_created']; ?></td>
										  <td><?php echo $activity['event_description']; ?></td>
									  </tr>
								  <?php } ?>
							  </tbody>
						  </table>
					  <?php } else { ?>
						  <p class="no-results"><?php echo $ms_dashboard_marketplace_activity_no_results; ?></p>
					  <?php } ?>
				  </div>
			  </div>
		  </div>

		  <div class="col-lg-6 col-md-6 col-sm-12">
			  <div class="ms-dashboard-widget list">
				  <div class="heading">
					  <h3><?php echo $ms_dashboard_latest_orders; ?></h3>
				  </div>
				  <div class="body table-responsive <?php echo empty($last_orders) ? 'empty' : ''; ?>">
					  <?php if (!empty($last_orders)) { ?>
						  <table class="table">
							  <thead>
								  <tr>
									  <td><?php echo $ms_report_column_date; ?></td>
									  <td><?php echo $ms_report_column_customer; ?></td>
									  <td><?php echo $ms_report_column_status; ?></td>
									  <td class="text-right"><?php echo $ms_report_column_total; ?></td>
								  </tr>
							  </thead>
							  <tbody>
								  <?php foreach ($last_orders as $last_order) { ?>
									  <tr>
										  <td><?php echo $last_order['date_added']; ?></td>
										  <td><?php echo $last_order['name']; ?></td>
										  <td><?php echo $last_order['order_status']; ?></td>
										  <td class="text-right"><?php echo $last_order['total']; ?></td>
									  </tr>
								  <?php } ?>
							  </tbody>
						  </table>
					  <?php } else { ?>
						  <p class="no-results"><?php echo $ms_dashboard_latest_orders_no_results; ?></p>
					  <?php } ?>
				  </div>
			  </div>
		  </div>
	  </div>
</div>
<script type="text/javascript">
	var msGlobals = {
		token: '<?php echo $token; ?>'
	};
</script>
<?php echo $footer; ?>