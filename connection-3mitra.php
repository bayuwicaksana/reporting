<?php 
define('DATABASE_NAME', 'cvtrimi_spse');
define('DATABASE_USER', 'cvtrimi_root');
define('DATABASE_PASS', 'kutu191919');
define('DATABASE_HOST', '173.0.137.3');

include_once('./class.DBPDO.php');
$DB = new DBPDO();

function data_output ( $columns, $data )
{
	$out = array();

	for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
		$row = array();

		for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
			$column = $columns[$j];

			// Is there a formatter?
			if ( isset( $column['formatter'] ) ) {
				$row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
			}
			else {
				$row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
			}
		}

		$out[] = $row;
	}

	return $out;
}
?>