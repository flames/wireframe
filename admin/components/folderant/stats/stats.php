<script language="javascript" type="text/javascript" src="<?php echo $URL_ROOT;?>admin/js/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="<?php echo $URL_ROOT;?>admin/js/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo $URL_ROOT;?>admin/js/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo $URL_ROOT;?>admin/js/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo $URL_ROOT;?>admin/js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo $URL_ROOT;?>admin/js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo $URL_ROOT;?>admin/js/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
<script type="text/javascript" src="<?php echo $URL_ROOT;?>admin/js/jqplot/plugins/jqplot.cursor.min.js"></script>
<?php
$downloads = $DB->select("SELECT COUNT(*) as `count`, DATE_FORMAT(`date`, '%c/%d/%Y') as `date` FROM wf_folderant_downloads WHERE `date` > DATE_SUB(CURDATE(), INTERVAL 365 DAY) GROUP BY `date` ORDER BY `date`;");
$chart2 = "[";
$first = TRUE;
foreach ($downloads as $download){
	if(!$first) $chart2.= ',';
	$chart2 .= '["'.$download["date"].'",'.$download["count"].']';
	$first = FALSE;
}
$chart2 .= "]";
$popular = $DB->select("SELECT name, downloads FROM wf_folderant_files ORDER BY downloads DESC LIMIT 20;");
$chart1 = "[";
$first = TRUE;
foreach ($popular as $pop){
	if(!$first) $chart1.= ',';
	$chart1 .= "['".$pop["name"]."', ".$pop["downloads"]."]";
	$first = FALSE;
}
$chart1 .= "]";
?>
<div id="downloads" style="height:400px;"></div><br/><br/>
<div id="popular" style="height:700px;"></div>
<script type="text/javascript">
$(document).ready(function(){
  var line1 = <?php echo $chart1; ?>;
 
  var plot1 = $.jqplot('popular', [line1], {
    title: 'Beliebteste Downloads',
    series:[{renderer:$.jqplot.BarRenderer}],
    axesDefaults: {
        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
        tickOptions: {
          angle: -75,
          fontSize: '10pt'
        }
    },
    axes: {
      xaxis: {
        renderer: $.jqplot.CategoryAxisRenderer
      }, 
      yaxis: { 
              tickOptions: {
                angle: 0,
                fontSize: '10pt'
              },
              tickInterval: 1, 
      } 
    }
  });
  var line2 =  <?php echo $chart2; ?>;
var plot2 = $.jqplot('downloads', [line2], { 
       title: 'Downloads', 
      series: [{ 
          label: 'Downloads', 
          neighborThreshold: -1,
          showMarker:false
      }], 
      axes: { 
          xaxis: { 
              renderer: $.jqplot.DateAxisRenderer,
              min:'<?php echo date ( "n/w/Y" ,time() -  31536000);?>',
              tickInterval: '1 months', 
              tickOptions:{formatString:'%d.%#m.%Y'} 
          }, 
          yaxis: { 
              min:0
          } 
      }, 
      cursor:{ 
        show: true,
        zoom:true, 
        showTooltip:false
      } 
  });

  $('.button-reset').click(function() { plot1.resetZoom() });
});
</script>