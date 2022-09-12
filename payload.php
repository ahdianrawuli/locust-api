<?php

error_reporting(E_ERROR | E_PARSE);

include "conn/db.php";

$sql = "select payload from data where queue_id=".$argv[1];
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$json_a = json_decode($row[payload], true);

echo "
import time
from locust import HttpUser, task, between

class QuickstartUser(HttpUser):
    wait_time = between(1, 5)
";

foreach($json_a['data'] as $data) {
        $i++;
	$headers = json_encode($data['headers']);
	$headers = str_replace("\\/", "/", $headers);
        echo "
    @task
    def index_".$i."(self):
";
        foreach($data['endpoint'] as $endpoint) {
		$body = json_encode($endpoint['body']);
		if($body=="null") {
			$body = "";
		} else {
			$body = ", json=".$body;
		}
                echo "
        self.client.".strtolower($data['method'])."('$endpoint[url]', headers=".$headers." ".$body.")
";
        }
}

?>
