<?php

require '../config/config.php';
require '../config/database.php';

$db = new Database();
$con = $db->conectar();

date_default_timezone_set("America/Argentina/Buenos_Aires");
// setlocale(LC_TIME, 'spanish');
setlocale(LC_TIME, 'es_ES.UTF8');

$payment = $_GET['payment_id'];
$status = $_GET['status'];
//$payment_type = $_GET['payment_type'];
$order_id = ($_GET['merchant_order_id'] == NULL) ? '' : $_GET['merchant_order_id'];
$external_reference = ($_GET['external_reference'] == NULL) ? '' : $_GET['external_reference'];
$collection_id = $_GET['collection_id'];
$preference_id = $_GET['preference_id'];

$fch = date("Y-m-d");

/////////////////////////////////////////////


$url = 'https://api.mercadopago.com/v1/payments/' . $payment;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 5);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . ACCESS_TOKEN_MARKETPLACE));


$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo curl_errno($curl);
} else {
    $contents = json_decode($response, true);
    $obj = json_decode($response);
}

$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
if ($httpcode == 200) {
    if (isset($contents['payer']['email'])) $email = $contents['payer']['email'];
    else $email = 'sixtored@hotmail.com';
    //$status = $contents->status ;
    $payment_method = $contents['payment_method_id'];
    $payment_type = $contents['payment_type_id'];
    $total = (float) $contents['transaction_amount'];
    $netocob = (float) $obj->transaction_details->net_received_amount;
    // $com_mk =  json_decode(json_encode($obj->charges_details,true),true) ;
    $data = (array) $contents['additional_info']['items'];
    // $commk = (float) $com_mk['0']['amounts']['original'] ;
    $external_reference = $contents['external_reference'];
    $com_mp =  json_decode(json_encode($obj->fee_details, true), true);
    $commp = (float) $com_mp['0']['amount'];
    // comision de mercado pago
    $commk = (float) $com_mp['1']['amount'];

    $netocob = $total - ($commp + $commk);
    // comision del marketplace..
    //var_dump($com_mp) ;

    $tpago = 1;
    $d = new DateTime($contents['date_created']);
    $fch = $d->format('Y-m-d H:i:s');
    $idcaja = 1;
    $idcob = 1;
    $payment = $data_id;
    $status = $contents['status'];
    if (isset($contents['collector_id'])) $collection_id = $contents['collector_id'];
    else $collection_id = '';
    $preference_id = $contents['id'];
    $noti = '<html>';
    foreach ($data as $item) {
        $id = $item['id'];
        $detalle = $item['title'];
        // Buscamos si existe el item de pagado..
        $subtotal = (float) $item['unit_price'];
        $sql = $con->prepare("SELECT id, idcta, idabonado, pagado, periodo, nombre FROM CTABOTONPAGO WHERE id = ?");
        $sql->execute([$id]);
        if ($sql->fetchColumn() > 0) {
            // existe el item pagado.. actualizamos el pago.. del abono 
            $sql = $con->prepare("SELECT id, idcta, idabonado, pagado, periodo, nombre FROM CTABOTONPAGO WHERE id = ?");
            $sql->execute([$id]);
            $dato = $sql->fetch(PDO::FETCH_ASSOC);
            $nombre = $dato['nombre'] . '[' . $dato['periodo'] . ']';
            $idabonado = $dato['idabonado'];
            $idcta = $dato['idcta'];
            $periodo = $dato['periodo'];
            $pagado = 1;

            $noti = $noti . '<br> idabonado: ' . $idabonado;
            $noti = $noti . '<br> Periodo: ' . $periodo;
            $noti = $noti . '<br> Detalle: ' . $nombre;
            $noti = $noti . '<br> Importe: ' . $subtotal;
            $noti = $noti . '<br><hr>';



            $noti = $noti . '<br> Total: ' . $total. '<br>' ;
            $noti = $noti . '<a href="'.URL_LINK_CONSULTA.'/'.$payment.'"/>Comprobante</a>' ;
            $noti = $noti . '</html>';
        }
    }
    //http_response_code(200);
    //return 200 ;
}


//////////////////////////////////////////
curl_close($curl);


if ($email != '') {
    include 'enviar_email.php';
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
