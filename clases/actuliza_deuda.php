<?php

require '../config/config.php' ;
require '../config/database.php' ;

if (isset($_POST['action'])) {

    $action = $_POST['action'] ;

    $id = isset($_POST['id']) ? $_POST['id'] : 0 ;

    if ($action == 'agregar') {
        //// AGREGAR ////
        $cant = isset($_POST['cantidad']) ? $_POST['cantidad'] : 1 ;
        $subtotal = agregar($id, $cant) ;

        if ($subtotal>0) {
            $datos['ok'] = true ;
        } else {
            $datos['ok'] = false ;
        }
        $datos['sub'] = MONEDA . number_format($subtotal,2,',','.') ;
    } else if ($action == 'eliminar') {
     ///// ELIMINAR /////
        $datos['ok'] = eliminar($id) ;
       
    } else {
        $datos['ok'] = false ;
    }

} else {
    $datos['ok'] = false ;
}

echo json_encode($datos) ;


function agregar($id, $cantidad) {

    $res = 0 ;
    if ($id > 0 && $cantidad>0 && is_numeric($cantidad)) {
        if (isset($_SESSION['carrito']['producto'][$id])) {
            $_SESSION['carrito']['producto'][$id] = $cantidad ;

            $db = new Database() ;
            $con = $db->conectar() ;

            $sql = $con->prepare("SELECT precio, descuento FROM productos WHERE id=? LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $precio = $row['precio'];
            $descuento = $row['descuento'];
            $precio_desc = $precio - (($precio * $descuento) / 100);
            $res = $cantidad * $precio_desc ;

            return $res ;
            
        }
    } else {
        return $res ;
    }
}

function eliminar($id) {
  
    if ($id > 0) {
        if (isset($_SESSION['carrito']['producto'][$id])) {
            unset($_SESSION['carrito']['producto'][$id]) ;
            return true ;
        }    

    } else {
        return false ;
    }


}
