	<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-5">
        
						<div class="row">
							<div class="col-lg-12">
								<div class="m-b-md">
									<h2># <?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?></h2>
									<h3 id="location_name"></h3>
									<?php if(isset($_GET['err'])){ ?>
									<a href="<?= URL_ROOT;?>view/ticket/?<?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?>"class="btn btn-success btn-xs" data-i18n="[html]tickets.create.label">Create ticket</a>
									<?php }; ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-lg-5">
								<dl class="dl-horizontal">
									<dt data-i18n="[html]location.connection">Connection:</dt> <dd id="conn_status"></dd>
								</dl>
							</div>
							<div class="col-lg-7" id="cluster_info">
								<dl class="dl-horizontal" id="location_status"> </dl>				
											
							</div>						
						</div>
						<div class="row">
							<div class="col-xs-6 col-sm-6 col-md-12">
								<div class="col-lg-5">
									<dl class="dl-horizontal">					
										<dt data-i18n="[html]location.address">Address:</dt> <dd id="location_address"></dd>
										<dt data-i18n="[html]location.zipcode">Postalcode:</dt> <dd id="location_zip"></dd>
										<dt data-i18n="[html]location.city">City:</dt> <dd id="location_city"></dd>
									</dl>
								</div>
								<div class="col-lg-7" id="cluster_info">				
									<dl class="dl-horizontal" >
										<dt data-i18n="[html]location.first">First signal:</dt> <dd id="location_first"> </dd>
										<dt data-i18n="[html]location.last">Last seen:</dt> <dd id="location_last"></dd>
										<dt data-i18n="[html]location.path">Path status:</dt> <dd ><h3 id="path_status"></h3></dd>
									</dl>
								</div>
							</div> 
							<div class="col-xs-6 col-sm-6 col-md-12">
								<div class="col-lg-5">
									<dl class="dl-horizontal">
									<dl class="dl-horizontal" >
										<dt data-i18n="[html]location.mac">MAC:</dt> <dd id="location_mac"> </dd>
										<dt data-i18n="[html]location.udid">UDID:</dt> <dd id="location_udid"></dd>
										<dt data-i18n="[html]location.lijn">Line name:</dt> <dd id="location_lijn"></dd>
									</dl>
									</dl>
								</div>
								<div class="col-lg-7" id="cluster_info">
									<dl class="dl-horizontal" >
										<dt data-i18n="[html]location.serie">Serial nr:</dt> <dd id="location_serie"> </dd>
										<dt data-i18n="[html]location.sim">Simcard nr:</dt> <dd id="location_sim"></dd>
										<dt data-i18n="[html]location.serviceid">ServiceID nr:</dt> <dd id="location_serviceid"></dd>
									</dl>				
								</div>						
							</div>				
						</div>				
				
						<div class="ibox float-e-margins" id="values" >
							<div class="ibox-title">
								<h5 data-i18n="[html]location.values.table.txt">Location values</h5>
							</div>
							<div class="ibox-content">
								<table class="table table-hover no-margins datatable_values" >
									<thead>
									<tr>
										<th data-i18n="[html]location.values.table.th1">Name</th>
										<th data-i18n="[html]location.values.table.th2">Value</th>
									</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
						
						<div class="ibox float-e-margins" id="comp">
							<div class="ibox-title">
								<h5 data-i18n="[html]location.components.table.txt">Location components</h5>
							</div>
							<div class="ibox-content">
								<table class="table table-hover no-margins datatable" id="list_comp">
									<thead>
									<tr>
										<th data-i18n="[html]location.components.table.th1">Component</th>
										<th data-i18n="[html]location.components.table.th2">Quantity</th>
										<th data-i18n="[html]location.components.table.th3">Serial</th>
										<th data-i18n="[html]location.components.table.th4">Install date</th>
										<th data-i18n="[html]location.components.table.th5">Expire date</th>
									</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
						</div>
	
                    </div>
					
                    <div class="col-lg-7">
						
						<div class="statistic-box">
							<div class="tabs-container">
								<ul class="nav nav-tabs">
									<li class="active"><a data-toggle="tab" href="#tab-1" data-i18n="[html]location.tab.tab1.text">Connection status</a></li>
									<li class=""><a data-toggle="tab" href="#tab-2" data-i18n="[html]location.tab.tab2.text">Poll delay</a></li>
									<li class=""><a data-toggle="tab" href="#tab-3" data-i18n="[html]location.tab.tab3.text">Location components</a></li>
								</ul>
								<div class="tab-content">
									<div id="tab-1" class="tab-pane active">
										<div class="panel-body" >
											<div>
												<span class="pull-right text-right">
													<span data-i18n="[html]location.tab.location">Location</span>: <b><?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?></b>
													<br/>
												</span>
												<h3 class="font-bold no-margins" data-i18n="[html]location.tab.tab1.h3">
													Locatie status
												</h3>
												<small><span data-i18n="[html]location.events.week">Week</span> #<?= date('W');?></small>
											</div>	
											<div class="row">
													<div class="col-md-6">
														<div id="first_pane"></div>
													</div>
													<div class="col-md-6">
														<div id="voeding_pane"></div>
													</div>
											</div>
											<div class="m-t-md">
												<small class="pull-right">
													<i class="fa fa-clock-o"> </i>
													<span data-i18n="[html]location.tab.update">Updated on</span> <?= date('y.m.d H:i:s');?>
												</small>
												<small>
													<b></b> 
												</small>
											</div>											
										</div>
									</div>
									<div id="tab-2" class="tab-pane" >
										<div class="panel-body" >
										
											<div>
												<span class="pull-right text-right">
													<span data-i18n="[html]location.tab.location">Location</span>: <b><?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?></b>
													<br/>
												</span>
												<h3 class="font-bold no-margins" data-i18n="[html]location.tab.tab2.h3">
													Poll delay count
												</h3>
												<small ><span data-i18n="[html]location.events.week">Week</span> #<?= date('W');?></small>
												<select id="pollchart_style" class="form-control" style="width:150px;">
													<option value="">Select</option>
													<option value="line">Line</option>
													<option value="spline">Spline</option>
													<option value="area-step">Step</option>
													<option value="bar">Bar</option>
													<option value="pie">Pie</option>
													<option value="donut">Donut</option>
													<option value="area">Area</option>
													<option value="area-spline">Area Spline<option>
												</select>												
											</div>
											<div class="m-t-sm">

												<div class="row">
													<div class="col-md-12">
														<div class="sk-spinner sk-spinner-wave" id="spinner">
															<div class="sk-rect1"></div>
															<div class="sk-rect2"></div>
															<div class="sk-rect3"></div>
															<div class="sk-rect4"></div>
															<div class="sk-rect5"></div>
														</div>
														<div id="poll_chart" ></div>
													</div>
												</div>
								
											</div>
											<div class="m-t-md">
												<small class="pull-right">
													<i class="fa fa-clock-o"> </i>
													<span data-i18n="[html]location.tab.update">Updated on</span> <?= date('y.m.d H:i:s');?>
												</small>
												<small>
													<b></b> 
												</small>
											</div>										
										</div>
									</div>
									<div id="tab-3" class="tab-pane" >
										<div class="panel-body" >
								
											<div>
												<div>
													<span class="pull-right text-right">
														<span data-i18n="[html]location.tab.location">Location</span>: <b><?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?></b>
														<br/>
													</span>
													<h3 class="font-bold no-margins" data-i18n="[html]location.tab.tab3.h3">
														Component status
													</h3>
													<small><span data-i18n="[html]location.events.week">Week</span> #<?= date('W');?></small>
													<select id="chart_style" class="form-control" style="width:150px;">
														<option value="">Select</option>
														<option value="line">Line</option>
														<option value="spline">Spline</option>
														<option value="area-step">Step</option>
														<option value="bar">Bar</option>
														<option value="pie">Pie</option>
														<option value="donut">Donut</option>
														<option value="area">Area</option>
														<option value="area-spline">Area Spline<option>
													</select>

												</div>	
												
											</div>
											<div class="m-t-sm">

												<div class="row">
													<div class="col-md-12">
														<div class="sk-spinner sk-spinner-wave" id="spinner2">
															<div class="sk-rect1"></div>
															<div class="sk-rect2"></div>
															<div class="sk-rect3"></div>
															<div class="sk-rect4"></div>
															<div class="sk-rect5"></div>
														</div>													
														<div id="comp_chart" ></div>
													</div>
												</div>
								
											</div>													
											<div class="m-t-md">
												<small class="pull-right">
													<i class="fa fa-clock-o"> </i>
													<span data-i18n="[html]location.tab.update">Updated on</span> <?= date('y.m.d H:i:s');?>
												</small>
												<small>
													<b></b> 
												</small>

											</div>
										</div>
																			
									</div>
								</div>
			
							</div>
			
						</div>
									
						<div class="ibox float-e-margins" id="pie_chart">
							<div class="ibox-content">
								<div>
									<span class="pull-right text-right">
										<span data-i18n="[html]location.tab.location">Location</span>: <b><?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?></b>
										<br/>
										<span data-i18n="[html]location.events.count">Total event(s)</span>: <b><span id="events_count_week"></span></b>
									</span>
									<h3 class="font-bold no-margins" data-i18n="[html]location.events.label">
										Events this week
									</h3>
									<small><span data-i18n="[html]location.events.week">Week</span> #<?= date('W');?></small>
								</div>
								<div class="m-t-sm">
					
									<div class="row">
										<div class="col-md-12">
											<div>
												<div id="echart_pie" style="height:350px; z-index:9999;"></div>
												
											</div>
										</div>
									</div>
					
								</div>
					
								<div class="m-t-md">
									<small class="pull-right">
										<i class="fa fa-clock-o"> </i>
										<span data-i18n="[html]location.tab.update">Updated on</span> <?= date('y.m.d H:i:s');?>
									</small>
									<small>
										<b></b> 
									</small>
								</div>
					
							</div>
						</div>			
							
                    </div>
                </div>
            </div>
        </div>	
		
    </div>
	
	<input type="text" hidden id="url_query" value="<?= $_SERVER['QUERY_STRING']; ?>" />	
	<input type="text" hidden id="mac_adres"  />	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'Src/controllers/location.controller.php';?>" />	
	
	<?php
		// View specific scripts
		array_push($arr_js, '/mdb/js/plugins/sparkline/jquery.sparkline.min.js');
		array_push($arr_js, '/mdb/js/plugins/dataTables/datatables.min.js');
		array_push($arr_js, '/mdb/js/plugins/d3/d3.min.js');
		array_push($arr_js, '/mdb/js/plugins/c3/c3.min.js');
		
	?>	
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.$js.'"></script>';
		}		
	?>		
	<script>
    var theme = {
          color: [
              '#bfd3b7', '#34495E', '#BDC3C7', '#26B99A',
              '#9B59B6', '#8abb6f', '#759c6a', '#3498DB'
          ],

          title: {
              itemGap: 8,
              textStyle: {
                  fontWeight: 'normal',
                  color: '#408829'
              }
          },

          dataRange: {
              color: ['#1f610a', '#97b58d']
          },

          toolbox: {
              color: ['#408829', '#408829', '#408829', '#408829']
          },

          tooltip: {
              backgroundColor: 'rgba(0,0,0,0.5)',
              axisPointer: {
                  type: 'line',
                  lineStyle: {
                      color: '#408829',
                      type: 'dashed'
                  },
                  crossStyle: {
                      color: '#408829'
                  },
                  shadowStyle: {
                      color: 'rgba(200,200,200,0.3)'
                  }
              }
          },

          dataZoom: {
              dataBackgroundColor: '#eee',
              fillerColor: 'rgba(64,136,41,0.2)',
              handleColor: '#408829'
          },
          grid: {
              borderWidth: 0
          },

          categoryAxis: {
              axisLine: {
                  lineStyle: {
                      color: '#408829'
                  }
              },
              splitLine: {
                  lineStyle: {
                      color: ['#eee']
                  }
              }
          },

          valueAxis: {
              axisLine: {
                  lineStyle: {
                      color: '#408829'
                  }
              },
              splitArea: {
                  show: true,
                  areaStyle: {
                      color: ['rgba(250,250,250,0.1)', 'rgba(200,200,200,0.1)']
                  }
              },
              splitLine: {
                  lineStyle: {
                      color: ['#eee']
                  }
              }
          },
          timeline: {
              lineStyle: {
                  color: '#408829'
              },
              controlStyle: {
                  normal: {color: '#408829'},
                  emphasis: {color: '#408829'}
              }
          },

          k: {
              itemStyle: {
                  normal: {
                      color: '#68a54a',
                      color0: '#a9cba2',
                      lineStyle: {
                          width: 1,
                          color: '#408829',
                          color0: '#86b379'
                      }
                  }
              }
          },
          map: {
              itemStyle: {
                  normal: {
                      areaStyle: {
                          color: '#ddd'
                      },
                      label: {
                          textStyle: {
                              color: '#c12e34'
                          }
                      }
                  },
                  emphasis: {
                      areaStyle: {
                          color: '#99d2dd'
                      },
                      label: {
                          textStyle: {
                              color: '#c12e34'
                          }
                      }
                  }
              }
          },
          force: {
              itemStyle: {
                  normal: {
                      linkStyle: {
                          strokeColor: '#408829'
                      }
                  }
              }
          },
          chord: {
              padding: 4,
              itemStyle: {
                  normal: {
                      lineStyle: {
                          width: 1,
                          color: 'rgba(128, 128, 128, 0.5)'
                      },
                      chordStyle: {
                          lineStyle: {
                              width: 1,
                              color: 'rgba(128, 128, 128, 0.5)'
                          }
                      }
                  },
                  emphasis: {
                      lineStyle: {
                          width: 1,
                          color: 'rgba(128, 128, 128, 0.5)'
                      },
                      chordStyle: {
                          lineStyle: {
                              width: 1,
                              color: 'rgba(128, 128, 128, 0.5)'
                          }
                      }
                  }
              }
          },
          gauge: {
              startAngle: 225,
              endAngle: -45,
              axisLine: {
                  show: true,
                  lineStyle: {
                      color: [[0.2, '#86b379'], [0.8, '#68a54a'], [1, '#408829']],
                      width: 8
                  }
              },
              axisTick: {
                  splitNumber: 10,
                  length: 12,
                  lineStyle: {
                      color: 'auto'
                  }
              },
              axisLabel: {
                  textStyle: {
                      color: 'auto'
                  }
              },
              splitLine: {
                  length: 18,
                  lineStyle: {
                      color: 'auto'
                  }
              },
              pointer: {
                  length: '90%',
                  color: 'auto'
              },
              title: {
                  textStyle: {
                      color: '#333'
                  }
              },
              detail: {
                  textStyle: {
                      color: 'auto'
                  }
              }
          },
          textStyle: {
              fontFamily: 'Arial, Verdana, sans-serif'
          }
      };
		
	var location_id = $('#url_query').val();
	var url_str = $('#url_string').val();

	function PieChart(data){
				
		var echartPie = echarts.init(document.getElementById('echart_pie'), theme);	
		
		echartPie.setOption({
			tooltip: {
				trigger: 'item',
				formatter: "{a} <br/>{b} : {c} ({d}%)"
			},
			legend: {
				x: 'center',
				y: 'bottom',
				data: data
			},
			toolbox: {
			show: true,
				feature: {
					magicType: {
					show: true,
					type: ['pie', 'funnel'],
						option: {
							funnel: {
							x: '25%',
							width: '50%',
							funnelAlign: 'left',
							max: 1548
							}
						}
					},
					restore: {
						show: true,
						title: "Restore"
					},
					saveAsImage: {
						show: true,
						title: "Save Image"
					}
				}
			},
			calculable: true,
			series: [{
				name: 'Event',
				type: 'pie',
				radius: '55%',
				center: ['50%', '48%'],
				data: data
			}]
		});					
	}

	var compchart = c3.generate({
		bindto: '#comp_chart',
		data: {
			x: 'x',
			xFormat: '%Y-%m-%d %H:%M',
			columns: []
		},
		type: 'spline',
		axis: {
			x: {
				type: 'category',
				tick: {
					rotate: 75,
					multiline: false,					
					//count: 10,
					fit: true,
					format: '%e %b %y %H:%M',
					centered: true
				}
			}
		},
		color: {
			pattern: ["#1ab394",  "#d3d3d3", "#1C84C6", "#bababa", "#79d2c0","#1ab394"]
		},		
		zoom: {
			enabled: true
		}			
	});

			
	var pollchart = c3.generate({
		bindto: '#poll_chart',
		data: {
			x: 'x',
			xFormat: '%Y-%m-%d %H:%M',
			columns: []
		},
		type: 'spline',
		axis: {
			x: {
				type: 'category',
				tick: {
					rotate: 75,
					multiline: false,					
					//count: 10,
					fit: true,
					format: '%e %b %y %H:%M',
					centered: true
				}
			}
		},
		color: {
			//pattern: [ '#bfd3b7', '#34495E', '#BDC3C7', '#26B99A', '#9B59B6', '#8abb6f', '#759c6a', '#3498DB' ]
			pattern: ["#1ab394",  "#d3d3d3",  "#1C84C6", "#bababa", "#79d2c0","#1ab394"]
		},		
		zoom: {
			enabled: true
		}	
	});

	</script>	

	<script>
	$(document).ready(function () {
		var refresh = 5000;	
		
		var location_id = $('#url_query').val();
		var location_mac = $('#mac_adres').val();
		var url_str = $('#url_string').val();
		var interval;
		
		getLocation(url_str, location_id);
		getLocationRMSPath(url_str, location_id);
		getLocationRMSVoeding(url_str, location_id);
		getLocationEventsPie();	
		getLocationRMSPoll(url_str, location_id);
		setInterval(function(){
			getLocation(url_str, location_id);
		}, refresh);
		
		interval = setInterval( function () {
			getLocationRMSPath(url_str, location_id);
			getLocationRMSVoeding(url_str, location_id);
		}, refresh );			
		
		$('a[href="#tab-1"]').on('shown.bs.tab', function (e) {
			clearInterval(interval);
			interval = setInterval( function () {
				getLocationRMSPath(url_str, location_id);
				getLocationRMSVoeding(url_str, location_id);
			}, refresh );						
		});
		
		$('a[href="#tab-2"]').on('shown.bs.tab', function (e) {
			clearInterval(interval);
			interval = setInterval( function () {
				getLocationRMSPoll(url_str, location_id);
			}, refresh );				
		});

		$('a[href="#tab-3"]').on('shown.bs.tab', function (e) {
			clearInterval(interval);
			
			//getLocationValue(url_str, location_id, 'S_DEVICE_no_1_TEMP');
			interval = setInterval( function () {
				getLocationRMSComponents(url_str, location_id)
			}, refresh );				
		});
		
		// Change comp chart style
		$("#chart_style").change(function(){
			var type_style = $(this).val();
			compchart.transform(type_style)		
		});
		// Change poll chart style
		$("#pollchart_style").change(function(){
			var type_style = $(this).val();
			pollchart.transform(type_style)		
		});		
		
		$.extend( true, $.fn.dataTable.defaults, {
			language: {
				url: '/mdb/js/plugins/dataTables/'+$('html').attr('lang')+'.json'
			},
			iDisplayLength: 10,
			deferRender: true,
			order: [[ 1, "desc"]],
			lengthMenu: [ 10, 20, 25 ],
			processing: true,
			serverSide: true,
		} );	
		
		$(".datatable_values").DataTable({
			ajax: {
				url: url_str+"?get=values&id="+location_id,
				complete: function(data){
					if(data.responseJSON.recordsTotal == 0){
						$('#values').addClass('hidden');
					}
					//console.log(data.responseJSON.recordsTotal);
				}
			},			
			fnInitComplete: function(oSettings, json) {
				var lang_code = $('html').attr('lang');
			
				$.i18n.init({
					resGetPath: '/mdb/src/lang/__lng__.json',
					load: 'unspecific',
					fallbackLng: false,
					lng: lang_code
				}, function (t){
					$('#i18container').i18n();
				});
			}			
		});		
		
		$(".datatable").DataTable({
			ajax: {
				url: url_str+"?get=comp&id="+location_id,
				complete: function(data){
					if(data.responseJSON.recordsTotal == 0){
						$('#comp').addClass('hidden');
					}
					//console.log(data.responseJSON.recordsTotal);
				}				
			},
			fnInitComplete: function(oSettings, json) {
				$("span.pie").peity("pie", {
					fill: ['#1ab394', '#d7d7d7', '#ffffff']
				});
			}			
		});

	});	
	// SCS
	function getLocation(url, id){
		$.ajax({
			type: 'POST',
			url: url+"?get=location",
			data: {ID: id},
			success: function(data) {
				$('#location_name').html(data.location_name);
				$('#location_address').html(data.location_address);
				$('#location_zip').html(data.location_zip);
				$('#location_city').html(data.location_city);
				$('#location_first').html(data.location_first);
				$('#location_last').html(data.location_last);
				$('#location_mac').html(data.location_mac);
				$('#mac_adres').attr('value',data.location_mac);
				$('#location_udid').html(data.location_udid);
				$('#location_serie').html(data.location_serie);
				$('#location_sim').html(data.location_sim);
				$('#location_lijn').html(data.location_lijn);
				$('#location_serviceid').html(data.location_serviceid);
				$('#conn_status').html(data.conn_status);
				var path ='';
				$.each(data.path_status, function (index, val) {
					if(val == '0'){
						path += '<i class="fa fa-circle text-danger"></i> ';
					} else if(val == '1'){
						path += '<i class="fa fa-circle text-navy"></i> ';
					} else {
						path += '';
					}
				});
				$('#path_status').html(path);
				if(data.oos_id){
					$('#location_status').html('<dt >Status:</dt> <dd>'+data.oos_icon+'</dd>');
				}
				//alert(data.connection);
	
			}
		});			
	}	

	function getLocationRMSComponents(url, id){
		$.ajax({
			type: 'POST',
			url: url+"?get=rms&comp",
			data: {ID: id},
			success: function(data) {
				if(data.status != 0){
					compchart.load({
						columns: [
							data.date,
							data.ups,
							data.fluid
						]				
					});
				} else {
					compchart.destroy();	
				}
				$('#spinner2').css('display','none');
						
			}
		});		
	}
	
    function getLocationEventsPie() {
        $.ajax({
			type: 'POST',
            dataType: 'json',
            url: url_str+"?get=events&pie&id="+location_id,
			data: {ID: location_id},
            success: function (data) {
				if(data.count == 0){
					$('#pie_chart').addClass('hidden');
				}
                PieChart(data.events);
				$('#events_count_week').html(data.count);
				
            }
        });

    }	
	
	// RMS
	function getLocationRMSPoll(url, id){

		$.ajax({
			type: 'POST',
			url: url+"?get=rms&poll",
			data: {ID: id},
			success: function(data) {
				if(data.poll != 0){
					pollchart.load({
						columns: data.poll
					});
				} else {
					pollchart.destroy();
				}
				$('#spinner').css('display','none');
			}
		});	
	}
	
	function getLocationRMSPath(url, id){
		$.ajax({
			type: 'POST',
			url: url+"?get=rms",
			data: {ID: id},
			success: function(data) {
				$('#first_pane').html(data.pane);
				//$('#second_pane').html(data.pane_second);

				$("#ip_line_total").sparkline(data.ip_total.total, {
					type: 'tristate',
					barWidth: '10%',
					barSpacing: '2',
					height: '50',
					colorMap: {'2': '#ed5565', '1': '#1ab394'},
					tooltipFormat: '{{offset:date}}<br> Status: <b>{{value:levels}}</b>',
					tooltipValueLookups: {
						levels: $.range_map({ '2': 'Down', '1': 'Up'}),
						date: $.range_map(data.ip_total.total_d)
					}						
				})
				
				var sparklineCharts = function(arr,id_class){
					//console.log("#ip_line"+id_class);
					$("#ip_line"+id_class).sparkline(arr.val, {
						type: 'tristate',
						barWidth: '10%',
						barSpacing: '2',
						height: '50',
						colorMap: {'2': '#ed5565', '1': '#1ab394'},
						tooltipFormat: '{{offset:date}}<br> Status: <b>{{value:levels}}</b>',
						tooltipValueLookups: {
							levels: $.range_map({ '2': 'Down', '1': 'Up'}),
							date: $.range_map(arr.date)
						}						
					});
				
				};				
					
				var arr_len = data.ip.length;
				//console.log(arr_len);

				for(var i=0; i < 5; i++){
					//console.log(i);
					$.each(data.ip[i], function(key, val) {
						//console.log(val);
						sparklineCharts(val,i);
					});	
				}
				
			}
		});		
	}

	function getLocationRMSVoeding(url, id){
		$.ajax({
			type: 'POST',
			url: url+"?get=rms&voeding",
			data: {ID: id},
			success: function(data) {
				$('#voeding_pane').html(data.pane);

				$("#status_voeding").sparkline(data.st_voeding, {
					type: 'tristate',
					barWidth: '10%',
					barSpacing: '2',
					height: '50',
					colorMap: {'2': '#ed5565', '1': '#1ab394'},
					tooltipFormat: '{{offset:date}}<br> Status: <b>{{value:levels}}</b>',
					tooltipValueLookups: {
						levels: $.range_map({ '2': 'Down', '1': 'Up', '0':'Not connected'}),
						date: $.range_map(data.st_date)
					}						
				});
				
				$("#status_battery").sparkline(data.st_battery, {
					type: 'tristate',
					barWidth: '10%',
					barSpacing: '2',
					height: '50',
					colorMap: {'2': '#ed5565', '1': '#1ab394'},
					tooltipFormat: '{{offset:date}}<br> Status: <b>{{value:levels}}</b>',
					tooltipValueLookups: {
						levels: $.range_map({ '2': 'Down', '1': 'Up', '0':'Not connected'}),
						date: $.range_map(data.st_date)
					}						
				});
			
	
			}
		});
	}
	</script>