<?php

require('includes/application_top.php');





//require(DIR_WS_INCLUDES . 'template_top.php');

//// mercadopago Starts
require('includes/modules/payment/mercadopago.php');
?>
<?php if ($_REQUEST['bt'] != ''){ ?>
    
    
    



 


<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="middle">
        
        
        
<div class="botao">
<?php if(MODULE_PAYMENT_MERCADOPAGO_COUNTRY == 'MLB'){ ?>
<div class="left" style="position:relative;float:left;"/><h3 style="margin: 10px;">Continue pagando com MercadoPago</h3></div>
<?php } else { ?>
<h1>Mercado Pago</h1>
<div class="left"/><h3 style="margin: 10px;">Continue pagando con MercadoPago</h3></div>
<?php } ?>
<div class="right" style="position:relative;float:right;" />
<a href="<?php echo $_REQUEST['bt']; ?>" id="btnPagar" name="MP-payButton" class="blue-l-rn-ar">Comprar</a>
</div>
</div>
<?php if(MODULE_PAYMENT_MERCADOPAGO_COUNTRY == 'MLB'){ ?>
<img src="images/mercadopago/mercadopagobr.jpg" alt="MercadoPago" title="MercadoPago" />
<?php } else { ?>
<img src="images/mercadopago/mercadopagoar.jpg" alt="MercadoPago" title="MercadoPago" />
<?php } ?>

<script type="text/javascript" src="https://www.mercadopago.com/org-img/jsapi/mptools/buttons/render.js"></script>
<script type="text/javascript">
function fireEvent(obj,evt){
var fireOnThis = obj;
if( document.createEvent ) {
var evObj = document.createEvent('MouseEvents');
evObj.initEvent( evt, true, false );
fireOnThis.dispatchEvent( evObj );
} else if( document.createEventObject ) {
var evObj = document.createEventObject();
fireOnThis.fireEvent('on' + evt, evObj );
}
 }
fireEvent(document.getElementById("btnPagar"), 'click')
</script>
<?php
$cart->reset(true);  ?>
    
    
    
    </td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
<?php 

} else {
   echo 'Error to get preference key, please contact the store owner'; 
}




if(!isset($_REQUEST['bt']) && isset($_REQUEST['id']) && isset($_REQUEST['topic']) && $_REQUEST['topic'] = 'payment'){
  
  $mb = new mercadopago();
  $status = $mb->retorno($_REQUEST['id']);

 }


?>
