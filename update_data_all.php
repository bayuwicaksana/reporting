<?php 
DEFINE("TITLE_ACTIVE", "Pengkinian Data"); 
define('PGHOST','202.159.24.54');
define('PGPORT',5432);
define('PGDATABASE','epns_prod');
define('PGUSER', 'smsbogor');
define('PGPASSWORD', 'smsbogor');

include_once("_header.php");
include_once("connection.php");
?>
<?php 

$mode = (isset($_GET['mode']))?$_GET['mode']:'';
$post_kodepaket = (isset($_POST['pg_kodepaket']))?$_POST['pg_kodepaket']:'';

$pg_sql = "select distinct paket.pkt_id, pkt_nama, stk_nama, 
	(select peg.peg_nama from anggota_panitia ap inner join pegawai peg on ap.peg_id = peg.peg_id where ap.agp_jabatan = 'K' and ap.pnt_id = paket.pnt_id) as ketua, 
	(select peg.peg_nama from anggota_panitia ap inner join pegawai peg on ap.peg_id = peg.peg_id where ap.agp_jabatan = 'S' and ap.pnt_id = paket.pnt_id) as sekretaris, 
	(select peg.peg_nama from anggota_panitia ap inner join pegawai peg on ap.peg_id = peg.peg_id where ap.agp_jabatan = 'A' and ap.pnt_id = paket.pnt_id limit 1 offset 0) as anggota1,
	(select peg.peg_nama from anggota_panitia ap inner join pegawai peg on ap.peg_id = peg.peg_id where ap.agp_jabatan = 'A' and ap.pnt_id = paket.pnt_id limit 1 offset 1) as anggota2,
	(select peg.peg_nama from anggota_panitia ap inner join pegawai peg on ap.peg_id = peg.peg_id where ap.agp_jabatan = 'A' and ap.pnt_id = paket.pnt_id limit 1 offset 2) as anggota3,
	pnt_no_sk, pnt_nama, 
	jadwal.dtj_tglawal, j.dtj_tglakhir, pkt_pagu, 
	(case kgr_id
		when 0 then 'Pengadaan Barang' 
		when 1 then 'Jasa Konsultansi'
		when 2 then 'Pekerjaan Konstruksi'
		else 'Jasa Lainnya'
	end) as kgr_nama,
	pkt_hps, coalesce(lls_diulang_karena, '') lls_diulang_karena,  p.peg_nama as ppk, 
	(
	select distinct coalesce(rkn_nama, '') rkn_nama
	from peserta
	left join rekanan on peserta.rkn_id = rekanan.rkn_id 
	left join nilai_evaluasi on nilai_evaluasi.psr_id = peserta.psr_id
	left join evaluasi on nilai_evaluasi.eva_id = evaluasi.eva_id
	where is_pemenang=1 and eva_jenis = 3 and peserta.lls_id = lelang_seleksi.lls_id and peserta.auditupdate in 
	(
	select p.auditupdate from peserta p where p.lls_id = lelang_seleksi.lls_id order by p.auditupdate desc limit 1
	)
	and evaluasi.eva_versi in 
	(
	select e.eva_versi from evaluasi e where e.lls_id = lelang_seleksi.lls_id order by e.eva_versi desc limit 1
	)
	) as pemenang, 
	(
	select distinct coalesce(nev_harga_terkoreksi, 0) nev_harga_terkoreksi 
	from peserta
	left join rekanan on peserta.rkn_id = rekanan.rkn_id 
	left join nilai_evaluasi on nilai_evaluasi.psr_id = peserta.psr_id
	left join evaluasi on nilai_evaluasi.eva_id = evaluasi.eva_id
	where is_pemenang=1 and eva_jenis = 3 and peserta.lls_id = lelang_seleksi.lls_id and peserta.auditupdate in 
	(
	select p.auditupdate from peserta p where p.lls_id = lelang_seleksi.lls_id order by p.auditupdate desc limit 1
	)
	and evaluasi.eva_versi in 
	(
	select e.eva_versi from evaluasi e where e.lls_id = lelang_seleksi.lls_id order by e.eva_versi desc limit 1
	)
	) as nilai_kontrak, 
	(
	select distinct s.sppbj_no from sppbj s where s.lls_id = lelang_seleksi.lls_id
	) as no_sppbj,
	(
	select distinct k.kontrak_no from kontrak k where k.lls_id = lelang_seleksi.lls_id
	) as no_kontrak,
	(
	select distinct k2.kontrak_mulai from kontrak k2 where k2.lls_id = lelang_seleksi.lls_id
	) as tglawal_kontrak,
	(
	select distinct k3.kontrak_akhir from kontrak k3 where k3.lls_id = lelang_seleksi.lls_id
	) as tglakhir_kontrak,
	panitia.audituser,
	pkt_tgl_buat
	from paket 
	inner join satuan_kerja on paket.stk_id = satuan_kerja.stk_id
	inner join panitia on paket.pnt_id = panitia.pnt_id
	inner join anggota_panitia on paket.pnt_id = anggota_panitia.pnt_id
	inner join pegawai on anggota_panitia.peg_id = pegawai.peg_id 
	inner join ppk on paket.ppk_id = ppk.ppk_id
	inner join pegawai as p on ppk.peg_id = p.peg_id
	inner join lelang_seleksi on paket.pkt_id = lelang_seleksi.pkt_id and lelang_seleksi.lls_versi_lelang in (select lls_versi_lelang from lelang_seleksi as ls where ls.pkt_id=paket.pkt_id and lls_status <> 0 order by lls_versi_lelang desc limit 1)
	inner join jadwal on jadwal.lls_id = lelang_seleksi.lls_id and jadwal.thp_id in (18807,18808) 
	inner join jadwal as j on j.lls_id = lelang_seleksi.lls_id and j.thp_id in (18803) 
	where 1=1 
		and paket.pkt_id not in (1577163,1576163, 1562163, 1571163, 1573163, 1570163, 1668163,  1785163,1776163) 
		and jadwal.dtj_tglawal is not null 
		and jadwal.dtj_tglakhir is not null";
		
if(!empty($post_kodepaket)){
	$pg_sql .= " AND paket.pkt_id not in (". $post_kodepaket . ")";
}
		
$pg_sql .= " order by dtj_tglawal";

?>
<form action="./update_data_all.php?mode=sync" method="POST">
<div class="row margin-bottom">
	<div class="col-sm-2">
		<h5>Pengecualian Kode Paket</h5>
	</div>
	<div class="col-sm-2">
		<input type="text" class="form-control" name="pg_kodepaket" id="pg_kodepaket" placeholder="'Kode1', 'Kode2'" value="<?php echo $post_kodepaket;?>"/>
	</div>
	<div class="col-sm-2">
		<input type="submit" name="btnProses" value="Update Data" class="btn btn-primary" />
	</div>
</div>

	<?php 
	
	if(!empty($mode)) 
	{
		$db = pg_connect('host=' . PGHOST . ' port='. PGPORT . ' dbname=' . PGDATABASE . ' user=' . PGUSER . ' password=' . PGPASSWORD); 

		$result = pg_query($pg_sql); 
		
		if (!$result) { 
			echo "Problem with query " . $query . "<br/>"; 
			echo pg_last_error(); 
			exit(); 
		} 
		else {
			//$mySqlresult = $DB->execute("insert into datamasterlog select *, now() from datamaster;");
			$mySqlresult = $DB->execute("truncate table datamaster");
		}
		
		$counter = 0;
		$statusproses = "PROSES";
		
		while($myrow = pg_fetch_assoc($result)) 
		{ 
			if (new DateTime() <= new DateTime($myrow['dtj_tglakhir']) && $myrow['pemenang'] == "" && $myrow['nilai_kontrak'] == "")
				$statusproses = "PROSES";
			elseif($myrow['lls_diulang_karena'] == "" && $myrow['pemenang'] != "" && $myrow['nilai_kontrak'] != "")
				$statusproses = "SELESAI";
			elseif (new DateTime($myrow['dtj_tglakhir']) < new DateTime() && $myrow['pemenang'] == "" && $myrow['nilai_kontrak'] == "")
				$statusproses = "GAGAL LELANG";
			elseif($myrow['lls_diulang_karena'] != "" && $myrow['pemenang'] != "" && $myrow['nilai_kontrak'] != "")
				$statusproses = "SELESAI LELANG ULANG";
			elseif($myrow['pemenang'] == "" && $myrow['nilai_kontrak'] == "" && $myrow['lls_diulang_karena'] != "" && new DateTime($myrow['dtj_tglakhir']) >= new DateTime())
				$statusproses = "PROSES LELANG ULANG";
			else
				;
			
			$sql = "INSERT INTO datamaster
					(
					tanggalprosesdata,
					statusproses,
					pg_kodepaket,
					pg_namapaket,
					pg_namastk,
					pg_ketua,
					pg_sekretaris,
					pg_anggota1,
					pg_anggota2,
					pg_anggota3,
					pg_nomorskpanitia,
					pg_namapanitia,
					pg_tanggalawallelang,
					pg_tanggalakhirlelang,
					pg_pagupaket,
					pg_jeniskegiatan,
					pg_hps,
					pg_lelangdiulang,
					pg_ppk,
					pg_pemenang,
					pg_nilaikontrak,
					pg_nomorsppbj,
					pg_nomorkontrak,
					pg_tanggalawalkontrak,
					pg_tanggalakhirkontrak,
					pg_audituser,
					pkt_tgl_buat
					)
					VALUES
					(
					 now(),
					'" . $statusproses . "',
					'" . $myrow['pkt_id'] ."',
					'" . $myrow['pkt_nama'] ."',
					'" . $myrow['stk_nama'] ."',
					'" . $myrow['ketua'] ."',
					'" . $myrow['sekertaris'] ."',
					'" . $myrow['angggota1'] ."',
					'" . $myrow['angggota2'] ."',
					'" . $myrow['angggota3'] ."',
					'" . $myrow['pnt_no_sk'] ."',
					'" . $myrow['pnt_nama'] ."',
					'" . ($myrow['dtj_tglawal'] != '' ? $myrow['dtj_tglawal'] : '1900-01-01 00:00') ."',
					'" . ($myrow['dtj_tglakhir'] != '' ? $myrow['dtj_tglakhir'] : '1900-01-01 00:00') ."',
					" . $myrow['pkt_pagu'] .",
					'" . $myrow['kgr_nama'] ."',
					" . $myrow['pkt_hps'] .",
					'" . $myrow['lls_diulang_karena'] ."',
					'" . $myrow['ppk'] ."',
					'" . $myrow['pemenang'] ."',
					" . ($myrow['nilai_kontrak'] != '' ? $myrow['nilai_kontrak'] : 0) .",
					'" . $myrow['no_sppbj'] ."',
					'" . $myrow['no_kontrak'] ."',
					'" . ($myrow['tglawal_kontrak'] != '' ? $myrow['tglawal_kontrak'] : '1900-01-01 00:00') ."',
					'" . ($myrow['tglakhir_kontrak'] != '' ? $myrow['tglakhir_kontrak'] : '1900-01-01 00:00') ."',
					'" . $myrow['audituser'] ."',
					'" . ($myrow['pkt_tgl_buat'] != '' ? $myrow['pkt_tgl_buat'] : '1900-01-01 00:00') ."')";

			$mySqlresult = $DB->execute($sql);
			$counter = $counter + 1;
			//echo($sql);
		}
		
		echo("Proses sinkronisasi data selesai. Jumlah data: " . number_format($counter, 0));
	}
	
?> 
</form>
  
<?php include_once("_footer.php");?>