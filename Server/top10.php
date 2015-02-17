<?php

$servername = 'localhost';
$user = 'root';
$password = 'eiqweiqw';
$database = 'test';
$conn = mysql_connect($servername,$user,$password);
if(!$conn)
{
  die('Could not connect: ' . mysql_error());
}

if(strlen($_SERVER["QUERY_STRING"]) > 1){
    echo json_encode(array("status" => "error","code" => "103", "message" => "Unknown parameter in request"));
}else {
    $sql = 'SELECT name,image,date,rating,age FROM films ORDER BY rating DESC LIMIT 10';
}

mysql_select_db($database);
$retval = mysql_query($sql);

if(mysql_num_rows($retval) == 0){
  json_ecode(array("status" => "error", "code" => "100", "message" => "Other Error"));
  exit;
}else{
    while($row = mysql_fetch_assoc($retval)){
        echo json_encode(array("status" => "okay",
                               "name" => $row["name"],
                               "image" => $row["image"],
                               "date" => $row["date"],
			       "rating" => $row["rating"],
                               "age" => $row["age"])) . "<br>";
    }
}

mysql_close($conn);

?>
