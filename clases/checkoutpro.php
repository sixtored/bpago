<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../vendor/autoload.php' ;

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

//MercadoPago\SDK::setAccessToken('APP_USR-1670974527854290-011600-aedea8921d5723ed3df144485bdb1134-1057624532');
MercadoPago\SDK::setAccessToken($mpaccess_token);
//MercadoPago\SDK::setPlatformId("PLATFORM_ID");
//MercadoPago\SDK::setIntegratorId("INTEGRATOR_ID");
//MercadoPago\SDK::setCorporationId("CORPORATION_ID");

$preference = new MercadoPago\Preference() ;
$productos_mp = array() ;


$productos = isset($_SESSION['carrito']['producto']) ? $_SESSION['carrito']['producto'] : null;


$lista_carrito = array();

if ($productos != null) {
    foreach ($productos as $clave => $cantidad) {
        $sql = $con->prepare("SELECT id, nombre, idcta, impo1, impo2, impo3, impo4, vto1, vto2, vto3, vto4,
        periodo, idabonado, docu, cantvtos FROM CTABOTONPAGO WHERE id=? AND pagado=0");
        $sql->execute([$clave]);
        $lista_carrito[] = $sql->fetch(PDO::FETCH_ASSOC);
      //  print_r($lista_carrito) ;
    }
}
//print_r($lista_carrito) ;
//session_destroy();

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
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

    <link href="css/estilos.css" rel="stylesheet">
    <script src="https://sdk.mercadopago.com/js/v2"></script>
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
                                <img src="../images/sixtored_logo.png" class="img-thumbnail rounded mx-auto d-block">

                                    <h4 class="text-center font-weight-light my-4">Confirma el Pago</h4>
                                </div>
                                <div class="card-body">
                                
                                <div class="table-responsive-sm">
                            <table class="table table-sm text-balck">
                                <thead>
                                    <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Detalle</th>
                                    <th scope="col">Periodo</th>
                                    <th scope="col">Importe/s</th>
                                    <th scope="col"></th>
                                    </tr>
                                </thead>

                    <tbody>
                        <?php
                        if ($lista_carrito == null) {
                            echo '<tr><td colspan="5" class="text-center"><b>Lista vacia</b></td></tr>';
                        } else {
                            $total = 0;
                            $ii = 1 ;
                            foreach ($lista_carrito as $dato) 
                            {
                                
                                $idcta = $dato['idcta'];
                                $periodo = $dato['periodo'];
                                $cantvtos = $dato['cantvtos'] ;

                                $id = $dato['id'];
                                $nombre = $dato['nombre'].'['.$dato['periodo'].']';
                                $impo1 = $dato['impo1'];
                                $importe = $dato['impo1'] ;
                                $qvto = $dato['vto1'] ;
                                $docu = $dato['docu'] ;
                                $idabonado = $dato['idabonado'] ;

                                $fecha_actual = date("Y-m-d");                                     

                                    //$fecha_entrada = strtotime("19-11-2008 21:00:00");
                                    
                                    switch ($cantvtos) {
                                        case 1:
                                          //code to be executed if n=label1;
                                          $importe = $dato['impo1'] ;
                                          $qvto = $dato['vto1'] ;
                                          break;
                                        case 2:
                                          //code to be executed if n=label2;
                                          if ($fecha_actual<= $dato['vto1']) {
                                            $importe = $dato['impo1'] ;
                                            $qvto = $dato['vto1'] ; 
                                          } else {
                                            $importe = $dato['impo2'] ;
                                            $qvto = $dato['vto2'] ;  
                                          }
                                          break;
                                        case 3:
                                         // code to be executed if n=label3;
                                         if ($fecha_actual<= $dato['vto1']){
                                            $importe = $dato['impo1'] ;
                                            $qvto = $dato['vto1'] ; 
                                          } else if ($fecha_actual<= $dato['vto2']) {
                                            $importe = $dato['impo2'] ;
                                            $qvto = $dato['vto2'] ;  
                                          } else {
                                            $importe = $dato['impo3'] ;
                                            $qvto = $dato['vto3'] ;  
                                          }
                                          break;
                                          //...
                                          case 4:
                                            // code to be executed if n=label3;
                                            if ($fecha_actual<= $dato['vto1']) {
                                               $importe = $dato['impo1'] ;
                                               $qvto = $dato['vto1'] ; 
                                             } else if ($fecha_actual<= $dato['vto2']) {
                                               $importe = $dato['impo2'] ;
                                               $qvto = $dato['vto2'] ;  
                                             } else if ($fecha_actual<= $dato['vto3']) {
                                               $importe = $dato['impo3'] ;
                                               $qvto = $dato['vto3'] ;  
                                             } else {
                                                $importe = $dato['impo4'] ;
                                                $qvto = $dato['vto4'] ;  
                                             }
                                             break;  
                                        default:
                                        $importe = $dato['impo1'] ;
                                        $qvto = $dato['vto1'] ;
                                         // code to be executed if n is different from all labels;
                                      }

                                
                                
                                $subtotal = $importe ;
                                $total = $total + $importe ;
                                // ITEM DE MP
                                $item = new MercadoPago\Item();
                                $item->id = $id ;
                                $item->title = $nombre ;
                                $item->currency_id = "ARS";
                                $item->quantity =  1 ;
                                $item->unit_price = floatval($importe) ;
                                //$item->currency_id = 'AR' ;

                                array_push($productos_mp,$item) ;
                                unset($item) ;
                              

                        ?>
                               <tr>
                                    <th scope="row"><?php echo $ii ;?></th>
                                    <td colspan="2"><?php echo $nombre ; ?></td>
                                   
                                    <td><?php echo MONEDA . number_format($importe, 2, ',', '.') ; ?></td>
                                    <td><a id="elimina" class="btn btn-danger btn-sm" data-bs-id="<?php echo $id; ?>" data-bs-toggle="modal" data-bs-target="#eliminaModal"><i class="fas fa-trash-restore-alt"></i></a></td>
                                    </tr>
                        <?php
                            $ii = $ii + 1 ;  }
                        ?>
                        <tr>
                            <td colspan="3"></td>
                            <td colspan="2">
                               <p class="h3"  id="total"> <?php echo MONEDA . number_format($total, 2, ',', '.'); ?></p>
                            </td>
                        </tr>
                        <?php    
                        }
                        ?>
                    </tbody>
                    </table>

                    

                               
                                <?php 

                               /*
                                    $preference = new MercadoPago\Preference();
                                    //...
                                    $preference->back_urls = array(
                                        "success" => "https://www.tu-sitio/success",
                                        "failure" => "http://www.tu-sitio/failure",
                                        "pending" => "http://www.tu-sitio/pending"
                                    );
                                    $preference->auto_return = "approved";
                                    // ...
                                  */

                                if ($lista_carrito != null) {
                                    $preference->items = $productos_mp ;

                                    $preference->back_urls = array(
                                        "success" => "https://www.quimili.com.ar/bpago/clases/captura.php",
                                        "failure" => "https://www.quimili.com.ar/bpago/clases/fallo.php"
                                    );
                                    
                                    $preference->auto_return = "approved" ;
                                    $preference->binary_mode = true ;

                                    // Opcional por si quieren quitar métodos de pago de la preferencia y setear las cuotas
                                    /*
                                    array("id" => "atm"),
                                    array('id' => 'bank_transfer')
                                    */
                                    $preference->payment_methods = array(
                                        "excluded_payment_types" => array(
                                            array('id' => 'ticket')
                                        )
                                       // "installments" => 12,
                                       // "default_installments" => 1
                                    );
                                    
                                    $mp_fee_owner = ($total * 2)/100 ;
                                    // Creación de un código external reference para vincular el pago con un pedido en nuestra DB
                                    $preference->external_reference = $idcta ;

                                    // Si van a cobrar una comision por venta
                                    $preference->client_id = CLIENT_IDMP ;
                                    $preference->marketplace = 'MP-MKT-'.CLIENT_IDMP ;
                                    $preference->marketplace_fee = floatval($mp_fee_owner);

                                    // Opcional para setear las url del webhook
                                    $preference->notification_url = 'https://wwww.quimili.com.ar/bpago/webhook/mercadopago.php';

                
                                    // Redirecciona al webcheckout de mercadopago, pueden usar este método u otros como poner el link en un boton y mostrar en una vista con el detalle de la compra y el boton pagar.

                                   // redirect($preference->{gcfg('mp_mode',NULL)},'refresh');



                                    // Datos del cliente..
                                    $docu = 12345678 ;
/*
                                    $payer = new MercadoPago\Payer();
                                    $payer->name = $nombre;
                                    $payer->surname = '';
                                    $payer->email = '' ;
                                    $payer->date_created = date('Y-m-d\TH:i:s.vP') ;

                                    $payer->identification = array(
                                        "type" => "DNI",
                                        "number" => $docu 
                                    );
                                    $payer->identification_type = 'DNI';
                                    $payer->identification_number = $docu;
                                    $payer->phone = array(
                                        "area_code" => "54",
                                        "number" => '3843461578'
                                    );
                                    $payer->area_code = '54';
                                    $payer->number = '3843461578';

                                    $payer->address = array(
                                        "street_name" => 'Avellaneda',
                                        "zip_code" => '3740'
                                        );

                                    $payer->authentication_type = 'Web Nativa'; // Pueden ser Gmail, Facebook, Web Nativa, Otro.
                                    $payer->registration_date = date('Y-m-d\TH:i:s.vP') ;
                                    $payer->is_first_purchase_online = true ;
                                    $payer->last_purchase = '';

                                    $preference->payer = $payer;
                                  var_dump($preference) ;
                                  */
                                    
                                    $preference->save();
                                ?>  
                                <div class="row">              
                                   <div class="col-md-8 offset-md-6 d-grid gap-2">
                                        <div class="checkout-btn">

                                        </div>
                                   </div>
                                </div>  
                                <?php } ?>  
                                <div class="row"> 
                                    <div class="col-md-6 offset-md-6 d-grid gap-2">
                                        <a href="../index.php" class="btn btn-secondary" >Volver</a>
                                    </div>
                                </div>
                                    
                               
                                <div class="card-footer text-center py-3">
                                    <span>3 de 3</span>
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


           
    <!-- Modal -->
<div class="modal fade" id="eliminaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Alerta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ¿desea eliminar el periodo de la lista..?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button id="btn-elimina" type="button" class="btn btn-danger" onclick="eliminar()">Eliminar</button>
      </div>
    </div>
  </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script>


        let eliminaModal = document.getElementById('eliminaModal') ;
        eliminaModal.addEventListener('show.bs.modal', function(event) {
            let button = event.relatedTarget 
            let id = button.getAttribute('data-bs-id') 
            let buttonelimina = eliminaModal.querySelector('.modal-footer #btn-elimina') 
            buttonelimina.value = id 
        }) ;


        function actualizaCantidad(cantidad, id) {
            let url = 'clases/actuliza_carrito.php';
            let formData = new FormData();
            formData.append('action', 'agregar');
            formData.append('id', id);
            formData.append('cantidad', cantidad);

            fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json()).then(data => {
                if (data.ok) {

                    let divsubtotal = document.getElementById('subtotal_'+id) ;
                    divsubtotal.innerHTML = data.sub ;

                    let total = 0.00 ;
                    let list = document.getElementsByName('subtotal[]') ;

                    for(let i=0; i<list.length; i++){
                        total += parseFloat(list[i].innerHTML.replace(/[$.]/g,'')) ;
                    }
                    //{minimumFractionDigits: 2}
                    total = new Intl.NumberFormat("de-DE",{minimumFractionDigits: 2}).format(total);
                    document.getElementById('total').innerHTML = '<?php echo MONEDA ;?>' + total ;
                    
                }
            })

        }


        function eliminar() {

            let botonelimina = document.getElementById('btn-elimina') 
            let id = botonelimina.value 
           // print_r(id) ;
            let url = 'actuliza_deuda.php'
            let formData = new FormData()
            formData.append('action', 'eliminar')
            formData.append('id', id)

            fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json()).then(data => {
                if (data.ok) {

                    location.reload()
                    
                }
            })

        }

       

        const mp = new MercadoPago(<?php echo "'".$mp_public_key."'" ?>,{locale: 'es-AR'})

        mp.checkout({
            preference: {
                id: '<?php echo $preference->id ; ?>'
            }, 
            render: {
                container: '.checkout-btn',
                label: 'Pagar con Mercado Pago'
            }
        })



    </script>

</body>

</html>