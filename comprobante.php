<?php
require_once 'config/config.php' ;
require_once 'config/database.php' ;

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

<?php 

if (isset($_GET['payment'])) {
    $payment = $_GET['payment'] ;
    $db = new Database() ;
    $con = $db->conectar();
    
    $sql = $con->prepare("SELECT id, idcta, idabonado, pagado, periodo, nombre, fchpago, 
    docu, precinto, pago_id, qimpo FROM CTABOTONPAGO WHERE pago_id = ? and pagado = 1");
    $sql->execute([$payment]);
    $dato = $sql->fetchAll(PDO::FETCH_ASSOC);
?>    

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
                                    <h4 class="text-center font-weight-light my-6">Detalle del pago</h4>
                                    <h4 class="text-center font-weight-light my-2"><?php echo $payment; ?></h4>
                                </div>
                                <div class="card-body">

                                <div class="table-responsive-sm">
                                        <table class="table table-sm text-balck">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Detalle</th>
                                                    <th scope="col">Periodo</th>
                                                    <th scope="col">Fch Pago</th>
                                                    <th scope="col">Importe/s</th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                  <?php 
                                  $total = 0.00 ;
                                  $ii = 1 ;
                                  foreach ($dato as $row) {?>
                                    <tr>
                                         <th scope="row"><?php echo $ii; ?></th>
                                            <td colspan="2"><?php echo $row['nombre'] . ' ' . $row['periodo']?></td>
                                            <td><?php echo date("d/m/Y", strtotime($row['fchpago'])) ?></td>
                                            <td><?php echo MONEDA . number_format($row['qimpo'], 2, ',', '.'); ?></td>
                                            <td></td>
                                    </tr>
                                   <?php  
        
                                    $total = $total + $row['qimpo'] ;
                                }
                                  ?>
                                            </tbody>
                                        </table>
                                </div>
                                </div>
                                <div class="card-footer text-center py-3">
                                <p class="h3" id="total">Total pagado<?php echo ' '.MONEDA . number_format($total, 2, ',', '.'); ?></p>

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
    
<script>

     
 </script>    
    
</body>

</html>


<?php


} else {

    header('Location: index.php');
}