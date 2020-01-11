<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Edit Data User"); ?>
<?php include_once("_header.php");?>

<?php

$get_username=(isset($_GET['un']))?$_GET['un']:'';

$post_cmd=(isset($_POST['cmd']))?$_POST['cmd']:'';


if ($get_username!='') {
	
	$sql="select * from datauser where username='".$get_username."'";
	$data=$DB->fetchAll($sql);
	
	if (count($data)>0){
		$username=$data[0]['username'];
		$password=$data[0]['password'];
		$user_group=$data[0]['user_group'];
	
	
	}
}

?>

<body>
<form action="./daftaruser.php" method="POST">
			<table style="border-collapse: separate; border-spacing: 3px 3px;">
				<tr>
					<td><font face="arial"  size=2px> User Name</font> </td>
					<td> &nbsp; : &nbsp;</td>
					<td> <input name="username" title="username" class="form-control" readonly value="<?php echo $username ?>" size="30" maxlength="2048" />
						<input type="hidden" name="cmd" value="1">
					</td>
				</tr>
				<tr>
					<td><font face="arial" size=2px> Password</font> </td>
					<td> &nbsp; : &nbsp; </td>
					<td> <input name="password" title="password" class="form-control" value="<?php echo $password ?>" size="30" maxlength="2048" /></td>
				</tr>
				<tr>
					<td><font face="arial" size=2px> Group</font></td>
					<td> &nbsp; : &nbsp; </td>
					<td> 
						<select name="grup" id="grup" class="form-control" >
							<option value="ADMIN" <?php if ($user_group=='ADMIN') echo 'selected="selected"' ?>>ADMIN</option>
							<option value="ULP" <?php if ($user_group=='ULP') echo 'selected="selected"' ?>>ULP</option>
							<option value="NONULP" <?php if ($user_group=='NONULP') echo 'selected="selected"' ?>>NON ULP</option>
						</select>
					</td>
				</tr>
				<tr>
					<td> </td>
					<td> </td>
					<td> <input type="submit" name="insert" value="Simpan" class="btn btn-primary" /> &nbsp; <INPUT TYPE="button" value="Batal" onClick="parent.location='daftaruser.php'" class="btn btn-primary"></td>
				</tr>
				
				
		
			</table>
</body>