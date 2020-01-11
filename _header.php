<?php include_once("connection.php");
//session_start();
//$user_check=$_SESSION['login_user'];
$user_check='admin';
 
$sql="SELECT username,password,user_group FROM datauser WHERE username='$user_check' ";
$row=$DB->fetchAll($sql);
 
$login_session=$row[0]['username'];
$user_group=$row[0]['user_group'];

if(!isset($login_session))
{
header("Location: login.php");
}
?>
<!DOCTYPE html>
<?php
$prosesdata = $DB->fetchAll("select max(tanggalprosesdata) as x from datamaster");
?>

<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>APLIKASI REPORTING PENGADAAN BARANG/JASA</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="css/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/iCheck/flat/blue.css">
    <!-- Morris chart -->
    <link rel="stylesheet" href="plugins/morris/morris.css">
    <!-- jvectormap -->
    <link rel="stylesheet" href="plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <!-- Date Picker -->
    <link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
    <!-- bootstrap wysihtml5 - text editor -->
    <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
	
	<!-- own css -->
	<style>
	div.scrollmenu {
		background-color: #fff;
		overflow: auto;
		white-space: nowrap;
	}

	div.scrollmenu a {
		display: inline-block;
		color: white;
		text-align: center;
		padding: 14px;
		text-decoration: none;
	}

	div.scrollmenu a:hover {
		background-color: #fff;
	}
	</style>
	<!-- own css -->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="js/html5shiv.min.js"></script>
		<script src="js/respond.min.js"></script>
    <![endif]-->
	    <!-- jQuery 2.1.4 -->
    <script src="plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="js/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Bootstrap 3.3.5 -->
    <script src="bootstrap/js/bootstrap.min.js"></script>

  </head>
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

      <header class="main-header">
        <!-- Logo -->
        <a href="index.php" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini">LP<B>SE</B></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg">LP<B>SE</B></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
				<span class="hidden-xs">
				  <img src="dist/img/lpse-bogor-lite.png" height="50" width="150">
				</span>
                  <!--<span class="hidden-xs"><font size="5">APLIKASI REPORTING PENGADAAN BARANG/JASA</font></span>-->
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <!--<img src="dist/img/pemkot-bogor.png" class="img-circle" alt="User Image">-->
                    <p><font size="1">APLIKASI REPORTING PENGADAAN BARANG/JASA</font></p>
                  </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="#" class="btn btn-default btn-flat">Profile</a>
                    </div>
                    <div class="pull-right">
                      <a href="logout.php" class="btn btn-default btn-flat">Sign out</a>
                    </div>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
              <li>
                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
              </li>
            </ul>
			
          </div>
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="dist/img/pemkot-bogor-2.png" width=15 height=15 class="" alt="User Image">
			  <!-- &nbsp;
			  <br/><br/><br/> -->
            </div>
            <div class="pull-left info">
              <font size="1">BAGIAN ADMINISTRASI<BR>PENGENDALIAN PEMBANGUNAN<BR>SEKRETARIAT DAERAH<BR>KOTA BOGOR</font>
              <a href="#"><i class=""></i>&nbsp;</a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">NAVIGASI UTAMA</li>
			<li class="header"><font color="white">Sinkronisasi Terakhir: <BR><?php echo $prosesdata[0]['x']; ?></font></li>
			<li class="active treeview">
              <a href="#">
                <i class="fa fa-pie-chart"></i>
                <span>Laporan</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="rekapitulasi_paket.php"><i class="fa fa-circle-o"></i> Rekapitulasi</a></li>
                <li><a href="rincian.php"><i class="fa fa-circle-o"></i> Rincian Paket</a></li>
                <li><a href="rekap_lelang.php"><i class="fa fa-circle-o"></i> Rekap Paket Lelang</a></li>
                <li><a href="daftar_lelang.php"><i class="fa fa-circle-o"></i> Daftar Paket Lelang</a></li>
				<li><a href="kumulatif_paket.php"><i class="fa fa-circle-o"></i> Rekap Paket SPSE Kota Bogor</a></li>
				<li><a href="progres_kumulatif.php"><i class="fa fa-circle-o"></i> Progres Kumulatif</a></li>
				<li><a href="existing_bogor.php"><i class="fa fa-circle-o"></i> Existing Bogor</a></li>
				<?php if ($user_group == 'ULP') { ?>
				<li><a href="rekap_penyedia.php"><i class="fa fa-circle-o"></i> Rekapitulasi Penyedia</a></li>
				<?php } ?>
              </ul>
            </li>
			<?php if ($user_group == 'ADMIN') { ?>
			<li class="treeview">
              <a href="#">
                <i class="fa fa-pie-chart"></i>
                <span>Bantuan</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li><a href="update_data_all.php"><i class="fa fa-circle-o"></i> Pengkinian Semua Data</a></li>
                <li><a href="update_data.php"><i class="fa fa-circle-o"></i> Pengkinian Tahun Berjalan</a></li>
                <li><a href="DaftarUser.php"><i class="fa fa-circle-o"></i> Manajemen User</a></li>
                <li><a href="KetuaLPSE.php"><i class="fa fa-circle-o"></i> Ketua LPSE</a></li>
              </ul>
            </li>
			<?php } ?>
          </ul>
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            <?php echo TITLE_ACTIVE;?>
          </h1>
          <ol class="breadcrumb">
            <li><a href="rekapitulasi_paket.php"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active"><?php echo TITLE_ACTIVE;?></li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">