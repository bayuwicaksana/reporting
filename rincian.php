<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Rincian Paket ".date("Y")); ?>
<?php include_once("_header.php");?>

<?php 

$post_namapaket = (isset($_POST['pg_namapaket']))?$_POST['pg_namapaket']:'';
$post_statusproses = (isset($_POST['statusproses']))?$_POST['statusproses']:'';
$post_tahunlelang = (isset($_POST['tahunlelang']))?$_POST['tahunlelang']:date("Y");
$post_bulanlelang = (isset($_POST['bulanlelang']))?$_POST['bulanlelang']:'';
$post_namastk = (isset($_POST['SatuanKerja']))?$_POST['SatuanKerja']:'';
$post_jnskeg = (isset($_POST['JenisKegiatan']))?$_POST['JenisKegiatan']:'';
$post_namapanitia = (isset($_POST['pg_namapanitia']))?$_POST['pg_namapanitia']:'';
$post_pemenang = (isset($_POST['pg_pemenang']))?$_POST['pg_pemenang']:'';
$post_ppk = (isset($_POST['pg_ppk']))?$_POST['pg_ppk']:'';
$post_nilaifilter = (isset($_POST['nilaifilter']))?$_POST['nilaifilter']:'';
$post_nilaifilterdari = (isset($_POST['nilaifilterdari']))?$_POST['nilaifilterdari']:'';
$post_nilaifiltersampai = (isset($_POST['nilaifiltersampai']))?$_POST['nilaifiltersampai']:'';
$post_lelangdiulang = (isset($_POST['pg_lelangdiulang']))?$_POST['pg_lelangdiulang']:'';
$post_Agency=(isset($_POST['Agency']))?$_POST['Agency']:'Agency';

//v2 - alter TMS 20171216
$sql="select distinct a.kodeagency, b.deskripsi from agency_tahun a inner join ref_agency b on a.kodeagency = b.kodeagency where a.tahun = ". $post_tahunlelang;
$dtAgency=$DB->fetchAll($sql);

//v1
//$sql="select distinct pg_instansi, deskripsi from ref_instansi ";

//v2 - alter TMS 20171216
$sql="select distinct a.pg_instansi, b.deskripsi from instansi_tahun a inner join ref_instansi b on a.pg_instansi = b.pg_instansi where 1=1 ";
if ($post_Agency!="" && $post_Agency!="Agency") {
	$sql.=" and kodeagency in (select pg_audituser from ref_adminagency where kodeagency ='".$post_Agency."')";
}
$sql.=" and a.tahun = ". $post_tahunlelang;
$dtStk=$DB->fetchAll($sql);

$sql = "SELECT datamaster.*, @curRow := @curRow + 1 AS row_number FROM datamaster
		JOIN (SELECT @curRow := 0) r
		WHERE 1=1";

switch($post_nilaifilter)
{
	case 'pg_pagupaket':
		if(!empty($post_nilaifilterdari) && !empty($post_nilaifiltersampai)){
			$sql .= " AND (pg_pagupaket >= ".$post_nilaifilterdari." AND pg_pagupaket <= ".$post_nilaifiltersampai.")";
		}else if(!empty($post_nilaifilterdari)){
			$sql .= " AND pg_pagupaket >= ".$post_nilaifilterdari;
		}else if(!empty($post_nilaifiltersampai)){
			$sql .= " AND pg_pagupaket <= ".$post_nilaifiltersampai;
		}
	break;
	case 'pg_hps':
		if(!empty($post_nilaifilterdari) && !empty($post_nilaifiltersampai)){
			$sql .= " AND (pg_hps >= ".$post_nilaifilterdari." AND pg_hps <= ".$post_nilaifiltersampai.")";
		}else if(!empty($post_nilaifilterdari)){
			$sql .= " AND pg_hps >= ".$post_nilaifilterdari;
		}else if(!empty($post_nilaifiltersampai)){
			$sql .= " AND pg_hps <= ".$post_nilaifiltersampai;
		}
	break;
	case 'pg_nilaikontrak':
		if(!empty($post_nilaifilterdari) && !empty($post_nilaifiltersampai)){
			$sql .= " AND (pg_nilaikontrak >= ".$post_nilaifilterdari." AND pg_nilaikontrak <= ".$post_nilaifiltersampai.")";
		}else if(!empty($post_nilaifilterdari)){
			$sql .= " AND pg_nilaikontrak >= ".$post_nilaifilterdari;
		}else if(!empty($post_nilaifiltersampai)){
			$sql .= " AND pg_nilaikontrak <= ".$post_nilaifiltersampai;
		}
	
	break;
}		
if(!empty($post_namapaket)){
	$sql .= " AND pg_namapaket LIKE '%".$post_namapaket."%'";
}
if(!empty($post_pemenang)){
	$sql .= " AND pg_pemenang LIKE '%".$post_pemenang."%'";
}
if(!empty($post_ppk)){
	$sql .= " AND pg_ppk LIKE '%".$post_ppk."%'";
}
if(!empty($post_namastk)){
	$sql .= " AND pg_namastk LIKE '%".$post_namastk."%'";
}
if(!empty($post_lelangdiulang)){
	$sql .= " AND pg_lelangdiulang LIKE '%".$post_lelangdiulang."%'";
}
if(!empty($post_namapanitia)){
	$sql .= " AND pg_namapanitia LIKE '%".$post_namapanitia."%'";
}
if(!empty($post_statusproses)){
	$sql .= " AND statusproses = '".$post_statusproses."'";
}
if(!empty($post_jnskeg)){
	$sql .= " AND pg_jeniskegiatan = '".$post_jnskeg."'";
}
if(!empty($post_tahunlelang)){
	$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$post_tahunlelang."'";
}
if(!empty($post_bulanlelang)){
	$sql .= " AND MONTH(datamaster.pg_tanggalawallelang) = '".$post_bulanlelang."'";
}
$data = $DB->fetchAll($sql);
				
$data_statusproses = $DB->fetchAll("SELECT datamaster.statusproses FROM datamaster GROUP BY datamaster.statusproses");					
$data_tahunlelang = $DB->fetchAll("SELECT YEAR(datamaster.pg_tanggalawallelang) AS tahunlelang FROM datamaster 
								WHERE YEAR(datamaster.pg_tanggalawallelang) != '0'
								GROUP BY YEAR(datamaster.pg_tanggalawallelang)
								ORDER BY YEAR(datamaster.pg_tanggalawallelang) ASC");					
$data_bulan = $DB->fetchAll("SELECT months.* FROM months");	
$data_jeniskegiatan = $DB->fetchAll("SELECT distinct pg_jeniskegiatan FROM datamaster");				

?>
<div class="row">
	<div class="col-xs-12">
	  <div class="box">
		<div class="box-header">
		  <h3 class="box-title"><?php echo TITLE_ACTIVE;?></h3>
		  <div class="box-tools">
			
		  </div>
		</div><!-- /.box-header -->
		<div class="box-body">
			<form action="./rincian.php" method="POST">
			<div class="row margin-bottom">
				<div class="col-sm-2">
					<input type="text" class="form-control" name="pg_namapaket" id="pg_namapaket" placeholder="Nama Paket" value="<?php echo $post_namapaket;?>"/>
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
						<option value="<?php echo $dtStk[$i]['deskripsi'];?>" <?php echo ($dtStk[$i]['deskripsi'] == $post_namastk)?'selected="selected"':'';?>><?php echo $dtStk[$i]['deskripsi'];?></option>
						<?php } 
						} ?>
					</select>
				</div>
				<div class="col-sm-2">
					<input type="text" class="form-control" name="pg_namapanitia" id="pg_namapanitia" placeholder="Nama Kepanitiaan" value="<?php echo $post_namapanitia;?>"/>
				</div>
				<div class="col-sm-2">
					<select name="statusproses" id="statusproses" class="form-control">
						<option value="">Status Proses</option>
						<?php if(count($data_statusproses)>0)
						{ 
						$isLelangUlang = 0;
						for($i=0;$i<count($data_statusproses);$i++){
							?>
						<option value="<?php echo $data_statusproses[$i]['statusproses'];?>" <?php echo ($data_statusproses[$i]['statusproses'] == $post_statusproses)?'selected="selected"':'';?>><?php echo ucfirst(strtolower($data_statusproses[$i]['statusproses']));?></option>
						<?php 
							if (strtolower($data_statusproses[$i]['statusproses']) == 'lelang ulang') {
								$isLelangUlang = 1;
								}
							} 
						if ($isLelangUlang == 0) {
						?>
						<option value="Lelang ulang">Lelang ulang</option>
						<?php
							}
						} ?>
					</select>
				</div>
				<div class="col-sm-2">
					<select name="tahunlelang" id="tahunlelang" class="form-control" onchange="this.form.submit();">
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
			</div>
			<div class="row margin-bottom">
				<div class="col-sm-2">
					<select name="bulanlelang" id="bulanlelang" class="form-control">
						<option value="">Bulan Lelang</option>
						<?php if(count($data_bulan)>0)
						{ 
						for($i=0;$i<count($data_bulan);$i++){
							?>
						<option value="<?php echo $data_bulan[$i]['months'];?>" <?php echo ($data_bulan[$i]['name'] == $post_bulanlelang)?'selected="selected"':'';?>><?php echo $data_bulan[$i]['name'];?></option>
						<?php } 
						} ?>
					</select>
				</div>
				<div class="col-sm-2">
					<input type="text" class="form-control" name="pg_pemenang" id="pg_pemenang" placeholder="Pemenang" value="<?php echo $post_pemenang;?>"/>
				</div>
				<div class="col-sm-2">
					<input type="text" class="form-control" name="pg_ppk" id="pg_ppk" placeholder="PPK" value="<?php echo $post_ppk;?>"/>
				</div>
				<div class="col-sm-2">
					<input type="text" class="form-control" name="pg_lelangdiulang" id="pg_lelangdiulang" placeholder="Ket Lelang Ulang" value="<?php echo $post_ppk;?>"/>
				</div>
				<div class="col-sm-2">
					<?php 
					$data_nilaifilter = array(
											array('id' => 'pg_pagupaket', 'name' => 'Pagu'),
											array('id' => 'pg_hps', 'name' => 'HPS'),
											array('id' => 'pg_nilaikontrak', 'name' => 'Nilai Kontrak')
										);
					?>
					<select name="nilaifilter" id="nilaifilter" class="form-control">
						<option value="">Nilai</option>
						<?php if(count($data_nilaifilter)>0)
						{ 
						for($i=0;$i<count($data_nilaifilter);$i++){
							?>
						<option value="<?php echo $data_nilaifilter[$i]['id'];?>" <?php echo ($data_nilaifilter[$i]['id'] == $post_nilaifilter)?'selected="selected"':'';?>><?php echo $data_nilaifilter[$i]['name'];?></option>
						<?php } 
						} ?>
					</select>
				</div>
				<div class="col-sm-2">
					<select name="JenisKegiatan" id="JenisKegiatan" class="form-control">
						<option value="">Jenis Kegiatan</option>
						<?php if(count($data_jeniskegiatan)>0)
						{ 
						for($i=0;$i<count($data_jeniskegiatan);$i++){
							?>
						<option value="<?php echo $data_jeniskegiatan[$i]['pg_jeniskegiatan'];?>" <?php echo ($data_jeniskegiatan[$i]['pg_jeniskegiatan'] == $post_jnskeg)?'selected="selected"':'';?>><?php echo $data_jeniskegiatan[$i]['pg_jeniskegiatan'];?></option>
						<?php } 
						} ?>
					</select>
				</div>
			</div>
			<div class="row margin-bottom">
				<div class="col-sm-2">
					<input type="text" class="form-control" name="nilaifilterdari" id="nilaifilterdari" placeholder="Dari" value="<?php echo $post_nilaifilterdari;?>"/>
				</div>
				<div class="col-sm-2">
					<input type="text" class="form-control" name="nilaifiltersampai" id="nilaifiltersampai" placeholder="Sampai Dengan" value="<?php echo $post_nilaifiltersampai;?>"/>
				</div>
				<div class="col-sm-2">
					<input type="submit" name="search" value="Search" class="btn btn-primary" />
					<input type="button" id="rst" value="Reset" class="btn btn-info" />
				</div>
				<!--
				<div class="col-sm-2">
					<input type="button" id="export" value="Export" class="btn btn-primary" onclick="openWin(' <?php echo "export_index.php?str=" . urlencode(serialize($data)) . ""; ?> ')" />
				</div>
				-->
			</div>
			</form>

<script type='text/javascript'>
//<![CDATA[
$(function() {
	$("#rst").on('click', function() {
		window.location.href='rincian.php';
	});
});
</script>
<script>
function openWin(url) {
    window.open(url);
}
</script>
			
			<?php 
			$array_column = array(
				array('id' => 'statusproses', 'value' => 'Status Proses'),
				array('id' => 'pg_namapaket', 'value' => 'Nama Paket'),
			);
			?>
			<div class="row">
				<div class="col-sm-12">
					<div class="btn-group">
					  <button class="btn btn-info" type="button">Pilih Kolom</button>
					  <button data-toggle="dropdown" class="btn btn-info dropdown-toggle" type="button">
						<span class="caret"></span>
						<span class="sr-only">Toggle Dropdown</span>
					  </button>
					  <ul role="menu" class="dropdown-menu">
						<li><a href="javascript:void(0);" onclick="select_column('statusproses');"><i id="check_statusproses" class="fa fa-fw fa-check-square"></i> Status</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_namapaket');"><i id="check_pg_namapaket" class="fa fa-fw fa-check-square"></i> Nama Paket</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_namastk');"><i id="check_pg_namastk" class="fa fa-fw fa-check-square"></i> SKPD</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_namapanitia');"><i id="check_pg_namapanitia" class="fa fa-fw fa-check-square"></i> Nama Kepanitiaan</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_tanggalawallelang');"><i id="check_pg_tanggalawallelang" class="fa fa-fw fa-check-square"></i> Mulai</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_tanggalakhirlelang');"><i id="check_pg_tanggalakhirlelang" class="fa fa-fw"></i> Selesai</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_pagupaket');"><i id="check_pg_pagupaket" class="fa fa-fw fa-check-square"></i> Pagu Anggaran</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_hps');"><i id="check_pg_hps" class="fa fa-fw fa-check-square"></i> HPS</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_lelangdiulang');"><i id="check_pg_lelangdiulang" class="fa fa-fw"></i> Keterangan Lelang Ulang</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_ppk');"><i id="check_pg_ppk" class="fa fa-fw fa-check-square"></i> PPK</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_pemenang');"><i id="check_pg_pemenang" class="fa fa-fw fa-check-square"></i> Pemenang</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_nilaikontrak');"><i id="check_pg_nilaikontrak" class="fa fa-fw fa-check-square"></i> Nilai Kontrak</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_bulan');"><i id="check_pg_bulan" class="fa fa-fw"></i> Bulan</a></li>
						<li><a href="javascript:void(0);" onclick="select_column('pg_audituser');"><i id="check_pg_audituser" class="fa fa-fw"></i> Agency</a></li>
					  </ul>
					</div>
				</div>
				<script type="text/javascript">
					function select_column(column){
						var status = $('#check_'+column).hasClass('fa-check-square');
						if(status == false){
							$('#check_'+column).addClass('fa-check-square');
							$('.td_'+column).removeClass('hidden').show();
						}else{
							$('#check_'+column).removeClass('fa-check-square');
							$('.td_'+column).addClass('hidden').hide();
						}
					}
				</script>
			</div>
			<!-- <div class="scrollmenu"> <!-- use own css -->
			<div style="width: 100%; height: 250px; overflow-y: scroll;">
				<table id="datatables" class="table table-bordered table-hover table-responsive">
					<thead>
						<tr>
							<th class="td_row_number">No.</th>
							<th class="td_statusproses">Status</th>
							<th class="td_pg_namapaket">Nama Paket</th>
							<th class="td_pg_namastk">SKPD</th>
							<th class="td_pg_namapanitia">Nama Kepanitiaan</th>
							<th class="td_pg_tanggalawallelang">Mulai</th>
							<th class="td_pg_tanggalakhirlelang hidden">Selesai</th>
							<th class="td_pg_pagupaket">Pagu Anggaran</th>
							<th class="td_pg_jeniskegiatan">Kategori Lelang</th>
							<th class="td_pg_hps">HPS</th>
							<th class="td_pg_lelangdiulang hidden">Keterangan Lelang Ulang</th>
							<th class="td_pg_ppk">PPK</th>
							<th class="td_pg_pemenang">Pemenang</th>
							<th class="td_pg_nilaikontrak">Nilai Kontrak</th>
							<th class="td_pg_efisiensi">Efisiensi</th>
							<th class="td_pg_bulan hidden">Bulan</th>
							<th class="td_pg_audituser hidden">Agency</th>
						</tr>
					</thead>
					<tbody>
						<?php if(count($data)>0)
						{ 
							for($i=0;$i<count($data);$i++){
								switch(strtolower($data[$i]['statusproses'])){
									case 'selesai':$status_color='bg-green';break;
									case 'proses':$status_color='bg-yellow';break;
									default:$status_color='bg-red';break;
								}
						?>
						<tr>
							<td class="td_row_number"><?php echo $data[$i]['row_number'];?></td>
							<td class="td_statusproses"><span class="badge <?php echo $status_color;?>"><?php echo $data[$i]['statusproses'];?></span></td>
							<td class="td_pg_namapaket"><?php echo $data[$i]['pg_namapaket'];?></td>
							<td class="td_pg_namastk"><?php echo $data[$i]['pg_namastk'];?></td>
							<td class="td_pg_namapanitia"><?php echo $data[$i]['pg_namapanitia'];?></td>
							<td class="td_pg_tanggalawallelang"><?php $d = $data[$i]['pg_tanggalawallelang'];echo date( 'd/m/Y H:i', strtotime($d));?></td>
							<td class="td_pg_tanggalakhirlelang hidden"><?php $d = $data[$i]['pg_tanggalakhirlelang'];echo date( 'd/m/Y H:i', strtotime($d));?></td>
							<td class="td_pg_pagupaket"><?php $d = $data[$i]['pg_pagupaket'];echo 'Rp. '.number_format($d);?></td>
							<td class="td_pg_jeniskegiatan"><?php $d = $data[$i]['pg_jeniskegiatan'];echo $d;?></td>
							<td class="td_pg_hps"><?php $d = $data[$i]['pg_hps'];echo 'Rp. '.number_format($d);?></td>
							<td class="td_pg_lelangdiulang hidden"><?php echo $data[$i]['pg_lelangdiulang'];?></td>
							<td class="td_pg_ppk"><?php echo $data[$i]['pg_ppk'];?></td>
							<td class="td_pg_pemenang"><?php echo $data[$i]['pg_pemenang'];?></td>
							<td class="td_pg_nilaikontrak"><?php $d = $data[$i]['pg_nilaikontrak'];echo 'Rp. '.number_format($d);?></td>
							<td class="td_pg_efisiensi"><?php $d = $data[$i]['pg_pagupaket'] - $data[$i]['pg_nilaikontrak'];echo 'Rp. '.number_format($d);?></td>
							<td class="td_pg_bulan hidden"><?php $d = $data[$i]['pg_tanggalawallelang'];echo date( 'm', strtotime($d));?></td>
							<td class="td_pg_audituser hidden"><?php echo $data[$i]['pg_audituser'];?></td>
						</tr>
						<?php 
							}
						} ?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="15"></th>
						</tr>
					</tfoot>
				</table>
			</div> <!-- use own css -->
		</div><!-- /.box-body -->
	  </div><!-- /.box -->
	</div><!-- /.col -->
</div><!-- /.row -->
<?php include_once("_footer.php");?>
