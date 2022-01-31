<?php 
require 'config/config.php';
require 'config/database.php';
require 'vendor/autoload.php' ;
//MercadoPago\SDK::setAccessToken(TOKEN_MP);

$idstatus = uniqid();

MercadoPago\SDK::setAccessToken(ACCESS_TOKEN_MARKETPLACE);
//MercadoPago\SDK::setIntegratorId(getenv('MP_DEV_CODE'));
//$info = json_decode($this->input->raw_input_stream);
$json = file_get_contents('php://input') ;
$datos = json_decode($json, true) ;
var_dump($datos) ;

var_dump(http_response_code(200)) ;

/*

class Webhooks {

    public function webhook()
    {
        MercadoPago\SDK::setAccessToken(ACCESS_TOKEN_MARKETPLACE);
        //MercadoPago\SDK::setIntegratorId(getenv('MP_DEV_CODE'));
        $info = json_decode($this->input->raw_input_stream);
    
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
    
                        $this->producers->update_mp_connect($data_update, $info->user_id);
                        $this->output->set_status_header(200);
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
                        'or_status' => gcfg($info->status,'or_status_collection_status')
                    );
    
                    $this->cart->update_ipn_order($data_update,$or_number);
    
                break;
    
                default:
                    $this->output->set_status_header(200);
                    return;
                break;
            }
        }
        $this->output->set_status_header(200);
        return;
    }   

 public function auth_provider()
    {
        $code = $this->input->get('code');
        $state = $this->input->get('state');

        // Compruebo que la url tenga el ?code= y el state de mercadopago
        if (isset($code) AND isset($docId)) {

            // Configuro para hacer el POST y obtener el token y datos del usuario

            $url = 'https://api.mercadopago.com/oauth/token';
            $post = '&client_secret='.$this->accessToken.'&grant_type=authorization_code&code='.$code.'&redirect_uri=https://dominio.com/auth/mercadopago';

            $mpResp = $this->utility->curlAPIRestPOST($url,$post,$this->accessToken);

            if ($mpResp->status == 200) {

                // Actualizo en mi DB los datos obtenidos
                $info = array(
                    'mp_access_token' => $mpResp->access_token,
                    'mp_public_key' => $mpResp->public_key,
                    'mp_refresh_token' => $mpResp->refresh_token,
                    'mp_user_id' => $mpResp->user_id,
                    'mp_expires_in' => $mpResp->expires_in,
                    'mp_created_at' => date('Y-m-d H:i:s', time()),
                    'mp_scope' => $mpResp->scope,
                    'mp_live_mode' => $mpResp->live_mode,
                    'mp_token_type' => $mpResp->token_type,
                    'mp_status' => 1
                );

                $update = $this->professionals->update_info($info, $docId);

                // Esto no es necesario pero lo hago para obtener el nick en ML del usuario para que pueda identificar la cuenta
                $url_get = 'https://api.mercadolibre.com/users/me';

                $mpResp = $this->utility->curlAPIRestGET($url_get,$this->accessToken);

                $info = array(
                    'doc_mp_nick_name' => $mpResp->nickname,
                    'doc_email_mercadopago' => $mpResp->email
                );

                $update = $this->professionals->update_info($info, $pdocIdid);
            }
        }
        // Redireccionar a otra web o mostrarle una vista.
     //   ........
    }

    public function curlAPIRestPOST($url,$post,$accessToken)
    {
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".$accessToken,
              ),
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $contents = json_decode($response);

        if ($httpcode == 200) {
            $contents->status = 200;
            return $contents;
        } else {
            $contents->status = 400;
            return $contents;
        }
    }

    public function curlAPIRestGET($url, $accessToken)
    {
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".$accessToken,
              ),
        ]);

        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $contents = json_decode($response);

        if ($httpcode == 200) {
            $contents->status = 200;
            return $contents;
        } else {
            $contents->status = 400;
            return $contents;
        }
    }


}   

*/