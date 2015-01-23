var chart;
var chartData = [];

AmCharts.ready(function () {
   // generate some random data first
    get_chart_data();
});
function get_chart_data(dtype, dparam){
  if(!dtype) dtype = 'hour';
  $.ajax({
      url:'_includes/amchart_data.php',
      type:'post',
      dataType :'text',
      data:{dtype:dtype, dparam:dparam},
      success:function(ret){
          //var res = $.parseJSON(ret);
          ret = ret.trim();
          if(ret.substr(0,1) == '['){
              var res = $.parseJSON(ret);
              createChart(res, dtype);
              draw_table(res);
          }else{
              alert("Can't load Data correctly");
          }
      },
      error:function(hdl, sta,er){
          console.log(sta);
          console.log(er);
          alert('errr');
      }
  });
}
// generate some random data, quite different range
function createChart(chartData, dtype){
// SERIAL CHART
   chart = new AmCharts.AmSerialChart();
   chart.theme = AmCharts.themes.dark;
   chart.pathToImages = "amcharts/images/";
   chart.dataProvider = chartData;
   chart.categoryField = dtype;

   // listen for "dataUpdated" event (fired when chart is inited) and call zoomChart method when it happens
   //chart.addListener("dataUpdated", zoomChart);

   // AXES
   // category
   var categoryAxis = chart.categoryAxis;
   categoryAxis.parseDates = false; // as our data is date-based, we set parseDates to true
   categoryAxis.minPeriod = "hh"; 
   categoryAxis.minorGridEnabled = true;
   categoryAxis.axisColor = "#DADADA";
   categoryAxis.color = "#DADADA";

   // first value axis (on the left)
   var valueAxis1 = new AmCharts.ValueAxis();
   valueAxis1.axisColor = "#ac8eb0";
   valueAxis1.color = "#ac8eb0";
   valueAxis1.axisThickness = 2;
   valueAxis1.gridAlpha = 0;
   chart.addValueAxis(valueAxis1);

   // second value axis (on the right)
   var valueAxis2 = new AmCharts.ValueAxis();
   valueAxis2.position = "right"; // this line makes the axis to appear on the right
   valueAxis2.axisColor = "#b0ae8e";
   valueAxis2.color = "#b0ae8e";
   valueAxis2.gridAlpha = 0;
   valueAxis2.axisThickness = 2;
   chart.addValueAxis(valueAxis2);

   // third value axis (on the left, detached)
   valueAxis3 = new AmCharts.ValueAxis();
   valueAxis3.offset = 50; // this line makes the axis to appear detached from plot area
   valueAxis3.gridAlpha = 0;
   valueAxis3.axisColor = "#8ea7b0";
   valueAxis3.color = "#8ea7b0";
   valueAxis3.axisThickness = 2;
   chart.addValueAxis(valueAxis3);

   // GRAPHS
   // first graph
   var graph1 = new AmCharts.AmGraph();
   graph1.valueAxis = valueAxis1; // we have to indicate which value axis should be used
   //graph1.type = "smoothedLine";
   graph1.title = "Power";
   graph1.valueField = "power";
   graph1.bullet = "round";
   graph1.hideBulletsCount = 30;
   graph1.bulletBorderThickness = 2;
   graph1.bulletBorderColor = "#ffffff";
   graph1.bulletColor = "#ac8eb0";
   graph1.lineColor = "#ac8eb0";
   chart.addGraph(graph1);

   // second graph
   var graph2 = new AmCharts.AmGraph();
   graph2.valueAxis = valueAxis2; // we have to indicate which value axis should be used
   graph2.title = "Temperature";
   //graph2.type = "smoothedLine";
   graph2.valueField = "temper";
   graph2.bullet = "round";
   graph2.hideBulletsCount = 30;
   graph2.bulletBorderThickness = 2;
   graph2.bulletBorderColor = "#ffffff";
   graph2.bulletColor = "#b0ae8e";
   graph2.lineColor = "#b0ae8e";
   chart.addGraph(graph2);

   // third graph
   var graph3 = new AmCharts.AmGraph();
   graph3.valueAxis = valueAxis3; // we have to indicate which value axis should be used
   graph3.title = "Light";
   //graph3.type = "smoothedLine";
   graph3.valueField = "light";
   graph3.bullet = "round";
   graph3.hideBulletsCount = 30;
   graph3.bulletBorderThickness = 2;
   graph3.bulletBorderColor = "#ffffff";
   graph3.bulletColor = "#8ea7b0";
   graph3.lineColor = "#8ea7b0";
   chart.addGraph(graph3);

   // CURSOR
   var chartCursor = new AmCharts.ChartCursor();
   chartCursor.cursorAlpha = 0.1;
   chartCursor.fullWidth = true;
   chart.addChartCursor(chartCursor);

   // SCROLLBAR
   /*
   var chartScrollbar = new AmCharts.ChartScrollbar();
   chart.addChartScrollbar(chartScrollbar);
  */
   // LEGEND
   /* */
   var legend = new AmCharts.AmLegend();
   legend.marginLeft = 110;
   legend.useGraphSettings = true;
   legend.color = "#ffffff";
   chart.addLegend(legend);
   
   // WRITE
   chart.write("chartdiv");
}
// this method is called when chart is first inited as we listen for "dataUpdated" event
function zoomChart() {
   // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
   chart.zoomOut();
}

function draw_table(ret){
  $('#datatable tbody').html('');
  for(x=0; x< ret.length; x++){
      var s = '<tr><td>' + ret[x].time + '</td><td>' + ret[x].temper + '</td><td>' + ret[x].light + '</td><td>' + ret[x].power + '</td></tr>';
      $('#datatable tbody').append(s);
  }
}
