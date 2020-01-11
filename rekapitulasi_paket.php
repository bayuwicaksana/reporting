<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Rekapitulasi Paket"); ?>
<?php include_once("_header.php");?>

<?php 
$post_TahunAwalKontrak=(isset($_POST['TahunAwalKontrak']))?$_POST['TahunAwalKontrak']:date("Y");
$post_Agency=(isset($_POST['Agency']))?$_POST['Agency']:'Agency';
$post_SatuanKerja=(isset($_POST['SatuanKerja']))?$_POST['SatuanKerja']:'Satuan Kerja';


$sql="select distinct year(pg_tanggalawallelang) as TahunAwalKontrak from datamaster where year(pg_tanggalawallelang)<>1900";
$dtTahunAwalKontrak=$DB->fetchAll($sql);

//v1
//$sql="select distinct kodeagency, deskripsi from ref_agency ";

//v2 - alter TMS 20171216
$sql="select distinct a.kodeagency, b.deskripsi from agency_tahun a inner join ref_agency b on a.kodeagency = b.kodeagency where a.tahun = ". $post_TahunAwalKontrak;
$dtAgency=$DB->fetchAll($sql);

//v1
//$sql="select distinct pg_instansi, deskripsi from ref_instansi ";

//v2 - alter TMS 20171216
$sql="select distinct a.pg_instansi, b.deskripsi from instansi_tahun a inner join ref_instansi b on a.pg_instansi = b.pg_instansi where 1=1 ";
if ($post_Agency!="" && $post_Agency!="Agency") {
	$sql.=" and kodeagency in (select pg_audituser from ref_adminagency where kodeagency ='".$post_Agency."')";
}
$sql.=" and a.tahun = ". $post_TahunAwalKontrak;
$dtStk=$DB->fetchAll($sql);


$data_jenis_kegiatan = $DB->fetchAll("SELECT * FROM ref_jeniskegiatan");

function get_total_Paket($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT COUNT(pg_kodepaket) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."'";
		
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}	

function get_total_Paket_Selesai($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT COUNT(pg_kodepaket) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."' and statusproses='selesai'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$sql .= " AND statusproses IN ('SELESAI','SELESAI LELANG ULANG')";
	
	

	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}

function get_Pagu($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT sum(pg_pagupaket) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}

function get_Pagu_Selesai($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT sum(pg_pagupaket) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."' and statusproses='selesai'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$sql .= " AND statusproses IN ('SELESAI','SELESAI LELANG ULANG')";
	


	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;

}

function get_Penawaran($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT sum(pg_nilaikontrak) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}

function get_HPS($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT sum(pg_HPS) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}

function get_HPSSelesai($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT sum(pg_HPS) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}

	$sql .= " AND statusproses IN ('SELESAI','SELESAI LELANG ULANG')";
	
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}

function get_HasilLelang($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT sum(pg_nilaikontrak) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
		
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}


?>
<form action="./rekapitulasi_paket.php" method="POST">
<div class="row margin-bottom">
	<div class="col-sm-2">
		<select name="TahunAwalKontrak" id="TahunAwalKontrak" class="form-control" onchange="this.form.submit();">
			<option value="">Tahun Awal Kontrak</option>
			<?php if(count($dtTahunAwalKontrak)>0)
			{ 
			for($i=0;$i<count($dtTahunAwalKontrak);$i++){
				?>
			<option value="<?php echo $dtTahunAwalKontrak[$i]['TahunAwalKontrak'];?>" <?php echo ($dtTahunAwalKontrak[$i]['TahunAwalKontrak'] == $post_TahunAwalKontrak)?'selected="selected"':'';?>> <?php echo $dtTahunAwalKontrak[$i]['TahunAwalKontrak'];?> </option>
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
		  <h3 class="box-title"><?php echo "DATA TAHUN ";?><?php echo $post_TahunAwalKontrak;?></h3> <br>
		  <?//php echo date("d M Y") ?>
		</div><!-- /.box-header -->
		<div class="box-body">
			<?php 
				$HPS=get_HPS($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);
				$HPSSelesai=get_HPSSelesai($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);
				$Penawaran=get_Penawaran($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);
				$PaguSelesai=get_Pagu_Selesai($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);
				$HPSEfisiensi=$HPSSelesai-$Penawaran;
			?>
		
		
			<table >
				
				<tr>
					<td width='200px'> Total Paket</td> <td width='30px'> : </td> <td style="text-align:right"> <?php echo get_total_Paket($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);?> </td>
				</tr>
				<tr>
					<td> Pagu</td> <td> : </td> <td style="text-align:right"> <?php echo "Rp. ".number_format(get_Pagu($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak),0,"",".").",-";?> </td>
				</tr>
				<tr>
					<td> Total Paket Selesai     </td> <td> : </td> <td style="text-align:right"> <?php echo get_total_Paket_Selesai($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);?> </td>
				</tr>
				<tr>
					<td> Pagu Paket Selesai</td> <td> : </td> <td style="text-align:right"> <?php echo "Rp. ".number_format($PaguSelesai,0,"",".").",-";?> </td>
				</tr>
				<tr>
					<td> HPS</td> <td> : </td> <td style="text-align:right"> <?php echo "Rp. ".number_format($HPS,0,"",".").",-";?> </td>
				</tr>
				<tr>
					<td> HPS Paket Selesai</td> <td> : </td> <td style="text-align:right"> <?php echo "Rp. ".number_format($HPSSelesai,0,"",".").",-";?> </td>
				</tr>
				<tr>
					<td> Penawaran</td> <td> : </td> <td style="text-align:right"> <?php echo "Rp. ".number_format($Penawaran,0,"",".").",-";?> </td>
				</tr>
				<tr>
					<td> Efisiensi HPS</td> <td> : </td> <td style="text-align:right"> 
					<?php if ($HPS>0): ?> 
					<?php echo "Rp. ".number_format($HPSEfisiensi,0,"",".").",- (".number_format((($HPSEfisiensi)/$HPSSelesai*100),2)."%)"  ?> 
					<?php else: ?>
					<?php echo "-" ?>
					<?php endif ?>
					</td>
				</tr>
				<tr>
					<td> Efisiensi Pagu Selesai</td> <td> : </td> <td style="text-align:right"> 
					<?php if ($PaguSelesai>0): ?> 
					<?php echo "Rp. ".number_format($PaguSelesai - $Penawaran,0,"",".").",- (".number_format(((($PaguSelesai - $Penawaran)/$PaguSelesai)*100),2)."%)"  ?> </td>
					<?php else: ?>
					<?php echo "-" ?>
					<?php endif ?>
				</tr>
			</table>
			
		</div><!-- /.box-body -->
	  </div><!-- /.box -->
	</div><!-- /.col -->
</div><!-- /.row -->
<?php include_once("_footer.php");?>
