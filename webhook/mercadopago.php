<?php 
    require_once '../config/config.php';
    require_once '../config/database.php';
    require_once '../vendor/autoload.php' ;
    
    $db = new Database();
    $con = $db->conectar();
    
    MercadoPago\SDK::setAccessToken('TEST-1491103792399277-100219-ba53f79570c6591431f83ae8d1b36a26-78486616');
   // MercadoPago\SDK::setIntegratorId(getenv('MP_DEV_CODE'));
    //$info = json_decode($this->input->raw_input_stream);
    $json = file_get_contents('php://input') ;
    $info = json_decode($json) ;

//if(is_array($info)){
    $archivo = fopen('appication.txt','w+');
    fputs($archivo, $info);
    fclose($archivo);
   echo http_response_code(200) ;
//}    

exit ;

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
                    $archivo = fopen('appication.txt','w+');
                    fputs($archivo,$info);
                    fclose($archivo);
                   echo http_response_code(200) ;
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

                $archivo = fopen('payment.txt','w+');
                    fputs($archivo,$info);
                    fclose($archivo);
                   echo http_response_code(200) ;

               // $this->cart->update_ipn_order($data_update,$or_number);

            break;

            default:
            $archivo = fopen('default.txt','w+');
            fputs($archivo,$info);
            fclose($archivo);
           echo http_response_code(200) ;
                return;
            break;
        }

    }
    echo http_response_code(200);
    return;