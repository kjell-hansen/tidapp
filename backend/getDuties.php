<?php

declare (strict_types=1);

require_once 'funktioner.php';

if (!$db=kopplaDB()) { 
    $fel= mysqli_error($db);
    $error=new stdClass();
    $error->error[]="Något gick fel vid databaskoppling";
    $error->error[]=$fel;
    echo skickaJSON($error, 500);
    exit();
}

$sql="select id, uppgift from Duties order by uppgift";
$resultat=$db->query($sql);
$out=new stdClass();
$out->duties=[];
while($row=$resultat->fetch_assoc()) {
    $rec=new stdClass();
    $rec->id=$row["id"];
    $rec->uppgift=$row["uppgift"];
    $out->duties[]=$rec;
}
echo skickaJSON($out);