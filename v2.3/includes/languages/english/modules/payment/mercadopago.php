<?php
/*
  $Id: mercadopago.php,v 2.0 2005/08/06 10:17:15 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
*/

define('MODULE_PAYMENT_MERCADOPAGO_TEXT_TITLE', 'MercadoPago');
define('MODULE_PAYMENT_MERCADOPAGO_TEXT_DESCRIPTION', 
        '<strong>MercadoPago</strong><br/>
  	  MercadoPago module for osCommerce. More info at 
          <a href="https://www.mercadopago.com.ar/developers/en/tools/modules/oscommerce/" style="text-decoration: underline;" target="_blank">MercadoPago Developers</a>.<br />
  	  <br />
          <strong>Requirements</strong><br />
          For credentials (Client Id & Client Secret), please visit:<br />
          <ul>
          <li><a href=\"https://www.mercadopago.com/mlb/ferramentas/aplicacoes\" target=\"_blank\" ><b>Brazil</b></a></li>
          <li><a href=\"https://www.mercadopago.com/mla/herramientas/aplicaciones\" target=\"_blank\"><b>Argentina</b></a></li>
          <li><a href=\"https://www.mercadopago.com/mlm/herramientas/aplicaciones\" target=\"_blank\" ><b>Mexico</b></a></li>
          <li><a href=\"https://www.mercadopago.com/mlv/herramientas/aplicaciones\" target=\"_blank\"><b>Venenzuela</b></a></li>
          </ul>'
        );

define('MP_ACTIVATION_TITLE', 'Setup MercadoPago');
define('MP_ACTIVATION_ACTIVATE_COUNTRY', 'Location');
define('MB_ACTIVATION_ACTIVATE_COUNTRY_TEXT', 'Select your country');
define('MB_ACTIVATION_CONTINUE_BUTTON', 'Continue');
define('MP_ACTIVATION_METHODS_COUNTRY', 'Payment Methods not accepted');
define('MB_ACTIVATION_ACTIVATE_METHODS_TEXT', 'Check only the payment methods that you <b>DO NOT</b> want to accept');

define('MP_CHECKOUT_METHOD_TEXT_DESCRIPTION','Accepted payment methods');
define('MP_CHECKOUT_TEXT_DESCRIPTION','Continue paying with MercadoPago');
define('MP_CHECKOUT_ERROR','Error to get preference key, please contact the store owner');

?>