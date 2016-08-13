


<?php

function getStatusArduino($link){
	//$response = false;

	$query = "SELECT status FROM arduino WHERE id = 1 LIMIT 1";
	$result=$link->query($query);
	$row = $result->fetch_row();

	return $row[0];
}

function changeStatusArduino($link, $status){
	$response = false;

	if($link->query("UPDATE arduino set status = $status Where id = 1")){
		$response = $status;
	}
	return $response;

}


function getTickets($link){
	$response = false;
	$query = "SELECT * FROM ticket WHERE status = 0 AND fecha = Curdate() ORDER BY ticket ASC";
	$result=$link->query($query);
	$array = null;
	while ($rows = mysqli_fetch_assoc($result)) {


		$array[] = $rows;

	}
	return $array[0];
}

function contador($link){
	$query = "SELECT COUNT(ticket) AS size FROM ticket WHERE fecha = CURDATE()";

	$result=$link->query($query);
	$row = $result->fetch_row();

	return $row[0];
}

function ultimoTicketTotem($link){

	$query = "SELECT * FROM ticket WHERE fecha = CURDATE() ORDER BY ticket DESC";

	$result=$link->query($query);
	$array = null;
	while ($rows = mysqli_fetch_assoc($result)) {

		$array[] = $rows;

	}

	return $array[0];
}

function contadorTicket($link){

	$query = "SELECT COUNT(ticket) AS size FROM ticket WHERE fecha = CURDATE() AND status = 0";

	$result=$link->query($query);
	$row = $result->fetch_row();

	return $row[0];
}

function isMyTurn($link, $ticket){

	$query = "SELECT * FROM ticket WHERE fecha = CURDATE() AND ticket=$ticket";

	$result=$link->query($query);
	$row = $result->fetch_row();

	return $row;
}

function contadorTicketMenores($link, $ticket){

	$query = "SELECT COUNT(ticket) AS size FROM ticket WHERE fecha = CURDATE() AND status = 0 AND ticket < $ticket";

	$result=$link->query($query);
	$row = $result->fetch_row();

	return $row[0];
}

function preTurno($link, $ticket){

	$query = "SELECT * FROM ticket WHERE fecha = CURDATE() AND status = 0 AND ticket > $ticket ORDER BY ticket ASC";

	$result=$link->query($query);
	$array = null;
	while ($rows = mysqli_fetch_assoc($result)) {

		$array[] = $rows;

	}

	return $array;
}

function ticketsdesde($link, $desde, $hasta){

	$query = "SELECT * FROM ticket WHERE fecha = CURDATE() AND ticket >= $desde AND ticket <= $hasta";

	$result=$link->query($query);
	$array = null;
	while ($rows = mysqli_fetch_assoc($result)) {

		$array[] = $rows;

	}

	return $array;
}

function ultimoTicketAtendido($link){

	$query = "SELECT * FROM ticket WHERE fecha = CURDATE() AND status = 1 ORDER BY ticket DESC";

	$result=$link->query($query);
	$array = null;
	while ($rows = mysqli_fetch_assoc($result)) {

		$array[] = $rows;

	}

	return $array[0];
}

function insertarArray($link, $array){
	
	$array = json_decode($array[0]);
	$size = count($array);
	echo $size;
	for ($i = 0; $i< $size; $i++){

		$ticket = $array[$i]->ticket;
		$gcm = $array[$i]->gcm;
		if($gcm==""){
			$gcm = '"'.'"';
		}
		$status = $array[$i]->status;
		$fecha = "'".$array[$i]->fecha."'";

		$hora = "'".$array[$i]->hora."'";
		$fecha_update = "'".$array[$i]->fecha_update."'";
		$hora_update = "'".$array[$i]->hora_update."'";
		$dispositivo = $array[$i]->dispositivo;

		$query="INSERT INTO ticket VALUES (null, $ticket,$gcm,$status,$fecha, $hora, $fecha_update, $hora_update, $dispositivo)";

		//$query="INSERT INTO ticket VALUES (null, ".(string)$array[$i]->ticket." , ".(string)$array[$i]->gcm.",".(string)$array[$i]->status.",".(string)$array[$i]->fecha.", ".(string)$array[$i]->hora.", ".(string)$array[$i]->fecha_update.", ".(string)$array[$i]->hora_update.", ".(string)$array[$i]->dispositivo.")";
		if($result=$link->query($query)){
		//echo "insert";
	}
	else{
		//echo $link->error;
		//echo "no";
	}
	}
}

function postticket($link, $gcm){

	$lastticket = ultimoTicketTotem($link);

	if($lastticket != null){
		$size = $lastticket['ticket'];
	}
	else{
		$size = "0";
	}

	$size = intval($size)+ 1;

	$query="INSERT INTO ticket VALUES (null, $size,$gcm,0,CURDATE(), CURTIME(), CURDATE(), CURTIME(), 1)";
	if($result=$link->query($query)){
		return $size;
	}
	else return -1;

}

function changeStatus($link, $id, $ticket, $status){
	$response = false;
	if($link->query("UPDATE ticket set status = $status Where ticket = $ticket AND fecha = CURDATE()")){
		$response = true;
	}
	return $response;

}
function cancelTicket($link, $ticket, $status){
	$response = false;
	if($link->query("UPDATE ticket set status = $status Where ticket = $ticket AND fecha = CURDATE()")){
		$response = true;
	}
	return $response;

}
function resetContador($link, $fila){
	$response = false;
	
	if ($link->query("UPDATE contador SET valor = 0 WHERE fila = $fila") === TRUE) {
		$response = true;
	}
	return $response;
}

function increaseContador($link, $fila, $value){
	$response = false;

	if($link->query("UPDATE contador SET valor = $value WHERE fila = $fila") === TRUE){
		$response = true;
	}
	return $response;
}


function getGCM($link, $ticket){
	$response = false;
	$query = "SELECT gcm FROM ticket WHERE ticket = $ticket AND fecha = CURDATE()";
	if($result=$link->query($query)){
		$row = $result->fetch_row();
	return $row[0];
	}
	else
		{return "";}
	
}

function sendGCM($registration_id, $msg){
	$url = 'https://android.googleapis.com/gcm/send';
	$fields = array(
		'registration_ids' => array($registration_id),
		'data' => array( "msg" => $msg )
		,
		);
  //  print_r($fields);
    // Update your Google Cloud Messaging API Key
	define("GOOGLE_API_KEY", "AIzaSyBEn0ZTm59i7NIaU1WsyeajBMI6Le0jSJU");        
	$headers = array(
		'Authorization: key=' . GOOGLE_API_KEY,
		'Content-Type: application/json'
		);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);   
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	$result = curl_exec($ch);               
	if ($result === FALSE) {
   //     die('Curl failed: ' . curl_error($ch));
	}
	curl_close($ch);
	return $result;
  //	echo $result;
}
function getTiempo($link,$fila){
	$longitud = count($fila);
	//$fila1001 = 0;
	$array = array();
	//$fila1002 = 0;

	$query_caja = "SELECT fila as 'fila', COUNT(fila) as cantidad, (SELECT tiempo as 'tiempo' FROM fila WHERE id = $fila) FROM ticket WHERE fecha_creacion = CURDATE() AND status = 0 AND fila=$fila GROUP BY fila";

	$infofila = new stdClass();
	if($result = $link->query($query_caja))
	{
		$fila="";
		$cantidad="0";
		$tiempo = "0";

		$row = $result->fetch_row();


		if(isset($row[1])){
			$cantidad = $row[1];		
		}

		if(isset($row[2])){
			$tiempo = $row[2]*$row[1];		
		}

		$infofila->cantidad=$cantidad;
		$infofila->tiempo=$tiempo;
		$array[]=$infofila;
	}
	$array = json_decode(json_encode($array), true);
	return $array;
	//echo $link->error;
}

function getAtendiendo($link,$filas){
	for($i = 0; $i<count($filas) ; $i++){
		$query = "SELECT ticket as 'ticket' FROM ticket WHERE fila = $filas[$i] AND status = 1 AND fecha_creacion =CURDATE() GROUP BY ticket DESC";

		$infofila = new stdClass();
		if($result = $link->query($query))
		{
			$row = $result->fetch_row();
			if($row[0]==null){
				$row[0]="---";
			}
			$infofila->ticket=$row[0];
			$array[]=$infofila;
		}
	}
	return json_encode($array);
}
function getNombre($link,$filas){
	for($i = 0; $i<count($filas) ; $i++){
		$query = "SELECT nombre as 'fila' FROM fila WHERE id = $filas[$i]";

		$infofila = new stdClass();
		if($result = $link->query($query))
		{
			$row = $result->fetch_row();
			if($row[0]==null){
				$row[0]="---";
			}
			$infofila->nombre=$row[0];
			$infofila->largo = count($filas);
			$array[]=$infofila;
		}
	}
	return json_encode($array);
}
function lastTicket($link,$ticket,$fila){
	$response = false;
	if($link->query("UPDATE ultimoticket SET ticket = $ticket, fila = $fila ,fecha_creacion = CURDATE()")){
		$response = true;
	}
	return $response;
}
function getLastTicket($link){
	$response = false;
	$query = "SELECT ticket, fila FROM ultimoticket WHERE fecha_creacion = CURDATE()";
	$result=$link->query($query);
	if(is_object($result)){
		$row = $result->fetch_row();
		return $row;
	}
	else
		return null;
}

function getultimo($link,$fila){
	
	$query = "SELECT ticket FROM ticket WHERE fecha_creacion = CURDATE() AND status = 1 AND fila = $fila GROUP BY ticket DESC";
	$result=$link->query($query);
	if(is_object($result)){
		$row = $result->fetch_row();
		return $row;
	}
	else return "---";
}
?>