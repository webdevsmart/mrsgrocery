$(function() {
	function initChart(chartId) {
		var ctx = $("#" + chartId);
		if (ctx.length == 0)
			return;

		var type = ctx.data('type');

		var chartData = function() {
			var arr = [];

			$.ajax({
				async: false,
				type: 'get',
				dataType: 'json',
				url: 'index.php?route=seller/account-dashboard/getChartData&type=' + encodeURIComponent(type),
				success: function(json) {
					arr = json['chart_data'];
				}
			});

			return arr;
		}();

		if (chartData) {
			var labels = [],
				datasets = [],
				options = {},
				type = '';

			if (typeof chartData['labels'] !== 'undefined') {
				labels = chartData['labels'];
			}

			if (typeof chartData['datasets'] !== 'undefined') {
				$.map(chartData['datasets'], function(item) {
					var newObj = {};
					$.each(item, function(key, value) {
						newObj[key] = value;
					});

					datasets.push(newObj);
				});
			}

			if (typeof chartData['type'] !== 'undefined') {
				type = chartData['type'];

				var xAxes = [],
					yAxes = [];

				// Get required options
				switch (type) {
					case 'line':
						$.map(chartData['xAxes'], function(item) {
							var newObj = {};
							$.each(item, function(key, value) {
								newObj[key] = value;
							});

							xAxes.push(newObj);
						});

						$.map(chartData['yAxes'], function(item) {
							var newObj = {};
							$.each(item, function(key, value) {
								newObj[key] = value;

								if (key == 'ticks') {
									newObj['ticks']['callback'] = function(val, index, values) {
										return typeof value['data_formatted'] !== 'undefined' ? value['data_formatted'][index] : val;
									}
								}
							});

							yAxes.push(newObj);
						});

						options = {
							elements: {
								line: { tension: 0 } // disables bezier curves
							},
							tooltips: {
								callbacks: {
									label: function(tooltipItem, data) {
										return typeof datasets[tooltipItem.datasetIndex] !== 'undefined'
											&& typeof datasets[tooltipItem.datasetIndex]['tooltip_data'][tooltipItem.index] !== 'undefined'
										? datasets[tooltipItem.datasetIndex]['tooltip_data'][tooltipItem.index]
										: '';
									}
								}
							},
							scales: {
								xAxes: xAxes,
								yAxes: yAxes
							},
							legend: {
								onClick: function(e) {
									e.stopPropagation();
								}
							},
							plugins: {
								datalabels: {
									display: false
								}
							}
						};

						break;

					case 'horizontalBar':
						$.map(chartData['xAxes'], function(item) {
							var newObj = {};
							$.each(item, function(key, value) {
								newObj[key] = value;
							});

							xAxes.push(newObj);
						});

						$.map(chartData['yAxes'], function(item) {
							var newObj = {};
							$.each(item, function(key, value) {
								newObj[key] = value;

								// Cut long labels
								if (key == 'afterFit') {
									newObj['afterFit'] = function(scaleInstance) {
										scaleInstance.width = value['label_width'];

										scaleInstance.options.ticks.callback = function(value, index, values) {
											return value.length > 14 ? value.substr(0, 11) + '...' : value;
										}
									}
								}
							});

							yAxes.push(newObj);
						});

						options = {
							layout: {
								padding: {
									right: 80
								}
							},
							legend: { display: false },
							tooltips: {
								callbacks: {
									label: function(tooltipItem, data) {
										return typeof datasets[tooltipItem.datasetIndex] !== 'undefined'
											&& typeof datasets[tooltipItem.datasetIndex]['tooltip_data'][tooltipItem.index] !== 'undefined'
										? datasets[tooltipItem.datasetIndex]['tooltip_data'][tooltipItem.index]
										: '';
									}
								}
							},
							scales: {
								xAxes: xAxes,
								yAxes: yAxes
							},
							plugins: {
								datalabels: {
									anchor: 'end',
									align: 'end',
									display: function(context) {
										return context.dataset.data[context.dataIndex] >= 1;
									},
									formatter: function(value, context) {
										return typeof datasets[context.datasetIndex] !== 'undefined'
											&& typeof datasets[context.datasetIndex]['data_formatted'] !== 'undefined'
											&& typeof datasets[context.datasetIndex]['data_formatted'][context.dataIndex] !== 'undefined'
										? datasets[context.datasetIndex]['data_formatted'][context.dataIndex]
										: value;
									}
								}
							}
						}

						break;
				}
			}

			if (type && labels.length > 0 && datasets.length > 0 && !$.isEmptyObject(options)) {
				var myChart = new Chart(ctx, {
					type: type,
					data: {
						labels: labels,
						datasets: datasets,
					},
					options: options
				});
			} else {
				ctx.closest('.body').addClass('empty');
				ctx.before('<p class="no-results">' + (typeof chartData['common'] !== 'undefined' && typeof chartData['common']['no_results_error'] !== 'undefined' ? chartData['common']['no_results_error'] : 'Not enough data.') + '</p>');
				ctx.remove();
			}
		}
	}

	// Desktops
	if (document.documentElement.clientWidth > 768) {
		$('#ms_sales_analytics_chart').attr('width', 3);
		$('#ms_top_products_views_chart').attr('width', 2);
		$('#ms_top_products_sales_chart').attr('width', 2);
	}

	// Tablets
	if (document.documentElement.clientWidth <= 768 && document.documentElement.clientWidth > 425) {
		$('#ms_sales_analytics_chart').attr('width', 2);
		$('#ms_top_products_views_chart').attr('width', 2);
		$('#ms_top_products_sales_chart').attr('width', 2);
	}

	// Mobile devices
	if (document.documentElement.clientWidth <= 425) {
		$('#ms_sales_analytics_chart').attr('width', 1);
		$('#ms_top_products_views_chart').attr('width', 1);
		$('#ms_top_products_sales_chart').attr('width', 1);
	}

	initChart('ms_sales_analytics_chart');
	initChart('ms_top_products_views_chart');
	initChart('ms_top_products_sales_chart');
});