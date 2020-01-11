<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Rekap Paket Lelang"); ?>
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

$sql = "SELECT months.months as BULAN, months.name as LABEL, 
	IFNULL(dataall.TOTAL,0) as TOTALPAKET,
    IFNULL(dataselesai.TOTAL,0) as TOTALSELESAI,
    IFNULL(dataall.PAGU,0) as PAGU,
    IFNULL(dataselesai.PAGU,0) as PAGUSELESAI,
    IFNULL(dataall.HPS,0) as HPS,
    IFNULL(dataselesai.HPS,0) as HPSSELESAI,
    IFNULL(dataall.HASILLELANG,0) as HASILLELANG,
    (IFNULL(dataselesai.PAGU,0) - IFNULL(dataall.HASILLELANG,0)) as SELISIHPAGU,
    IFNULL(((IFNULL(dataselesai.PAGU,0) - IFNULL(dataall.HASILLELANG,0)) / IFNULL(dataselesai.PAGU,0)) * 100,0) as SELISIHPAGUPERSEN,
    (IFNULL(dataselesai.HPS,0) - IFNULL(dataall.HASILLELANG,0)) as SELISIHHPS,
    IFNULL(((IFNULL(dataselesai.HPS,0) - IFNULL(dataall.HASILLELANG,0)) / IFNULL(dataselesai.HPS,0)) * 100, 0) as SELISIHHPSPERSEN
FROM months 
	LEFT JOIN ( SELECT month(pg_tanggalawallelang) as bulan, COUNT(*) as TOTAL,
					SUM(pg_pagupaket) as PAGU, 
                    SUM(pg_hps) as HPS,
                    SUM(pg_nilaikontrak) as HASILLELANG
				FROM datamaster 
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

$sql .= " group by month(pg_tanggalawallelang) 
			) as dataall ON dataall.bulan = months.months
	LEFT JOIN ( SELECT month(pg_tanggalawallelang) as bulan, COUNT(*) as TOTAL,
					SUM(pg_pagupaket) as PAGU, 
                    SUM(pg_hps) as HPS
				FROM datamaster 
                WHERE statusproses IN ('SELESAI','SELESAI LELANG ULANG') ";

if(!empty($post_tahunlelang)){
	$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$post_tahunlelang."'";
}
if(!empty($post_Agency)){
	$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$post_Agency."')";
}
if(!empty($post_SatuanKerja)){
	$sql .= " AND pg_namastk = '".$post_SatuanKerja."'";
}

$sql .= " group by month(pg_tanggalawallelang) 
			) as dataselesai ON dataselesai.bulan = months.months";

$data_total = $DB->fetchAll($sql);

$data_tahunlelang = $DB->fetchAll("SELECT YEAR(datamaster.pg_tanggalawallelang) AS tahunlelang FROM datamaster 
								WHERE YEAR(datamaster.pg_tanggalawallelang) != '0'
								GROUP BY YEAR(datamaster.pg_tanggalawallelang)
								ORDER BY YEAR(datamaster.pg_tanggalawallelang) ASC");
								
//echo ($sql);

?>
<form action="./rekap_lelang.php" method="POST">
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
		<select name="SatuanKerja" id="SatuanKerja" class="form-control" onchange="this.form.submit();">
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
		<input type="button" id="export" value="Export" class="btn btn-primary" onclick="openWin(' <?php echo "export_rekap_lelang.php?str=" . urlencode(serialize($data_total)) . ""; ?> ')" />
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
	<div class="col-md-13">
	  <div class="box">
		<div class="box-header with-border">
		  <h3 class="box-title">Rekap Paket Lelang Tahun <?php echo $post_tahunlelang; ?> </h3>
		</div><!-- /.box-header -->
		<div class="scrollmenu"><!-- using own css -->
			<div class="box-body">
			
			  <table class="table table-bordered">
				<tbody>
				<tr>
				  <th rowspan="2" style="width: 10px">#</th>
				  <th rowspan="2">Bulan</th>
				  <th rowspan="2">Total Paket</th>
				  <th rowspan="2">Total Paket Selesai</th>
				  <th rowspan="2">Pagu Anggaran (Rp)</th>
				  <th rowspan="2">Pagu Anggaran Paket Yang Selesai (Rp)</th>
				  <th rowspan="2">HPS (Rp)</th>
				  <th rowspan="2">HPS Paket Yang Selesai (Rp)</th>
				  <th rowspan="2">Hasil Lelang (Rp)</th>
				  <th colspan="2">Selish Pagu dan Hasil Lelang</th>
				  <th colspan="2">Selish HPS dan Hasil Lelang</th>
				</tr>
				<tr>
				  <th>Rp</th>
				  <th>%</th>
				  <th>Rp</th>
				  <th>%</th>
				</tr>
				<?php if(count($data_total)>0){
					$total = 0;
					$totalselesai = 0;
					$pagu = 0;
					$paguselesai = 0;
					$hps = 0;
					$hpsselesai = 0;
					$hasillelang = 0;
					$selisihpagu = 0;
					$selisihhps = 0;
					
					for($i=0;$i<count($data_total);$i++){ ?>
				<tr>
				  <td><?php echo $data_total[$i]['BULAN'];?>.</td>
				  <td><?php echo $data_total[$i]['LABEL'];?></td>
				  <td><?php $total = $total + $data_total[$i]['TOTALPAKET'];echo $data_total[$i]['TOTALPAKET'];?></td>
				  <td><?php $totalselesai = $totalselesai + $data_total[$i]['TOTALSELESAI'];echo $data_total[$i]['TOTALSELESAI'];?></td>
				  <td><?php $pagu = $pagu + $data_total[$i]['PAGU'];echo number_format($data_total[$i]['PAGU']);?></td>
				  <td><?php $paguselesai = $paguselesai + $data_total[$i]['PAGUSELESAI'];echo number_format($data_total[$i]['PAGUSELESAI']);?></td>
				  <td><?php $hps = $hps + $data_total[$i]['HPS'];echo number_format($data_total[$i]['HPS']);?></td>
				  <td><?php $hpsselesai = $hpsselesai + $data_total[$i]['HPSSELESAI'];echo number_format($data_total[$i]['HPSSELESAI']);?></td>
				  <td><?php $hasillelang = $hasillelang + $data_total[$i]['HASILLELANG'];echo number_format($data_total[$i]['HASILLELANG']);?></td>
				  <td><?php $selisihpagu = $selisihpagu + $data_total[$i]['SELISIHPAGU'];echo number_format($data_total[$i]['SELISIHPAGU']);?></td>
				  <td><?php echo number_format($data_total[$i]['SELISIHPAGUPERSEN'],2);?></td>
				  <td><?php $selisihhps = $selisihhps + $data_total[$i]['SELISIHHPS'];echo number_format($data_total[$i]['SELISIHHPS']);?></td>
				  <td><?php echo number_format($data_total[$i]['SELISIHHPSPERSEN'],2);?></td>
				</tr>
				<?php }
				} ?>
				<tr>
				  <td>&nbsp;</td>
				  <td><b>Total</b></td>
				  <td><b><?php echo number_format($total);?></b></td>
				  <td><b><?php echo number_format($totalselesai);?></b></td>
				  <td><b><?php echo number_format($pagu);?></b></td>
				  <td><b><?php echo number_format($paguselesai);?></b></td>
				  <td><b><?php echo number_format($hps);?></b></td>
				  <td><b><?php echo number_format($hpsselesai);?></b></td>
				  <td><b><?php echo number_format($hasillelang);?></b></td>
				  <td><b><?php echo number_format($selisihpagu);?></b></td>
				  <td><b><?php if ($paguselesai > 0) { echo number_format(($selisihpagu/$paguselesai)*100, 2); } else { echo number_format(0);} ?></b></td>
				  <td><b><?php echo number_format($selisihhps);?></b></td>
				  <td><b><?php if ($hpsselesai > 0) { echo number_format(($selisihhps/$hpsselesai)*100, 2); } else { echo number_format(0);} ?></b></td>
				</tr>
			  </tbody>
			</table>
			</div><!-- /.box-body -->
		</div><!-- using own css -->
	  </div><!-- /.box -->
  </div>
  
<?php include_once("_footer.php");?>