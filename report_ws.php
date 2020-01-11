<?php include_once("connection.php");?>

<?php 
function get_total($nama_instansi = '', $nama_jenis_kegiatan = '', $statusproses = '', $year = '')
{
	$DB = new DBPDO();
	$sql = "SELECT COUNT(*) AS TOTAL FROM datamaster
			WHERE pg_jeniskegiatan = '".$nama_jenis_kegiatan."'
			AND pg_namastk = '".$nama_instansi."'
			";
	if(!empty($statusproses)){
		if($statusproses == 'SELESAI')
			$sql .= " AND (statusproses = 'SELESAI' OR statusproses = 'SELESAI LELANG ULANG')";
		else
			$sql .= " AND statusproses = '".$statusproses."'";
	}
	if(!empty($year)){
		$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$year."'";
	}
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}
function get_total_Paket($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT COUNT(pg_kodepaket) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."'";
		
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}	

function get_total_Paket_Selesai($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT COUNT(pg_kodepaket) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."' and statusproses='selesai'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$sql .= " AND statusproses IN ('SELESAI','SELESAI LELANG ULANG')";

	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}

function get_Pagu($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT sum(pg_pagupaket) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}

function get_Pagu_Selesai($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT sum(pg_pagupaket) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."' and statusproses='selesai'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$sql .= " AND statusproses IN ('SELESAI','SELESAI LELANG ULANG')";

	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}

function get_Penawaran($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT sum(pg_nilaikontrak) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}

function get_HPS($nama_Agency = '', $Satuan_Kerja_Agency = '', $tahun=0)
{
	$DB = new DBPDO();
	$sql = "SELECT sum(pg_HPS) AS TOTAL FROM datamaster
			WHERE year(pg_tanggalawallelang)='".$tahun."'"; // date(pg_tanggalawallelang) <= '".date("Y-m-d")."'";
			
	if(!empty($nama_Agency)){
		if($nama_Agency == 'Agency' || $nama_Agency == '')
			$sql .= "";
		else
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$nama_Agency."')";
	}
	
	if(!empty($Satuan_Kerja_Agency)){
		if($Satuan_Kerja_Agency == 'pg_namastk' || $Satuan_Kerja_Agency == 'Satuan Kerja' || $Satuan_Kerja_Agency == '' )
			$sql .= "";
		else
			$sql .= " AND pg_namastk = '".$Satuan_Kerja_Agency."'";
	}
	
	$data = $DB->fetchAll($sql);
	return (count($data)>0)?$data[0]['TOTAL']:0;
}						
?>
<?php 
if(isset($_GET['key'])) 
{
	$key = (isset($_GET['key']))?$_GET['key']:"";
	$ops = (isset($_GET['ops']))?$_GET['ops']:"";
	$subops = (isset($_GET['subops']))?$_GET['subops']:"";
	$format = (isset($_GET['format']))?$_GET['format']:"json";
	
	$year = (isset($_GET['year']))?$_GET['year']:date("Y");
	$agency = (isset($_GET['agency']))?$_GET['agency']:"";
	$satker = (isset($_GET['satker']))?$_GET['satker']:"";
	
	$format = "json";
	
	if ($key == "161ebd7d45089b3446ee4e0d86dbcf92")
	{
		if ($ops == "1") // Rekapitulasi Paket
		{
			$TotalPaket=get_total_Paket($agency, $satker, $year);
			$Pagu=get_Pagu($agency, $satker, $year);
			$TotalPaketSelesai=get_total_Paket_Selesai($agency, $satker, $year);
			$PaguPaketSelesai=get_Pagu_Selesai($agency, $satker, $year);
			$HPS=get_HPS($agency, $satker, $year);
			$Penawaran=get_Penawaran($agency, $satker, $year);
			$EfisiensiHPSNilai=$HPS-$Penawaran;
			$EfisiensiHPSPersen=(($HPS-$Penawaran)/$HPS) * 100;
			$EfisiensiPaguNilai=$PaguPaketSelesai-$Penawaran;
			$EfisiensiPaguPersen=(($PaguPaketSelesai-$Penawaran)/$PaguPaketSelesai) * 100;
			
			$sql = "SELECT " . $TotalPaket . " as totalpaket,";
			$sql = $sql . $Pagu . " as pagu,";
			$sql = $sql . $TotalPaketSelesai . " as totalpaketselesai,";
			$sql = $sql . $PaguPaketSelesai . " as pagupaketselesai,";
			$sql = $sql . $HPS . " as hps,";
			$sql = $sql . $Penawaran . " as penawaran,";
			$sql = $sql . $EfisiensiHPSNilai . " as efisiensihps,";
			$sql = $sql . $EfisiensiHPSPersen . " as efisiensihpspersen,";
			$sql = $sql . $EfisiensiPaguNilai . " as efisiensipagu,";
			$sql = $sql . $EfisiensiPaguPersen . " as efisiensipagupersen";
		}
		
		if ($ops == "2")
		{
			$sql = "SELECT months.months as bulan, months.name as label, 
			IFNULL(dataall.TOTAL,0) as totalpaket,
			IFNULL(dataselesai.TOTAL,0) as totalselesai,
			IFNULL(dataall.PAGU,0) as pagu,
			IFNULL(dataselesai.PAGU,0) as paguselesai,
			IFNULL(dataall.HPS,0) as hps,
			IFNULL(dataselesai.HPS,0) as hpsselesai,
			IFNULL(dataall.HASILLELANG,0) as hasillelang,
			(IFNULL(dataselesai.PAGU,0) - IFNULL(dataall.HASILLELANG,0)) as selisihpagu,
			IFNULL(((IFNULL(dataselesai.PAGU,0) - IFNULL(dataall.HASILLELANG,0)) / IFNULL(dataselesai.PAGU,0)) * 100,0) as selisihpagupersen,
			(IFNULL(dataselesai.HPS,0) - IFNULL(dataall.HASILLELANG,0)) as selisihhps,
			IFNULL(((IFNULL(dataselesai.HPS,0) - IFNULL(dataall.HASILLELANG,0)) / IFNULL(dataselesai.HPS,0)) * 100, 0) as selisihhpspersen
		FROM months 
			LEFT JOIN ( SELECT month(pg_tanggalawallelang) as bulan, COUNT(*) as TOTAL,
							SUM(pg_pagupaket) as PAGU, 
							SUM(pg_hps) as HPS,
							SUM(pg_nilaikontrak) as HASILLELANG
						FROM datamaster 
						WHERE 1=1 ";
						
		if(!empty($year)){
			$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$year."'";
		}
		if(!empty($agency)){
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$agency."')";
		}
		if(!empty($satker)){
			$sql .= " AND pg_namastk = '".$satker."'";
		}

		$sql .= " group by month(pg_tanggalawallelang) 
					) as dataall ON dataall.bulan = months.months
			LEFT JOIN ( SELECT month(pg_tanggalawallelang) as bulan, COUNT(*) as TOTAL,
							SUM(pg_pagupaket) as PAGU, 
							SUM(pg_hps) as HPS
						FROM datamaster 
						WHERE statusproses IN ('SELESAI','SELESAI LELANG ULANG') ";

		if(!empty($year)){
			$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$year."'";
		}
		if(!empty($agency)){
			$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$agency."')";
		}
		if(!empty($satker)){
			$sql .= " AND pg_namastk = '".$satker."'";
		}

		$sql .= " group by month(pg_tanggalawallelang) 
					) as dataselesai ON dataselesai.bulan = months.months";
			
		}
		
		if ($ops == "3") // Rekap Paket SPSE Kota Bogor
		{
			if ($subops == "1")
			{
				$sql = "SELECT months.months as bulan, months.name as label, IFNULL(datas.TOTAL,0) as total
				FROM months 
				LEFT JOIN
				(
				SELECT month(pg_tanggalawallelang) as bulan, COUNT(*) as TOTAL FROM datamaster
				WHERE 1=1 ";
				if(!empty($year)){
					$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$year."'";
				}
				if(!empty($agency)){
					$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$agency."')";
				}
				if(!empty($satker)){
					$sql .= " AND pg_namastk = '".$satker."'";
				}
				$sql .= " AND datamaster.statusproses <> 'GAGAL LELANG' ";
				$sql .= " group by month(pg_tanggalawallelang)
				) as datas ON datas.bulan = months.months";
			}
			if ($subops == "2")
			{
				$sql = "SELECT datamaster.statusproses, count(*) as jumlah, @curRow := @curRow + 1 AS row_number FROM datamaster
				JOIN (SELECT @curRow := 0) r WHERE 1=1";
				if(!empty($year)){
					$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$year."'";
				}		
				if(!empty($agency)){
					$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$agency."')";
				}
				if(!empty($satker)){
					$sql .= " AND pg_namastk = '".$satker."'";
				}
				$sql .= " AND datamaster.statusproses <> 'GAGAL LELANG' ";
				$sql .= " GROUP BY datamaster.statusproses ORDER BY row_number";
			}
		}
		
		if ($ops == "4")
		{
			if ($subops == "1")
			{
				$sql = "SELECT months.months as bulan, months.name as label, (@cum_sum:=@cum_sum+IFNULL(datas.TOTAL,0)) as total
				FROM months 
				LEFT JOIN
				(
				SELECT month(pg_tanggalawallelang) as bulan, COUNT(*) as TOTAL FROM datamaster
				WHERE 1=1 ";

				if(!empty($year)){
					$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$year."'";
				}

				if(!empty($agency)){
					$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$agency."')";
				}

				if(!empty($satker)){
					$sql .= " AND pg_namastk = '".$satker."'";
				}

				$sql .= " group by month(pg_tanggalawallelang)
				) as datas ON datas.bulan = months.months JOIN (select @cum_sum := 0.0) B";
			}
			
			if ($subops == "2")
			{
				$sql = "SELECT months.months as bulan, months.name as label, IFNULL(datas.TOTAL,0) as total
				FROM months 
				LEFT JOIN
				(
				SELECT month(pg_tanggalawallelang) as bulan, COUNT(*) as TOTAL FROM datamaster
				WHERE 1=1 ";

				if(!empty($year)){
					$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$year."'";
				}

				if(!empty($agency)){
					$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$agency."')";
				}

				if(!empty($satker)){
					$sql .= " AND pg_namastk = '".$satker."'";
				}

				$sql .= " group by month(pg_tanggalawallelang)
				) as datas ON datas.bulan = months.months";
			}
		}
		
		if ($ops == "5")
		{
			$sql = "SELECT datamaster.statusproses as status, count(0) as jumlah FROM datamaster Where 1=1";

			if(!empty($year)){
				$sql .= " AND YEAR(datamaster.pg_tanggalawallelang) = '".$year."'";
			}		
			if(!empty($agency)){
				$sql .= " AND pg_audituser in (select pg_audituser from ref_adminagency where kodeagency = '".$agency."')";
			}
			if(!empty($satker)){
				$sql .= " AND pg_namastk = '".$satker."'";
			}

			$sql .= " GROUP BY datamaster.statusproses";
		}
		
		//echo($sql);
		
		$posts = array();
		$post = $DB->fetchAll($sql);
		$posts[] = array('post'=>$post);
		
		/* output in necessary format */
		if($format == 'json') {
			header('Content-type: application/json');
			echo json_encode(array('posts'=>$posts));
		}
		else {
			header('Content-type: text/xml');
			echo ("<posts>");
			foreach($posts as $index => $post) {
				if(is_array($post)) {
					foreach($post as $key => $value) {
						echo ("<".$key.">");
						if(is_array($value)) {
							foreach($value as $tag => $val) {
								//echo ("<".$tag.">".htmlentities($val, ENT_XML1)."</".$tag.">");
							}
						}
						echo ("</".$key.">");
					}
				}
			}
			echo ("</posts>");
		}
	}
}
?>
