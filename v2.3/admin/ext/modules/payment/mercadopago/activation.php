<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

chdir('../../../../');
require('includes/application_top.php');
require('../includes/languages/' . $language . '/modules/payment/mercadopago.php');
require('../includes/modules/payment/mercadopago.php');
  
$action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');
$posts = (isset($_POST) ? $_POST : '');


require(DIR_WS_INCLUDES . 'template_top.php');

$mp = new mercadopago;

?>

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
  <?php  
    if ($action == '') { 

    $sites = array(
        'MLA' =>'Argentina',
        'MLB' =>'Brazil',
        'MLC' =>'Chile',
        'MCO' =>'Colombia',
        'MLM' =>'Mexico',
        'MPE' =>'Peru',
        'MLV' =>'Venezuela'
    ); ?>

    <p><strong><u><?php echo MP_ACTIVATION_ACTIVATE_COUNTRY; ?></u></strong></p>
    <p><?php echo MB_ACTIVATION_ACTIVATE_COUNTRY_TEXT; ?></p>

    <form name="activation" action="<?php echo tep_href_link('ext/modules/payment/mercadopago/activation.php', '&action=country'); ?>" method="post">
    
    <?php

      $showcountries  = '<select name="country" id="country">';

      foreach ($sites as $site_id => $site_name) {
        if ($site_id == MODULE_PAYMENT_MERCADOPAGO_COUNTRY) { 
          $showcountries  .= '<option value="'. $site_id .'" selected="selected" id="'. $site_id .'">'.$site_name.'</option>';
        } else { 
          $showcountries  .=  '<option value="'. $site_id .'" id="'.$site_id.'">'.$site_name .'</option>';
        } 
      }
      $showcountries  .=  '</select>';
      echo $showcountries;
    ?>    
      <p><input type="submit" value="<?php echo MB_ACTIVATION_CONTINUE_BUTTON; ?>"></p>
    </form>
  <?php

    } elseif ($action == 'country') {
        
        $methods = $mp->GetMethods($posts['country']);

        foreach ($methods as $method) {
          
          if($method['id'] != 'account_money') {
            if($mercadopago_methods != null && in_array($method['id'], $mercadopago_methods)){
              $showmethods .= ' <input name="methods[]" type="checkbox" checked="yes" value="'.$method['id'].'">'.$method['name'].'<br />'; 
            } else {
              $showmethods .= '<input name="methods[]" type="checkbox" value="'.$method['id'].'"> '.$method['name'].'<br />';    
            }
          }
        }
  ?> 
  <p><strong><u><?php echo MP_ACTIVATION_METHODS_COUNTRY; ?></u></strong></p>
  <p><?php echo MB_ACTIVATION_ACTIVATE_METHODS_TEXT; ?></p>

  <form name="activation" action="<?php echo tep_href_link('ext/modules/payment/mercadopago/activation.php', '&action=payment_method'); ?>" method="post">
    <?php  echo $showmethods;  ?>
    <input type="hidden" value="<?php echo $posts['country']; ?>" name="country"></p>   
    <p><input type="submit" value="<?php echo MB_ACTIVATION_CONTINUE_BUTTON; ?>"></p>
  </form>
<?php
  
  } elseif ($action == 'payment_method') {

    if (!empty($posts['methods'])) {
      $methods = implode(",", $posts['methods']);
    }

    $categories = $mp->GetCategories();

    $html_categories  = '<select name="categories" style="width: 200px">';

    foreach ($categories as $category) {

      $html_categories .=  '<option value="'. $category['id'] .'" id="'.$category["id"].'">'.$category["description"] .'</option>';
      
    }

    $html_categories .= '</select>';

?>

  <form name="activation" action="<?php echo tep_href_link(FILENAME_MODULES,'set=payment&module=mercadopago&action=install&active=true'); ?>" method="post">
    <?php echo $html_categories; ?>
    <p><input type="hidden" value="<?php echo $posts['country']; ?>" name="country"></p>   
    <p><input type="hidden" value="<?php echo $methods; ?>" name="methods"></p>   
    <p><input type="submit" value="<?php echo MB_ACTIVATION_CONTINUE_BUTTON; ?>"></p>
  </form>

<?php

}

require(DIR_WS_INCLUDES . 'template_bottom.php');
require(DIR_WS_INCLUDES . 'application_bottom.php');

?>
