<?php

error_reporting(E_ERROR | E_PARSE);

include "conn/db.php";

if($_GET[act]=="plan" && $_GET[type]=="request") {
	$payload = file_get_contents('php://input');
	$json_a = json_decode($payload, true);
	$queue = time();
	$project = $json_a[project];
	$url = $json_a[url];
	$sql = "select id,queue_id,project,complete from data where project='$project' order by id DESC limit 1";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	if($row[complete]==1 || $row[complete]=="" || $row[complete]==null) {

		$sql = "INSERT INTO data (queue_id, project, url, payload, user, spawn, time)
		VALUES ('$queue', '$project', '$url', '$payload', '$json_a[user]', '$json_a[spawn]','$json_a[time]')";

		if ($conn->query($sql) === TRUE) {
		  echo "New record created successfully";
		} else {	
		  echo "Error";
		}

	} else {
	        $sqlx = "select id,queue_id,project,complete from data where complete=0";
        	$resultx = $conn->query($sqlx);
		echo "#### Antrian ####\r\n\r\nProject Name\r\n";
        	while ($rowx = $resultx->fetch_assoc()) {
			$x++;
			$data = $rowx[project];
			if($data==$project) {
				echo "[$x] ".$rowx[project]." (*) => you are here..\r\n";
			} else  {
				echo "[$x] ".$data."\r\n";
			}
		}
		echo "\r\n\r\nPlease check your report here\r\n";
		echo "http://128.199.192.183:8083/report/".$row[queue_id]."_".$project.".html\r\n\r\n";
	}

	$conn->close();
} else if($_GET[act]=="plan" && $_GET[type]=="report") {

	$project = $_GET[project];

	$sql = "SELECT * FROM data where project='$project'";
	$result = $conn->query($sql);
	while($row = $result->fetch_assoc()) {
		echo date('Y-M-d H:i:s', $row[queue_id])." - <a href='http://128.199.192.183:8083/report/$row[queue_id]_$row[project].html'>http://128.199.192.183:8083/report/$row[queue_id]_$row[project].html</a><br>";
	}

	$conn->close();
}

?>
