<?php

include("conexion.php");
include("utils.php");

$glosa = 0;
$error = false;
$status = false;

if ($link->connect_errno) {

	$glosa = -1;
	$error = true;

}

if( !$error){
	$status = $_REQUEST['status'];
	$statusBD = getStatusArduino($link);

	if( $statusBD != null){
		if($status != $statusBD){

			$status = changeStatusArduino($link, $status);
		}
	}
	else{

		$error = true;
	}
}

if ($error) {
	$glosa = -1;
	$status = false;
}

$json = new stdClass();
$json->glosa = $glosa;
$json->status = $status;
echo json_encode($json);

?>