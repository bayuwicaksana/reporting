<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Tambah Ketua LPSE"); ?>
<?php include_once("_header.php");?>

<?php

$post_cmd=(isset($_POST['cmd']))?$_POST['cmd']:'';
$post_nip=(isset($_POST['nip']))?$_POST['nip']:'';
$post_nama=(isset($_POST['nama']))?$_POST['nama']:'';

?>

<body>
<form action="./KetuaLPSE.php" method="POST">
			<table style="border-collapse: separate; border-spacing: 3px 3px;">
				<tr>
					<td><font face="arial"  size=2px> NIP</font> </td>
					<td> &nbsp; : &nbsp;</td>
					<td> <input name="nip" title="nip" class="form-control" value="" size="30" maxlength="2048" />
					<input type="hidden" name="cmd" value="0"></td>
				</tr>
				<tr>
					<td><font face="arial" size=2px> Nama</font> </td>
					<td> &nbsp; : &nbsp; </td>
					<td> <input name="nama" title="nama" class="form-control" value="" size="30" maxlength="2048" /></td>
				</tr>
				<tr>
					<td> </td>
					<td> </td>
					<td> <input type="submit" name="insert" value="Simpan" class="btn btn-primary" /> &nbsp; <INPUT TYPE="button" value="Batal" onClick="parent.location='KetuaLPSE.php'" class="btn btn-primary"> </td>
				</tr>
				
				
		
			</table>
</body>