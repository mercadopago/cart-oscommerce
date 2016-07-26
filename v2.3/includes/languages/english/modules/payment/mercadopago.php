<?php
/*
  $Id: mercadopago.php,v 2.0 2005/08/06 10:17:15 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Released under the GNU General Public License
*/

/*
  These constants will be used to show informations at:
  Admin > Modules > Payment > Mercado Pago

  @author cadu (dot) rcorrea (at) gmail (dot) com
*/

define('MP_ADMIN_CREDENTIALS_SELECTION', 'For credentials, please visit:');
define('MP_ADMIN_ENABLE', 'Enable Mercado Pago module');


define('MODULE_PAYMENT_MERCADOPAGO_TEXT_TITLE', 'Mercado Pago');
define('MODULE_PAYMENT_MERCADOPAGO_TEXT_DESCRIPTION', MP_ADMIN_CREDENTIALS_SELECTION . '
	<ul>
	<li><a href=\"https://www.mercadopago.com/mla/herramientas/aplicaciones\" target=\"_blank\"><b>ARGENTINA</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mlb/ferramentas/aplicacoes\" target=\"_blank\" ><b>BRAZIL</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mlc/herramientas/aplicaciones\" target=\"_blank\" ><b>CHILE</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mco/herramientas/aplicaciones\" target=\"_blank\" ><b>COLOMBIA</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mlm/herramientas/aplicaciones\" target=\"_blank\" ><b>MEXICO</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mpe/herramientas/aplicaciones\" target=\"_blank\" ><b>PERU</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mlv/herramientas/aplicaciones\" target=\"_blank\"><b>VENEZUELA</b></a></li>
	</ul>
');
define('MP_ACTIVATION_TITLE', 'Setup Mercado Pago');
define('MP_ACTIVATION_TITLE', 'Mercado Pago');
define('MP_ACTIVATION_ACTIVATE_COUNTRY', 'Location');
define('MB_ACTIVATION_ACTIVATE_COUNTRY_TEXT', 'Select your country');
define('MB_ACTIVATION_CONTINUE_BUTTON', 'Continue');
define('MP_ACTIVATION_METHODS_COUNTRY', 'Payment Methods not accepted');
define('MB_ACTIVATION_ACTIVATE_METHODS_TEXT', 'Check only the payment methods that you <b>DO NOT</b> want to accept');
define('MODULE_PAYMENT_MERCADOPAGO_TEXT_DESCRIPTION', '');

?>