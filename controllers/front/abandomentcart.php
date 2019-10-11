<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Icommkt
 * @copyright Icommkt
 * @license   GPLv3
 *
 */

class IcommktconnectorAbandomentcartModuleFrontController extends ModuleFrontController
{

    public $php_self;

    private $loaded_cart;
    private $loaded_customer;
    private $redirect;

    public function __construct()
    {
        //$this->php_self = 'abandomentcart';
        $this->context = Context::getContext();
        parent::__construct();
    }

    public function init()
    {
        parent::init();

        $action = Tools::getValue('action');
        $secure_token = Tools::getValue('secure_token');
        $secure_token_back = Configuration::get('ICOMMKT_SECURE_TOKEN');
        $id_cart = Tools::getValue('id_cart');

        if (empty($secure_token) || ($secure_token != $secure_token_back)) {
            $this->errors[] = Tools::displayError('El token no coincide');

            //die(('El token no coincide'));
        }

        switch ($action) {
            case 'load_cart':
                if (!empty($id_cart)) {
                    $this->loaded_cart = new Cart($id_cart);
                    $this->loaded_customer = new Customer($this->loaded_cart->id_customer);

                    $this->reloadOldCart($id_cart);

                    Tools::redirect($this->redirect);
                }
                break;
            case 'sendAbandomentcarts':
                $this->connectApi();
                break;
            default:
                break;
        }

        if (count($this->errors)) {
            foreach ($this->errors as $error) {
                echo $error;
            }
            exit();
        }
    }

    /*$id_cart: carrito a cargar*/
    public function reloadOldCart($id_cart = false/*, $id_customer = false*/)
    {
        if (!$id_cart) {
            return false;
        }

        $cart = $this->context->cart;
        $customer = $this->context->customer;

        $loaded_cart = !empty($id_cart) ? new Cart($id_cart) : $this->loaded_cart;
        /*$loaded_customer = !empty($id_customer) ? new Customer($id_customer) : $this->loaded_customer;*/
        /*$redirect = false;*/
        $newCart = false;

        // 1. Comprobar que el carrito no está ya cargado.
        // 2. Comprobar si hay usuario.
        if ($loaded_cart->id == $cart->id) {
            $this->redirect = $this->context->link->getPageLink('order');
            return false;
        }

        // 3. Comprobar que existe el carrito que recibimos.
        if (!empty($loaded_cart->id)) {
            $order = new Order();
            $order->getOrderByCartId($loaded_cart->id);

            // si el carrito tiene orden asociada
            if ($order->id) {
                return false;
                /*

                if(!empty($customer->id)){
                    //Comprobar si tiene un carrito vacio
                    $emptyCart = $this->searchEmptyCart($customer->id);
                    if(empty($emptyCart)){
                        //Si el usuario no tiene un carrito vacio
                        $newCart = $this->createSharedCart($oldcart->id);
                    }else{
                        //Si el usuario tiene un carrito vacio
                        $newCart = $this->createSharedCart($oldcart->id,$emptyCart);
                    }
                }else{
                    //Si no es usuario
                    $newCart = $this->createSharedCart($oldcart->id);
                }

                 * */

                /*Si es usuario*/
            } else {
                //3. Comprobar si coincide el usuario actual con el del carrito
                if ($loaded_cart->id_customer == $customer->id) {
                    $newCart = $loaded_cart;
                    $this->redirect = $this->context->link->getPageLink('order');
                } else {
                    $params = array('back' => $this->context->link->getPageLink('order'));
                    $this->redirect = $this->context->link->getPageLink('authentication', null, null, $params);
                }
            }

            if ($newCart) {
                $context = Context::getContext();
                $this->context->cookie->id_cart = $newCart->id;
                $context->cookie->id_cart = $newCart->id;
                $this->context->cart = $newCart;
                $context->cart = $newCart;
                $this->context->cart->update();
                $context->cart->update();
                //https://ahorrototal.com.t1.webimpacto.net/es/abandomentcart?action=load_cart&id_cart=103748
            }
        }

        return true;
    }

    public function connectApi()
    {
        $days_to_abandon_value = Configuration::get('ICOMMKT_DAYS_TO_ABANDON', null);
        $days_to_abandon = !empty($days_to_abandon_value) ? $days_to_abandon_value : '1';
        $secure_token = Configuration::get('ICOMMKT_SECURE_TOKEN', null);
        $app_key = 'MzY1ODg30';
        $url = 'https://api.icommarketing.com/Contacts/SaveMultiContact.Json/';
        $icommkt_connector = Module::getInstanceByName('icommktconnector');


        if (empty($app_key)) {
            return false;
        }

        if (!$this->errors) {
            $query = '	SELECT result.*
                FROM (SELECT DISTINCT c.id_customer, c.id_cart, cus.email, cus.firstname, c.date_upd
                    FROM (
                                SELECT c_.* FROM `' . _DB_PREFIX_ . 'cart` c_
                                LEFT JOIN ' . _DB_PREFIX_ . 'cart_product cp ON (c_.id_cart = cp.id_cart)
                                GROUP by cp.id_cart
                    ) AS c
                    LEFT JOIN `' . _DB_PREFIX_ . 'customer` `cus` ON c.id_customer = cus.id_customer
                    inner JOIN  `' . _DB_PREFIX_ . 'cart_product` cp ON (cp.id_cart = c.id_cart)
                    WHERE (c.date_upd > DATE_ADD(NOW(), INTERVAL -' . $days_to_abandon . ' DAY))
                    AND (c.id_cart NOT IN (SELECT id_cart FROM ' . _DB_PREFIX_ . 'commktconnector_abandomentcarts))
                    AND (c.id_cart NOT IN (SELECT id_cart FROM ' . _DB_PREFIX_ . 'orders))
                    AND (cus.email <> \'\')
                    ORDER BY c.id_cart DESC) AS result
                GROUP BY result.id_customer';

            $results = Db::getInstance()->ExecuteS($query);

            $contacts = array();
            foreach ($results as $r) {
                $date = date("d/m/Y", strtotime($r['date_upd']));
                $contacts[] = array(
                    'Email' => $r['email'],
                    'CustomFields' => array(
                        array(
                            'Key' => 'nombre',
                            'Value' => $r['firstname']
                        ),
                        array(
                            'Key' => 'fecha_abandono',
                            'Value' => $date
                        ),
                        array(
                            'Key' => 'url_carrito',
                            'Value' => $icommkt_connector->getFormattedLink(array(
                                'action' => 'load_cart',
                                'secure_token' => $secure_token,
                                'id_cart' => $r['id_cart']
                            ))
                        ),
                    )
                );
            }

            $data = array(
                'ProfileKey' => $app_key,
                'ContactList' => $contacts
            );

            /* Conectar a API ICOMMKT y realizar petición*/

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);

            $payload = json_encode($data);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type:application/json',
                'Authorization:MTQ2Ny0zODg0LWFob3Jyb3RvdF91c3I1:'
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $result = curl_exec($ch);
            curl_close($ch);

            $insert_array = array();
            $insert_error = array();

            foreach (json_decode($result)->SaveMultiContactJsonResult->Responses as $key => $value) {
                if ($value->StatusCode == 1) {
                    $insert_array[] = array(
                        'id_cart' => $results[$key]['id_cart'],
                        'send' => 1
                    );
                } else {
                    $insert_error[] = array(
                        'id_cart' => $results[$key]['id_cart'],
                        'error' => (String)pSQL(json_encode($value)),
                    );
                }
            }

            var_dump(count(json_decode($result)->SaveMultiContactJsonResult->Responses));

            /* Guardar datos de los mails registrado en la tabla "ps_commktconnector_abandomentcarts" y
             * los que han dado error en la tabla "commktconnector_abandomentcarts_error"
            */

            if (!empty($insert_array)) {
                Db::getInstance()->insert('commktconnector_abandomentcarts', $insert_array);
            }

            if (!empty($insert_error)) {
                Db::getInstance()->insert('commktconnector_abandomentcarts_error', $insert_error);
            }

            die('Carritos enviados correctamente');
        }
    }
}
