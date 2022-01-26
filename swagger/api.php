<?php
require("../init.php");
$json = \MapDapRest\OpenApi::generate();
echo json_encode($json);