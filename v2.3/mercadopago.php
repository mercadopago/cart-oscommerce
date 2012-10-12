<?php
require('includes/application_top.php');
require(DIR_WS_INCLUDES . 'template_top.php');
require('includes/modules/payment/mercadopago.php');
?>
<h1>Mercado Pago</h1>

<?php if ($_REQUEST['bt'] != ''){ ?>
    
    
    


<div class="botao">
<?php if(MODULE_PAYMENT_MERCADOPAGO_COUNTRY == 'MLB'){ ?>
<div class="left" style="position:relative;float:left;"/><h3 style="margin: 10px;">Continue pagando com MercadoPago</h3></div>
<?php } else { ?>
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
$cart->reset(true);
} else {
   echo 'Error to get preference key, please contact the store owner'; 
}

   
   require(DIR_WS_INCLUDES . 'template_bottom.php');
   require(DIR_WS_INCLUDES . 'application_bottom.php'); 
 

if(!isset($_REQUEST['bt']) && isset($_REQUEST['id']) && isset($_REQUEST['topic']) && $_REQUEST['topic'] = 'payment'){
  
  $mb = new mercadopago();
  $status = $mb->retorno($_REQUEST['id']);

 }
   
    


?>
