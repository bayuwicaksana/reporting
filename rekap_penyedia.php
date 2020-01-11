<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Rekap Penyedia"); ?>
<?php include_once("_header.php");?>
<?php 
$post_tahunlelang = (isset($_POST['tahunlelang']))?$_POST['tahunlelang']:date("Y");
$post_Agency=(isset($_POST['Agency']))?$_POST['Agency']:'Agency';
$post_SatuanKerja=(isset($_POST['SatuanKerja']))?$_POST['SatuanKerja']:'Satuan Kerja';

$sql="select distinct kodeagency, deskripsi from ref_agency ";
$dtAgency=$DB->fetchAll($sql);

$sql="select distinct pg_instansi, deskripsi from ref_instansi ";
if ($post_Agency!="" && $post_Agency!="Agency") {
	$sql.="where kodeagency in (select pg_audituser from ref_adminagency where kodeagency ='".$post_Agency."')";
}
$dtStk=$DB->fetchAll($sql);

$sql = "SELECT pg_pemenang, count(pg_pemenang) as jumlah FROM datamaster WHERE 1=1 ";
				
if(!empty($post_tahunlelang)){
	$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$post_tahunlelang."'";
}
if(!empty($post_Agency)){
	$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$post_Agency."')";
}
if(!empty($post_SatuanKerja)){
	$sql .= " AND pg_namastk = '".$post_SatuanKerja."'";
}

$sql .= " AND pg_nilaikontrak > 0 group by pg_pemenang";

$data_total = $DB->fetchAll($sql);

$data_tahunlelang = $DB->fetchAll("SELECT YEAR(datamaster.pg_tanggalawallelang) AS tahunlelang FROM datamaster 
								WHERE YEAR(datamaster.pg_tanggalawallelang) != '0'
								GROUP BY YEAR(datamaster.pg_tanggalawallelang)
								ORDER BY YEAR(datamaster.pg_tanggalawallelang) ASC");
								
//echo ($sql);
?>
<form action="./rekap_penyedia.php" method="POST">
<div class="row margin-bottom">
	<div class="col-sm-2">
		<select name="tahunlelang" id="tahunlelang" class="form-control">
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
	<div class="col-md-13">
	  <div class="box">
		<div class="box-header with-border">
		  <h3 class="box-title">Rekap Penyedia Tahun <?php echo $post_tahunlelang; ?> </h3>
		</div><!-- /.box-header -->
		<div class="box-body">
		
		  <table class="table table-bordered">
			<tbody>
			<tr>
			  <th style="width: 10px">#</th>
			  <th >Nama Pemenang</th>
			  <th >Jumlah Menang</th>
			  <th >Detail</th>
			</tr>
			<?php if(count($data_total)>0){
				$nomer = 0;
				for($i=0;$i<count($data_total);$i++){ 
					$nomer++;
			?>
			<tr>
			  <td><?php echo $nomer;?>.</td>
			  <td><?php echo $data_total[$i]['pg_pemenang'];?></td>
			  <td><?php echo $data_total[$i]['jumlah'];?></td>
			  <td><a href="detail_penyedia.php?pemenang=<?php echo $data_total[$i]['pg_pemenang'];?>&tahun=<?php echo $post_tahunlelang; ?>" target=_new>detail</a></td>
			</tr>
			<?php }
			} ?>
		  </tbody>
		</table>
		</div><!-- /.box-body -->
	  </div><!-- /.box -->
  </div>
  
<?php include_once("_footer.php");?>