<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=utf-8");

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../vendor/autoload.php';
require_once '../clases/Response.php';
$res = new Response();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $json = file_get_contents('php://input');
    $info = json_decode($json);
    if ((isset($info->topic)) || (isset($info->type))) {

        $db = new Database();
        $con = $db->conectar();


        $clave = 1;

        $sql = $con->prepare("SELECT mp_access_token, mp_public_key, mp_user_id, mp_expired_in FROM MP_USERS WHERE id=?");
        $sql->execute([$clave]);
        $dato = $sql->fetch(PDO::FETCH_ASSOC);


        $mpaccess_token = $dato['mp_access_token'];
        $mp_public_key = $dato['mp_public_key'];

        MercadoPago\SDK::setAccessToken($mpaccess_token);


        if (isset($info->topic)) {
            $collection_id = $info->resource;
            $data_id = substr($collection_id, 13);
            $user_id = $info->user_id;
            $topic = $info->topic;
            $application_id = $info->application_id;
            $version = $info->attempts;
            $action = 'topic';
            if ($info->live_mode = "true") {
            $live_mode = 1;
            } else {$live_mode = 0;}
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
                        else $email = '';
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
                        // comision del marketplace..
                        $netocob = $total - ($commp + $commk);
                        
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

                        $sql_consul = $con->prepare("SELECT id, payment_id FROM COBROS_MP WHERE payment_id =?");
                        $sql_consul->execute([$payment]);
                        if ($sql_consul->fetchColumn() > 0) {
                            // ya se encuentra
                            echo http_response_code(200);
                        } else {

                            $sql = $con->prepare("INSERT INTO COBROS_MP (payment_id, _status, email, payment_type, payment_method, order_id, external_reference, collection_id,
                     preference_id, fchpago, total, commp, commk, netocob) 
                                values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $sql->execute([$payment, $status, $email, $payment_type, $payment_method, $order_id, $external_reference, $collection_id, $preference_id, $fch, $total, $commp, $commk, $netocob]);
                            $idcobro = $con->lastInsertId();
          
                        $noti = '<html>' ;
                        $noti = $noti . '<br>'.'El id de su pago es: '. $payment ; ;
                        foreach ($data as $item) {
                            $id = $item['id'];
                            $detalle = $item['title'];
                            // Buscamos si existe el item de pagado..
                            $subtotal = (float) $item['unit_price'];
                            $sql = $con->prepare("SELECT id, idcta, idabonado, pagado, periodo, nombre FROM CTABOTONPAGO WHERE id = ? and pagado = 0");
                            $sql->execute([$id]);
                            if ($sql->fetchColumn() > 0) {
                                // existe el item pagado.. actualizamos el pago.. del abono 
                                $sql = $con->prepare("SELECT id, idcta, idabonado, pagado, periodo, nombre FROM CTABOTONPAGO WHERE id = ? and pagado = 0");
                                $sql->execute([$id]);
                                $dato = $sql->fetch(PDO::FETCH_ASSOC);

                                $nombre = $dato['nombre'] . '[' . $dato['periodo'] . ']';
                                $idabonado = $external_reference;
                                $idcta = $dato['idcta'];
                                $periodo = $dato['periodo'];
                                $pagado = 1;

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

                                    $noti = $noti . '<br> idabonado: ' . $idabonado;
                                    $noti = $noti . '<br> Periodo: ' . $periodo;
                                    $noti = $noti . '<br> Detalle: ' . $detalle;
                                    $noti = $noti . '<br> Importe: ' . $subtotal;
                                    $noti = $noti . '<br><hr>';

                        }

                        $noti = $noti . '<br> Total: ' . $total . '<br>';
                        $noti = $noti . URL_LINK_CONSULTA . '?payment=' . $payment ;
                        $noti = $noti . '</html>';

                       
                        if ($email != '') {
                            include 'enviar_email.php';
                        } else {
                            $email = 'sixtored@hotmail.com' ;
                            include 'enviar_email.php';
                        }


                       echo http_response_code(200);
                        //json_encode($res->getResponse("(OK)", $data_id, 200, "Pago Creado"));
                       
                        exit(1);
                    }
                    } else {
                        // no existe el id del pago..
                        $email = '';
                        //$status = '' ;
                        $payment_method = '';
                        $payment_type = '';
                        $total = 0;
                       echo http_response_code(200);
                        //echo json_encode($res->getResponse("(OK)", " ", 200, "Id Pago No exite.."));

                        exit(1);
                    }


                    break;

                default:
                    $sql = $con->prepare("INSERT INTO WEBHOOKS (type, info, action, live_mode)
                VALUE (?, ?, ?, ?)");
                    $sql->execute(['default topic', $json, 'test', 1]);
                   echo http_response_code(201);
                    $email = 'sixtored@hotmail.com' ;
                    $noti = 'Notificacion Test';
                    include '../clases/enviar_email.php';
                    //echo json_encode($res->getResponse("(CREATED)", " ", 201, "Topic no existe.."));
                    exit(1);
            }
        } else {
            if (isset($info->type)) {

                if ($info->live_mode = "true") $live_mode = 1;
                else $live_mode = 0;
                $id             = $info->id;
                $application_id = $info->application_id;
                $user_id        = $info->user_id;
                $version        = $info->version;
                //$version = '22';
                $api_version    = $info->api_version;
                $type           = $info->type;
                $action         = $info->action;
                $data_id        = $info->data->id;

                $sql = $con->prepare("INSERT INTO WEBHOOKS (id_mp, live_mode, aplication_id, user_id, version, api_version, type, action, info)
        VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $sql->execute([$id, $live_mode, $application_id, $user_id, $version, $api_version, $type, $action, $json]);

                switch ($type) {
                    case 'payments':


                        $url = 'https://api.mercadopago.com/v1/payments/' . $data_id;

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
                            else $email = ' ';
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

                            $sql_consul = $con->prepare("SELECT id, payment_id FROM COBROS_MP WHERE payment_id =?");
                            $sql_consul->execute([$payment]);
                            if ($sql_consul->fetchColumn() > 0) {
                                // ya se encuentra
                                echo http_response_code(200);
                            } else {

                                $sql = $con->prepare("INSERT INTO COBROS_MP (payment_id, _status, email, payment_type, payment_method, order_id, external_reference, collection_id,
                         preference_id, fchpago, total, commp, commk, netocob) 
                                    values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                $sql->execute([$payment, $status, $email, $payment_type, $payment_method, $order_id, $external_reference, $collection_id, $preference_id, $fch, $total, $commp, $commk, $netocob]);
                                $idcobro = $con->lastInsertId();
                           

                            $noti = '<html>' ;
                            $noti = $noti . '<br>'.'El id de su pago es: '. $payment ; ;
                            foreach ($data as $item) {
                                $id = $item['id'];
                                $detalle = $item['title'];
                                // Buscamos si existe el item de pagado..
                                $subtotal = (float) $item['unit_price'];
                                $sql = $con->prepare("SELECT id, idcta, idabonado, pagado, periodo, nombre FROM CTABOTONPAGO WHERE id = ? and pagado = 0");
                                $sql->execute([$id]);
                                if ($sql->fetchColumn() > 0) {
                                    // existe el item pagado.. actualizamos el pago.. del abono 
                                    $sql = $con->prepare("SELECT id, idcta, idabonado, pagado, periodo, nombre FROM CTABOTONPAGO WHERE id = ? and pagado = 0");
                                    $sql->execute([$id]);
                                    $dato = $sql->fetch(PDO::FETCH_ASSOC);
                                    $nombre = $dato['nombre'] . '[' . $dato['periodo'] . ']';
                                    $idabonado = $external_reference ;
                                    $idcta = $dato['idcta'];
                                    $periodo = $dato['periodo'];
                                    $pagado = 1;

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


                                    $noti = $noti . '<br> idabonado: ' . $idabonado;
                                    $noti = $noti . '<br> Periodo: ' . $periodo;
                                    $noti = $noti . '<br> Detalle: ' . $detalle;
                                    $noti = $noti . '<br> Importe: ' . $subtotal;
                                    $noti = $noti . '<br><hr>';
                                   
                                }
                            }

                            $noti = $noti . '<br> Total: ' . $total . '<br>';
                            $noti = $noti . URL_LINK_CONSULTA . '?payment=' . $payment ;
                            $noti = $noti . '</html>';

                            if ($email != '') {
                                include 'enviar_email.php';
                            } else {
                                $email = 'sixtored@hotmail.com' ;
                                include 'enviar_email.php';
                            }

                           
                           echo http_response_code(200);
                        
                            // echo json_encode($res->getResponse("(OK)", $data_id, 200, "Pago Creado"));
                            exit(1);
                            //http_response_code(200);
                        }
                        } else {
                            // no existe el id del pago..
                            $email = '';
                            //$status = '' ;
                            $payment_method = '';
                            $payment_type = '';
                            $total = 0;
                            echo http_response_code(200);
                            // echo json_encode($res->getResponse("(OK)", " ", 200, "Id Pago No existe"));
                            exit(1);
                        }


                        break;

                    default:
/*
                        $email = 'sixtored@hotmail.com' ;
                        $noti = 'Notificacion Test';
                        include 'enviar_email.php';
*/

                        $sql = $con->prepare("INSERT INTO WEBHOOKS (type, info, action, live_mode)
                        VALUE (?, ?, ?, ?)");
                        $sql->execute(['default', $json, 'test', 1]);
                        //echo http_response_code(201);
                        //echo json_encode($res->getResponse("(CREATED)", " ", 201, "Type no existe.."));
                        
                        echo http_response_code(200);
                       
  
                        exit(1);
                }
            }
        }
    } else {
        //echo http_response_code(405); // Method not allowed
        //echo json_encode($res->getResponse("warning", null, 405, "Estructura no valida.."));
       echo http_response_code(405);
        exit(1);
    }
} else {
    // echo http_response_code(405); // Method not allowed
    //echo json_encode($res->getResponse("warning", null, 405, "método no permitido"));

   echo http_response_code(405);
    exit(1);
}
