<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Daftar Paket Lelang"); ?>
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

$sql = "SELECT (select deskripsi from ref_agency where kodeagency in (select ref_adminagency.kodeagency from ref_adminagency where ref_adminagency.pg_audituser = datamaster.pg_audituser)) as deskripsi, pg_namastk, pg_namapaket, IFNULL( pg_pagupaket, 0 ) AS pg_pagupaket, IFNULL( pg_hps, 0 ) AS pg_hps, IFNULL( pg_nilaikontrak, 0 ) AS pg_nilaikontrak, pg_pemenang FROM  `datamaster` WHERE 1=1 ";
				
if(!empty($post_tahunlelang)){
	$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$post_tahunlelang."'";
}
if(!empty($post_Agency)){
	$tmp_pg_audituser = $DB->fetchAll("select pg_audituser from `ref_adminagency` where kodeagency = '".$post_Agency."'");	
	$sql .= " AND pg_audituser in (";
	if(count($tmp_pg_audituser)>0) {
		for($i=0;$i<count($tmp_pg_audituser);$i++){
			$sql .= "'".$tmp_pg_audituser[$i]['pg_audituser']."'";
			if ($i != (count($tmp_pg_audituser)-1)) { $sql .= ","; }
		}
	}
	$sql .= ")";
}
if(!empty($post_SatuanKerja)){
	$sql .= " AND pg_namastk = '".$post_SatuanKerja."'";
}

$sql .= " AND statusproses IN ('SELESAI','SELESAI LELANG ULANG')";

//echo ($sql);

$data_total = $DB->fetchAll($sql);

$data_tahunlelang = $DB->fetchAll("SELECT YEAR(datamaster.pg_tanggalawallelang) AS tahunlelang FROM datamaster 
								WHERE YEAR(datamaster.pg_tanggalawallelang) != '0'
								GROUP BY YEAR(datamaster.pg_tanggalawallelang)
								ORDER BY YEAR(datamaster.pg_tanggalawallelang) ASC");
								
?>
<form action="./daftar_lelang.php" method="POST">
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
		<input type="button" id="export" value="Export" class="btn btn-primary" onclick="openWin(' <?php echo "export_daftar_lelang.php?str=" . urlencode(serialize($data_total)) . ""; ?> ')" />
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
		  <h3 class="box-title">Daftar Paket Lelang Tahun <?php echo $post_tahunlelang; ?> </h3>
		</div><!-- /.box-header -->
		<div class="box-body">
		
		  <table class="table table-bordered">
			<tbody>
			<tr>
			  <th style="width: 10px">#</th>
			  <th >Nama Agency</th>
			  <th >Nama OPD (Satker)</th>
			  <th >Nama Paket</th>
			  <th >Pagu (Rp)</th>
			  <th >HPS (Rp)</th>
			  <th >Nilai Kontrak (Rp)</th>
			  <th >Pemenang</th>
			</tr>
			<?php if(count($data_total)>0){
				$nomer = 0;
				$pagu = 0;
				$hps = 0;
				$kontrak = 0;
				for($i=0;$i<count($data_total);$i++){ 
					$nomer++;
					$pagu = $pagu + $data_total[$i]['pg_pagupaket'];
					$hps = $hps + $data_total[$i]['pg_hps'];
					$kontrak = $kontrak + $data_total[$i]['pg_nilaikontrak'];
			?>
			<tr>
			  <td><?php echo $nomer;?>.</td>
			  <td><?php echo $data_total[$i]['deskripsi'];?></td>
			  <td><?php echo $data_total[$i]['pg_namastk'];?></td>
			  <td><?php echo $data_total[$i]['pg_namapaket'];?></td>
			  <td><?php echo number_format($data_total[$i]['pg_pagupaket']);?></td>
			  <td><?php echo number_format($data_total[$i]['pg_hps']);?></td>
			  <td><?php echo number_format($data_total[$i]['pg_nilaikontrak']);?></td>
			  <td><?php echo $data_total[$i]['pg_pemenang'];?></td>
			</tr>
			<?php }
			} ?>
			<tr>
			  <td><b>Total</b></td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td><b><?php echo number_format($pagu);?></b></td>
			  <td><b><?php echo number_format($hps);?></b></td>
			  <td><b><?php echo number_format($kontrak);?></b></td>
			  <td>&nbsp;</td>
			</tr>
		  </tbody>
		</table>
		</div><!-- /.box-body -->
	  </div><!-- /.box -->
  </div>
  
<?php include_once("_footer.php");?>