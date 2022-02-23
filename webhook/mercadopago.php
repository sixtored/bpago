<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../vendor/autoload.php';

$db = new Database();
$con = $db->conectar();

MercadoPago\SDK::setAccessToken(ACCESS_TOKEN_MARKETPLACE);
// MercadoPago\SDK::setIntegratorId(getenv('MP_DEV_CODE'));
//$info = json_decode($this->input->raw_input_stream);
$json = file_get_contents('php://input');
$info = json_decode($json);

//if(is_array($info)){
$archivo = fopen('appication.txt', 'w+');
fwrite($fh, $json);
//fputs($archivo, $info);
fclose($archivo);
//echo http_response_code(200) ;
//} 

if (isset($info->topic)) {
    $collection_id = $info->resource;
    $user_id = $info->user_id;
    $topic = $info->topic;
    $application_id = $info->application_id;
    $version = $info->attempts;
    $sql = $con->prepare("INSERT INTO WEBHOOKS (aplication_id, user_id, version, type, info, resource)
                VALUE (?, ?, ?, ?, ?, ?)");

    $sql->execute([$application_id, $user_id, $version, $topic, $json, $collection_id]);
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

echo http_response_code(200);
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

*/

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
