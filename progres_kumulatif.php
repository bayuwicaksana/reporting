<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Progres Kumulatif "); ?>
<?php include_once("_header.php");?>
<?php 
$post_tahunlelang = (isset($_POST['tahunlelang']))?$_POST['tahunlelang']:date("Y");
$post_Agency=(isset($_POST['Agency']))?$_POST['Agency']:'Agency';
$post_SatuanKerja=(isset($_POST['SatuanKerja']))?$_POST['SatuanKerja']:'Satuan Kerja';
$post_tipeprogres = (isset($_POST['TipeProgres']))?$_POST['TipeProgres']:"";

$sql = "SELECT months.months as BULAN, months.name as LABEL, IFNULL(datas.TOTAL,0) as TOTAL
FROM months 
LEFT JOIN
(
SELECT month(pg_tanggalawallelang) as bulan, COUNT(*) as TOTAL FROM datamaster
WHERE 1=1 ";
if ($post_tipeprogres=="BOGOR"){
	$sql .=" AND pg_audituser in ('ANIKAGENCY','SARIAGENCY')";
}elseif($post_tipeprogres=="NON BOGOR"){
	$sql .=" AND not pg_audituser  in ('ANIKAGENCY','SARIAGENCY')";
}

if(!empty($post_tahunlelang)){
	$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$post_tahunlelang."'";
}

if(!empty($post_Agency)){
	if($post_Agency == 'Agency' || $post_Agency == '')
		$sql .= "";
	else
		$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$post_Agency."')";
}


if(!empty($post_SatuanKerja)){
	if($post_SatuanKerja == 'pg_namastk' || $post_SatuanKerja == 'Satuan Kerja' || $post_SatuanKerja == '' )
		$sql .= "";
	else
		$sql .= " AND pg_namastk = '".$post_SatuanKerja."'";
}

$sql .= " group by month(pg_tanggalawallelang)
) as datas ON datas.bulan = months.months
";
	

$data_total = $DB->fetchAll($sql);

$data_tahunlelang = $DB->fetchAll("SELECT YEAR(datamaster.pg_tanggalawallelang) AS tahunlelang FROM datamaster 
								WHERE YEAR(datamaster.pg_tanggalawallelang) != '0'
								GROUP BY YEAR(datamaster.pg_tanggalawallelang)
								ORDER BY YEAR(datamaster.pg_tanggalawallelang) ASC");

//v1
//$sql="select distinct kodeagency, deskripsi from ref_agency ";

//v2 - alter TMS 20171216
$sql="select distinct a.kodeagency, b.deskripsi from agency_tahun a inner join ref_agency b on a.kodeagency = b.kodeagency where a.tahun = ". $post_tahunlelang;
/*$sql .=" and kodeagency in (select kodeagency from ref_adminagency ";
if ($post_tipeprogres=="BOGOR"){
	$sql .=" where pg_audituser in ('ANIKAGENCY','SARIAGENCY')";
}elseif($post_tipeprogres=="NON BOGOR"){
	$sql .=" where not pg_audituser  in ('ANIKAGENCY','SARIAGENCY')";
}
$sql .=")";*/

$dtAgency=$DB->fetchAll($sql);

//v1
//$sql="select distinct pg_instansi, deskripsi from ref_instansi ";

//v2 - alter TMS 20171216
$sql="select distinct a.pg_instansi, b.deskripsi from instansi_tahun a inner join ref_instansi b on a.pg_instansi = b.pg_instansi where 1=1 ";
if ($post_Agency!="" && $post_Agency!="Agency") {
	$sql.=" and kodeagency in (select pg_audituser from ref_adminagency where kodeagency ='".$post_Agency."')";
}
$sql.=" and a.tahun = ". $post_tahunlelang;
$dtStk=$DB->fetchAll($sql);
								?>
<form action="./progres_kumulatif.php" method="POST">
<div class="row margin-bottom">
	<div class="col-sm-2">
		<select name="tahunlelang" id="tahunlelang" class="form-control" onchange="this.form.submit();">
			<option value="">Tahun Lelang</option>
			<?php if(count($data_tahunlelang)>0)
			{ 
			for($i=0;$i<count($data_tahunlelang);$i++){
				?>
			<option value="<?php echo $data_tahunlelang[$i]['tahunlelang'];?>" <?php echo ($data_tahunlelang[$i]['tahunlelang'] == $post_tahunlelang)?'selected="selected"':'';?>><?php echo $data_tahunlelang[$i]['tahunlelang'];?></option>
			<?php } 
			} ?>
		</select>
	</div>
	<!--
	<div class="col-sm-2">
		<select name="TipeProgres" id="TipeProgres" class="form-control" onchange="this.form.submit();">
			<?php 
				if($post_tipeprogres=="TOTAL" || $post_tipeprogres==""){
					echo '<option value="" selected="selected" >TOTAL</option>'; 
				}else{
					echo '<option value="" >TOTAL</option>';
				}
			?>
			<?php 
				if($post_tipeprogres=="BOGOR"){
					echo '<option value="BOGOR" selected="selected" >BOGOR</option>'; 
				}else{
					echo '<option value="BOGOR" >BOGOR</option>';
				}
			?>
			<?php 
				if($post_tipeprogres=="NON BOGOR" ){
					echo '<option value="NON BOGOR" selected="selected" >NON BOGOR</option>'; 
				}else{
					echo '<option value="NON BOGOR" >NON BOGOR</option>';
				}
			?>
			
			
		</select>
	</div>
	-->
	<div class="col-sm-2">
		<select name="Agency" id="Agency" class="form-control" onchange="this.form.submit();">
			<option value="">Agency</option>
			<?php if(count($dtAgency)>0)
			{ 
			for($i=0;$i<count($dtAgency);$i++){
				?>
			<option value="<?php echo $dtAgency[$i]['kodeagency'];?>" <?php echo ($dtAgency[$i]['kodeagency'] == $post_Agency)?'selected="selected"':'';?>> <?php echo $dtAgency[$i]['deskripsi'];?> </option>
			<?php } 
			} ?>
		</select>
	</div>
	<div class="col-sm-2">
		<select name="SatuanKerja" id="SatuanKerja" class="form-control">
			<option value="">SatuanKerja</option>
			<?php if(count($dtStk)>0)
			{ 
			for($i=0;$i<count($dtStk);$i++){
				?>
			<option value="<?php echo $dtStk[$i]['deskripsi'];?>" <?php echo ($dtStk[$i]['deskripsi'] == $post_SatuanKerja)?'selected="selected"':'';?>><?php echo $dtStk[$i]['deskripsi'];?></option>
			<?php } 
			} ?>
		</select>
	</div>
	
	
	<div class="col-sm-2">
		<input type="submit" name="search" value="Search" class="btn btn-primary" />
		<input type="button" id="print" value="Print" class="btn btn-primary" />
	</div>
	<!--
	<div class="col-sm-2">
		<input type="button" id="export" value="Export" class="btn btn-primary" onclick="openWin(' <?php echo "export_progres_kumulatif.php?str=" . urlencode(serialize($data_total)) . ""; ?> ')" />
	</div>
	-->
</div>
</form>
<script src="./plugins/jquery.print/jQuery.print.js"></script>
 <script type='text/javascript'>
//<![CDATA[
$(function() {
	$("#print").on('click', function() {
		//Print ele4 with custom options
		$("#printbox").print({
			//Use Global styles
			globalStyles : true,
			//Add link with attrbute media=print
			mediaPrint : false,
			stylesheet: null,
			//Custom stylesheet
			iframe : false,
			//Don't print this
			noPrintSelector : ".avoid-this",
		});
	});
	
});
</script>
<script>
function openWin(url) {
    window.open(url);
}
</script>
<div class="row" id="printbox">
	<div class="col-md-4">
	  <div class="box">
		<div class="box-header with-border">
		  <h3 class="box-title">Total Kumulatif Per Bulan</h3>
		</div><!-- /.box-header -->
		<div class="box-body">
		
		  <table class="table table-bordered">
			<tbody><tr>
			  <th style="width: 10px">#</th>
			  <th>Bulan</th>
			  <th>Jumlah</th>
			</tr>
			<?php if(count($data_total)>0){
				$total = 0;
				for($i=0;$i<count($data_total);$i++){ ?>
			<tr>
			  <td><?php echo $data_total[$i]['BULAN'];?>.</td>
			  <td><?php echo $data_total[$i]['LABEL'];?></td>
			  <td><?php $total = $total + $data_total[$i]['TOTAL'];echo $total;?></td>
			</tr>
			<?php }
			} ?>
		  </tbody></table>
		</div><!-- /.box-body -->
		<div class="box-footer clearfix">
		  <ul class="pagination pagination-sm no-margin pull-right hidden">
			<li><a href="#">«</a></li>
			<li><a href="#">1</a></li>
			<li><a href="#">2</a></li>
			<li><a href="#">3</a></li>
			<li><a href="#">»</a></li>
		  </ul>
		</div>
	  </div><!-- /.box -->

	  <div class="box">
		<div class="box-header">
		  <h3 class="box-title">Total Per Bulan</h3>
		</div><!-- /.box-header -->
		<div class="box-body no-padding">
		  <table class="table table-bordered">
			<tbody><tr>
			  <th style="width: 10px">#</th>
			  <th>Bulan</th>
			  <th>Jumlah</th>
			</tr>
			<?php if(count($data_total)>0){
				for($i=0;$i<count($data_total);$i++){ ?>
			<tr>
			  <td><?php echo $data_total[$i]['BULAN'];?>.</td>
			  <td><?php echo $data_total[$i]['LABEL'];?></td>
			  <td><?php echo $data_total[$i]['TOTAL'];?></td>
			</tr>
			<?php }
			} ?>
		  </tbody></table>
		</div><!-- /.box-body -->
	  </div><!-- /.box -->
	</div><!-- /.col -->
	<div class="col-md-8">
	  <div class="box">
		<div class="box-header">
		  <h3 class="box-title">Grafik Kumulatif</h3>
		  
		</div><!-- /.box-header -->
		<div class="box-body no-padding">

			<!-- amCharts javascript sources -->
			<script src="plugins/amchart/amcharts.js" type="text/javascript"></script>
			<script src="plugins/amchart/serial.js" type="text/javascript"></script>
			<script src="plugins/amchart/themes/light.js" type="text/javascript"></script>

			<?php
			$JudulGrafikKumulatif="Jumlah Paket yang dilelangkan pada SPSE Kota Bogor (Kumulatif)";
			$JudulGrafikBulan="Jumlah paket yang dilelangkan pada SPSE Kota Bogor (Per Bulan)";
			
			if($post_tipeprogres=="BOGOR"){
				$JudulGrafikKumulatif="Jumlah Paket OPD Kota Bogor yang dilelangkan pada SPSE Kota Bogor (Kumulatif)";
				$JudulGrafikBulan="Jumlah paket Agency Kota Bogor yang dilelangkan pada SPSE Kota Bogor (Per Bulan)";
			}elseif($post_tipeprogres=="NON BOGOR"){
				$JudulGrafikKumulatif="Jumlah Paket OPD Luar Bogor yang dilelangkan pada SPSE Kota Bogor (Kumulatif)";
				$JudulGrafikBulan="Jumlah paket OPD Luar Bogor yang dilelangkan pada SPSE Kota Bogor (Per Bulan)";
			}
			
			?>
			
			
			<!-- amCharts javascript code -->
			<script type="text/javascript">
				AmCharts.makeChart("chartdiv",
					{
						"type": "serial",
						"categoryField": "category",
						"angle": 30,
						"depth3D": 30,
						"startDuration": 1,
						"theme": "light",
						"categoryAxis": {
							"gridPosition": "start",
							"labelRotation": 45
						},
						"trendLines": [],
						"graphs": [
							{
								"balloonText": "[[title]] of [[category]]:[[value]]",
								"fillAlphas": 1,
								"id": "AmGraph-1",
								"title": "graph 1",
								"type": "column",
								"valueField": "column-1",
								"fillColors": "#9400D3",
								"lineColor": "#9400D3",
							}
						],
						"guides": [],
						"valueAxes": [
							{
								"id": "ValueAxis-1",
								"title": "Jumlah"
							}
						],
						"allLabels": [],
						"balloon": {},
						"titles": [
							{
								"id": "Title-1",
								"size": 12,
								"text": "<?php echo $JudulGrafikKumulatif; ?>"
							}
						],
						"dataProvider": [
							<?php if(count($data_total)>0){
								$total = 0;
								for($i=0;$i<count($data_total);$i++){ ?>
									{
										"category": "<?php echo $data_total[$i]['LABEL'];?>",
										"column-1": <?php $total = $total + $data_total[$i]['TOTAL'];echo $total;?>,
									},
							<?php }
							} ?>
						]
					}
				);
			</script>
		
		
			<div id="chartdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
		
		</div><!-- /.box-body -->
	  </div><!-- /.box -->

	  <div class="box">
		<div class="box-header">
		  <h3 class="box-title">Grafik Per Bulan</h3>
		</div><!-- /.box-header -->
		<div class="box-body no-padding">
			<!-- amCharts javascript code -->
			<script type="text/javascript">
				AmCharts.makeChart("chartdiv2",
					{
						"type": "serial",
						"categoryField": "category",
						"angle": 30,
						"depth3D": 30,
						"startDuration": 1,
						"theme": "light",
						"categoryAxis": {
							"gridPosition": "start",
							"labelRotation": 45
						},
						"trendLines": [],
						"graphs": [
							{
								"balloonText": "[[title]] of [[category]]:[[value]]",
								"fillAlphas": 1,
								"id": "AmGraph-1",
								"title": "graph 1",
								"type": "column",
								"valueField": "column-1",
							}
						],
						"guides": [],
						"valueAxes": [
							{
								"id": "ValueAxis-1",
								"title": "Jumlah"
							}
						],
						"allLabels": [],
						"balloon": {},
						"titles": [
							{
								"id": "Title-1",
								"size": 12,
								"text": "<?php echo $JudulGrafikBulan; ?>"
							}
						],
						"dataProvider": [
							<?php if(count($data_total)>0){
								$total = 0;
								for($i=0;$i<count($data_total);$i++){ ?>
									{
										"category": "<?php echo $data_total[$i]['LABEL'];?>",
										"column-1": <?php echo $data_total[$i]['TOTAL'];?>,
									},
							<?php }
							} ?>
						]
					}
				);
			</script>
		
		
			<div id="chartdiv2" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
		
		</div><!-- /.box-body -->
	  </div><!-- /.box -->
	</div><!-- /.col -->
  </div>
  
<?php include_once("_footer.php");?>