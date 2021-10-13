<?php 
error_reporting(E_ALL);

require_once("./orders.php");

const TESTENV = true;

try{
	$order = orders::create(1,"2077-01-01 00:00:00",100,1,50,1);
	echo "barcode: ".$order;
} catch(Exception $error){
	echo "Ошибка: ".$error->getMessage();
}

?>