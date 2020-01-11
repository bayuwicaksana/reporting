<?php 
include_once("connection.php");

//session_start();
if($_SERVER["REQUEST_METHOD"] == "POST")
{
// username and password sent from Form
$myusername=(isset($_POST['UserName']))?$_POST['UserName']:'';
$mypassword=(isset($_POST['Password']))?$_POST['Password']:'';
 
$sql="SELECT username,password FROM datauser WHERE username='$myusername' and password='$mypassword'";

$result=$DB->fetchAll($sql);
$row=count($result);
$count=count($result);
 
 
 // If result matched $myusername and $mypassword, table row must be 1 row
if($count==1)
{
//session_register("myusername");
//$_SESSION['login_user']=$myusername;
header("location: rekapitulasi_paket.php");
}
else
{
$error="Your Login Name or Password is invalid";
}
}
?>


<?php 
$post_TahunAwalKontrak=(isset($_POST['TahunAwalKontrak']))?$_POST['TahunAwalKontrak']:date("Y");
//$post_TahunAwalKontrak=2017;
$post_Agency=(isset($_POST['Agency']))?$_POST['Agency']:'Agency';
$post_SatuanKerja=(isset($_POST['SatuanKerja']))?$_POST['SatuanKerja']:'Satuan Kerja';

$sql="select distinct year(pg_tanggalawalkontrak) as TahunAwalKontrak from datamaster where year(pg_tanggalawalkontrak)<>1900";
$dtTahunAwalKontrak=$DB->fetchAll($sql);

//$sql="select distinct pg_AuditUser as Agency from datamaster where year(pg_tanggalawalkontrak)='".$post_TahunAwalKontrak."'" ;
//$dtAgency=$DB->fetchAll($sql);

//v2 - alter TMS 20171216
$sql="select distinct a.kodeagency, b.deskripsi from agency_tahun a inner join ref_agency b on a.kodeagency = b.kodeagency where a.tahun = ". $post_TahunAwalKontrak;
$dtAgency=$DB->fetchAll($sql);

//$sql="select distinct pg_NamaStk as SatuanKerja from datamaster ";
//if ($post_Agency!="" && $post_Agency!="Agency") $sql.="where pg_AuditUser='".$post_Agency."' and year(pg_tanggalawalkontrak)='".$post_TahunAwalKontrak."' ";
//$dtStk=$DB->fetchAll($sql);

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


<body style="margin: 0px;">
<div style="position: absolute; left: 20px; top: 10px; z-index: 1;width:100%;">
<table style="width:100%; border-collapse: collapse;">
	<tr style="width:100%;">
		<td>
			<table>
				<tr>
					<td><img src="dist/img/pemkot-bogor-2.png" height="100px" /><td>
					<td><font face="arial" color="white"><b>BAGIAN ADMINISTRASI <br>
						PENGENDALIAN PEMBANGUNAN<br>
						SEKRETARIAT DAERAH<br>
						KOTA BOGOR</b></font> </td>
				</tr>
			</table>
		</td>
		<td>
		</td>
		<td align="right"><img src="dist/img/lpse-bogor-liteGlow.png" height="120px" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td>
		</td>
		<td>
			<br><br><br><br>

			 
			<table>
				<tr>
					<td><img src="images/LoginIco.png" height="50px"></td><td><font face="arial" color="white"><b>L O G I N</b></font></td>
				</tr>
			</table>
			<form action="./login.php" method="POST">
			<table>
				<tr>
					<td><font face="arial" color="white" size=2px> User Name</font></td>
					<td>  </td>
					<td> <input name="UserName" title="Username" value="" size="30" maxlength="2048" /></td>
				</tr>
				<tr>
					<td><font face="arial" color="white" size=2px> Password</font></td>
					<td>  </td>
					<td> <input type="password" name="Password" title="Password" value="" size="30" maxlength="2048" /></td>
				</tr>
				<tr>
					<td> </td>
					<td> </td>
					<td> <input type="submit" name="Login" value="Login" class="btn btn-primary" /> </td>
				</tr>
			</table>
			</form>
		</td>
		<td>
		</td>
	</tr>
</table>

</div>

<div style="position: absolute; left: 220px; top: 200px; z-index: 1;">

</div>
<table  border="0" style="width:100%; border-collapse: collapse;">
	<tr>
		<td style="width:100%; background:repeat url(images/HBGfill2.jpg); vertical-align: top;" > <img src="images/HBG2.jpg" /></td>
	</tr>
	<tr align=center>
		<td>
			<?php 
				$HPS=get_HPS($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);
				$HPSSelesai=get_HPSSelesai($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);
				$Penawaran=get_Penawaran($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);
				$PaguSelesai=get_Pagu_Selesai($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);
				$HPSEfisiensi=$HPSSelesai-$Penawaran;
			?>
		
			
			
			
			<font face="arial" size="4"><b>Rekapitulasi Paket Tahun <?php echo date("Y");?></b> </font> 
			<br><br>
			<font face="arial" size="2">
			<table style="font-family:arial; font-size:small;">	
				<tr>
					<td>Total Paket</td>
					<td>:</td>
					<td style="text-align:right"><?php echo get_total_Paket($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);?></td>
				</tr>
				<tr>
					<td>Pagu</td>
					<td>:</td>
					<td style="text-align:right"><?php echo "Rp. ".number_format(get_Pagu($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak),0,"",".").",-";?></td>
				</tr>
				<tr>
					<td>Total Paket Selesai</td>
					<td>:</td>
					<td style="text-align:right"><?php echo get_total_Paket_Selesai($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak);?></td>
				</tr>
				<tr>
					<td>Pagu Paket Selesai</td>
					<td>:</td>
					<td style="text-align:right"><?php echo "Rp. ".number_format($PaguSelesai,0,"",".").",-";?></td>
				</tr>
				<tr>
					<td>HPS</td>
					<td>:</td>
					<td style="text-align:right"><?php echo "Rp. ".number_format($HPS,0,"",".").",-";?></td>
				</tr>
				<tr>
					<td> HPS Paket Selesai</td> 
					<td> : </td> 
					<td style="text-align:right"> <?php echo "Rp. ".number_format($HPSSelesai,0,"",".").",-";?> </td>
				</tr>
				<tr>
					<td>Penawaran</td>
					<td>:</td>
					<td style="text-align:right"><?php echo "Rp. ".number_format($Penawaran,0,"",".").",-";?></td>
				</tr>
				<tr>
					<td>Efisiensi HPS</td>
					<td>:</td>
					<td style="text-align:right">
					<?php if ($HPS>0): ?> 
					<?php echo "Rp. ".number_format($HPSEfisiensi,0,"",".").",- (".number_format((($HPSEfisiensi)/$HPSSelesai*100),2)."%)"  ?> 
					<?php else: ?>
					<?php echo "-" ?>
					<?php endif ?>
					</td>
				</tr>
				<tr>
					<td>Efisiensi Pagu Selesai</td>
					<td>:</td>
					<td>
					<?php if ($PaguSelesai>0): ?> 
					<?php echo "Rp. ".number_format($PaguSelesai - $Penawaran,0,"",".").",- (".number_format(((($PaguSelesai - $Penawaran)/$PaguSelesai)*100),2)."%)"  ?> </td>
					<?php else: ?>
					<?php echo "-" ?>
					<?php endif ?>
					</td>
				</tr>
				<!--
				<tr>
					<td>Jumlah Penyedia</td>
					<td>:</td>
					<td style="text-align:right"><?php echo get_Penyedia($post_Agency, $post_SatuanKerja, $post_TahunAwalKontrak)." Penyedia/Vendor";?></td>
				</tr>
				-->
			
			</table>
			</font>
		</td>
	</tr>
</table>
<br>
<table width=400px align='center'>
	<tr>
	<td> <marquee>SPSE REPORTING - SPSE REPORTING - SPSE REPORTING - SPSE REPORTING  </marquee> </td>
	</tr>
</table>

</body>
