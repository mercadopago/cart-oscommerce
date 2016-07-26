<?php

/*
  $Id: mercadopago.php,v 1.1.0 2016/07/26 11:30:00 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
 */

class Basic {

    public $accesstoken;
    protected $client_id;
    protected $client_secret;
    public $error;
    protected $date;
    protected $expired;

    ///// function just to debug the code if is needed

    static function debug($error) {
        echo ('<pre>');
        print_r($error);
        echo ('</pre>');
    }

    ///// function to post the datas
    public function DoPost($fields, $url, $heads, $codeexpect, $type, $method) {

        // buld the post data follwing the api needs
        if ($type == 'json') {
            $posts = json_encode($fields);
        } else if ($type == 'none') {
            $posts = $fields;
        } else {
            $posts = http_build_query($fields);
        }

        // change the curl method follwing the api needs
        switch ($method):
            case 'get':
                $options = array(
                    CURLOPT_RETURNTRANSFER => '1',
                    CURLOPT_HTTPHEADER => $heads,
                    CURLOPT_URL => $url,
                    CURLOPT_POSTFIELDS => $posts,
                    CURLOPT_CUSTOMREQUEST => "GET"
                );
                break;
            case 'put':
                $options = array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_HTTPHEADER => $heads,
                    CURLOPT_URL => $url,
                    CURLOPT_POSTFIELDS => $posts,
                    CURLOPT_CUSTOMREQUEST => "PUT",
                    CURLOPT_HEADER => 1
                );
                break;
            case 'post':
                $options = array(
                    CURLOPT_RETURNTRANSFER => '1',
                    CURLOPT_HTTPHEADER => $heads,
                    CURLOPT_URL => $url,
                    CURLOPT_POSTFIELDS => $posts,
                    CURLOPT_CUSTOMREQUEST => "POST"
                );
                break;
            case 'delete':
                $options = array(
                    CURLOPT_RETURNTRANSFER => '1',
                    CURLOPT_HTTPHEADER => $heads,
                    CURLOPT_URL => $url,
                    CURLOPT_POSTFIELDS => $posts,
                    CURLOPT_CUSTOMREQUEST => "DELETE"
                );

                break;
            default:
                $options = array(
                    CURLOPT_RETURNTRANSFER => '1',
                    CURLOPT_HTTPHEADER => $heads,
                    CURLOPT_URL => $url,
                    CURLOPT_POSTFIELDS => $posts,
                    CURLOPT_CUSTOMREQUEST => "GET"
                );
                break;
        endswitch;

        // do a curl call
        $call = curl_init();
        curl_setopt_array($call, $options);
        // execute the curl call
        $dados = curl_exec($call);
        // get the curl statys
        $status = curl_getinfo($call);
        // close the call
        curl_close($call);
        // check to see if the call was succesful 
        if ($status['http_code'] != $codeexpect) {
            $this->debug($dados);
            //  $this->debug($status);
            return false;
        } else {
            // change the json retur to a php array and return it
            return json_decode($dados, true);
        }
    }

    public function getAccessToken() {

        $data = getdate();
        $time = $data[0];


        // verifica se já existe accesstoken valido, caso exista, retorna o accesstoken
        if (isset($this->accesstoken) && isset($this->date)) {
            $timedifference = $time - $this->date;
            if ($timedifference < $this->expired) {
                return $this->accesstoken;
            }
        }
        // get the clients variables
        $post = array(
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'client_credentials'
        );
        // set the header
        $header = array('Accept: application/json', 'Content-Type: application/x-www-form-urlencoded');
        // set the url to get the access token
        $url = 'https://api.mercadolibre.com/oauth/token';
        // call the post function. expection 200 as return
        $dados = $this->DoPost($post, $url, $header, '200', 'post', 'post');
        // set the access token
        $this->accesstoken = $dados['access_token'];
        // guarta o hoarario, prazo de expiração e returna o access token
        $this->date = $time;
        $this->expired = $dados['expires_in'];
        return $dados['access_token'];
    }

}

Class MPShop extends Basic {

    // do the client authentication
    public function __construct($client = null, $secret = null) {
        $this->client_id = $client;
        $this->client_secret = $secret;
    }

    // Generate the botton
    public function GetCheckout($data, $excludes, $installments) {

        $items = array(
            array(
                "id" => $data['external_reference'], // updated
                "title" => $data['title'],
                "description" => $data['description'],
                "quantity" => 1,
                "unit_price" => round($data['amount'], 2),
                "currency_id" => $data['currency'],
                "picture_url" => $data['image']
            )
        );

        $payer = array(
            "name" => $data['payment_firstname'],
            "surname" => $data['payment_lastname'],
            "email" => $data['email'],
            "phone" => array(
                "area_code" => " ",
                "number" => $data['phone']
            ),
            "address" => array(
                "zip_code" => $data['customer_zipcode'],
                "street_name" => $data['customer_address'],
                "street_number" => " "
            ),
            "date_created" => $data['customer_date_created']
        );

        $shipments = array(
            "receiver_address" => array(
                "zip_code" => $data['delivery_zipcode'],
                "street_name" => $data['delivery_address'],
                "street_number" => " ",
                "floor" => " ",
                "apartment" => " "
            )
        );

        $back_urls = array(
            "pending" => $data['pending'],
            "success" => $data['approved']
        );

        if ($excludes != ''){

            $methods_excludes = preg_split("/[\s,]+/", $excludes);
            
            foreach ($methods_excludes as $exclude) {
                $excludemethods[] = array('id' => $exclude);
            }

            $payment_methods = array(
                "excluded_payment_methods" => $excludemethods,
                "installments" => $installments
            );

        } else {

            //case not exist exclude methods
            $payment_methods = array(
                "installments" => $installments
            );
        }

        //mount array pref
        $pref = array();
        $pref['external_reference'] = $data['external_reference'];
        $pref['items'] = $items;
        $pref['payer'] = $payer;
        $pref['shipments'] = $shipments;
        $pref['back_urls'] = $back_urls;
        //$pref['payment_methods'] = $payment_methods;
        
        if (MODULE_PAYMENT_MERCADOPAGO_SANDBOX == 'false'):
            $pref['sponsor_id'] = $data['sponsor_id'];
        endif;

        if (MODULE_PAYMENT_MERCADOPAGO_AUTORETURN == 'true'):
            $pref['auto_return'] = "approved";
        endif;

        $pref['notification_url'] = HTTP_SERVER . DIR_WS_CATALOG . 'mercadopago.php';

        $this->getAccessToken();
        $url = 'https://api.mercadolibre.com/checkout/preferences?access_token=' . $this->accesstoken;
        $header = array('Content-Type:application/json', 'User-Agent:MercadoPago OsCommerce-2.3 Cart v1.1.0', 'Accept: application/json');
        $dados = $this->DoPost($pref, $url, $header, '201', 'json', 'post');

        /* Return sandbox_init_point or init_point
        *   Depends what was choosed on admin painel Mercado Pago
        * 
        * @author Carlos Correa (cadu.rcorrea@gmail.com)
        */
        $link = (MODULE_PAYMENT_MERCADOPAGO_SANDBOX == 'true') ? $dados['sandbox_init_point'] : $dados['init_point'];

        return $link;
    }

    public function GetStatus($id) {

         $this->getAccessToken(); 
         $url = "https://api.mercadolibre.com/collections/notifications/" . $id . "?access_token=" . $this->accesstoken;
         $header = array('Accept: application/json', 'Content-Type: application/x-www-form-urlencoded');
         $dados = $this->DoPost($pref=null,$url,$header,'200','none','post');
        
         // return $retorno  
         $order_id = $dados['collection']['external_reference'];



        // the actual order status
        $order_status = $dados['collection']['status'];
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


        // get the order
        require(DIR_WS_CLASSES . 'order.php');
        $order = new order($order_id);



        if ($order->info['orders_status'] != $status) {


            // update the order status
            $data = array('orders_status' => $status);
            tep_db_perform(TABLE_ORDERS, $data, 'update', "orders_id = '" . $order_id . "'");


            // incriment stock again if status is cancelled, refunded or reject


            if ($status == MODULE_PAYMENT_MERCADOPAGO_STATUS_CANCELED ||
                    $status == MODULE_PAYMENT_MERCADOPAGO_STATUS_REFUNDED ||
                    $status == MODULE_PAYMENT_MERCADOPAGO_STATUS_REJECT) {


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

class mercadopago {

    var $code, $title, $description, $enabled;

    function mercadopago() {
        global $order;

        $this->code = 'mercadopago';
        $this->title = MODULE_PAYMENT_MERCADOPAGO_TEXT_TITLE;
        $this->description = MODULE_PAYMENT_MERCADOPAGO_TEXT_DESCRIPTION;
        $this->sort_order = MODULE_PAYMENT_MERCADOPAGO_SORT_ORDER;

        $this->enabled = ((MODULE_PAYMENT_MERCADOPAGO_STATUS == 'True') ? true : false);


        if ((int) MODULE_PAYMENT_MERCADOPAGO_ORDER_STATUS_ID > 0) {
            $this->order_status = MODULE_PAYMENT_MERCADOPAGO_ORDER_STATUS_ID;
        }

        if (is_object($order))
            $this->update_status();

//$this->form_action_url = 'https://www.mercadopago.com/mlb/buybutton';
//$this->form_action_url = '';
    }

    function retorno($id) {
        $mp = new MPShop(MODULE_PAYMENT_MERCADOPAGO_CLIENTID, MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET);



        return $mp->GetStatus($id);
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

    /* function selection() {
      return array('id' => $this->code,
      'module' => $this->title);
      } */

//una version mejorada de 29/06
    function selection() {
        
        $country = MODULE_PAYMENT_MERCADOPAGO_COUNTRY;

        $fields = array();

        switch ($country) {
            case 'MLA': 
                $mercadopago_image = "http://imgmp.mlstatic.com/org-img/banners/ar/medios/468X60.jpg";
                $fields[] = array('title' => 'Medios de pago aceptados:',
                'text' => '');
                break;
            case 'MLB': 
                $mercadopago_image = "http://imgmp.mlstatic.com/org-img/MLB/MP/BANNERS/tipo2_575X40.jpg";
                $fields[] = array('title' => 'Modos de pagamento aceitos:',
                'text' => '');
                break;
            case 'MLC':
                $mercadopago_image = "https://www.mercadopago.cl/banner/468x60_banner.jpg";
                $fields[] = array('title' => 'Medios de pago aceptados:',
                'text' => '');
                break;
            case 'MCO':
                $mercadopago_image = "https://secure.mlstatic.com/developers/site/cloud/banners/co/468x60_Todos-los-medios-de-pago.jpg";
                $fields[] = array('title' => 'Medios de pago aceptados:',
                'text' => '');
                break;
            case 'MLM':
                $mercadopago_image = "http://imgmp.mlstatic.com/org-img/banners/mx/medios/MLM_468X60.JPG";
                $fields[] = array('title' => 'Medios de pago aceptados:',
                'text' => '');
                break;
            case 'MPE':
                $mercadopago_image = "https://mercadopago.mlstatic.com/images/desktop-logo-mercadopago.png";
                $fields[] = array('title' => 'Medios de pago aceptados:',
                'text' => '');
                break;
            case 'MLV':
                $mercadopago_image = "https://imgmp.mlstatic.com/org-img/banners/ve/medios/468X60.jpg";
                $fields[] = array('title' => 'Medios de pago aceptados:',
                'text' => '');
                break;
        }
             
        $fields[] = array('title' => '<img src="' . $mercadopago_image . '">',
            'text' => '');
        return array('id' => $this->code,
            'module' => $this->title,
            'fields' => $fields);
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
        global $insert_id, $order;
        
        $country = MODULE_PAYMENT_MERCADOPAGO_COUNTRY;

        switch ($country) {
            case 'MLA': 
                $currency = 'ARS';
                break;
            case 'MLB': 
                $currency = 'BRL';
                break;
            case 'MLC':
                $currency = 'CLP';
                break;
            case 'MCO':
                $currency = 'COP';
                break;
            case 'MLM':
                $currency = 'MXN';
                break;
            case 'MPE':
                $currency = 'PEN';
                break;
            case 'MLV':
                $currency = 'VEF';
                break;
        }

        switch ($country) {
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

        $dados = array(
            'external_reference' => $insert_id, // seu codigo de referencia, i.e. Numero do pedido da sua loja 
            'currency' => $order->info['currency'],
            //'currency' => $currency,
            'sponsor_id' => $sponsor_id,
            'title' => $order->products[0]['name'], //string
            'description' => $order->products[0]['name'], // string
            'quantity' => $order->products[0]['qty'], // int
            'image' => 'https://www.mercadopago.com/org-img/MP3/home/logomp3.gif', // Imagem, string
            'amount' => $order->info['total'], //decimal
            'category' => MODULE_PAYMENT_MERCADOPAGO_CATEGORIES,
            'payment_firstname' => $order->customer['firstname'], // string
            'payment_lastname' => $order->customer['lastname'], // string
            'email' => $order->customer['email_address'], // string
            'phone' => $order->customer['telephone'],
            'customer_date_created' => $this->getCustomerDateCreated($order->customer['email_address']),
            'customer_zipcode' => $order->customer['postcode'],
            'customer_address' => $order->customer['street_address'],
            'delivery_zipcode' => $order->delivery['postcode'],
            'delivery_address' => $order->delivery['street_address'],
            'pending' => MODULE_PAYMENT_MERCADOPAGO_PENDING_URL, // string
            'approved' => MODULE_PAYMENT_MERCADOPAGO_SUCESS_URL, // string 
        );
        
        $exclude = MODULE_PAYMENT_MERCADOPAGO_METHODS;  // string
        $limit = MODULE_PAYMENT_MERCADOPAGO_INSTALLMENTS;
        $pagamento = New MPShop(MODULE_PAYMENT_MERCADOPAGO_CLIENTID, MODULE_PAYMENT_MERCADOPAGO_CLIENTSECRET);
        tep_session_register('botton');
        $botton = $pagamento->GetCheckout($dados, $exclude, $limit);

        // unregister session variables used during checkout
        tep_session_unregister('sendto');
        tep_session_unregister('billto');
        tep_session_unregister('shipping');
        tep_session_unregister('payment');
        tep_session_unregister('comments');
      
        tep_redirect(tep_href_link('mercadopago.php', 'bt=' . trim($botton), 'SSL'));
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

      return date('Y-m-d',$date_created) . "T" . date('H:i:s',$date_created);
    }

    public function getCountries() {
        $mp = new MPShop();
        $url = 'https://api.mercadolibre.com/sites/';
        $header = array('Accept: application/json');
        $countries = $mp->DoPost(null, $url, $header, '200', 'none', 'get');
        return $countries;
    }

    public function GetMethods($country_id = null) {
        $mp = new MPShop();
        $url = "https://api.mercadolibre.com/sites/" . $country_id . "/payment_methods";
        $header = array('Accept: application/json');
        $methods = $mp->DoPost(null, $url, $header, '200', 'none', 'get');
        return $methods;
    }

    public function GetCategories() {
        $mp = new MPShop();
        $url = "https://api.mercadolibre.com/item_categories";
        $header = array('Accept: application/json');
        $categories = $mp->DoPost(null, $url, $header, '200', 'none', 'get');
        return $categories;
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
            $status_id = $this->addStatus('MERCADO PAGO [InProcess]');
            $status_id = $this->addStatus('MERCADO PAGO [Reject]');
            $status_id = $this->addStatus('MERCADO PAGO [Aproved]');
            $status_id = $this->addStatus('MERCADO PAGO [refunded]');
            $status_id = $this->addStatus('MERCADO PAGO [Canceled]');
            $status_id = $this->addStatus('MERCADO PAGO [InMediation]');
        }


// get the id of the status
        $stpending = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [Pending]' limit 1");
        $pending = tep_db_fetch_array($stpending);
        $stprocess = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [InProcess]' limit 1");
        $process = tep_db_fetch_array($stprocess);
        $streject = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [Reject]' limit 1");
        $reject = tep_db_fetch_array($streject);
        $staproved = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [Aproved]' limit 1");
        $aproved = tep_db_fetch_array($staproved);
        $strefunded = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [refunded]' limit 1");
        $refunded = tep_db_fetch_array($strefunded);
        $stinmediation = tep_db_query("select orders_status_id from " . TABLE_ORDERS_STATUS . " where orders_status_name = 'MERCADO PAGO [InMediation]' limit 1");
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
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Sandbox?', 'MODULE_PAYMENT_MERCADOPAGO_SANDBOX', 'true', 'Enable test environment', '6', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Kind of Checkout?', 'MODULE_PAYMENT_MERCADOPAGO_CHECKOUT', 'Transparent', 'Checkout opening mode', '6', '5', 'tep_cfg_select_option(array(\'Transparent\', \'Lightbox\', \'Redirect\'), ', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Sucess Url','MODULE_PAYMENT_MERCADOPAGO_SUCESS_URL','Do not use LOCALHOST','" . HTTP_SERVER . DIR_WS_CATALOG . "account_history.php','6','4', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Pending url','MODULE_PAYMENT_MERCADOPAGO_PENDING_URL','Do not use LOCALHOST','" . HTTP_SERVER . DIR_WS_CATALOG . "account_history.php','6','5', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Auto Return?', 'MODULE_PAYMENT_MERCADOPAGO_AUTORETURN', 'true', 'Enable automatic redirection to Success URL after approval', '6', '5', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status pending','MODULE_PAYMENT_MERCADOPAGO_STATUS_PENDING','Automatically generated','" . $pending['orders_status_id'] . "','6','6', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status aproved','MODULE_PAYMENT_MERCADOPAGO_STATUS_APROVED','Automatically generated','" . $aproved['orders_status_id'] . "','6','7', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status in process','MODULE_PAYMENT_MERCADOPAGO_STATUS_PROCESS','Automatically generated','" . $process['orders_status_id'] . "','6','8', now())");
        tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_description, configuration_value, configuration_group_id, sort_order, date_added) values ('Cod Status Reject','MODULE_PAYMENT_MERCADOPAGO_STATUS_REJECT','Automatically generated','" . $reject['orders_status_id'] . "','6','9', now())");
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
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_PENDING',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_APROVED',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_PROCESS',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_REJECT',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_REFUNDED',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_MEDIATION',
            'MODULE_PAYMENT_MERCADOPAGO_STATUS_CANCELED'
        );
    }

}

?>
