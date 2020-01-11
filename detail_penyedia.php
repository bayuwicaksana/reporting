<?php include_once("connection.php");?>
<?php DEFINE("TITLE_ACTIVE", "Detail Penyedia"); ?>
<?php include_once("_header.php");?>
<?php 
$qs_namapemenang = $_GET['pemenang'];
$qs_tahun = $_GET['tahun'];

$sql = "SELECT pg_namapaket, pg_namastk, pg_nilaikontrak, pg_tanggalakhirlelang FROM datamaster WHERE 1=1 ";
				
$sql .= " AND pg_pemenang = '".$qs_namapemenang."' ";

$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$qs_tahun."' ";

$data_total = $DB->fetchAll($sql);

//echo ($sql);
?>
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
	<div class="col-md-13">
	  <div class="box">
		<div class="box-header with-border">
		  <h3 class="box-title">Detail Penyedia : <?php echo $qs_namapemenang; ?> </h3>
		</div><!-- /.box-header -->
		<div class="box-body">
		
		  <table class="table table-bordered">
			<tbody>
			<tr>
			  <th style="width: 10px">#</th>
			  <th >Nama Paket</th>
			  <th >SKPD</th>
			  <th >Nilai Kontrak</th>
			  <th >Selesai Lelang</th>
			</tr>
			<?php if(count($data_total)>0){
				$nomer = 0;
				for($i=0;$i<count($data_total);$i++){ 
					$nomer++;
			?>
			<tr>
			  <td><?php echo $nomer;?>.</td>
			  <td><?php echo $data_total[$i]['pg_namapaket'];?></td>
			  <td><?php echo $data_total[$i]['pg_namastk'];?></td>
			  <td><?php echo number_format($data_total[$i]['pg_nilaikontrak']);?></td>
			  <td><?php echo $data_total[$i]['pg_tanggalakhirlelang'];?></td>
			</tr>
			<?php }
			} ?>
		  </tbody>
		</table>
		</div><!-- /.box-body -->
	  </div><!-- /.box -->
  </div>
  
<?php include_once("_footer.php");?>