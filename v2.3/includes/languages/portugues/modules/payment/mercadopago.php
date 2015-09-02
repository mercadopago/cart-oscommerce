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
  	  Módulo MercadoPago para osCommerce. Para mais informações visite 
          <a href="https://www.mercadopago.com.br/developers/pt/tools/modules/oscommerce/" style="text-decoration: underline;" target="_blank">MercadoPago Developers</a>.<br />
  	  <br />
          <strong>Requisitos</strong><br />
          Para obter credenciais (Client Id e Client Secret), acesse:<br />
          <ul>
          <li><a href=\"https://www.mercadopago.com/mlb/ferramentas/aplicacoes\" target=\"_blank\" ><b>Brasil</b></a></li>
          <li><a href=\"https://www.mercadopago.com/mla/herramientas/aplicaciones\" target=\"_blank\"><b>Argentina</b></a></li>
          <li><a href=\"https://www.mercadopago.com/mlm/herramientas/aplicaciones\" target=\"_blank\" ><b>México</b></a></li>
          <li><a href=\"https://www.mercadopago.com/mlv/herramientas/aplicaciones\" target=\"_blank\"><b>Venenzuela</b></a></li>
          </ul>'
        );

define('MP_ACTIVATION_TITLE', 'Configurar MercadoPago');
define('MP_ACTIVATION_ACTIVATE_COUNTRY', 'Localização');
define('MB_ACTIVATION_ACTIVATE_COUNTRY_TEXT', 'Selecione seu país');
define('MB_ACTIVATION_CONTINUE_BUTTON', 'Continuar');
define('MP_ACTIVATION_METHODS_COUNTRY', 'Formas de pagamento não desejadas');
define('MB_ACTIVATION_ACTIVATE_METHODS_TEXT', 'Marque apenas as formas de pagamento que você <b>não</b> deseja aceitar');

define('MP_CHECKOUT_METHOD_TEXT_DESCRIPTION','Modos de pagamento aceitos');
define('MP_CHECKOUT_TEXT_DESCRIPTION','Continue pagando com MercadoPago');
define('MP_CHECKOUT_ERROR','Error to get preference key, please contact the store owner');


?>