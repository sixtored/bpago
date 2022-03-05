<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../vendor/autoload.php';

$db = new Database();
$con = $db->conectar();

$clave = 1 ;

$sql = $con->prepare("SELECT mp_access_token, mp_public_key, mp_user_id, mp_expired_in FROM MP_USERS WHERE id=?");
$sql->execute([$clave]);
$dato = $sql->fetch(PDO::FETCH_ASSOC);

//echo $dato['mp_access_token'];
$mpaccess_token = $dato['mp_access_token'];
$mp_public_key = $dato['mp_public_key'];
//echo $mpaccess_token ;
//echo '<br>' ;
//echo $mp_public_key ;

MercadoPago\SDK::setAccessToken($mpaccess_token);

//MercadoPago\SDK::setAccessToken(ACCESS_TOKEN_MARKETPLACE);
// MercadoPago\SDK::setIntegratorId(getenv('MP_DEV_CODE'));
//$info = json_decode($this->input->raw_input_stream);
$json = file_get_contents('php://input');
$info = json_decode($json);

/*
$sql = $con->prepare("INSERT INTO WEBHOOKS (type, info, action, live_mode)
            VALUE (?, ?, ?, ?)");

$sql->execute(['pueba', $json, 'test', 1]);
*/

$collection_id = $info->resource;
$data_id = substr($collection_id, 13);
$user_id = $info->user_id;
$topic = $info->topic;
$application_id = $info->application_id;
$version = $info->attempts;
$action = 'topic';
$live_mode = 1;

if (isset($info->topic)) {
    $collection_id = $info->resource;
    $data_id = substr($collection_id, 13);
    $user_id = $info->user_id;
    $topic = $info->topic;
    $application_id = $info->application_id;
    $version = $info->attempts;
    $action = 'topic';
    $live_mode = 1;
    $sql = $con->prepare("INSERT INTO WEBHOOKS (aplication_id, user_id, version, type, info, data_id, resource, action, live_mode)
                VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $sql->execute([$application_id, $user_id, $version, $topic, $json, $data_id, $collection_id, $action, $live_mode]);

    switch ($topic) {
        case 'payments':


            $url = 'https://api.mercadopago.com/v1/payments/' . $data_id;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . ACCESS_TOKEN_MARKETPLACE));


            $response = curl_exec($curl);

            if (curl_errno($curl)) {echo curl_errno($curl); }
            else { 
                $contents = json_decode($response, true);
                $obj = json_decode($response) ;
            }    

            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($httpcode == 200) {
                if (isset($contents['payer']['email'])) $email = $contents['payer']['email'] ; else $email = ' ' ;
                //$status = $contents->status ;
                $payment_method = $contents['payment_method_id'];
                $payment_type = $contents['payment_type_id'];
                $total = (float) $contents['transaction_amount'];
                $netocob = (float) $obj->transaction_details->net_received_amount ;
               // $com_mk =  json_decode(json_encode($obj->charges_details,true),true) ;
                $data = (array) $contents['additional_info']['items'] ;
               // $commk = (float) $com_mk['0']['amounts']['original'] ;
                $external_reference = $contents['external_reference'] ;
                $com_mp =  json_decode(json_encode($obj->fee_details,true),true) ;
                $commp = (float) $com_mp['0']['amount'] ;
                // comision de mercado pago
                $commk = (float) $com_mp['1']['amount'] ;

                $netocob = $total - ($commp + $commk) ;
                // comision del marketplace..
                //var_dump($com_mp) ;
                
                $tpago = 1;
                $d = new DateTime($contents['date_created']);
                $fch = $d->format('Y-m-d H:i:s');
                $idcaja = 1;
                $idcob = 1;
                $payment = $data_id;
                $status = $contents['status'];
                if (isset($contents['collector_id'])) $collection_id = $contents['collector_id']; else $collection_id = '' ;
                $preference_id = $contents['id'];

                $sql_consul = $con->prepare("SELECT id, payment_id FROM COBROS_MP WHERE payment_id =?");
                $sql_consul->execute([$payment]);
                if ($sql_consul->fetchColumn() > 0) {
                    // ya se encuentra
                } else {

                    $sql = $con->prepare("INSERT INTO COBROS_MP (payment_id, _status, email, payment_type, payment_method, order_id, external_reference, collection_id,
                     preference_id, fchpago, total, commp, commk, netocob) 
                                values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $sql->execute([$payment, $status, $email, $payment_type, $payment_method, $order_id, $external_reference, $collection_id, $preference_id, $fch, $total, $commp, $commk, $netocob]);
                    $idcobro = $con->lastInsertId();
                }


                foreach ($data as $item) {
                    $id = $item['id'];
                    $detalle = $item['title'] ;
                    // Buscamos si existe el item de pagado..
                    $subtotal = (float) $item['unit_price'];
                    $sql = $con->prepare("SELECT id, idcta, idabonado, pagado, periodo, nombre FROM CTABOTONPAGO WHERE id = ? and pagado = 0");
                    $sql->execute([$id]);
                    if ($sql->fetchColumn() > 0) {
                        // existe el item pagado.. actualizamos el pago.. del abono 
                        $dato = $sql->fetch(PDO::FETCH_ASSOC);
                        $nombre = $dato['nombre'] . '[' . $dato['periodo'] . ']';
                        $idabonado = $dato['idabonado'];
                        $idcta = $dato['idcta'];
                        $periodo = $dato['periodo'];
                        $pagado = 1 ;

                        $sql_upd = $con->prepare("UPDATE CTABOTONPAGO SET qimpo = :_qimpo, pagado = :_pagado, idcob = :_idcob, tpago = :_tpago,
                        idcaja = :_idcaja, fchpago = :_fch, pago_id = :_pago_id where id = :_id");
                        $sql_upd->bindParam(':_qimpo', $subtotal);
                        $sql_upd->bindParam(':_pagado', $pagado);
                        $sql_upd->bindParam(':_idcob', $idcob);
                        $sql_upd->bindParam(':_tpago', $tpago);
                        $sql_upd->bindParam(':_idcaja', $idcaja);
                        $sql_upd->bindParam(':_fch', $fch);
                        $sql_upd->bindParam(':_pago_id', $payment);
                        $sql_upd->bindParam(':_id', $id);
                        $sql_upd->execute();


                        $sql_insert = $con->prepare("INSERT INTO COBROS_MP_DETALLE (id_cobrosmp, idabonado, periodo, importe, detalle,
                        fchpago, idcta, idctapago) VALUE (?, ?, ?, ?, ?, ?, ?, ?)");
                        $sql_insert->execute([$idcobro, $idabonado, $periodo, $subtotal, $detalle,  $fch, $idcta, $id]);
                    }
                }
                return http_response_code(200);
            } else {
                // no existe el id del pago..
               return http_response_code(200);
                $email = '';
                //$status = '' ;
                $payment_method = '';
                $payment_type = '';
                $total = 0;
            }

           

            break;

        default:
            $sql = $con->prepare("INSERT INTO WEBHOOKS (type, info, action, live_mode)
            VALUE (?, ?, ?, ?)");
            $sql->execute(['default', $json, 'test', 1]);
    }
} else {
    if (isset($info->type)) {

        $sql = $con->prepare("INSERT INTO WEBHOOKS (id_mp, live_mode, aplication_id, user_id, version, api_version, type, action, info)
        VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($info->live_mode = "true") $live_mode = 1;
        else $live_mode = 0;
        $id             = $info->id;
        $application_id = $info->application_id;
        $user_id        = $info->user_id;
        //$version        = $info->version ;
        $version = '22';
        $api_version    = $info->api_version;
        $type           = $info->type;
        $action         = $info->action;
        $sql->execute([$id, $live_mode, $application_id, $user_id, $version, $api_version, $type, $action, $json]);
    }
}

/*
$sql = $con->prepare("INSERT INTO WEBHOOKS (id_mp, live_mode, aplication_id, user_id, version, api_version, type, action, info)
                VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)");
if ($info->live_mode = "true") $live_mode = 1;
else $live_mode = 0;
$id             = $info->id;
$application_id = $info->application_id;
$user_id        = $info->user_id;
//$version        = $info->version ;
$version = '22';
$api_version    = $info->api_version;
$type           = $info->type;
$action         = $info->action;
$sql->execute([$id, $live_mode, $application_id, $user_id, $version, $api_version, $type, $action, $json]);


echo http_response_code(200);



if (isset($info->type)) {
    switch ($info->type) {
        case 'mp-connect':
            // Desvinculo de mi sistema cuando el usuario desautoriza la app desde su cuenta de Mercadopago.
            if ($info->action == 'application.deauthorized') {

                $data_update = array(
                    'mp_access_token' => NULL,
                    'mp_public_key' => NULL,
                    'mp_refresh_token' => NULL,
                    'mp_user_id' => NULL,
                    'mp_expires_in' => NULL,
                    'mp_status' => 0
                );

                // $this->producers->update_mp_connect($data_update, $info->user_id);
                // $this->output->set_status_header(200);
                //updated_users($data_update,$info->user_id) ;
                //var_dump($data_update) ;
                $archivo = fopen('appication.txt', 'w+');
                fwrite($fh, $info);
                fclose($archivo);
                echo http_response_code(200);
                return;
            }

            // Pueden tomar otra acción si el $info->action = 'application.authrized'
            break;

        case 'payment':
            // Actualizo la información de pago recibida.
            $or_collection_id = $info->data->id;
            $info = MercadoPago\Payment::find_by_id($or_collection_id);
            $or_number = $info->external_reference;

            $data_update = array(
                'or_collection_status' => $info->status,
                'or_collection_status_detail' => $info->status_detail,
                'or_payment_type' => $info->payment_type_id,
                'or_payment_method' => $info->payment_method_id,
                'or_status' => ''
            );
            //gcfg($info->status,'or_status_collection_status')

            $archivo = fopen('payment.txt', 'w+');
            fwrite($fh, $info);
            fclose($archivo);
            echo http_response_code(200);

            // $this->cart->update_ipn_order($data_update,$or_number);

            break;

        default:
            $archivo = fopen('default.txt', 'w+');
            fwrite($fh, $info);
            fclose($archivo);
            echo http_response_code(200);
            return;
            break;
    }
}
echo http_response_code(200);
return;
*/
