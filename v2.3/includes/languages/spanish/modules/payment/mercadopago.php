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

define('MODULE_PAYMENT_MERCADOPAGO_TEXT_TITLE', 'Mercado Pago');
define('MODULE_PAYMENT_MERCADOPAGO_TEXT_DESCRIPTION', MP_ADMIN_CREDENTIALS_SELECTION . '
	<ul>
	<li><a href=\"https://www.mercadopago.com/mla/herramientas/aplicaciones\" target=\"_blank\"><b>ARGENTINA</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mlb/ferramentas/aplicacoes\" target=\"_blank\" ><b>BRASIL</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mlc/herramientas/aplicaciones\" target=\"_blank\" ><b>CHILE</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mco/herramientas/aplicaciones\" target=\"_blank\" ><b>COLOMBIA</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mlm/herramientas/aplicaciones\" target=\"_blank\" ><b>MÉXICO</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mpe/herramientas/aplicaciones\" target=\"_blank\" ><b>PERÚ</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mlv/herramientas/aplicaciones\" target=\"_blank\"><b>VENEZUELA</b></a></li>
	</ul>
');
define('MP_ACTIVATION_TITLE', 'Configurar Mercado Pago');
define('MP_ACTIVATION_TITLE', 'Mercado Pago');
define('MODULE_PAYMENT_MERCADOPAGO_TEXT_DESCRIPTION', 'Su medio de pago en Internet');
define('MP_ACTIVATION_ACTIVATE_COUNTRY', 'Localización');
define('MB_ACTIVATION_ACTIVATE_COUNTRY_TEXT', 'Selecione su país');
define('MB_ACTIVATION_CONTINUE_BUTTON', 'Continuar');
define('MP_ACTIVATION_METHODS_COUNTRY', 'Medios de pago no aceptados');
define('MB_ACTIVATION_ACTIVATE_METHODS_TEXT', 'Marque sólo las formas de pago que <b>NO</b> desea aceptar');

define('MP_ADMIN_CREDENTIALS_SELECTION', 'Para obtener sus credenciales, acceda a:');
define('MP_ADMIN_ENABLE', 'Habilitar módulo Mercado Pago');

?>