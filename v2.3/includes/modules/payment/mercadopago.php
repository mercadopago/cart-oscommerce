<?php
/*
  $Id: mercadopago.php,v 2.0.1 2016/07/26 11:30:00 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
 */

require_once "mercadopago/sdk/mercadopago.php";

class mercadopago {

  var $code, $title, $description, $enabled;

  function mercadopago() {
    global $order;

    $this->code = 'mercadopago';
    $this->title = MODULE_PAYMENT_MERCADOPAGO_TEXT_TITLE;
    $this->description = MODULE_PAYMENT_MERCADOPAGO_TEXT_DESCRIPTION;
    $this->sort_order = MODULE_PAYMENT_MERCADOPAGO_SORT_ORDER;

    $this->enabled = ((MODULE_PAYMENT_MERCADOPAGO_STATUS == 'True') ? true : false);

    if(defined('MODULE_PAYMENT_MERCADOPAGO_ORDER_STATUS_ID')){
      if ((int) MODULE_PAYMENT_MERCADOPAGO_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_MERCADOPAGO_ORDER_STATUS_ID;
      }
    }

    //is admin!?
    if((isset($_REQUEST['set']) && isset($_REQUEST['module'])) && $_REQUEST['set'] == "payment" && $_REQUEST['module'] == "mercadopago"){
      $this->updateApiAccountSettings();
      $this->updateApiAnalytics();
    }

    if (is_object($order)){
      $this->update_status();
    }
  }

  function processIPNMerchantOrder() {

    $mercadopago = new MP(MODULE_PAYMENT_MERCADOPAGO_CLIENTID, MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET);
    $merchant_order = $mercadopago->get_merchant_order($_REQUEST['id']);

    if($merchant_order['status'] == 200){
      if($merchant_order['response']['status'] == "closed"){

        $payment = $merchant_order['response']['payments'][0];
        $payment_status = $payment['status'];

        //two cards
        if(count($merchant_order['response']['payments']) > 1){
          $payment = $this->overOnePaymentsIPN($merchant_order['response']);
          $payment_status = $payment['status'];
        }

        //get order id
        $order_id = $merchant_order['response']['external_reference'];

        // the actual order status
        $order_status = $payment_status;

        //init env
        $status = MODULE_PAYMENT_MERCADOPAGO_STATUS_PENDING;
        $statustxt = 'Mercado Pago automatic change the status to Pending';

        // verify the status
        switch ($order_status) {
          case 'approved':
            $status = MODULE_PAYMENT_MERCADOPAGO_STATUS_APROVED;
            $statustxt = 'Mercado Pago automatic change the status to Approved';
            break;
          case 'pending':
            $status = MODULE_PAYMENT_MERCADOPAGO_STATUS_PENDING;
            $statustxt = 'Mercado Pago automatic change the status to Pending';
            break;
          case 'in_process':
            $status = MODULE_PAYMENT_MERCADOPAGO_STATUS_PROCESS;
            $statustxt = 'Mercado Pago automatic change the status to InProcess';
            break;
          case 'reject':
            $status = MODULE_PAYMENT_MERCADOPAGO_STATUS_REJECT;
            $statustxt = 'Mercado Pago automatic change the status to Reject';
            break;
          case 'refunded':
            $status = MODULE_PAYMENT_MERCADOPAGO_STATUS_REFUNDED;
            $statustxt = 'Mercado Pago automatic change the status to Refunded';
            break;
          case 'cancelled':
            $status = MODULE_PAYMENT_MERCADOPAGO_STATUS_CANCELED;
            $statustxt = 'Mercado Pago automatic change the status to Canceled';
            break;
          case 'in_metiation':
            $status = MODULE_PAYMENT_MERCADOPAGO_STATUS_MEDIATION;
            $statustxt = 'Mercado Pago automatic change the status to Mediation';
            break;
          default:
            $status = MODULE_PAYMENT_MERCADOPAGO_STATUS_PENDING;
            $statustxt = 'Mercado Pago automatic change the status to Pending';
            break;
        }

        $statustxt .= "\n ID: " . $payment['id'];

        // get the order
        require(DIR_WS_CLASSES . 'order.php');
        $order = new order($order_id);

        if ($order->info['orders_status'] != $status) {

          // update the order status
          $data = array('orders_status' => $status);
          tep_db_perform(TABLE_ORDERS, $data, 'update', "orders_id = '" . $order_id . "'");


          // incriment stock again if status is cancelled, refunded or reject
          if ($status == MODULE_PAYMENT_MERCADOPAGO_STATUS_CANCELED || $status == MODULE_PAYMENT_MERCADOPAGO_STATUS_REFUNDED || $status == MODULE_PAYMENT_MERCADOPAGO_STATUS_REJECT) {
            // verify if stock control is active
            if (STOCK_LIMITED == 'true') {
              foreach ($order->products as $product) {
                $quantity = $product['qty'];
                $pid = $product['id'];

                // get the atual stock
                $stock_query = tep_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . $pid . "'");
                $product = tep_db_fetch_array($stock_query);

                // count stock
                $finalquantity = $product['products_quantity'] + $quantity;

                //  update stock
                $data = array('products_quantity' => $finalquantity);
                tep_db_perform(TABLE_PRODUCTS, $data, 'update', "products_id = '" . $pid . "'");
              }
            }
          }


          $comments = $statustxt ;

          $data_history = array(
            'orders_id' => $order_id,
            'orders_status_id' => $status,
            'date_added' => 'now()',
            'customer_notified' => '0',
            'comments' => $comments);

            // update order history
            tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $data_history);

          }
        }
      }
    }

    function addStatus($descStatus) {

      $status_query = tep_db_query("select max(orders_status_id) as status_id from " . TABLE_ORDERS_STATUS);

      $status = tep_db_fetch_array($status_query);

      $status_id = $status['status_id'] + 1;

      $languages = tep_get_languages();


      foreach ($languages as $lang) {

        tep_db_query("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('" . $status_id . "', '" . $lang['id'] . "', '" . $descStatus . "')");
      }

      return $status_id;
    }

    function update_status() {
        global $order;

        if (($this->enabled == true) && ((int) MODULE_PAYMENT_MERCADOPAGO_ZONE > 0)) {
            $check_flag = false;
            $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_MERCADOPAGO_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
            while ($check = tep_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_id'] == $order->billing['zone_id']) {
                    $check_flag = true;
                    break;
                }
            }

            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
    }

    function javascript_validation() {
        return false;
    }

    //una version mejorada de 29/06
    function selection() {

      //get info user
      $sponsor_and_site = $this->getSponsorAndSite();

      $country = $sponsor_and_site['site_id'];

      $fields = array();

      switch ($country) {
        case 'MLA':
          $mercadopago_image = "http://imgmp.mlstatic.com/org-img/banners/ar/medios/468X60.jpg";
          $fields[] = array(
            'title' => 'Medios de pago aceptados:',
            'text' => ''
          );
          break;
        case 'MLB':
          $mercadopago_image = "http://imgmp.mlstatic.com/org-img/MLB/MP/BANNERS/tipo2_575X40.jpg";
          $fields[] = array(
            'title' => 'Métodos de pagamento aceitos:',
            'text' => ''
          );
          break;
        case 'MLC':
          $mercadopago_image = "https://www.mercadopago.cl/banner/468x60_banner.jpg";
          $fields[] = array(
            'title' => 'Medios de pago aceptados:',
            'text' => ''
          );
          break;
        case 'MCO':
          $mercadopago_image = "https://secure.mlstatic.com/developers/site/cloud/banners/co/468x60_Todos-los-medios-de-pago.jpg";
          $fields[] = array(
            'title' => 'Medios de pago aceptados:',
            'text' => ''
          );
          break;
        case 'MLM':
          $mercadopago_image = "http://imgmp.mlstatic.com/org-img/banners/mx/medios/MLM_468X60.JPG";
          $fields[] = array(
            'title' => 'Medios de pago aceptados:',
            'text' => ''
          );
          break;
        case 'MPE':
          $mercadopago_image = "https://mercadopago.mlstatic.com/images/desktop-logo-mercadopago.png";
          $fields[] = array(
            'title' => 'Medios de pago aceptados:',
            'text' => ''
          );
          break;
        case 'MLV':
          $mercadopago_image = "https://imgmp.mlstatic.com/org-img/banners/ve/medios/468X60.jpg";
          $fields[] = array(
            'title' => 'Medios de pago aceptados:',
            'text' => ''
          );
          break;
      }

      $fields[] = array(
        'title' => '<img src="' . $mercadopago_image . '">',
        'text' => ''
      );
      return array(
        'id' => $this->code,
        'module' => $this->title,
        'fields' => $fields
      );
    }

    function pre_confirmation_check() {
        return false;
    }

    function confirmation() {
        return false;
    }

    function process_button() {
        return false; //$process_button_string;
    }

    function before_process() {
        return false;
    }

    function after_process() {

        //env global
        global $insert_id, $order;

        //init mercado pago
        $mercadopago = new MP(MODULE_PAYMENT_MERCADOPAGO_CLIENTID, MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET);

        //init preference
        $pref = array();

        //get info user
        $sponsor_and_site = $this->getSponsorAndSite();
        if($sponsor_and_site['sponsor_id'] != ""){
          $pref['sponsor_id'] = $sponsor_and_site['sponsor_id'];
        }

        //site_id
        $site_id = $sponsor_and_site['site_id'];

        $items = array(
            array(
                "id" => $insert_id, // updated
                "title" => $order->products[0]['name'],
                "description" => $order->products[0]['name'],
                "quantity" => 1,
                "unit_price" => round($order->info['total'], 2),
                "category_id" => MODULE_PAYMENT_MERCADOPAGO_CATEGORIES
                // "picture_url" => $data['image']
            )
        );

        $payer = array(
            "name" => $order->customer['firstname'],
            "surname" => $order->customer['lastname'],
            "email" => $order->customer['email_address'],
            "phone" => array(
                "area_code" => " ",
                "number" => $order->customer['telephone']
            ),
            "address" => array(
                "zip_code" => $order->customer['postcode'],
                "street_name" => $order->customer['street_address'],
                "street_number" => " "
            ),
            "date_created" => $this->getCustomerDateCreated($order->customer['email_address'])
        );

        $shipments = array(
            "receiver_address" => array(
                "zip_code" => $order->delivery['postcode'],
                "street_name" => $order->delivery['street_address'],
                "street_number" => " ",
                "floor" => " ",
                "apartment" => " "
            )
        );

        $back_urls = array(
            "pending" => MODULE_PAYMENT_MERCADOPAGO_PENDING_URL,
            "success" => MODULE_PAYMENT_MERCADOPAGO_SUCESS_URL
        );

        //exclude payment methods
        if (MODULE_PAYMENT_MERCADOPAGO_METHODS != ''){
            $pref['payment_methods']['excluded_payment_methods'] = array();
            $methods_excludes = preg_split("/[\s,]+/", MODULE_PAYMENT_MERCADOPAGO_METHODS);

            foreach ($methods_excludes as $exclude) {
                $pref['payment_methods']['excluded_payment_methods'][] = array('id' => $exclude);
            }
        }

        $pref['payment_methods']['installments'] = (int) MODULE_PAYMENT_MERCADOPAGO_INSTALLMENTS;

        $pref['external_reference'] = $insert_id;
        $pref['items'] = $items;
        $pref['payer'] = $payer;
        $pref['shipments'] = $shipments;
        $pref['back_urls'] = $back_urls;

        if (MODULE_PAYMENT_MERCADOPAGO_AUTORETURN == 'true'):
            $pref['auto_return'] = "approved";
        endif;

        $pref['notification_url'] = HTTP_SERVER . DIR_WS_CATALOG . 'mercadopago.php';

        $preference = $mercadopago->create_preference($pref);

        //registre variable
        tep_session_register('botton');

        // default button production
        $botton = $preference['response']['init_point'];
        if(MODULE_PAYMENT_MERCADOPAGO_SANDBOX == 'true'){
          $botton = $preference['response']['sandbox_init_point']  ;
        }

        // unregister session variables used during checkout
        tep_session_unregister('sendto');
        tep_session_unregister('billto');
        tep_session_unregister('shipping');
        tep_session_unregister('payment');
        tep_session_unregister('comments');

        tep_redirect(tep_href_link('mercadopago.php', 'bt=' . trim($botton) . '&site_id=' . $site_id, 'SSL'));
    }

    function output_error() {
        return true;
    }

    function check() {
        if (!isset($this->_check)) {
            $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_MERCADOPAGO_STATUS'");
            $this->_check = tep_db_num_rows($check_query);
        }
        return $this->_check;
    }

    public function getCustomerDateCreated($customer_email = null) {
      $customer_info_query = tep_db_query("select ci.customers_info_date_account_created from customers_info ci, customers cu where ci.customers_info_id = cu.customers_id and customers_email_address = '$customer_email'");
      $customer_info = tep_db_fetch_array($customer_info_query);

      $date_created = $customer_info['customers_info_date_account_created'];
      $date_formated = date('Y-m-d', $date_created) . "T" . date('H:i:s', $date_created);
      return $date_formated;
    }

    public function getCountries() {
      $request = array(
        "uri" => "/sites"
      );
      $sites = MPRestClient::get($request);
      return $sites['response'];
    }

    public function GetMethods($country_id = null) {

      $request = array(
        "uri" => "/sites/" . $country_id . "/payment_methods"
      );
      $methods = MPRestClient::get($request);

      return $methods['response'];
    }

    public function GetCategories() {

      $request = array(
        "uri" => "/item_categories"
      );
      $categories = MPRestClient::get($request);

      return $categories['response'];
    }

    function install() {

      $active = (isset($HTTP_GET_VARS['actitve']) ? $HTTP_GET_VARS['actitve'] : '');
      if ($_REQUEST['active'] != 'true') {
        tep_redirect(tep_href_link('ext/modules/payment/mercadopago/activation.php'));
      }

      $posts = (isset($_POST) ? $_POST : '');

      // get categories from post parameters
      // if 'categories' is empty, set default with 'others'
      // @author Carlos Correa (cadu.rcorrea@gmail.com)
      $categories = empty($posts["categories"]) ? "others" : $posts['categories'];

      // verify if Mercado Pago Status is already setup
      $check_query = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [Pending]' limit 1");


      // if not, create Mercado Pago status
      if (tep_db_num_rows($check_query) < 1) {
        $status_id = $this->addStatus('MERCADO PAGO [Pending]');
        $status_id = $this->addStatus('MERCADO PAGO [In Process]');
        $status_id = $this->addStatus('MERCADO PAGO [Rejected]');
        $status_id = $this->addStatus('MERCADO PAGO [Approved]');
        $status_id = $this->addStatus('MERCADO PAGO [Refunded]');
        $status_id = $this->addStatus('MERCADO PAGO [Canceled]');
        $status_id = $this->addStatus('MERCADO PAGO [In Mediation]');
      }


      // get the id of the status
      $stpending = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [Pending]' limit 1");
      $pending = tep_db_fetch_array($stpending);
      $stprocess = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [In Process]' limit 1");
      $process = tep_db_fetch_array($stprocess);
      $streject = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [Rejected]' limit 1");
      $reject = tep_db_fetch_array($streject);
      $staproved = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [Approved]' limit 1");
      $aproved = tep_db_fetch_array($staproved);
      $strefunded = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [Refunded]' limit 1");
      $refunded = tep_db_fetch_array($strefunded);
      $stinmediation = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [In Mediation]' limit 1");
      $mediation = tep_db_fetch_array($stinmediation);
      $stcancel = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [Canceled]' limit 1");
      $cancel = tep_db_fetch_array($stcancel);

      $country = $_POST['country'];

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Mercado Pago module', 'MODULE_PAYMENT_MERCADOPAGO_STATUS', 'True', 'Do you want to accept payments through Mercado Pago?', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_group_id, sort_order, date_added) values ('Client_id','MODULE_PAYMENT_MERCADOPAGO_CLIENTID','Insert your client_id', '6','1',now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_group_id, sort_order, date_added) values ('Client_secret','MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET','Insert your client_secret','6','2', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order', 'MODULE_PAYMENT_MERCADOPAGO_SORT_ORDER', '1', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_group_id, sort_order, configuration_value, date_added) values ('Country','MODULE_PAYMENT_MERCADOPAGO_COUNTRY','Recommended to remove and install the module again if you need to change the country', '6','3','" . $country . "',now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_group_id, sort_order, configuration_value, date_added) values ('Exclude Methods','MODULE_PAYMENT_MERCADOPAGO_METHODS','Recommended to remove and install the module again if you need to change the no accepted methods', '6','3','" . $posts['methods'] . "',now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_group_id, sort_order, configuration_value, date_added) values ('Categories','MODULE_PAYMENT_MERCADOPAGO_CATEGORIES','Recommended to remove and install the module again if you need to change categories', '6','3','" . $categories . "',now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, set_function, date_added) values ('Limit installments','MODULE_PAYMENT_MERCADOPAGO_INSTALLMENTS','Limit the number of installments','24','6','5','tep_cfg_select_option(array(\'24\',\'18\',\'15\',\'12\',\'10\',\'9\',\'6\',\'5\',\'4\',\'3\',\'2\',\'1\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Sandbox?', 'MODULE_PAYMENT_MERCADOPAGO_SANDBOX', 'false', 'Enable test environment', '6', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Kind of Checkout?', 'MODULE_PAYMENT_MERCADOPAGO_CHECKOUT', 'Transparent', 'Checkout opening mode', '6', '5', 'tep_cfg_select_option(array(\'Transparent\', \'Lightbox\', \'Redirect\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Sucess Url','MODULE_PAYMENT_MERCADOPAGO_SUCESS_URL','Do not use LOCALHOST','" . HTTP_SERVER . DIR_WS_CATALOG . "account_history.php','6','4', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Pending url','MODULE_PAYMENT_MERCADOPAGO_PENDING_URL','Do not use LOCALHOST','" . HTTP_SERVER . DIR_WS_CATALOG . "account_history.php','6','5', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Auto Return?', 'MODULE_PAYMENT_MERCADOPAGO_AUTORETURN', 'true', 'Enable automatic redirection to Success URL after approval', '6', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");

      //two cards
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Two Card in Basic Checkout', 'MODULE_PAYMENT_MERCADOPAGO_TWO_CARDS_BASIC_CHECKOUT', 'active', 'Enables the buyer to pay with two cards', '6', '5', 'tep_cfg_select_option(array(\'active\', \'inactive\'), ', now())");

      // ipn status
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status pending','MODULE_PAYMENT_MERCADOPAGO_STATUS_PENDING','Automatically generated','" . $pending['orders_status_id'] . "','6','6', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status approved','MODULE_PAYMENT_MERCADOPAGO_STATUS_APROVED','Automatically generated','" . $aproved['orders_status_id'] . "','6','7', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status in process','MODULE_PAYMENT_MERCADOPAGO_STATUS_PROCESS','Automatically generated','" . $process['orders_status_id'] . "','6','8', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status rejected','MODULE_PAYMENT_MERCADOPAGO_STATUS_REJECT','Automatically generated','" . $reject['orders_status_id'] . "','6','9', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status refunded','MODULE_PAYMENT_MERCADOPAGO_STATUS_REFUNDED','Automatically generated','" . $refunded['orders_status_id'] . "','6','10', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status mediation','MODULE_PAYMENT_MERCADOPAGO_STATUS_MEDIATION','Automatically generated','" . $mediation['orders_status_id'] . "','6','11', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status canceled','MODULE_PAYMENT_MERCADOPAGO_STATUS_CANCELED','Automatically generated','" . $cancel['orders_status_id'] . "','6','11', now())");
    }

    function remove() {
        tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
        return array('MODULE_PAYMENT_MERCADOPAGO_STATUS',
            'MODULE_PAYMENT_MERCADOPAGO_CLIENTID',
            'MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET',
            'MODULE_PAYMENT_MERCADOPAGO_SORT_ORDER',
            'MODULE_PAYMENT_MERCADOPAGO_SUCESS_URL',
            'MODULE_PAYMENT_MERCADOPAGO_PENDING_URL',
            'MODULE_PAYMENT_MERCADOPAGO_AUTORETURN',
            'MODULE_PAYMENT_MERCADOPAGO_COUNTRY',
            'MODULE_PAYMENT_MERCADOPAGO_METHODS',
            'MODULE_PAYMENT_MERCADOPAGO_CATEGORIES',
            'MODULE_PAYMENT_MERCADOPAGO_INSTALLMENTS',
            'MODULE_PAYMENT_MERCADOPAGO_SANDBOX',
            'MODULE_PAYMENT_MERCADOPAGO_CHECKOUT',
            'MODULE_PAYMENT_MERCADOPAGO_TWO_CARDS_BASIC_CHECKOUT',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_PENDING',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_APROVED',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_PROCESS',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_REJECT',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_REFUNDED',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_MEDIATION',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_CANCELED'
        );
    }

    function getSponsorAndSite(){

      $user_info = array(
        "site_id" => "MLA",
        "sponsor_id" => ""
      );

      //check credentials configured
      if(MODULE_PAYMENT_MERCADOPAGO_CLIENTID != "" && MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET != ""){
        //init mercado pago
        $mercadopago = new MP(MODULE_PAYMENT_MERCADOPAGO_CLIENTID, MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET);

        //get info user
        $request = array(
          "uri" => "/users/me",
          "params" => array(
            "access_token" => $mercadopago->get_access_token()
          )
        );

        $user = MPRestClient::get($request);

        //check is a test
        if ($user['status'] == 200) {

          $user_info['site_id'] = $user['response']['site_id'];

          if(!in_array("test_user", $user['response']['tags'])){
            $sponsor_id = "";

            switch ($user_info['site_id']) {
              case 'MLA':
              $sponsor_id = 222656987;
              break;
              case 'MLB':
              $sponsor_id = 222656076;
              break;
              case 'MLC':
              $sponsor_id = 222657121;
              break;
              case 'MCO':
              $sponsor_id = 222657165;
              break;
              case 'MLM':
              $sponsor_id = 222656391;
              break;
              case 'MPE':
              $sponsor_id = 222656497;
              break;
              case 'MLV':
              $sponsor_id = 222658605;
              break;
            }

            $user_info['sponsor_id'] = $sponsor_id;
          }

        }
      }

      return $user_info;
    }

    function overOnePaymentsIPN($merchant_order){
      $total_amount = $merchant_order['total_amount'];
      $total_paid_approved = 0;
      $payment_return = array(
        "status" => "pending",
        "id" => ""
      );
      foreach($merchant_order['payments'] as $payment){
        //apenas soma quando for aprovado para mudar o status do pedido
        if($payment['status'] == "approved"){
          $total_paid_approved += $payment['total_paid_amount'];
        }
        //caso seja aprovado, authorized ou pendente adiciona os ids para mostrar na tela
        if($payment['status'] == "approved" || $payment['status'] == "authorized" || $payment['status'] == "pending"){
          $separator = "";
          if($payment_return['id'] != ""){
            $separator = " | ";
          }
          $payment_return['id'] .= $separator . $payment['id'];
        }
      }
      if($total_paid_approved >= $total_amount){
        $payment_return['status'] = "approved";
      }
      return $payment_return;
    }

    function updateApiAnalytics(){

      if((defined('MODULE_PAYMENT_MERCADOPAGO_CLIENTID') && defined('MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET')) && (MODULE_PAYMENT_MERCADOPAGO_CLIENTID != "" && MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET != "")){
        $status_module = MODULE_PAYMENT_MERCADOPAGO_STATUS;
        $status_two_cards = MODULE_PAYMENT_MERCADOPAGO_TWO_CARDS_BASIC_CHECKOUT == "active" ? "true": "false";

				//init mercado pago
				$mercadopago = new MP(MODULE_PAYMENT_MERCADOPAGO_CLIENTID, MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET);
				//get info user
        $request = array(
					"uri" => "/modules/tracking/settings",
					"params" => array(
						"access_token" => $mercadopago->get_access_token()
					),
					"data" => array(
						"two_cards" => strtolower($status_two_cards),
						"checkout_basic" => $status_module,
						"platform" => "OsCommerce",
						"platform_version" => PROJECT_VERSION,
            "module_version" => "2.0.2",
						"php_version" => phpversion()
					),
					"headers" => array(
							"content-type" => "application/json"
					)
				);

				$analytics = MPRestClient::post($request);

			}
		}

    function updateApiAccountSettings(){
      if((defined('MODULE_PAYMENT_MERCADOPAGO_CLIENTID') && defined('MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET')) && (MODULE_PAYMENT_MERCADOPAGO_CLIENTID != "" && MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET != "")){
				//init mercado pago
				$mercadopago = new MP(MODULE_PAYMENT_MERCADOPAGO_CLIENTID, MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET);

        //get info user
				$request = array(
					"uri" => "/account/settings",
					"params" => array(
						"access_token" => $mercadopago->get_access_token()
					),
					"data" => array(
						"two_cards" => MODULE_PAYMENT_MERCADOPAGO_TWO_CARDS_BASIC_CHECKOUT
					),
					"headers" => array(
							"content-type" => "application/json"
					)
				);
				$account_settings = MPRestClient::put($request);
        if($account_settings['status'] == 200){
          return true;
        }
			}
    }

}

?>
