<?php
include './functions.php';

header("Access-Control-Allow-Origin: *");

$arr = db__getData(db__connect("log"), "proj");

echo json_encode($arr);
