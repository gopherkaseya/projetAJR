<!DOCTYPE html>
<!-- saved from url=(0089)file:///C:/wamp64/www/cool/chador/AdminLTE-2.4.3/AdminLTE-2.4.3/pages/charts/chartjs.html -->
<html style="height: auto; min-height: 100%;">
<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>AdminLTE 2 | ChartJS</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="./snp_rapport_files/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="./snp_rapport_files/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="./snp_rapport_files/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./snp_rapport_files/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="./snp_rapport_files/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="skin-blue sidebar-mini" style="height: auto; min-height: 100%;">
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-4" style="display:<?php echo (isset($_GET['danut']) and $_GET['danut']) ?'':'none'; ?>"  >

          <!-- DONUT CHART -->
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Representation circulaire sur le long de l'annee</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <canvas id="pieChart" style="height: 323px; width: 647px;" width="647" height="323"></canvas>
			  
	  <?php if(isset($_GET['CENTRES'])){ ?>
			  <p>
                <button type="button" class="btn bg-maroon btn-flat margin" style="background-color:#f00 !important">CENTRES</button>
                <button type="button" class="btn bg-navy btn-flat margin" style="background-color:#00f !important">BUREAUX</button>
                <button type="button" class="btn bg-purple btn-flat margin" style="background-color:#0f0 !important">ANTENNES</button>
              </p>
	  <?php }else{ ?>
			  <p>
                <button type="button" class="btn bg-maroon btn-flat margin" style="background-color:#f00 !important">ORDOS</button>
                <button type="button" class="btn bg-purple btn-flat margin" style="background-color:#0f0 !important">RECS</button>
                <button type="button" class="btn bg-navy btn-flat margin" style="background-color:#00f !important">NIVS</button>
                <button type="button" class="btn bg-orange btn-flat margin" style="background-color:#000 !important">RESTES</button>
              </p>
              <?php } ?>
            </div>
          </div>
        </div>
        <!-- /.col (LEFT) -->
        <div class="col-md-8">
          <!-- LINE CHART -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Representation lineraire sur le long de l'annee</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="lineChart" style="height: 250px; width: 627px;" width="627" height="250"></canvas>
              </div>
            </div>
            <!-- /.box-body -->
          </div>
		  
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Representation en batonnet sur le long de l'annee</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="chart">
                <canvas id="barChart" style="height: 230px; width: 627px;" width="627" height="230"></canvas>
              </div>
            </div>
          </div>
          <!-- /.box -->

        </div>
        <!-- /.col (RIGHT) -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="./snp_rapport_files/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="./snp_rapport_files/bootstrap.min.js"></script>
<!-- ChartJS -->
<script src="./snp_rapport_files/Chart.js"></script>
<!-- FastClick -->
<script src="./snp_rapport_files/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="./snp_rapport_files/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="./snp_rapport_files/demo.js"></script>
<!-- page script -->
<script>
  $(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- AREA CHART -
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    // var areaChartCanvas = $('#areaChart').get(0).getContext('2d')
    // This will get the first returned node in the jQuery collection.
    // var areaChart       = new Chart(areaChartCanvas)

	
	var color_1 = '#f00';//rgba(210, 214, 222, 1)
	var color_2 = '#0f0';//rgba(60,141,188,0.8)
	var color_3 = '#00f';//rgba(255,100,088,0.9)
	var color_4 = '#000';//rgba(255,100,088,0.9)
	var color_5 = '#ffffffff';//rgba(255,100,088,0.9)
    var areaChartData = {
      labels  : ['JANVIER','FEVRIER','MARS','AVRIL','MAI','JUIN','JUILLET','AOUT','SEPTEMBRE','OCTOBRE','NOVEMBRE','DECEMBRE'],
      datasets: [
	  <?php if(isset($_GET['CENTRES'])){ ?>
       
       /*  {
          label               : 'LES BUREAUX2',
          fillColor           : color_5,
          strokeColor         : color_5,
          data                : [0,0,0,0,0,0,0,0,0,0,0,0]
        },{
          label               : 'LES BUREAUX3',
          fillColor           : color_5,
          strokeColor         : color_5,
          data                : [0,0,0,0,0,0,0,0,0,0,0,0]
        }, */
		{
          label               : 'LES CENTRES',
          fillColor           : color_1,
          strokeColor         : color_1,
          pointColor          : color_1,
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [ <?php echo isset($_GET['CENTRES'])?$_GET['CENTRES']:'' ; ?> ]
        },
        {
          label               : 'LES BUREAUX',
          fillColor           : color_3,
          strokeColor         : color_3,
          data                : [<?php echo isset($_GET['BUREAUX'])?$_GET['BUREAUX']:'' ; ?>]
        },
        {
          label               : 'LES ANTENNES',
          fillColor           : color_2,
          strokeColor         : color_2,
          pointColor          : color_2,
          pointStrokeColor    : color_2,
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(255,100,088,1)',
          data                : [<?php echo isset($_GET['ANTENNES'])?$_GET['ANTENNES']:'' ; ?>]
        }
	  <?php } ?>
      <?php if(isset($_GET['ORDO'])){ ?>
        {
          label               : 'LES ORDOS',
          fillColor           : color_1,
          strokeColor         : color_1,
          pointColor          : color_1,
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [ <?php echo isset($_GET['ORDO'])?$_GET['ORDO']:'' ; ?> ]
        },
        {
          label               : 'LES RECS',
          fillColor           : color_2,
          strokeColor         : color_2,
          pointColor          : color_2,
          pointStrokeColor    : color_2,
          pointHighlightFill  : '#fff',
          pointHighlightStroke: color_2,
		  
          // fillColor           : 'rgba(0, 0, 0, 1)',
          // strokeColor         : 'rgba(0, 0, 0, 1)',
          // pointColor          : 'rgba(0, 0, 0, 1)',
          // pointStrokeColor    : '#c1c7d1',
          // pointHighlightFill  : '#fff',
          // pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [<?php echo isset($_GET['REC'])?$_GET['REC']:'' ; ?>]
        },
        {
          label               : 'LES NIVS',
          fillColor           : color_3,
          strokeColor         : color_3,
          pointColor          : color_3,
          pointStrokeColor    : color_3,
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(255,100,088,1)',
          data                : [<?php echo isset($_GET['NIV'])?$_GET['NIV']:'' ; ?>]
        },
        {
          label               : 'LES RESTES',
          fillColor           : color_4,
          strokeColor         : color_4,
          pointColor          : color_4,
          pointStrokeColor    : color_4,
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(255,100,088,1)',
          data                : [<?php echo isset($_GET['RES'])?$_GET['RES']:'' ; ?>]
        }
	  <?php } ?>
      ]
    }

    var areaChartOptions = {
      //Boolean - If we should show the scale at all
      showScale               : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : false,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - Whether the line is curved between points
      bezierCurve             : true,
      //Number - Tension of the bezier curve between points
      bezierCurveTension      : 0.3,
      //Boolean - Whether to show a dot for each point
      pointDot                : false,
      //Number - Radius of each point dot in pixels
      pointDotRadius          : 4,
      //Number - Pixel width of point dot stroke
      pointDotStrokeWidth     : 1,
      //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
      pointHitDetectionRadius : 20,
      //Boolean - Whether to show a stroke for datasets
      datasetStroke           : true,
      //Number - Pixel width of dataset stroke
      datasetStrokeWidth      : 2,
      //Boolean - Whether to fill the dataset with a color
      datasetFill             : true,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio     : true,
      //Boolean - whether to make the chart responsive to window resizing
      responsive              : true
    }

    //Create the line chart
    // areaChart.Line(areaChartData, areaChartOptions)

    //-------------
    //- LINE CHART -
    //--------------
    var lineChartCanvas          = $('#lineChart').get(0).getContext('2d')
    var lineChart                = new Chart(lineChartCanvas)
    var lineChartOptions         = areaChartOptions
    lineChartOptions.datasetFill = false
    lineChart.Line(areaChartData, lineChartOptions)

    //-------------
    //- PIE CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieChart       = new Chart(pieChartCanvas)
    var PieData        = [
      
	  <?php
	  if(isset($_GET['danut'])){
		  $s = $_GET['danut'];
		  $s = str_replace('_c_',",color:'#",$s);
		  $s = str_replace('_h_',"',highlight:'#",$s);
		  $s = str_replace('_l_',"',label:'",$s);
		  echo $s.'';
	  }
	  ?>
    ]
    var pieOptions     = {
      //Boolean - Whether we should show a stroke on each segment
      segmentShowStroke    : true,
      //String - The colour of each segment stroke
      segmentStrokeColor   : '#fff',
      //Number - The width of each segment stroke
      segmentStrokeWidth   : 2,
      //Number - The percentage of the chart that we cut out of the middle
      percentageInnerCutout: 50, // This is 0 for Pie charts
      //Number - Amount of animation steps
      animationSteps       : 100,
      //String - Animation easing effect
      animationEasing      : 'easeOutBounce',
      //Boolean - Whether we animate the rotation of the Doughnut
      animateRotate        : true,
      //Boolean - Whether we animate scaling the Doughnut from the centre
      animateScale         : false,
      //Boolean - whether to make the chart responsive to window resizing
      responsive           : true,
      // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
      maintainAspectRatio  : true,
      //String - A legend template
      legendTemplate       : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    pieChart.Doughnut(PieData, pieOptions)
	
    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas                   = $('#barChart').get(0).getContext('2d')
    var barChart                         = new Chart(barChartCanvas)
    var barChartData                     = areaChartData
    barChartData.datasets[1].fillColor   = '#00a65a'
    barChartData.datasets[1].strokeColor = '#00a65a'
    barChartData.datasets[1].pointColor  = '#00a65a'
    var barChartOptions                  = {
      //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
      scaleBeginAtZero        : true,
      //Boolean - Whether grid lines are shown across the chart
      scaleShowGridLines      : true,
      //String - Colour of the grid lines
      scaleGridLineColor      : 'rgba(0,0,0,.05)',
      //Number - Width of the grid lines
      scaleGridLineWidth      : 1,
      //Boolean - Whether to show horizontal lines (except X axis)
      scaleShowHorizontalLines: true,
      //Boolean - Whether to show vertical lines (except Y axis)
      scaleShowVerticalLines  : true,
      //Boolean - If there is a stroke on each bar
      barShowStroke           : true,
      //Number - Pixel width of the bar stroke
      barStrokeWidth          : 2,
      //Number - Spacing between each of the X value sets
      barValueSpacing         : 5,
      //Number - Spacing between data sets within X values
      barDatasetSpacing       : 1,
      //String - A legend template
      legendTemplate          : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
      //Boolean - whether to make the chart responsive
      responsive              : true,
      maintainAspectRatio     : true
    }

    barChartOptions.datasetFill = false
    barChart.Bar(barChartData, barChartOptions)

  })


</script>


</body></html>