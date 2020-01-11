<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Rekapitulasi Paket SPSE Kota Bogor"); ?>
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

//v1
//$sql = "SELECT deskripsi as nama_instansi FROM ref_instansi WHERE 1=1";

//v2 - alter TMS 20171216
$sql="select distinct b.deskripsi as nama_instansi from instansi_tahun a inner join ref_instansi b on a.pg_instansi = b.pg_instansi where 1=1 ";
if(!empty($post_Agency)){
		$tmp_pg_audituser = $DB->fetchAll("select pg_audituser from `ref_adminagency` where kodeagency = '".$post_Agency."'");	
		$sql .= " AND kodeagency in (";
		if(count($tmp_pg_audituser)>0) {
			for($i=0;$i<count($tmp_pg_audituser);$i++){
				$sql .= "'".$tmp_pg_audituser[$i]['pg_audituser']."'";
				if ($i != (count($tmp_pg_audituser)-1)) { $sql .= ","; }
			}
		}
		$sql .= ")";
		$sql.=" and a.tahun = ". $post_tahunlelang;
	}
if(!empty($post_SatuanKerja)){
		$sql .= " AND deskripsi = '".$post_SatuanKerja."'";
	}
//echo $sql;
$data = $DB->fetchAll($sql);

$data_tahunlelang = $DB->fetchAll("SELECT YEAR(datamaster.pg_tanggalawallelang) AS tahunlelang FROM datamaster 
								WHERE YEAR(datamaster.pg_tanggalawallelang) != '0'
								GROUP BY YEAR(datamaster.pg_tanggalawallelang)
								ORDER BY YEAR(datamaster.pg_tanggalawallelang) ASC");

$data_jenis_kegiatan = $DB->fetchAll("SELECT * FROM ref_jeniskegiatan");
function get_total($nama_instansi = '', $nama_jenis_kegiatan = '', $statusproses = '', $year = '')
{
	$DB = new DBPDO();
	$sql = "SELECT COUNT(*) AS TOTAL FROM datamaster
			WHERE pg_jeniskegiatan = '".$nama_jenis_kegiatan."'
			AND pg_namastk = '".$nama_instansi."'
			";
	if(!empty($statusproses)){
		if($statusproses == 'SELESAI')
			$sql .= " AND (statusproses = 'SELESAI' OR statusproses = 'SELESAI LELANG ULANG')";
		else
			//$sql .= " AND (statusproses = 'PROSES' OR statusproses = 'PROSES LELANG ULANG' OR statusproses = 'GAGAL LELANG')";
			$sql .= " AND statusproses not in ('SELESAI','SELESAI LELANG ULANG')";
	}
	if(!empty($year)){
		$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$year."'";
	}
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}							
?>
<form action="./kumulatif_paket.php" method="POST">
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
<div class="row" id="printbox">
	<div class="col-xs-12">
	  <div class="box">
		<div class="box-header">
		  <h3 class="box-title"><?php echo "DATA LELANG KOTA BOGOR UNTUK PAKET PENGADAAN BARANG & KONSTRUKSI > 200 JT DAN JASA KONSULTANSI > 50 JT";?></h3>
		</div><!-- /.box-header -->
		<div class="box-body">
		
			<table id="datatables" class="table table-bordered table-hover">
				<thead>
					<tr>
						<th rowspan="2">No.</th>
						<th rowspan="2">SKPD</th>
						<?php $total = 0;
						if(count($data_jenis_kegiatan)>0)
						{ 
							for($i=0;$i<count($data_jenis_kegiatan);$i++){
						?>
							<th colspan="3"><?php echo $data_jenis_kegiatan[$i]['deskripsi'];?></th>
						<?php 
							}
						} ?>
						<th rowspan="2">Jumlah Eproc (yang sudah dilaksanakan)</th>
					</tr>
					<tr>
						<?php $total = 0;
						if(count($data_jenis_kegiatan)>0)
						{ 
							for($i=0;$i<count($data_jenis_kegiatan);$i++){
						?>
							<th>Sedang Proses</th>
							<th>Selesai</th>
							<th>Jumlah</th>
						<?php 
							}
						} ?>
					</tr>
				</thead>
				<tbody>
					<?php 
					$total_all_data = 0;
					$total_all_proses = array();
					$total_all_selesai = array();
					$total_all_jumlah = array();
					if(count($data)>0)
					{ 
						for($r=0;$r<count($data);$r++){
							
					?>
						<tr>
							<td><?php echo ($r+1);?></td>
							<td><?php echo $data[$r]['nama_instansi'];?></td>
							<?php $total = 0;
							if(count($data_jenis_kegiatan)>0)
							{ 
								for($i=0;$i<count($data_jenis_kegiatan);$i++){
							?>
								<td><?php $total_proses = get_total($data[$r]['nama_instansi'], $data_jenis_kegiatan[$i]['deskripsi'], 'PROSES', $post_tahunlelang);
								echo $total_proses; ?></td>
								<td><?php $total_selesai = get_total($data[$r]['nama_instansi'], $data_jenis_kegiatan[$i]['deskripsi'], 'SELESAI', $post_tahunlelang);
								echo $total_selesai; ?></td>
								<td class="bg-gray"><?php $total_all = $total_proses + $total_selesai; echo $total_all;
								$total = $total + $total_all;
								$total_all_data = $total_all_data + $total_all;
								
								$total_all_proses[$i] = (isset($total_all_proses[$i])?$total_all_proses[$i]:0) + $total_proses;
								$total_all_selesai[$i] = (isset($total_all_selesai[$i])?$total_all_selesai[$i]:0) + $total_selesai;
								$total_all_jumlah[$i] = (isset($total_all_jumlah[$i])?$total_all_jumlah[$i]:0) + $total_all;
								?></td>
							<?php 
								}
							} ?>
							<td class="bg-gray-active"><?php echo $total;?></td>
						</tr>
					<?php 
						}
					} ?>
					<tr>
						<th colspan="2">TOTAL</th>
						<?php $total = 0;
						if(count($data_jenis_kegiatan)>0)
						{ 
							for($i=0;$i<count($data_jenis_kegiatan);$i++){
						?>
							<th><?php echo $total_all_proses[$i];?></th>
							<th><?php echo $total_all_selesai[$i];?></th>
							<th class="bg-gray"><?php echo $total_all_jumlah[$i];?></th>
						<?php 
							}
						} ?>
						<th class="bg-gray-active"><?php echo $total_all_data;?></th>
					</tr>
				</tbody>
				
			</table>
		
		<?php 
		$sql = "SELECT months.months as BULAN, months.name as LABEL, IFNULL(datas.TOTAL,0) as TOTAL
		FROM months 
		LEFT JOIN
		(
		SELECT month(pg_tanggalawallelang) as bulan, COUNT(*) as TOTAL FROM datamaster
		WHERE 1=1 ";
		if(!empty($post_tahunlelang)){
			$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$post_tahunlelang."'";
		}
		if(!empty($post_Agency)){
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$post_Agency."')";
		}
		if(!empty($post_SatuanKerja)){
			$sql .= " AND pg_namastk = '".$post_SatuanKerja."'";
		}
		//$sql .= " AND datamaster.statusproses <> 'GAGAL LELANG' ";
		$sql .= " group by month(pg_tanggalawallelang)
		) as datas ON datas.bulan = months.months
		";

		$data_total_grafik12 = $DB->fetchAll($sql);
		
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
		//$sql .= " AND datamaster.statusproses <> 'GAGAL LELANG' ";
		$sql .= " GROUP BY datamaster.statusproses ORDER BY row_number";
		$data_grafikexisting = $DB->fetchAll($sql);
		?>
		<!-- amCharts javascript sources -->
		<script src="plugins/amchart/amcharts.js" type="text/javascript"></script>
		<script src="plugins/amchart/serial.js" type="text/javascript"></script>
		<script src="plugins/amchart/themes/light.js" type="text/javascript"></script>

			<div class="row">
				<div class="col-sm-6 col-xs-6">
					<!-- amCharts javascript code -->
					<script type="text/javascript">
						AmCharts.makeChart("chartdiv1",
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
										"size": 15,
										"text": "Jumlah Paket yang dilelangkan pada SPSE Kota Bogor (Kumulatif)"
									}
								],
								"dataProvider": [
									<?php if(count($data_total_grafik12)>0){
										$total = 0;
										for($i=0;$i<count($data_total_grafik12);$i++){ ?>
											{
												"category": "<?php echo $data_total_grafik12[$i]['LABEL'];?>",
												"column-1": <?php $total = $total + $data_total_grafik12[$i]['TOTAL'];echo $total;?>,
											},
									<?php }
									} ?>
								]
							}
						);
					</script>
				
				
					<div id="chartdiv1" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
				
				</div>
				<div class="col-sm-6 col-xs-6">
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
										"size": 15,
										"text": "Jumlah paket yang dilelangkan pada SPSE Kota Bogor (Per Bulan)"
									}
								],
								"dataProvider": [
									<?php if(count($data_total_grafik12)>0){
										$total = 0;
										for($i=0;$i<count($data_total_grafik12);$i++){ ?>
											{
												"category": "<?php echo $data_total_grafik12[$i]['LABEL'];?>",
												"column-1": <?php echo $data_total_grafik12[$i]['TOTAL'];?>,
											},
									<?php }
									} ?>
								]
							}
						);
					</script>
				
				
					<div id="chartdiv2" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-6 col-xs-6">
					<!-- amCharts javascript code -->
					<script type="text/javascript">
						AmCharts.makeChart("chartdiv3",
							{
								"type": "serial",
								"categoryField": "category",
								"rotate": true,
								"startDuration": 1,
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
									if(count($data_grafikexisting)>0)
									{ 
										for($i=0;$i<count($data_grafikexisting);$i++){
											$total=$total+$data_grafikexisting[$i]['jumlah'];
											if ($data_grafikexisting[$i]['statusproses'] == 'SELESAI' || $data_grafikexisting[$i]['statusproses'] == 'SELESAI LELANG ULANG') {
												$totalselesai = $totalselesai + $data_grafikexisting[$i]['jumlah'];
											} elseif ($data_grafikexisting[$i]['statusproses'] == 'GAGAL LELANG') {
													$totalgagal = $totalgagal + $data_grafikexisting[$i]['jumlah'];
											} else {
												if ($data_grafikexisting[$i]['statusproses'] == 'PROSES' || $data_grafikexisting[$i]['statusproses'] == 'PROSES LELANG ULANG') {
													if ($data_grafikexisting[$i]['statusproses'] == 'PROSES') {
														$totallelang1 = $totallelang1 + $data_grafikexisting[$i]['jumlah'];
													}
													if ($data_grafikexisting[$i]['statusproses'] == 'PROSES LELANG ULANG') {
														$totallelangulang = $totallelangulang + $data_grafikexisting[$i]['jumlah'];
													}
													$totalproses = $totalproses + $data_grafikexisting[$i]['jumlah'];
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
				
					<div id="chartdiv3" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
				
				</div>
				<div class="col-sm-6 col-xs-6">
					<center>
					Ketua LPSE,				
					<br/>
					<br/>
					<br/>
					<br/>
					<br/>
<?php
		$sql = "SELECT nip,nama from ref_ketua_lpse WHERE 1=1";
		$data_ketualpse = $DB->fetchAll($sql);
		if(count($data_ketualpse)>0)
		{ 
			echo $data_ketualpse[0]['nama'];
?>
<br/>
NIP. 
<?php
			echo $data_ketualpse[0]['nip'];
		}
?>
</center>

				</div>
			</div>
		</div><!-- /.box-body -->
	  </div><!-- /.box -->
	</div><!-- /.col -->
</div><!-- /.row -->
<?php include_once("_footer.php");?>
