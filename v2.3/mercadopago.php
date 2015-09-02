<?php

require('includes/application_top.php');
require(DIR_WS_INCLUDES . 'template_top.php');
require('includes/modules/payment/mercadopago.php');
require(DIR_WS_LANGUAGES . $language . '/modules/payment/mercadopago.php');

$mp = new mercadopago();

  if ($_REQUEST['bt'] != '') { 

    switch (MODULE_PAYMENT_MERCADOPAGO_CHECKOUT) {
      case 'Transparent':

        ?>

        <iframe src="<?php echo $_REQUEST['bt']; ?>" name="MP-Checkout" width="620" height="795" frameborder="0"></iframe>

        <script type="text/javascript">
            (function(){function $MPBR_load(){window.$MPBR_loaded !== true && (function(){var s = document.createElement("script");s.type = "text/javascript";s.async = true;
            s.src = ("https:"==document.location.protocol?"https://www.mercadopago.com/org-img/jsapi/mptools/buttons/":"http://mp-tools.mlstatic.com/buttons/")+"render.js";
            var x = document.getElementsByTagName('script')[0];x.parentNode.insertBefore(s, x);window.$MPBR_loaded = true;})();}
            window.$MPBR_loaded !== true ? (window.attachEvent ? window.attachEvent('onload', $MPBR_load) : window.addEventListener('load', $MPBR_load, false)) : null;})();
        </script>        

        <?php

        break;

      default:

          if (MODULE_PAYMENT_MERCADOPAGO_CHECKOUT == 'Lightbox') {
            $mode = "modal";
          } else {
            $mode = "redirect";
          }

          ?>

          <h1>Mercado Pago</h1>

          <div class="botao">
            <div class="left">
              <h3 style="margin: 10px;"><?= MP_CHECKOUT_TEXT_DESCRIPTION ?></h3>
              <img src="<?= $mp->get_mercadopago_methods_image() ?>" alt="MercadoPago" title="MercadoPago" style="margin: 10px;"/>
              <a href="<?php echo $_REQUEST['bt']; ?>" id="btnPagar" name="MP-Checkout" class="blue-l-rn-ar" mp-mode="<?php echo $mode; ?>">Comprar</a>
            </div>
          </div>
          <!-- http://imgmp.mlstatic.com/org-img/MLB/MP/BANNERS/tipo2_468X60.jpg -->

          <script type="text/javascript">
              (function(){function $MPBR_load(){window.$MPBR_loaded !== true && (function(){var s = document.createElement("script");s.type = "text/javascript";s.async = true;
              s.src = ("https:"==document.location.protocol?"https://www.mercadopago.com/org-img/jsapi/mptools/buttons/":"http://mp-tools.mlstatic.com/buttons/")+"render.js";
              var x = document.getElementsByTagName('script')[0];x.parentNode.insertBefore(s, x);window.$MPBR_loaded = true;})();}
              window.$MPBR_loaded !== true ? (window.attachEvent ? window.attachEvent('onload', $MPBR_load) : window.addEventListener('load', $MPBR_load, false)) : null;})();
          </script>

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
            fireEvent(document.getElementById("btnPagar_"), 'click')
          </script>
  
          <?php
          $cart->reset(true);

        break;
    }

  } else {
   
   echo MP_CHECKOUT_ERROR; 
  
  }
 
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php'); 


  if(!isset($_REQUEST['bt']) && isset($_REQUEST['id']) && isset($_REQUEST['topic']) && $_REQUEST['topic'] = 'payment'){

    $status = $mp->retorno($_REQUEST['id']);

  }

?>
