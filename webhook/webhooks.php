<?php
  MercadoPago\SDK::setAccessToken("ENV_ACCESS_TOKEN");
  switch($_POST["type"]) {
      case "payment":
          $payment = MercadoPago\Payment::find_by_id($_POST["data"]["id"]);
          
          break;
      case "plan":
          $plan = MercadoPago\Plan::find_by_id($_POST["data"]["id"]);
          break;
      case "subscription":
          $plan = MercadoPago\Subscription::find_by_id($_POST["data"]["id"]);
          break;
      case "invoice":
          $plan = MercadoPago\Invoice::find_by_id($_POST["data"]["id"]);
          break;
      case "point_integration_wh":
          // $_POST contiene la informaciòn relacionada a la notificaciòn.
          break;
  }
?>