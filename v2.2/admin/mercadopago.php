<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

//chdir('../../../../');

require('includes/application_top.php');
require('../includes/languages/' . $language . '/modules/payment/mercadopago.php');
require('../includes/modules/payment/mercadopago.php');
  
$action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
$posts = (isset($_POST) ? $_POST : '');


//require(DIR_WS_INCLUDES . 'template_top.php');
$mp = new mercadopago;


?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->


<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
    <!-- body_text //-->
    <td width="100%" valign="top">

<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo MP_ACTIVATION_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
      <td class="main">
<?php  if ($action == '') {
        $countries = $mp->getCountries();
?>      <p><strong><u><?php echo MP_ACTIVATION_ACTIVATE_COUNTRY; ?></u></strong></p>
        <p><?php echo MB_ACTIVATION_ACTIVATE_COUNTRY_TEXT; ?></p>
        <form name="activation" action="<?php echo tep_href_link('mercadopago.php', '&action=country'); ?>" method="post">
        <?php     
        $showcountries  = '<select name="country" id="country">';
        foreach ($countries as $country) {
        if ($country['id'] == MODULE_PAYMENT_MERCADOPAGO_COUNTRY) { 
        $showcountries  .= '<option value="'. $country["id"].'" selected="selected" id="'. $country["id"] .'">'.$country["name"].'</option>';
        } else { 
        $showcountries  .=  '<option value="'. $country['id'] .'" id="'.$country["id"].'">'.$country["name"] .'</option>';
        } 
        }
        $showcountries  .=  '</select>';
        echo $showcountries;
        ?>    
        <p><input type="submit" value="<?php echo MB_ACTIVATION_CONTINUE_BUTTON; ?>"></p>
        </form>
<?php
      } elseif  ($action == 'country') {
        
           $methods = $mp->GetMethods($posts['country']);

        foreach ($methods as $method):
        if($method['id'] != 'account_money'){
              if($mercadopago_methods != null && in_array($method['id'], $mercadopago_methods)){
              $showmethods .= ' <input name="methods[]" type="checkbox" checked="yes" value="'.$method['id'].'">'.$method['name'].'<br />'; 
              } else {
              $showmethods .= '<input name="methods[]" type="checkbox" value="'.$method['id'].'"> '.$method['name'].'<br />';    
        }}
         endforeach;
?>        
        <p><strong><u><?php echo MP_ACTIVATION_METHODS_COUNTRY; ?></u></strong></p>
        <p><?php echo MB_ACTIVATION_ACTIVATE_METHODS_TEXT; ?></p>
        <form name="activation" action="<?php echo tep_href_link(FILENAME_MODULES,'set=payment&module=mercadopago&action=install&active=true'); ?>" method="post">
        <?php  echo $showmethods;  ?>
        <input type="hidden" value="<?php echo $posts['country']; ?>" name="country"></p>   
        <p><input type="submit" value="<?php echo MB_ACTIVATION_CONTINUE_BUTTON; ?>"></p>
        </form>
      
        
        </td>
      </tr>
<?php
  }
?>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>

<?php   
      
  

  //require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
