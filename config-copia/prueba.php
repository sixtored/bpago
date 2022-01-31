<?php 
$hoy = date('Y-m-d') ;
$dias = intval(15552000/86400) ;

$expira = strtotime($hoy." + $dias days") ;

echo date('d-m-Y',$expira) ;