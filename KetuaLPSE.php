<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Ketua LPSE"); ?>
<?php include_once("_header.php");?>

<?php
$post_cmd=(isset($_POST['cmd']))?$_POST['cmd']:'';
$post_nip=(isset($_POST['nip']))?$_POST['nip']:'';
$post_nama=(isset($_POST['nama']))?$_POST['nama']:'';


if ($post_cmd=='1') {
	if ($post_nip!='') echo UpdateData($post_nip,$post_nama);
}

if($post_cmd=='0'){
	if ($post_nip!='') echo InsertData($post_nip,$post_nama);
}

if($post_cmd=='3'){
	if ($post_nip!='') echo DeleteData($post_nip);
}

function InsertData($nip = '', $nama='')
{
	$DB = new DBPDO();
	try 
	{
		//cek apakah nip sudah ada
		$sql="select * from ref_ketua_lpse where nip='".$nip."'";
		$data=$DB->fetchAll($sql);
		
		if (count($data)>0)
			echo "NIP sudah ada";
		else
		{
			$sql="Insert into ref_ketua_lpse values('".$nip."', '".$nama."')";
			$DB->execute($sql);
			
		}	
	} 
	catch (Exception $e) 
	{
		echo $e;
	}
}

function UpdateData($nip = '', $nama='')
{
	$DB = new DBPDO();
	try 
	{
			$sql="update ref_ketua_lpse set nama='".$nama."' where nip='".$nip."' ";
			
			$DB->execute($sql);
			
			
	} 
	catch (Exception $e) 
	{
		echo $e;
	}
}

function DeleteData($nip = '')
{
	$DB = new DBPDO();
	try 
	{
			$sql="delete from ref_ketua_lpse where nip='".$nip."' ";
			
			$DB->execute($sql);
			
			
	} 
	catch (Exception $e) 
	{
		echo $e;
	}
}


$sql="select * from ref_ketua_lpse ";
$data=$DB->fetchall($sql);
			
?>

<body>
<div class="box">
<div class="box-body">
			<table class="table table-bordered">
			<tbody>
				<tr>
					<td><font face="arial"  size=2px> NIP</font> </td>
					<td><font face="arial"  size=2px> Nama</font> </td>
					<td><font face="arial"  size=2px> </font> </td>
					
				</tr>
				
				<?php if(count($data)>0)
				{ 
				for($i=0;$i<count($data);$i++){
				?>
					<tr>
					<td><font face="arial"  size=2px><?php echo $data[$i]['nip'];?></td>
					<td><font face="arial"  size=2px><?php echo $data[$i]['nama'];?></td>
					<td><font face="arial"  size=2px>
						<a href="editketualpse.php?nip=<?php echo $data[$i]['nip'];?>"> <img src="images/updateicon.jpg" alt="Edit" width="20" height="20"> </a>
						<a href="deleteketualpse.php?nip=<?php echo $data[$i]['nip'];?>"><img src="images/deleteicon.jpg" alt="Hapus" width="20" height="20"></a>
					</td>
				<?php }
				}
					?>
				
			</tbody>
			</table>
</div>
&nbsp; &nbsp; <INPUT TYPE="button" value="Tambah" onClick="parent.location='addketualpse.php'" class="btn btn-primary">
<br>
&nbsp;
</div>
</body>