<?php 
require_once 'config/config.php' ;
 if (isset($_SESSION['carrito']['producto'])) {
    session_unset() ;
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
                                    <h4 class="text-center font-weight-light my-4">Busca tu deuda</h4>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="clases/deuda.php">
                                        <input name="action" value="agregar" type="hidden"/>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="idabonado" name="idabonado" type="text" placeholder="nombre de usuario" />
                                            <label for="usuario">ID ABONADO</label>
                                            <span class="font-13 text-muted">"Ingrese el ID ABONADO"</span>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="dni" name="dni" type="text" placeholder="DNI ABONADO" />
                                            <label for="inputPassword">DNI ABONADO</label>
                                            <span class="font-13 text-muted">"Ingrese el DNI o CUIT"</span>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-primary" type="submit">Continuar</button>
                                        </div>

                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <p>1 de 3</p>

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
                let url = 'clases/carrito.php';
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