<?php 
/*
* Define PostgreSQL database server connect parameters.
*/

define('PGHOST','202.159.24.54');
define('PGPORT',5432);
define('PGDATABASE','epns_prod');
define('PGUSER', 'smsbogor');
define('PGPASSWORD', 'smsbogor');
define('PGCLIENTENCODING','UNICODE');
define('ERROR_ON_CONNECT_FAILED','Sorry, can not connect the database server now!');

function pg_connect()
{
	//echo('host=' . PGHOST . ' port='. PGPORT . ' dbname=' . PGDATABASE . ' user=' . PGUSER . ' password=' . PGPASSWORD);
	//$connstring = 'host=' . PGHOST . 'port='. PGPORT . 'dbname=' . PGDATABASE . 'user=' . PGUSER . 'password=' . PGPASSWORD;
	//die();
	
	try 
	{
		$db = pg_connect('host=' . PGHOST . ' port='. PGPORT . ' dbname=' . PGDATABASE . ' user=' . PGUSER . ' password=' . PGPASSWORD);
		
		return true;
	} 
	catch (Exception $e) 
	{
		return false;
	}
}

function pg_fetch($query, $values = null)
{
	$result = pg_query($query); 
	
	if (!$result) { 
		echo "Problem with query " . $query . "<br/>"; 
		echo pg_last_error(); 
		exit(); 
	} 

	$data = pg_fetch_assoc($result);

	return $data;
}

?>