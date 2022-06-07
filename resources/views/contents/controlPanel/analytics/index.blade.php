
<link type="text/css" href="/admin/plugins/charts-chartistjs/chartist.min.css" rel="stylesheet">
<link type="text/css" href="/admin/css/analytics.css" rel="stylesheet">

<script type="text/javascript" src="/admin/plugins/charts-chartistjs/chartist.min.js"></script>
<script type="text/javascript" src="/admin/plugins/charts-chartistjs/chartist-plugin-tooltip.js"></script>
<script type="text/javascript" src="/admin/js/analytics.js"></script>

<!--- charts --->

<div data-widget-group="group1" id="analytics" data-active-page-id="{{ $activePageId }}" data-active-analytics-id="{{ $activeAnalyticsId }}" data-analytics-require-route="{{ route('useranalyticsrequire') }}">

	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default demo-dashboard-graph" data-widget="">
				<div class="panel-heading">
					<div class="panel-ctrls button-icon-bg"
						data-actions-container=""
						data-action-collapse='{"target": ".panel-body"}'
						data-action-refresh='{"type": "circular"}'>
					</div>
					<h2 style="font-size: 20px">
						<ul class="nav nav-tabs" id="chartist-tab">
							<li data-analytics-id="pmmzy4q2vq" class="active" data-chart="chart1">
								<a href="#today-statistics" data-toggle="tab" data-is-linear="1">
									<i class="fa fa-user visible-xs"></i>
									<span class="hidden-xs font-6">დღევანდელი ნახვები</span>
								</a>
							</li>
							<li data-analytics-id="iry1tax1z2" data-chart="chart2">
								<a href="#week-statistics" data-toggle="tab" data-is-linear="0">
									<i class="fa fa-bar-chart-o visible-xs"></i>
									<span class="hidden-xs font-6">კვირის ნახვები</span>
								</a>
							</li>
							<li data-analytics-id="lochs6uu6e" data-chart="chart3">
								<a href="#month-statistics" data-toggle="tab" data-is-linear="0">
									<i class="fa fa-bar-chart-o visible-xs"></i>
									<span class="hidden-xs font-6">თვის ნახვები</span>
								</a>
							</li>
							<li data-analytics-id="4yh6vmzl39" data-chart="chart4">
								<a href="#year-statistics" data-toggle="tab" data-is-linear="0">
									<i class="fa fa-bar-chart-o visible-xs"></i>
									<span class="hidden-xs font-6">წლის ნახვები</span>
								</a>
							</li>
						</ul>
					</h2>
				</div>

				<div class="panel-editbox" data-widget-controls=""></div>

				<div class="panel-body">
					<div class="tab-content">
						<div id="today-statistics" class="tab-pane active">
							<div class="demo-chartist" id="chart1"></div>
						</div>
						<div id="week-statistics" class="tab-pane">
							<div class="demo-chartist" id="chart2"></div>
						</div>
						<div id="month-statistics" class="tab-pane">
							<div class="demo-chartist" id="chart3"></div>
						</div>
						<div id="year-statistics" class="tab-pane">
							<div class="demo-chartist" id="chart4"></div>
						</div>
					</div>
				</div>

        <div class="analytics-control-container">

					<div class="analytics-info-container">
						<div class="font-6 analytics-info">
						 <i class="fas fa-info"></i>
						 <span> არჩეულ გვერდზე დაფიქსირდა</span>
						 <span id="total-number-of-visitors"> 0 </span>
						 <span class="font-6"> ნახვა </span> <br>

						 <i class="fas fa-info"></i>
						 <span> ყველა გვერდზე დაფიქსირდა</span>
						 <span id="absolute-number-of-visitors"> 0 </span>
						 <span class="font-6"> ნახვა </span>
						</div>
					</div>

           <form method="POST" action="{{ route('useranalyticsdestroy') }}" class="data-form-no-reload">
					   <a id="refresh-button" class="font-6 btn btn-default mr15">მონაცემების განახლება</a>
						 <input id="analytics-id" name="analytics-id" type="hidden" value="{{ $activeAnalyticsId }}">
						 <select id="page-id" name="page-id">
               @foreach($pages as $page)
							  <option class="font-6" value="{{ $page -> id }}">{{ $page -> title }}</option>
							 @endforeach
						 </select>
						 <button class="font-6 btn-primary btn ml30">მონაცემების წაშლა</button>
					  </form>

				</div>
			</div>
		</div>
	</div>
</div>

@include('parts.controlPanel.parameters')
