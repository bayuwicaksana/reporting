<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "User Baru"); ?>
<?php include_once("_header.php");?>

<?php

$post_cmd=(isset($_POST['cmd']))?$_POST['cmd']:'';
$post_username=(isset($_POST['username']))?$_POST['username']:'';
$post_password=(isset($_POST['password']))?$_POST['password']:'';
$post_grup=(isset($_POST['grup']))?$_POST['grup']:'';









?>

<body>
<form action="./daftaruser.php" method="POST">
			<table style="border-collapse: separate; border-spacing: 3px 3px;">
				<tr>
					<td><font face="arial"  size=2px> User Name</font> </td>
					<td> &nbsp; : &nbsp;</td>
					<td> <input name="username" title="username" class="form-control" value="" size="30" maxlength="2048" />
					<input type="hidden" name="cmd" value="0"></td>
				</tr>
				<tr>
					<td><font face="arial" size=2px> Password</font> </td>
					<td> &nbsp; : &nbsp; </td>
					<td> <input name="password" title="password" class="form-control" value="" size="30" maxlength="2048" /></td>
				</tr>
				<tr>
					<td><font face="arial" size=2px> Group</font></td>
					<td> &nbsp; : &nbsp; </td>
					<td> 
						<select name="grup" id="grup" class="form-control" >
							<option value="ADMIN">ADMIN</option>
							<option value="ULP">ULP</option>
							<option value="NONULP">NON ULP</option>
						</select>
					</td>
				</tr>
				<tr>
					<td> </td>
					<td> </td>
					<td> <input type="submit" name="insert" value="Simpan" class="btn btn-primary" /> &nbsp; <INPUT TYPE="button" value="Batal" onClick="parent.location='daftaruser.php'" class="btn btn-primary"> </td>
				</tr>
				
				
		
			</table>
</body>