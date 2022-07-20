<?php
require_once 'config/config.php' ;
require_once 'config/database.php' ;

if (isset($_GET['payment'])) {
$payment = $_GET['payment'] ;
$db = new Database() ;
$con = $db->conectar();

$sql = $con->prepare("SELECT id, idcta, idabonado, pagado, periodo, nombre, fchpago, 
docu, precinto, pago_id, qimpo FROM CTABOTONPAGO WHERE pago_id = ? and pagado = 1");
$sql->execute([$payment]);
$dato = $sql->fetchAll(PDO::FETCH_ASSOC);

print_r($dato) ;

} else {

    header('Location: index.php');
}