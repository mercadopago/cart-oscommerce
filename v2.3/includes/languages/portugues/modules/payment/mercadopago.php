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
	<li><a href=\"https://www.mercadopago.com/mco/herramientas/aplicaciones\" target=\"_blank\" ><b>COLÔMBIA</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mlm/herramientas/aplicaciones\" target=\"_blank\" ><b>MÉXICO</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mpe/herramientas/aplicaciones\" target=\"_blank\" ><b>PERU</b></a></li>
	<li><a href=\"https://www.mercadopago.com/mlv/herramientas/aplicaciones\" target=\"_blank\"><b>VENEZUELA</b></a></li>
	</ul>
');
define('MP_ACTIVATION_TITLE', 'Configurar Mercado Pago');
define('MP_ACTIVATION_TITLE', 'Mercado Pago');
define('MODULE_PAYMENT_MERCADOPAGO_TEXT_DESCRIPTION', 'Seu meio de pagamento na Internet');
define('MP_ACTIVATION_ACTIVATE_COUNTRY', 'Localização');
define('MB_ACTIVATION_ACTIVATE_COUNTRY_TEXT', 'Selecione seu país');
define('MB_ACTIVATION_CONTINUE_BUTTON', 'Continuar');
define('MP_ACTIVATION_METHODS_COUNTRY', 'Formas de pagamento não desejadas');
define('MB_ACTIVATION_ACTIVATE_METHODS_TEXT', 'Marque apenas as formas de pagamento que você <b>NÃO</b> deseja aceitar');

define('MP_ADMIN_CREDENTIALS_SELECTION', 'Para obter as credenciais, acesse:');
define('MP_ADMIN_ENABLE', 'Habilitar módulo Mercado Pago');

?>