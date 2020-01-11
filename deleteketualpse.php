<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Hapus Data User"); ?>
<?php include_once("_header.php");?>

<?php

$get_nip=(isset($_GET['nip']))?$_GET['nip']:'';

$post_cmd=(isset($_POST['cmd']))?$_POST['cmd']:'';

if ($get_nip!='') {
	
	$sql="select * from ref_ketua_lpse where nip='".$get_nip."'";
	$data=$DB->fetchAll($sql);
	
	if (count($data)>0){
		$nip=$data[0]['nip'];
		$nama=$data[0]['nama'];
	
	
	}
}

?>

<body>
<form action="./KetuaLPSE.php" method="POST">
			Apakah data berikut akan dihapus? 

			<table style="border-collapse: separate; border-spacing: 3px 3px;">
				<tr>
					<td><font face="arial"  size=2px> NIP</font> </td>
					<td> &nbsp; : &nbsp;</td>
					<td> <input name="nip" title="nip" class="form-control" readonly value="<?php echo $nip ?>" size="30" maxlength="2048" />
						<input type="hidden" name="cmd" value="3">
					</td>
				</tr>
				<tr>
					<td><font face="arial" size=2px> Nama</font> </td>
					<td> &nbsp; : &nbsp; </td>
					<td> <input name="nama" title="nama" class="form-control" readonly value="<?php echo $nama ?>" size="30" maxlength="2048" /></td>
				</tr>
				<tr>
					<td> </td>
					<td> </td>
					<td> <input type="submit" name="submit" value="Hapus" class="btn btn-primary" /> &nbsp; <INPUT TYPE="button" value="Batal" onClick="parent.location='KetuaLPSE.php'" class="btn btn-primary"> </td>
				</tr>
				
				
		
			</table>
</body>