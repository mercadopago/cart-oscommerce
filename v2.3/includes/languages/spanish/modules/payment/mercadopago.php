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
  	  Módulo de MercadoPago para osCommerce, para mas información visite 
          <a href="https://www.mercadopago.com.ar/developers/es/tools/modules/oscommerce/" style="text-decoration: underline;" target="_blank">MercadoPago Developers</a>.<br />
  	  <br />
          <strong>Requerimientos</strong><br />
          Para obtener las credenciales (Client Id y Client Secret), visite:<br />
          <ul>
          <li><a href=\"https://www.mercadopago.com/mlb/ferramentas/aplicacoes\" target=\"_blank\" ><b>Brasil</b></a></li>
          <li><a href=\"https://www.mercadopago.com/mla/herramientas/aplicaciones\" target=\"_blank\"><b>Argentina</b></a></li>
          <li><a href=\"https://www.mercadopago.com/mlm/herramientas/aplicaciones\" target=\"_blank\" ><b>México</b></a></li>
          <li><a href=\"https://www.mercadopago.com/mlv/herramientas/aplicaciones\" target=\"_blank\"><b>Venenzuela</b></a></li>
          </ul>'
        );

define('MP_ACTIVATION_TITLE', 'Configurar MercadoPago');
define('MP_ACTIVATION_ACTIVATE_COUNTRY', 'País');
define('MB_ACTIVATION_ACTIVATE_COUNTRY_TEXT', 'Seleccione su país');
define('MB_ACTIVATION_CONTINUE_BUTTON', 'Continuar');
define('MP_ACTIVATION_METHODS_COUNTRY', 'Medios de pago no deseados');
define('MB_ACTIVATION_ACTIVATE_METHODS_TEXT', 'Seleccione solo las formas de pago que <b>NO</b> quiere aceptar');

define('MP_CHECKOUT_METHOD_TEXT_DESCRIPTION','Medios de pago aceptados');

?>