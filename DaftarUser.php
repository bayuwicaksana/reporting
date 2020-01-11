<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Daftar User"); ?>
<?php include_once("_header.php");?>

<?php
$post_cmd=(isset($_POST['cmd']))?$_POST['cmd']:'';
$post_username=(isset($_POST['username']))?$_POST['username']:'';
$post_password=(isset($_POST['password']))?$_POST['password']:'';
$post_grup=(isset($_POST['grup']))?$_POST['grup']:'';


if ($post_cmd=='1') {
	if ($post_username!='') echo UpdateData($post_username,$post_password,$post_grup);
}

if($post_cmd=='0'){
	if ($post_username!='') echo InsertData($post_username,$post_password,$post_grup);
}

if($post_cmd=='3'){
	if ($post_username!='') echo DeleteData($post_username);
}

function InsertData($username = '', $pass = '', $grup='')
{
	$DB = new DBPDO();
	try 
	{
		//cek apakah username sudah ada
		$sql="select * from datauser where username='".$username."'";
		$data=$DB->fetchAll($sql);
		
		if (count($data)>0)
			echo "User Name sudah ada";
		else
		{
			$sql="Insert into datauser(Username,password,user_group) values('".$username."', '".$pass."','".$grup."')";
			$DB->execute($sql);
			
		}	
	} 
	catch (Exception $e) 
	{
		echo $e;
	}
}

function UpdateData($username = '', $pass = '', $grup='')
{
	$DB = new DBPDO();
	try 
	{
			$sql="update datauser set password='".$pass."', user_group='".$grup."' where username='".$username."' ";
			
			$DB->execute($sql);
			
			
	} 
	catch (Exception $e) 
	{
		echo $e;
	}
}

function DeleteData($username = '')
{
	$DB = new DBPDO();
	try 
	{
			$sql="delete from datauser where username='".$username."' ";
			
			$DB->execute($sql);
			
			
	} 
	catch (Exception $e) 
	{
		echo $e;
	}
}


$sql="select * from datauser ";
$data=$DB->fetchall($sql);
			
?>

<body>
<div class="box">
<div class="box-body">
			<table class="table table-bordered">
			<tbody>
				<tr>
					<td><font face="arial"  size=2px> User Name</font> </td>
					<td><font face="arial"  size=2px> Password</font> </td>
					<td><font face="arial"  size=2px> Group</font> </td>
					<td><font face="arial"  size=2px> </font> </td>
					
				</tr>
				
				<?php if(count($data)>0)
				{ 
				for($i=0;$i<count($data);$i++){
				?>
					<tr>
					<td><font face="arial"  size=2px><?php echo $data[$i]['username'];?></td>
					<td><font face="arial"  size=2px><?php echo $data[$i]['password'];?></td>
					<td><font face="arial"  size=2px><?php echo $data[$i]['user_group'];?></td>
					<td><font face="arial"  size=2px>
						<a href="edituser.php?un=<?php echo $data[$i]['username'];?>"> <img src="images/updateicon.jpg" alt="Edit" width="20" height="20"> </a>
						<a href="deleteuser.php?un=<?php echo $data[$i]['username'];?>"><img src="images/deleteicon.jpg" alt="Hapus" width="20" height="20"></a>
					</td>
				<?php }
				}
					?>
				
			</tbody>
			</table>
</div>
&nbsp; &nbsp; <INPUT TYPE="button" value="User Baru" onClick="parent.location='adduser.php'" class="btn btn-primary">
<br>
&nbsp;
</div>
</body>