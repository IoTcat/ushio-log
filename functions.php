<?php
include '/mnt/config/dbKeys/log.php';

/**database connection**/

//connect to database
function db__connect($servername="",$username="",$password="",$dbname="")
{echo $GLOBALS['g_db_serverName'];
	/* reset */
	if($servername=="") $servername=$GLOBALS['g_db_serverName'];
	if($username=="") $username=$GLOBALS['g_db_usrName'];
	if($password=="") $password=$GLOBALS['g_db_psswd'];
	if($dbname=="") $dbname=$GLOBALS['g_db_dbName'];

	if($servername == "log"){

		$servername = $GLOBALS['g_db_log_serverName'];
		$username = $GLOBALS['g_db_log_usrName'];
		$password = $GLOBALS['g_db_log_psswd'];
		$dbname = $GLOBALS['g_db_log_dbName'];
	}elseif($servername == "yulu"){

		$servername = $GLOBALS['g_db_serverName'];
		$username = $GLOBALS['g_db_usrName'];
		$password = $GLOBALS['g_db_psswd'];
		$dbname = "yulu";
	}
	
	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) 
	{
		die("Mysql Connect Failed: " . $conn->connect_error);
	} 

	return ($conn);
}

//get table row number::(data_cnnct var,table name) ::(row number)
function db__rowNum($conn,$table,$clmnName="",$value="",$clmnName2="",$value2="")
{
	
	$table=db__antisql($table);
	$clmnName=db__antisql($clmnName);
	$value=db__antisql($value);
	$clmnName2=db__antisql($clmnName2);
	$value2=db__antisql($value2);
	
	
	if($clmnName=="") $sql = "SELECT COUNT(*) FROM $table";
	elseif($clmnName2=="") $sql = "SELECT COUNT(*) FROM $table where $clmnName='$value'";
	else $sql = "SELECT COUNT(*) FROM $table where $clmnName='$value' AND $clmnName2='$value2'";
	
	$row_count = $conn->query($sql);   
	list($row_num) = $row_count->fetch_row(); 
	return ($row_num);
}

//get row data from database::(data_cnnct var, table name,column name, column value)::(row info)
function db__getData($conn,$table,$clmnName="",$value="",$clmnName2="",$value2="")
{
	
	$table=db__antisql($table);
	$clmnName=db__antisql($clmnName);
	$value=db__antisql($value);
	$clmnName2=db__antisql($clmnName2);
	$value2=db__antisql($value2);
		

	if($clmnName=="") $sql = "SELECT * FROM $table";
	elseif($clmnName2=="") $sql = "SELECT * FROM $table where $clmnName='$value'";
	else $sql = "SELECT * FROM $table where $clmnName='$value' AND $clmnName2='$value2'";
		
	$result = $conn->query($sql);
	//no data
	if ($result->num_rows > 0) {}else{return 404;}

	$i=0;
	$arr=array();
	while($row = $result->fetch_assoc()) {
		$arr[$i++]=$row;
	}
	return ($arr);
}


//fnct for insert a row to database
function db__insertData($conn,$table,$content)
{	
	$table=db__antisql($table);
	
	$key=array_keys($content);
	
	$key=db__antisql($key);
	
	$sql="insert INTO $table (";
	
	for($i=0;$i<count($key);$i++)
	{
		$sql.="$key[$i]";
		if($i!=count($key)-1) $sql.=", ";
	}
	
	$sql.=") VALUES (";
	
	for($i=0;$i<count($key);$i++)
	{
		$tmp_key=$key[$i];
		$content[$tmp_key]=db__antisql($content[$tmp_key]);
		$sql.="'$content[$tmp_key]'";
		if($i!=count($key)-1) $sql.=", ";
	}
	
	$sql.=")";
	
	if (!($conn->query($sql) === TRUE))  echo "SQL Insert Error: " . $sql . "<br>" . $conn->error;

}


//fnct for update a row to database without check
function db__updateData($conn,$table,$content,$index)
{	
	$key=array_keys($content);
	$key=db__antisql($key);
	
	$sql="UPDATE $table SET ";
	
	for($i=0;$i<count($key);$i++)
	{
		$tmp_key=$key[$i];
		$content[$tmp_key]=db__antisql($content[$tmp_key]);
		$sql.="$key[$i]='$content[$tmp_key]'";
		if($i!=count($key)-1) $sql.=", ";
	}
	
	$key=array_keys($index);
	$key=db__antisql($key);
	
	$sql.=" WHERE ";
	
	for($i=0;$i<count($key);$i++)
	{
		$tmp_key=$key[$i];
		$index[$tmp_key]=db__antisql($index[$tmp_key]);
		$sql.="$tmp_key='$index[$tmp_key]'";
		if($i!=count($key)-1) $sql.=" AND ";
	}
	
	if (!($conn->query($sql) === TRUE))  echo "SQL Insert Error: " . $sql . "<br>" . $conn->error;

}




//push row data from database::(data_cnnct var, table name,column name, column value)::(row info)
function db__pushData($conn,$table,$content,$index="",$is_force=1)
{
	if($index)
	{
		$index_keys=array_keys($index);

		if(count($index_keys)==1) $result=db__rowNum($conn,$table,$index_keys[0],$index[$index_keys[0]]); 
			
		elseif(count($index_keys)==2)	$result=db__rowNum($conn,$table,$index_keys[0],$index[$index_keys[0]],$index_keys[1],$index[$index_keys[1]]); 
			
		else return -1;
			
		if($result>0) db__updateData($conn,$table,$content,$index);
		else if($is_force) db__insertData($conn,$table,$content);
			
	}
	else
		db__insertData($conn,$table,$content);
}


function db__delData($conn, $table, $clmnName, $value)
{
	$value=db__antisql($value);
	$clmnName=db__antisql($clmnName);

	$sql = "DELETE FROM $table WHERE $clmnName = '$value'";
	$conn->query($sql);
}


//anti sql
function db__antisql($str)
{
	return(str_ireplace("'","",$str));
}


/*****log******/
function yimian__log($table, $val, $index = "", $cnt = null){

	if(!isset($cnt)) $cnt = db__connect("log");
	if($index != "") db__pushData($cnt, $table, $val, $index);
	else db__pushData($cnt, $table, $val);
}

/** get from address **/
function get_from(){

	if($_SERVER['HTTP_REFERER']) return $_SERVER['HTTP_REFERER'];
	elseif($_REQUEST['from']) return $_REQUEST['from'];
}

function get_from_domain(){

	$str = str_replace("http://","",get_from());
	$str = str_replace("https://","",$str);
	$strdomain = explode("/",$str);
	return $strdomain[0];
}


/* get IP */
function getIp()
{
	if (isset($_SERVER)) {
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

			foreach ($arr as $ip) {
				$ip = trim($ip);

				if ($ip != 'unknown') {
					$realip = $ip;
					break;
				}
			}
		} else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$realip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (isset($_SERVER['REMOTE_ADDR'])) {
			$realip = $_SERVER['REMOTE_ADDR'];
		} else {
			$realip = '0.0.0.0';
		}
	} else if (getenv('HTTP_X_FORWARDED_FOR')) {
		$realip = getenv('HTTP_X_FORWARDED_FOR');
	} else if (getenv('HTTP_CLIENT_IP')) {
		$realip = getenv('HTTP_CLIENT_IP');
	} else {
		$realip = getenv('REMOTE_ADDR');
	}

	preg_match('/[\\d\\.]{7,15}/', $realip, $onlineip);
	$realip = (!empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0');
	return $realip;
}



