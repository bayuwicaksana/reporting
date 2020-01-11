<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Existing Bogor"); ?>
<?php include_once("_header.php");?>

<?php 
$post_tahunlelang = (isset($_POST['tahunlelang']))?$_POST['tahunlelang']:date("Y");
$post_Agency=(isset($_POST['Agency']))?$_POST['Agency']:'Agency';
$post_SatuanKerja=(isset($_POST['SatuanKerja']))?$_POST['SatuanKerja']:'Satuan Kerja';

//v1
//$sql="select distinct kodeagency, deskripsi from ref_agency ";

//v2 - alter TMS 20171216
$sql="select distinct a.kodeagency, b.deskripsi from agency_tahun a inner join ref_agency b on a.kodeagency = b.kodeagency where a.tahun = ". $post_tahunlelang;
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

$sql = "SELECT datamaster.statusproses, count(*) as jumlah, @curRow := @curRow + 1 AS row_number FROM datamaster
		JOIN (SELECT @curRow := 0) r WHERE 1=1";

if(!empty($post_tahunlelang)){
	$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$post_tahunlelang."'";
}		
if(!empty($post_Agency)){
	$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$post_Agency."')";
}
if(!empty($post_SatuanKerja)){
	$sql .= " AND pg_namastk = '".$post_SatuanKerja."'";
}

$sql .= " GROUP BY datamaster.statusproses ORDER BY row_number";
$data = $DB->fetchAll($sql);

$data_tahunlelang = $DB->fetchAll("SELECT YEAR(datamaster.pg_tanggalawallelang) AS tahunlelang FROM datamaster 
								WHERE YEAR(datamaster.pg_tanggalawallelang) != '0'
								GROUP BY YEAR(datamaster.pg_tanggalawallelang)
								ORDER BY YEAR(datamaster.pg_tanggalawallelang) ASC");
					
?>
<form action="./existing_bogor.php" method="POST">
<div class="row margin-bottom avoid-this">
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
			<option value="">Satuan Kerja</option>
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
	<div class="col-sm-2">
		<input type="button" id="export" value="Export" class="btn btn-primary" onclick="openWin(' <?php echo "export_existing_bogor.php?str=" . urlencode(serialize($data)) . ""; ?> ')" />
	</div>
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
	<div class="col-xs-12">
	  <div class="box">
		<div class="box-header">
		  <h3 class="box-title"><?php echo "Konsidi Eksisting Proses Pengadaan Agency Kota Bogor";?></h3>
		</div><!-- /.box-header -->
		<div class="box-body">
		
			<table id="datatables" class="table table-bordered table-hover">
				<thead>
					<tr>
						<th>No.</th>
						<th>Keterangan</th>
						<th>Jumlah</th>
					</tr>
				</thead>
				<tbody>
					<?php $total = 0;
					$totalselesai = 0;
					$totalproses = 0;
					$totalgagal = 0;
					$totallelangulang = 0;
					$totallelang1 = 0;
					if(count($data)>0)
					{ 
						for($i=0;$i<count($data);$i++){
							$total=$total+$data[$i]['jumlah'];
							if ($data[$i]['statusproses'] == 'SELESAI' || $data[$i]['statusproses'] == 'SELESAI LELANG ULANG') {
								$totalselesai = $totalselesai + $data[$i]['jumlah'];
							}  elseif ($data[$i]['statusproses'] == 'GAGAL LELANG') {
									$totalgagal = $totalgagal + $data[$i]['jumlah'];
							} else {
								if ($data[$i]['statusproses'] == 'PROSES' || $data[$i]['statusproses'] == 'PROSES LELANG ULANG') {
									if ($data[$i]['statusproses'] == 'PROSES') {
										$totallelang1 = $totallelang1 + $data[$i]['jumlah'];
									}
									if ($data[$i]['statusproses'] == 'PROSES LELANG ULANG') {
										$totallelangulang = $totallelangulang + $data[$i]['jumlah'];
									}
									$totalproses = $totalproses + $data[$i]['jumlah'];
								}
					?>
					<!--
					<tr>
						<td><?php echo $data[$i]['row_number'];?></td>
						<td><?php echo $data[$i]['statusproses'];?></td>
						<td><?php echo $data[$i]['jumlah'];?></td>
					</tr>
					-->
					<?php 
							}
						}
					?>
					<tr>
						<td><?php $i++; echo $i-4;?></td>
						<td>PROSES LELANG PERTAMA</td>
						<td><?php echo $totallelang1;?></td>
					</tr>
					<tr>
						<td><?php $i++; echo $i-4;?></td>
						<td>PROSES LELANG ULANG</td>
						<td><?php echo $totallelangulang;?></td>
					</tr>
					<tr>
						<td><?php $i++; echo $i-4;?></td>
						<td>TOTAL PROSES</td>
						<td><?php echo $totalproses;?></td>
					</tr>
					<tr>
						<td><?php $i++; echo $i-4;?></td>
						<td>SELESAI</td>
						<td><?php echo $totalselesai;?></td>
					</tr>
					<tr>
						<td><?php $i++; echo $i-4;?></td>
						<td>GAGAL LELANG</td>
						<td><?php echo $totalgagal;?></td>
					</tr>
					<?php
					} ?>
				</tbody>
				
			</table>
			
		<!-- amCharts javascript sources -->
		<script src="plugins/amchart/amcharts.js" type="text/javascript"></script>
		<script src="plugins/amchart/serial.js" type="text/javascript"></script>

		<!-- amCharts javascript code -->
		<script type="text/javascript">
			AmCharts.makeChart("chartdiv",
				{
					"type": "serial",
					"categoryField": "category",
					"rotate": true,
					"startDuration": 0,
					"categoryAxis": {
						"gridPosition": "start"
					},
					"trendLines": [],
					"graphs": [
						{
							"balloonText": "[[title]] of [[category]]:[[value]]",
							"fillAlphas": 1,
							"fillColors": "",
							"id": "AmGraph-1",
							"lineColor": "#238BFA",
							"title": "graph 1",
							"type": "column",
							"valueField": "column-1"
						}
					],
					"guides": [],
					"valueAxes": [
						{
							"id": "ValueAxis-1",
							"title": ""
						}
					],
					"allLabels": [],
					"balloon": {},
					"titles": [
						{
							"id": "Title-1",
							"size": 15,
							"text": "Grafik Konsidi Eksisting"
						}
					],
					"dataProvider": [
						<?php $total = 0;
						$totalselesai = 0;
						$totalproses = 0;
						$totalgagal = 0;
						$totallelangulang = 0;
						$totallelang1 = 0;
						if(count($data)>0)
						{ 
							for($i=0;$i<count($data);$i++){
								$total=$total+$data[$i]['jumlah'];
								if ($data[$i]['statusproses'] == 'SELESAI' || $data[$i]['statusproses'] == 'SELESAI LELANG ULANG') {
									$totalselesai = $totalselesai + $data[$i]['jumlah'];
								} elseif ($data[$i]['statusproses'] == 'GAGAL LELANG') {
									$totalgagal = $totalgagal + $data[$i]['jumlah'];
								} else {
								if ($data[$i]['statusproses'] == 'PROSES' || $data[$i]['statusproses'] == 'PROSES LELANG ULANG') {
										if ($data[$i]['statusproses'] == 'PROSES') {
											$totallelang1 = $totallelang1 + $data[$i]['jumlah'];
										}
										if ($data[$i]['statusproses'] == 'PROSES LELANG ULANG') {
											$totallelangulang = $totallelangulang + $data[$i]['jumlah'];
										}
										$totalproses = $totalproses + $data[$i]['jumlah'];
									}
						?>
						<?php
								}
							}
						?>
						{
							"category": "PROSES LELANG PERTAMA",
							"column-1": <?php echo $totallelang1;?>,
						},						
						{
							"category": "PROSES LELANG ULANG",
							"column-1": <?php echo $totallelangulang;?>,
						},						
						{
							"category": "TOTAL PROSES",
							"column-1": <?php echo $totalproses;?>,
						},						
						{
							"category": "SELESAI",
							"column-1": <?php echo $totalselesai;?>,
						},						
						{
							"category": "GAGAL LELANG",
							"column-1": <?php echo $totalgagal;?>,
						},						
						<?php
						} ?>
						
					]
				}
			);
		</script>
	
		<div id="chartdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
	
		</div><!-- /.box-body -->
	  </div><!-- /.box -->
	</div><!-- /.col -->
</div><!-- /.row -->
<?php include_once("_footer.php");?>
