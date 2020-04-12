<?php echo $header; ?>
<div class="container">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>
	<?php if (isset($success) && $success) { ?>
	<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
	<?php } ?>

	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?> ms-account-dashboard seller-dashboard"><?php echo $content_top; ?>
			<div class="mm_dashboard col-sm-12">
				<h1><i class="fa fa-tachometer"></i><?php echo $ms_account_dashboard ;?></h1>

				<div class="row">
					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="ms-dashboard-widget total balances">
							<div class="heading">
								<?php echo $ms_account_dashboard_total_current_balance; ?>
							</div>
							<div class="body">
								<i class="fa fa-usd" aria-hidden="true"></i>
								<p class="pull-right"><?php echo $current_balance; ?></p>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="ms-dashboard-widget total sales">
							<div class="heading">
								<?php echo $ms_account_dashboard_total_earnings; ?>
							</div>
							<div class="body">
								<i class="fa fa-bar-chart" aria-hidden="true"></i>
								<p class="pull-right"><?php echo $total_earnings; ?></p>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="ms-dashboard-widget total orders">
							<div class="heading">
								<?php echo $ms_account_dashboard_total_orders; ?>
							</div>
							<div class="body">
								<i class="fa fa-shopping-cart" aria-hidden="true"></i>
								<p class="pull-right"><?php echo $total_orders; ?></p>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="ms-dashboard-widget total views">
							<div class="heading">
								<?php echo $ms_account_dashboard_total_views; ?>
							</div>
							<div class="body">
								<i class="fa fa-eye" aria-hidden="true"></i>
								<p class="pull-right"><?php echo $total_products_views; ?></p>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="ms-dashboard-widget chart">
							<div class="heading">
								<h3><?php echo $ms_account_dashboard_sales_analytics; ?></h3>
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
								<h3><?php echo $ms_account_dashboard_top_selling_products; ?></h3>
							</div>
							<div class="body">
								<canvas id="ms_top_products_sales_chart" width="2" height="1" data-type="top_products_sales"></canvas>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12">
						<div class="ms-dashboard-widget chart">
							<div class="heading">
								<h3><?php echo $ms_account_dashboard_top_viewed_products; ?></h3>
							</div>
							<div class="body">
								<canvas id="ms_top_products_views_chart" width="2" height="1" data-type="top_products_views"></canvas>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="ms-dashboard-widget list">
							<div class="heading">
								<h3><?php echo $ms_account_dashboard_last_orders; ?></h3>
							</div>
							<div class="body table-responsive <?php echo empty($last_orders) ? 'empty' : ''; ?>">
								<?php if (!empty($last_orders)) { ?>
									<table class="mm_dashboard_table table table-borderless table-hover" id="mm_dashboard_top_viewed_products">
										<thead>
											<tr>
												<td class="col-md-3 text-left"><?php echo $ms_account_dashboard_column_date; ?></td>
												<td class="col-md-4 text-left"><?php echo $ms_account_dashboard_column_customer; ?></td>
												<td class="col-md-4 text-left"><?php echo $ms_account_dashboard_column_status; ?></td>
												<td class="col-md-1 text-left"><?php echo $ms_account_dashboard_column_total; ?></td>
											</tr>
										</thead>

										<tbody>
											<?php foreach ($last_orders as $order) { ?>
												<tr>
													<td class="col-md-3 text-left"><?php echo $order['date_added']; ?></td>
													<td class="col-md-4 text-left"><?php echo $order['customer_name']; ?></td>
													<td class="col-md-4 text-left"><?php echo $order['order_status']; ?></td>
													<td class="col-md-1 text-left"><?php echo $order['total']; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								<?php } else { ?>
									<p class="no-results"><?php echo $ms_account_dashboard_no_results_orders; ?></p>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<?php if ($this->config->get('msconf_reviews_enable')) { ?>
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="ms-dashboard-widget list">
								<div class="heading">
									<h3><?php echo $ms_account_dashboard_last_reviews; ?></h3>
								</div>
								<div class="body table-responsive <?php echo empty($last_reviews) ? 'empty' : ''; ?>">
									<?php if (!empty($last_reviews)) { ?>
										<table class="mm_dashboard_table table table-borderless table-hover" id="mm_dashboard_top_viewed_products">
											<thead>
												<tr>
													<td class="col-md-1 text-left"><?php echo $ms_account_dashboard_column_date; ?></td>
													<td class="col-md-3 text-left"><?php echo $ms_account_dashboard_column_customer; ?></td>
													<td class="col-md-3 text-left"><?php echo $ms_account_dashboard_column_product; ?></td>
													<td class="col-md-1 text-left"><?php echo $ms_account_dashboard_column_rating; ?></td>
													<td class="col-md-4 text-left"><?php echo $ms_account_dashboard_column_comment; ?></td>
												</tr>
											</thead>

											<tbody>
												<?php foreach ($last_reviews as $review) { ?>
													<tr>
														<td class="col-md-1 text-left"><?php echo $review['date_created']; ?></td>
														<td class="col-md-3 text-left"><?php echo $review['customer_name']; ?></td>
														<td class="col-md-3 text-left"><?php echo $review['product_name']; ?></td>
														<td class="col-md-1 text-left"><?php echo $review['rating_stars']; ?></td>
														<td class="col-md-4 text-left truncate"><?php echo $review['comment']; ?></td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									<?php } else { ?>
										<p class="no-results"><?php echo $ms_account_dashboard_no_results_reviews; ?></p>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="ms-dashboard-widget list">
							<div class="heading">
								<h3><?php echo $ms_account_dashboard_last_messages; ?></h3>
							</div>
							<div class="body table-responsive <?php echo empty($last_messages) ? 'empty' : ''; ?>">
								<?php if (!empty($last_messages)) { ?>
									<table class="mm_dashboard_table table table-borderless table-hover" id="mm_dashboard_top_viewed_products">
										<thead>
											<tr>
												<td class="col-md-1 text-left"><?php echo $ms_account_dashboard_column_date; ?></td>
												<td class="col-md-3 text-left"><?php echo $ms_account_dashboard_column_from; ?></td>
												<td class="col-md-3 text-left"><?php echo $ms_account_dashboard_column_conversation; ?></td>
												<td class="col-md-5 text-left"><?php echo $ms_account_dashboard_column_message; ?></td>
											</tr>
										</thead>

										<tbody>
											<?php foreach ($last_messages as $message) { ?>
												<tr>
													<td class="col-md-1 text-left"><?php echo $message['date_created']; ?></td>
													<td class="col-md-3 text-left"><?php echo $message['author']; ?></td>
													<td class="col-md-3 text-left truncate"><?php echo $message['title']; ?></td>
													<td class="col-md-5 text-left truncate"><?php echo $message['message']; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								<?php } else { ?>
									<p class="no-results"><?php echo $ms_account_dashboard_no_results_messages; ?></p>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="ms-dashboard-widget list">
							<div class="heading">
								<h3><?php echo $ms_account_dashboard_last_invoices; ?></h3>
							</div>
							<div class="body table-responsive <?php echo empty($last_invoices) ? 'empty' : ''; ?>">
								<?php if (!empty($last_invoices)) { ?>
									<table class="mm_dashboard_table table table-borderless table-hover" id="mm_dashboard_top_viewed_products">
										<thead>
											<tr>
												<td class="col-md-1 text-left"><?php echo $ms_account_dashboard_column_date; ?></td>
												<td class="col-md-3 text-left"><?php echo $ms_account_dashboard_column_type; ?></td>
												<td class="col-md-5 text-left"><?php echo $ms_account_dashboard_column_description; ?></td>
												<td class="col-md-2 text-left"><?php echo $ms_account_dashboard_column_status; ?></td>
												<td class="col-md-1 text-left"><?php echo $ms_account_dashboard_column_total; ?></td>
											</tr>
										</thead>

										<tbody>
											<?php foreach ($last_invoices as $invoice) { ?>
												<tr>
													<td class="col-md-1 text-left"><?php echo $invoice['date_created']; ?></td>
													<td class="col-md-3 text-left"><?php echo $invoice['request_type']; ?></td>
													<td class="col-md-5 text-left truncate"><?php echo $invoice['description']; ?></td>
													<td class="col-md-2 text-left"><?php echo $invoice['request_status']; ?></td>
													<td class="col-md-1 text-left"><?php echo $invoice['amount']; ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
								<?php } else { ?>
									<p class="no-results"><?php echo $ms_account_dashboard_no_results_invoices; ?></p>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<?php echo $footer; ?>
