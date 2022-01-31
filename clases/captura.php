<?php

require '../config/config.php';
require '../config/database.php';

$db = new Database();
$con = $db->conectar();

date_default_timezone_set("America/Argentina/Buenos_Aires") ;
		// setlocale(LC_TIME, 'spanish');
setlocale(LC_TIME, 'es_ES.UTF8');

$payment = $_GET['payment_id'];
$status = $_GET['status'];
//$payment_type = $_GET['payment_type'];
$order_id = ($_GET['merchant_order_id'] == NULL ) ? '' : $_GET['merchant_order_id'];
$external_reference = ($_GET['external_reference'] == NULL ) ? '' : $_GET['external_reference'] ;
$collection_id = $_GET['collection_id'];
$preference_id = $_GET['preference_id'];

$fch = date("Y-m-d");

$url = 'https://api.mercadopago.com/v1/payments/' . $payment;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 5);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . TOKEN_MP));


$response = curl_exec($curl);

if (curl_errno($curl)) echo curl_errno($curl);
else $contents = json_decode($response);

$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
if ($httpcode == 200) {
    $email = $contents->payer->email;
    //$status = $contents->status ;
    $payment_method = $contents->payment_method_id;
    $payment_type = $contents->payment_type_id;
    $total = (double) $contents->transaction_amount;
   // var_dump($contents) ;
} else {
    $email = '';
    //$status = '' ;
    $payment_method = '';
    $payment_type = '';
    $total = 0;
}

curl_close($curl);


echo '<h3> Pago Exitoso..</h3>';

$sql = $con->prepare("INSERT INTO COBROS_MP (payment_id, _status, email, payment_type, payment_method, order_id, external_reference, collection_id, preference_id, fchpago, total) 
values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$sql->execute([$payment, $status, $email, $payment_type, $payment_method, $order_id, $external_reference, $collection_id, $preference_id, $fch, $total]);
$idcobro = $con->lastInsertId();
$productos = isset($_SESSION['carrito']['producto']) ? $_SESSION['carrito']['producto'] : null;

$noti = "<html>" ;
$noti = $noti . '<h4>Detalle del pago realizado</h4>' ;
$noti = $noti . '<br> id del pago: ' . $payment ;

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {
        $sql = $con->prepare("SELECT id, nombre, idcta, impo1, impo2, impo3, impo4, vto1, vto2, vto3, vto4,
            periodo, idabonado, docu, cantvtos FROM CTABOTONPAGO WHERE id=? AND pagado=0");
        $sql->execute([$clave]);
        $dato = $sql->fetch(PDO::FETCH_ASSOC);

        $idcta = $dato['idcta'];
        $periodo = $dato['periodo'];
        $cantvtos = $dato['cantvtos'];
        $idabonado = $dato['idabonado'] ;

        $id = $dato['id'];
        $nombre = $dato['nombre'] . '[' . $dato['periodo'] . ']';
        $impo1 = $dato['impo1'];
        $importe = $dato['impo1'];
        $qvto = $dato['vto1'];

        $fecha_actual = date("Y-m-d");

        //$fecha_entrada = strtotime("19-11-2008 21:00:00");

        switch ($cantvtos) {
            case 1:
                //code to be executed if n=label1;
                $importe = $dato['impo1'];
                $qvto = $dato['vto1'];
                break;
            case 2:
                //code to be executed if n=label2;
                if ($fecha_actual <= $dato['vto1']) {
                    $importe = $dato['impo1'];
                    $qvto = $dato['vto1'];
                } else {
                    $importe = $dato['impo2'];
                    $qvto = $dato['vto2'];
                }
                break;
            case 3:
                // code to be executed if n=label3;
                if ($fecha_actual <= $dato['vto1']) {
                    $importe = $dato['impo1'];
                    $qvto = $dato['vto1'];
                } else if ($fecha_actual <= $dato['vto2']) {
                    $importe = $dato['impo2'];
                    $qvto = $dato['vto2'];
                } else {
                    $importe = $dato['impo3'];
                    $qvto = $dato['vto3'];
                }
                break;
                //...
            case 4:
                // code to be executed if n=label3;
                if ($fecha_actual <= $dato['vto1']) {
                    $importe = $dato['impo1'];
                    $qvto = $dato['vto1'];
                } else if ($fecha_actual <= $dato['vto2']) {
                    $importe = $dato['impo2'];
                    $qvto = $dato['vto2'];
                } else if ($fecha_actual <= $dato['vto3']) {
                    $importe = $dato['impo3'];
                    $qvto = $dato['vto3'];
                } else {
                    $importe = $dato['impo4'];
                    $qvto = $dato['vto4'];
                }
                break;
            default:
                $importe = $dato['impo1'];
                $qvto = $dato['vto1'];
                // code to be executed if n is different from all labels;
        }



        $subtotal = $importe;

        $sql_insert = $con->prepare("INSERT INTO COBROS_MP_DETALLE (id_cobrosmp, idabonado, periodo, importe, detalle,
         fchpago, idcta, idctapago) VALUE (?, ?, ?, ?, ?, ?, ?, ?)");
        $sql_insert->execute([$idcobro, $idabonado, $periodo, $subtotal, $nombre,  $fecha_actual, $idcta, $id]);


        $pagado = 1 ;
        $idcob = 1;
        $tpago = 1 ;
        $idcaja = 1 ;

        $noti = $noti . '<br> idabonado: ' . $idabonado  ;
        $noti = $noti . '<br> Periodo: ' . $periodo ;
        $noti = $noti . '<br> Detalle: ' . $nombre ;
        $noti = $noti . '<br> Importe: ' . $subtotal ;
        $noti = $noti . '<br><hr>' ;

        $sql_upd = $con->prepare("UPDATE CTABOTONPAGO SET qimpo = :qimpo, pagado = :pagado, idcob = :idcob, tpago = :tpago,
        idcaja = :idcaja, fchpago = :fch, pago_id = :pago_id where id = :id") ;
        $sql_upd->bindParam(':qimpo', $subtotal);
        $sql_upd->bindParam(':pagado', $pagado);
        $sql_upd->bindParam(':idcob', $idcob);
        $sql_upd->bindParam(':tpago', $tpago);
        $sql_upd->bindParam(':idcaja', $idcaja);
        $sql_upd->bindParam(':fch', $fecha_actual);
        $sql_upd->bindParam(':pago_id', $payment);
        $sql_upd->bindParam(':id', $id);
        $sql_upd->execute() ;
    }
    $noti = $noti . '<br> Total: ' . $total ;
    $noti = $noti . '</html>' ;
    if ($email != '') {
        include 'enviar_email.php';
    }
}

unset($_SESSION['carrito']);

header('Location: ../index.php');





/*
http://localhost/shop-sixtored/captura.php?collection_id=1244496718&collection_status=approved&
payment_id=1244496718
&status=approved
&external_reference=null
&payment_type=credit_card
&merchant_order_id=3756441306
&preference_id=78486616-56604ae3-41de-467e-b07d-d914e7c6c90e
&site_id=MLA
&processing_mode=aggregator
&merchant_account_id=null
*/
