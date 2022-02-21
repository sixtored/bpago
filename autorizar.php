<?php

require 'config/config.php';
require 'config/database.php';

$idstatus = uniqid();
$url = "https://auth.mercadopago.com.ar/authorization?client_id=".CLIENT_IDMP."&response_type=code&platform_id=mp&state=".$idstatus."&redirect_uri=https://www.quimili.com.ar/bpago/redirecuri.php" ;
//https://www.quimili.com.ar/bpago/webhooks.php?code=TG-61e77bcc3fcc7f001a4fe35f-1057629763&state=61e77b3313bc

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
                                    <h4 class="text-center font-weight-light my-4">Autorizar Aplicacion</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-center mt-4 mb-0">    
                                        <a href="<?php echo $url; ?>" class="btn btn-primary">Solicitar autorizacion</a>
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