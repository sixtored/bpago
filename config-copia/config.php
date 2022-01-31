<?php

define("CLIENT_ID","PAYPAL") ;
define("TOKEN_MP","TOKEN_MP") ;
define("PUBLIC_KEYMP","PUBLIC_KEYMP") ;
define("CURRENCY","MXN") ;
define("LOCALE","es_AR") ;
define("KEY_TOKEN","ABD-359>=?sd") ;
define("MONEDA","$") ;
define("ACCESS_TOKEN_MARKETPLACE","TOKEN_MARTEPLACE") ; 
define("CLIENT_IDMP"," ");
define("CLIENT_SECRETMP", " ") ;
session_start() ;

date_default_timezone_set("America/Argentina/Buenos_Aires") ;
		// setlocale(LC_TIME, 'spanish');
setlocale(LC_TIME, 'es_ES.UTF8');

$num_cart = 0 ;
if (isset($_SESSION['carrito']['producto'])){
    $num_cart = count($_SESSION['carrito']['producto']) ;
}    