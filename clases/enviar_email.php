<?php
use PHPMailer\PHPMailer\{PHPMailer, SMTP, Exception} ;

require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';
require '../phpmailer/src/Exception.php';


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;  // SMTP::DEBUG_OFF   Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'sixtod@gmail.com';                     //SMTP username
    $mail->Password   = 'rtblbyqsfewvtbmh';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('sixtod@gmail.com', 'Pagos-Sixtored');
    $mail->addAddress($email);     //Add a recipient
   // $mail->addAddress('ellen@example.com');               //Name is optional
   // $mail->addReplyTo('info@example.com', 'Information');
   // $mail->addCC('cc@example.com');
   // $mail->addBCC('bcc@example.com');

    //Attachments
  //  $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
  //  $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Detalle del pago';
    $cuerpo = $noti ;
   // $cuerpo .= '<p> El id de su pago es <b> '.$payment . '</b></p>' ;
    $mail->Body    = utf8_decode($cuerpo) ;
    $mail->AltBody = 'Le enviamos el detalle de su pago..';
    $mail->setLanguage('es', '../phpmailer/lenguage/phpmailer.lang-es.php') ;
    $mail->send();
   // echo 'Email ha sido enviado..';
} catch (Exception $e) {
    echo "Email no se pudo enviar.. Hubo un error: {$mail->ErrorInfo}";
}