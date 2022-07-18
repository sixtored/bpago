<?php
require '../config/config.php';
require '../config/database.php';

if (isset($_SESSION['carrito']['producto'])) {
    session_unset();
}

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
    <link href="../css/estilos.css" rel="stylesheet">

</head>
<?php

if (isset($_POST['action'])) {

    $action = $_POST['action'];

    $id = isset($_POST['idabonado']) ? $_POST['idabonado'] : 0;
    $docu = isset($_POST['dni']) ? $_POST['dni'] : '';

    //echo $action. '<br>' .$id .'<br>' .$docu;

    //exit ;
    $res = array();
    $datos['ok'] = true;

    if ($id == 0 && $docu <> '') {
        //// BUSCAR IDABONADO
        $db = new Database();
        $con = $db->conectar();
        $sql = $con->prepare("SELECT count(idabonado) FROM CTABOTONPAGO WHERE docu=? AND pagado=0");
        $sql->execute([$docu]);
        if ($sql->fetchColumn() > 0) {

            $sql = $con->prepare("SELECT idabonado, docu, idcta, id FROM CTABOTONPAGO WHERE docu=? and pagado = 0 LIMIT 1");
            $sql->execute([$docu]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $idabonado = $row['idabonado'];
            $idcta = $row['idcta'];
            $_id = $row['id'];
            $id = $idabonado;
        } else {
            $datos['ok'] = false;
        }
    }

    if ($action == 'agregar' && $datos['ok']) {
        //// AGREGAR ////
        $res = agregar($id);
        /// MOSTRAR DEUDA... ///

    } else if ($action == 'eliminar') {
        ///// ELIMINAR /////
        $datos['ok'] = eliminar($id);
    } else {
        $datos['ok'] = false;
    }
} else {
    $datos['ok'] = false;
}

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
                                <img src="../images/sixtored_logo.png" class="img-thumbnail rounded mx-auto d-block">

                                    <h4 class="text-center font-weight-light my-4">Selecciona tu deuda</h4>
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

                                                if (count($res) > 0) { ?>
                                                    <?php
                                                    $total = 0.00;
                                                    $ii = 1;
                                                    foreach ($res as $dato) {
                                                        $cantvtos = $dato['cantvtos'];
                                                        $importe = $dato['impo1'];
                                                        $qvto = $dato['vto1'];

                                                        $fecha_actual = date("Y-m-d");

                                                        //$fecha_entrada = strtotime("19-11-2008 21:00:00");

                                                        switch ($cantvtos) {
                                                            case 1:
                                                                //code to be executed if n=label1;
                                                                $importe = $dato['impo1'];
                                                                $qvto = $dato['vto1'];
                                                                break;
                                                            case 2:
                                                                //code to be executed if n=label2;
                                                                if ($fecha_actual <= $dato['vto1']) {
                                                                    $importe = $dato['impo1'];
                                                                    $qvto = $dato['vto1'];
                                                                } else {
                                                                    $importe = $dato['impo2'];
                                                                    $qvto = $dato['vto2'];
                                                                }
                                                                break;
                                                            case 3:
                                                                // code to be executed if n=label3;
                                                                if ($fecha_actual <= $dato['vto1']) {
                                                                    $importe = $dato['impo1'];
                                                                    $qvto = $dato['vto1'];
                                                                } else if ($fecha_actual <= $dato['vto2']) {
                                                                    $importe = $dato['impo2'];
                                                                    $qvto = $dato['vto2'];
                                                                } else {
                                                                    $importe = $dato['impo3'];
                                                                    $qvto = $dato['vto3'];
                                                                }
                                                                break;
                                                                //...
                                                            case 4:
                                                                // code to be executed if n=label3;
                                                                if ($fecha_actual <= $dato['vto1']) {
                                                                    $importe = $dato['impo1'];
                                                                    $qvto = $dato['vto1'];
                                                                } else if ($fecha_actual <= $dato['vto2']) {
                                                                    $importe = $dato['impo2'];
                                                                    $qvto = $dato['vto2'];
                                                                } else if ($fecha_actual <= $dato['vto3']) {
                                                                    $importe = $dato['impo3'];
                                                                    $qvto = $dato['vto3'];
                                                                } else {
                                                                    $importe = $dato['impo4'];
                                                                    $qvto = $dato['vto4'];
                                                                }
                                                                break;
                                                            default:
                                                                $importe = $dato['impo1'];
                                                                $qvto = $dato['vto1'];
                                                                // code to be executed if n is different from all labels;
                                                        }

                                                    ?>
                                                        <tr>
                                                            <th scope="row"><?php echo $ii; ?></th>
                                                            <td colspan="2"><?php echo $dato['nombre'] . ' ' . $dato['periodo'] . ' ' . date("d/m/Y", strtotime($qvto)) ?></td>

                                                            <td><?php echo MONEDA . number_format($importe, 2, ',', '.'); ?></td>

                                                            <td>
                                                                <button class="btn btn-success" type="button" onclick="addporducto(<?php echo $dato['id']; ?>,'<?php echo hash_hmac('sha1', $dato['id'], KEY_TOKEN); ?>')"><i class="far fa-plus-square"></i></button>
                                                            </td>
                                                        </tr>

                                                    <?php
                                                        $total = $total + $importe;
                                                        $ii = $ii + 1;
                                                    } ?>
                                                    <tr>
                                                        <td colspan="3">
                                                            <p class="h3" id="total">Total deuda</p>
                                                        </td>

                                                        <td colspan="2">
                                                            <p class="h3" id="total"> <?php echo MONEDA . number_format($total, 2, ',', '.'); ?></p>
                                                        </td>
                                                    </tr>
                                                <?php
                                                } else { ?>
                                                    <tr>
                                                        <td colspan="5">
                                                            <div class="alert alert-danger" role="alert">
                                                                Atencion..!! no se encotro deuda cargada..
                                                            </div>
                                                        </td>
                                                    </tr>

                                                <?php } ?>


                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                                <div class="d-flex align-items-center justify-content-center mt-4 mb-0">
                                    <a href="../index.php" class="btn btn-outline-primary">Volver</a>
                                    <a href="checkoutpro.php" class="btn btn-primary">Pagar<span id="num_car" class="badge bg-secondary"><?php echo $num_cart; ?></span></a>
                                </div>
                                <p></p>
                                <div class="card-footer text-center py-3">
                                    <p>2 de 3</p>
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
        function addporducto(id, token) {
            let url = 'addeuda.php';
            let formData = new FormData();
            formData.append('id', id);
            formData.append('token', token);

            fetch(url, {
                method: 'POST',
                body: formData,
                mode: 'cors'
            }).then(response => response.json()).then(data => {
                if (data.ok) {
                    let elemento = document.getElementById("num_car")
                    elemento.innerHTML = data.numero
                }
            })

        }
    </script>



</body>

</html>

<?php


function agregar($id)
{

    $db = new Database();
    $con = $db->conectar();

    $sql = $con->prepare("SELECT idabonado, idcta, periodo, nombre, impo1, impo2, impo3, impo4,
            vto1, vto2, vto3, vto4, cantvtos, id FROM CTABOTONPAGO WHERE idabonado=? and pagado = 0");
    $sql->execute([$id]);
    $row = $sql->fetchall(PDO::FETCH_ASSOC);

    //echo var_dump($row) ;

    return $row;
}

function xeliminar($id)
{

    if ($id > 0) {
        if (isset($_SESSION['carrito']['producto'][$id])) {
            unset($_SESSION['carrito']['producto'][$id]);
            return true;
        }
    } else {
        return false;
    }
}
