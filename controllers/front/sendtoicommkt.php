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

class IcommktconnectorSendToIcommktModuleFrontController extends ModuleFrontController
{

    public $php_self;

    public function __construct()
    {
        $this->context = Context::getContext();
        parent::__construct();
    }

    public function init()
    {
        parent::init();

        $action = Tools::getValue('action');
        $secure_token = Tools::getValue('secure_token');
        $secure_token_back = Configuration::get('ICOMMKT_SECURE_TOKEN');

        if (empty($secure_token) || ($secure_token != $secure_token_back)) {
            echo 'El token no coincide';
            exit();
        }

        switch ($action) {
            case 'sendtoicommktuser':
                $this->sendUsersIcommkt();
                break;
            default:
                break;
        }
    }

    public function sendUsersIcommkt()
    {
        if (!Module::isInstalled('icommktconnector')) {
            echo 'Error: módulo no instalado.';
            exit();
        } else {
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                $sql  = 'SELECT id, email, newsletter_date_add FROM ' . _DB_PREFIX_ . 'emailsubscription 
                    WHERE is_send_icommkt IS NULL OR is_send_icommkt = 0';
                $table = 'emailsubscription';
            } else {
                $sql  = 'SELECT id, email, newsletter_date_add FROM ' . _DB_PREFIX_ . 'newsletter 
                    WHERE is_send_icommkt IS NULL OR is_send_icommkt = 0';
                $table = 'newsletter';
            }
            
            $result = Db::getInstance()->executeS($sql);

            foreach ($result as $user) {
                if ($user['email']) {
                    $response = $this->doRequestIcommkt($user['email'], $user['newsletter_date_add']);
                    $response = json_decode($response, true);
                    if ($response['SaveContactJsonResult']['StatusCode'] == 1) {
                        Db::getInstance()->update(
                            $table,
                            array(
                            'is_send_icommkt'=> 1,
                            'date_send_icommkt' => date('Y-m-d H:i:s')),
                            'id='.$user['id']
                        );
                    }
                }
            }
        }
    }

    public function doRequestIcommkt($email, $newsletter_date_add)
    {
        $url =  "https://api.icommarketing.com/Contacts/SaveContact.Json/";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = array(
            'ProfileKey' => Configuration::get('ICOMMKT_PROFILEKEY'),
            'Contact' => array (
                'Email'=> $email,
                'CustomFields'=> array(
                    array (
                        'Key' => 'newsletter_date_add',
                        'Value' => $newsletter_date_add
                    )
                )
            ),
        );
        $payload = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization:' . Configuration::get('ICOMMKT_APPKEY')
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
