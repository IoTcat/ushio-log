<?php
include './functions.php';

header("Access-Control-Allow-Origin: *");

$fp=$_REQUEST['fp'];
$ip=getIp();
$domain=$_SERVER['HTTP_HOST'];
$from=$_REQUEST['url'];
$lang=$_REQUEST['lang'];
$timezone=$_REQUEST['timezone'];
$platform=$_REQUEST['platform'];
$ram=$_REQUEST['ram'];
$screen=$_REQUEST['screen'];
$userAgent=$_REQUEST['userAgent'];
$webgl=$_REQUEST['webgl'];
$rsid=$_REQUEST['sid'];
$sessiontime=$_REQUEST['sessiontime'];


if(isset($rsid) && isset($sessiontime)){
    yimian__log("log_iis", array("sessiontime"=>$sessiontime), array("sid"=>$rsid));
    die();
}


if(!isset($fp) || !isset($ip) || !isset($domain)) die();

if(!(isset($from) && strlen($from) > 5)) $from=$_SERVER['HTTP_REFERER'];
$sid=substr(md5(time()*rand()), 0, 16);

ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; 4399Box.560; .NET4.0C; .NET4.0E)');

$ipData = file_get_contents('http://ip-api.com/json/49.64.195.96');
$ipData = json_decode($ipData,true);

yimian__log("log_iis",array("sid"=>$sid,"country"=>$ipData['country'],"region"=>$ipData['regionName'],"city"=>$ipData['city'],"isp"=>$ipData['isp'],"address"=>$ipData['as'],"org"=>$ipData['org'],"latitude"=>$ipData['lat'],"longitude"=>$ipData['lon'],"fp"=>$fp,"ip"=>ip2long($ip),"domain"=>get_from_domain(),"url"=>$from,"timestamp"=>date('Y-m-d H:i:s', time()),"language"=>$lang,"timezone"=>$timezone,"platform"=>$platform,"ram"=>$ram,"screen"=>$screen,"useragent"=>$userAgent,"webgl"=>$webgl));

echo json_encode(array("sid"=>$sid, "ip"=>$ip));
