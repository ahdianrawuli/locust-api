<?php

error_reporting(E_ERROR | E_PARSE);

include "conn/db.php";

$execstring='/bin/ps uax | grep -v grep | grep locust';
$output="";
exec($execstring, $output);

if($output[1]) {
	echo "please wait until locust finished the jobs";
} else {
	$sql = "SELECT * FROM data where complete=0 order by id";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			exec("/usr/bin/php /opt/locust-api/payload.php $row[queue_id] > /opt/locust-api/execute_payload.py");
			exec("/bin/bash /opt/locust-api/locust $row[url] $row[project] $row[queue_id] $row[user] $row[spawn] $row[time]");
		}
	} else {
	  echo "[".date('Y/M/d H:i:s')."] No Jobs..\r\n";
	}
	$conn->close();
}
?>
