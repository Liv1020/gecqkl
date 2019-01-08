$(function() {
	$(document).ready(function() {
		Highcharts.setOptions({
			global: {
				useUTC: false
			}
		});
		var chart;
		$('#container').highcharts({
			chart: {
				type: 'spline',
				animation: Highcharts.svg,
				marginRight: 10,
				events: {
					load: function() {
						var series = this.series[0];
						/*setInterval(function() {
							var x = (new Date()).getTime(),
								y = Math.random();
							series.addPoint([x, y], true, true);
						}, 1000);*/
					}
				}
			},
			title: {
				text: ' 每日登陆量 '
			},
			xAxis: {
				type: 'datetime',
				tickPixelInterval: 150
			},
			yAxis: {
				title: {
					text: 'Value'
				},
				plotLines: [{
					value: 0,
					width: 1,
					color: '#808080'
				}]
			},
			tooltip: {
				formatter: function() {
					return '<b>' + this.series.name + '</b><br/>' +
						Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
						Highcharts.numberFormat(this.y, 2);
				}
			},
			legend: {
				enabled: false
			},
			exporting: {
				enabled: false
			},
			series: [{
				name: 'Random data',
				data: (function() {
					var data = [],
						time = (new Date()).getTime(),
						i;
					for(i = -7; i <= 0; i++) {
						data.push({
							x: time + i * 1000,
							y: Math.random()
						});
						
					}
					return data;
				})()
			}]
		});
	});
});