<?php
require 'config/config.php';
require 'config/database.php';
require 'vendor/autoload.php';

// Compruebo que la url tenga el ?code= y el state de mercadopago
$code = isset($_GET['code'])?$_GET['code']:"";
if (isset($_GET['code']) and isset($_GET['state'])) {

    $db = new Database();
    $con = $db->conectar();

    $ok = false ;
    $code =  $_GET['code'];
    $state = $_GET['state'];
    // Configuro para hacer el POST y obtener el token y datos del usuario

    $url = 'https://api.mercadopago.com/oauth/token';
    $post = '&client_secret='.CLIENT_SECRETMP.'&client_id='.CLIENT_IDMP.'&grant_type=authorization_code&code=' . $code . '&redirect_uri=https://www.quimili.com.ar/bpago/redirecuri.php';
    //$post = '&client_secret='.ACCESS_TOKEN_MARKETPLACE.'&grant_type=authorization_code&code=' . $code . '&redirect_uri=https://www.quimili.com.ar/bpago/redirecuri.php';

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    //curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . CLIENT_SECRETMP));


    $response = curl_exec($curl);

    //var_dump($response) ;
    /*
    {"access_token":"APP_USR-1827711016259681-011905-46b99b8aa9760afa190a8d377522c713-1057624532",
    "token_type":"bearer",
    "expires_in":15552000,
    "scope":"offline_access read write",
    "user_id":1057624532,
    "refresh_token":"TG-61e79c793fcc7f001a5016c9-1057624532",
    "public_key":"APP_USR-d1a5f6ac-3aaa-4bcd-a4e0-8892c0ea998d",
    "live_mode":true}"
 */

    if (curl_errno($curl)) echo curl_errno($curl);
    else $contents = json_decode($response, true);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    echo $httpcode ;
    var_dump($contents);
    if (isset($contents['user_id'])) {
        $ok = true ;
       
        $access_token   = $contents['access_token'];
        $token_type     = $contents['token_type'];
        $expires_in     = $contents['expires_in'];
        $scope          = $contents['scope'];
        $user_id        = $contents['user_id'];
        $refresh_token  = $contents['refresh_token'];
        $public_key     = $contents['public_key'];
        $live_mode      = $contents['live_mode'];
        $hoy = date('Y-m-d') ;
        $dias = intval($expires_in/86400) ;
        $expira = strtotime($hoy." + $dias days") ;
        if ($live_mode) $mpstatus = 1;
        else $mpstatus = 0;

        $sql = $con->prepare("SELECT count(mp_user_id) FROM MP_USERS WHERE mp_user_id=?");
        $sql->execute([$user_id]);
        if ($sql->fetchColumn() > 0) {
            $sql = $con->prepare("SELECT mp_user_id, id FROM MP_USERS WHERE mp_user_id == ?");
            $sql->execute([$user_id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $idusers = $row['id'];
            // UPDATE..
            $sql_upd = $con->prepare("UPDATE MP_USERS SET mp_access_token = :access_token, mp_public_key = :public_key,
            mp_refesh_token = :refresh_token, mp_expired_in = :expired_in, mp_status = :mpstatus, mp_scope = :scope,
            mp_token_type = :token_type, mp_livemode = :livemode where id = :id");
            $sql_upd->bindParam(':access_token', $access_token);
            $sql_upd->bindParam(':public_key', $public_key);
            $sql_upd->bindParam(':refresh_token', $refresh_token);
            $sql_upd->bindParam(':expired_in', $expira);
            $sql_upd->bindParam(':mpstatus', $mpstatus);
            $sql_upd->bindParam(':scope', $scope);
            $sql_upd->bindParam(':token_type', $token_type);
            $sql_upd->bindParam(':livemode', $live_mode);
            $sql_upd->bindParam(':id', $idusers);
            $sql_upd->execute();
        } else {
            // INSERT

            $sql_insert = $con->prepare("INSERT INTO MP_USERS (mp_access_token, mp_public_key, mp_refresh_token, 
            mp_user_id, mp_expired_in, mp_status, mp_scope, mp_token_type, mp_livemode)
            VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $sql_insert->execute([$access_token, $public_key, $refresh_token, $user_id, $expira, $mpstatus,  $scope, $token_type, $live_mode]);
            echo '------<br>';
            echo $access_token . '<br>';
            echo $public_key . '<br>';
            echo $refresh_token . '<br>';
            echo $mpstatus . '<br>';
            echo $scope . '<br>';
            echo $token_type . '<br>';
            echo $user_id . '<br>';
        }
        $ok = true ;
    } else {
        $ok = false;
    }

    curl_close($curl);
    if ($ok) echo http_response_code(200) ; else echo http_response_code(400);
   // var_dump($contents) ;

    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>PAGOS - SIXTORED</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <img src="images/sixtored_logo.png" class="img-thumbnail rounded mx-auto d-block">
                                    <h4 class="text-center font-weight-light my-4">Aplicacion Autorizada</h4>
                                </div>
                                <div class="card-body">
                                  <?php   
                                  if ($ok) {
                                    echo 'user id:'.$user_id . '<br>';
                                    echo 'scope:'.$scope . '<br>';
                                    echo 'token_type: '.$token_type . '<br>';
                                  } else {
                                      echo '<br>'.'No se pudo autorizar.. intente mas tarde..' ;
                                  }
                                  ?>  

                                    <div class="d-flex align-items-center justify-content-center mt-4 mb-0">    
                                        <a href="index.php" class="btn btn-primary">Ir a inicio</a>
                                    </div>
                                </div>
                                    
                                <div class="card-footer text-center py-3">
                                 <p>Autoriza esta aplicacion a gestionar cobros por mercadopago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Sixtored <?php echo date('Y'); ?>
                            <a href="https://www.facebook.com/sixtoredsoftware" target="_blank">facebook</a>
                            &middot;
                            <a href="https://www.sixtored.com.ar/" target="_blank">WebSite</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    

    
</body>

</html>

<?php     
}

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