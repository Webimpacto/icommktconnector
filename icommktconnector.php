<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Icommktconnector extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'icommktconnector';
        $this->tab = 'emailing';
        $this->version = '1.0.0';
        $this->author = 'icommkt';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('ICOMMKT Connector');
        $this->description = $this->l('Enabled API service to connect to ICOMMKT service');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {

        return parent::install() &&
            //$this->registerHook('header') &&
            //$this->registerHook('backOfficeHeader') &&
            //$this->registerHook('actionOrderStatusPostUpdate') &&
            //$this->registerHook('actionOrderStatusUpdate') &&
            //$this->registerHook('actionValidateOrder') &&
            //$this->registerHook('displayAdminOrderContentOrder') &&
            $this->registerHook('moduleRoutes') &&
            $this->registerHook('displayAdminOrderTabOrder');
    }

    public function uninstall()
    {

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitIcommktconnectorModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitIcommktconnectorModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-gear"></i>',
                        'desc' => $this->l('You can custom this value as you wish'),
                        'name' => 'ICOMMKT_APPKEY',
                        'label' => $this->l('App KEY'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-gear"></i>',
                        'desc' => $this->l('You can custom this value as you wish'),
                        'name' => 'ICOMMKT_APPTOKEN',
                        'label' => $this->l('App TOKEN'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'ICOMMKT_APPKEY' => Configuration::get('ICOMMKT_APPKEY', null),
            'ICOMMKT_APPTOKEN' => Configuration::get('ICOMMKT_APPTOKEN', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
    
    public function getApiBodyRequest(){
        $input_xml = null;
        $putresource = fopen("php://input", "r");
        while ($putData = fread($putresource, 1024)) {
            $input_xml .= $putData;
        }
        fclose($putresource);
        return $input_xml;
    }
    
    public function authorizeRequest(){
        $headers = getallheaders();
        $headerKey = '';
        $headerToken = '';
        foreach($headers as $key => $value){
            if(strtoupper($key) == 'X-VTEX-API-APPKEY'){
                $headerKey = $value;
            }
            if(strtoupper($key) == 'X-VTEX-API-APPTOKEN'){
                $headerToken = $value;
            }
        }
        if(!empty($headerKey) && !empty($headerToken)){
            $query = sprintf("SELECT * FROM "._DB_PREFIX_."configuration WHERE name='ICOMMKT_APPKEY' "
                    . "AND value='%s' AND id_shop IS NOT NULL",pSQL($headerKey));
            $result = Db::getInstance()->getRow($query);
            if($result){
                $this->context_id_shop = $result['id_shop'];
                $this->context_id_shop_group = $result['id_shop_group'];
            }else{
                $this->setError('Cannot find store for ICOMMKT_APPKEY - '.$headerKey, 400,  json_encode($headers),false);
                header("HTTP/1.1 400 Forbidden");
                die();
            }
        }
        $apiKey = Configuration::get('ICOMMKT_APPKEY',null,$this->context_id_shop_group,  $this->context_id_shop);
        $apiToken = Configuration::get('ICOMMKT_APPTOKEN',null,$this->context_id_shop_group,  $this->context_id_shop);
        
        if(empty($headerKey) || empty($headerToken) || $headerKey != $apiKey || $headerToken != $apiToken){
            $this->setError('Bad credentials', 403,  json_encode($headers),false);
            header("HTTP/1.1 403 Forbidden");
            die();
        }
        
    }
    
    public function controllerSetRespondeHeaders(){
        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }
        header('Content-type: application/json');
        header('Cache-Control: no-store, no-cache');
    }
    
    public function setError($message,$level,$additional_data = false,$stop=true){
        PrestaShopLogger::addLog('ICOMMKTCONNECTOR - ERROR: '.$message.' - '.$additional_data, 4, $level);
        $this->controllerSetRespondeHeaders();
        //http_response_code($level);
        if($stop){
            exit(json_encode(array('error' => $message)));
        }
    }
    
    public function hookModuleRoutes($params)
    {
        return array(
            'oms_list_orders' => array(
                //https://documenter.getpostman.com/view/487146/vtex-oms-api/6tjSKqi#209cb0dd-4877-4db8-a372-95173f49be07
                //List Orders
                'controller' =>    'oms',
                'keywords' => array(),
                'rule' =>        'icommkt/oms/pvt/orders',
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name
                ),
            ),
            /*'authorization' => array(
                'controller' =>    'authorization',
                'rule' =>        'authorization/{key}',
                'keywords' => array(
                    'key' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*', 'param' => 'key')
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name
                ),
            ),
            'transactions' => array(
                'controller' =>    'transactions',
                'rule' =>        'transactions/{transactionId}/payments/{paymentId}/return',
                'keywords' => array(
                    'transactionId' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*', 'param' => 'transactionId'),
                    'paymentId' =>            array('regexp' => '[_a-zA-Z0-9\pL\pS-]*', 'param' => 'paymentId'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name
                ),
            ),
            'paymentredsys' => array(
                'controller' =>    'payment',
                'rule' =>        'setpayment/{paymentId}/{tpv_id}',
                'keywords' => array(
                    'paymentId' =>            array('regexp' => '[_a-zA-Z0-9\pL\pS-]*', 'param' => 'paymentId'),
                    'tpv_id' =>        array('regexp' => '[_a-zA-Z0-9\pL\pS-]*', 'param' => 'tpv_id'),
                ),
                'params' => array(
                    'fc' => 'module',
                    'module' => $this->name
                ),
            ),*/
        );
    }

    public function hookActionOrderStatusPostUpdate()
    {
        /* Place your code here. */
    }

    public function hookActionOrderStatusUpdate()
    {
        /* Place your code here. */
    }

    public function hookActionValidateOrder()
    {
        /* Place your code here. */
    }

    public function hookDisplayAdminOrderContentOrder()
    {
        /* Place your code here. */
    }

    public function hookDisplayAdminOrderTabOrder()
    {
        /* Place your code here. */
    }
    
    public function getOrders(){
        $orders = $this->getOrdersWithInformations();
        $ordersFormatVtex = array();
        foreach($orders as &$order){
            $order['product_list'] = OrderDetail::getList($order['id_order']);
            $ordersFormatVtex = $this->formatListOrder($order);
        }
        ddd($orders[0]);
    }
    
    public function formatListOrder($order){
        $data = array(
            'orderId' => $order['id_order'],
            'creationDate' => gmdate("c", $order['date_add']),
            'clientName' => $order['firstname'].' '.$order['lastname']
        );
        
        return $data;
              /*{
		"orderId": "$order->",
		"creationDate": "2018-06-27T05:16:20.0000000+00:00",
		"clientName": "Luis Morales",
		"items": [{
			"seller": "1",
			"quantity": 1,
			"description": "Pantaloncillo B\u00f3xer Medio Estampado Azul XL",
			"ean": "7704863998766",
			"refId": "44000193-5064-Azul-XL",
			"id": "2086859",
			"productId": "2007061",
			"sellingPrice": 1049400,
			"price": 1049400
		}, {
			"seller": "1",
			"quantity": 1,
			"description": "Pantaloncillo B\u00f3xer Medio Estampado Morado XL",
			"ean": "7704863998889",
			"refId": "44000193-74924-Morado-XL",
			"id": "2086865",
			"productId": "2007061",
			"sellingPrice": 1049400,
			"price": 1049400
		}, {
			"seller": "1",
			"quantity": 1,
			"description": "Pantaloncillo Boxer Estampado Azul Oscuro XL",
			"ean": "7702218788512",
			"refId": "44000034-51-XL",
			"id": "2001743",
			"productId": "2000045",
			"sellingPrice": 1049400,
			"price": 1049400
		}],
		"totalValue": 3748200,
		"paymentNames": "Visa",
		"status": "handling",
		"statusDescription": "Preparando Entrega",
		"marketPlaceOrderId": null,
		"sequence": "10198287",
		"salesChannel": "1",
		"affiliateId": "",
		"origin": "Marketplace",
		"workflowInErrorState": false,
		"workflowInRetry": false,
		"lastMessageUnread": " PatPrimo HOMBRE MUJER TALLAS 14 - 24 SALE Pedido realizado con \u00e9xito! realizado en: 27\/06\/2018 Hola, Luis \u00a1Gracias por comprar en: www.patp",
		"ShippingEstimatedDate": "2018-07-04T05:17:07.0000000+00:00",
		"orderIsComplete": true,
		"listId": null,
		"listType": null,
		"authorizedDate": "2018-06-27T05:17:08.0000000+00:00",
		"callCenterOperatorName": null
	}  
EOF;*/
                
    }
    
    public function getOrdersWithInformations($limit = null, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $sql = 'SELECT *, (
					SELECT osl.`name`
					FROM `'._DB_PREFIX_.'order_state_lang` osl
					WHERE osl.`id_order_state` = o.`current_state`
					AND osl.`id_lang` = '.(int)$context->language->id.'
					LIMIT 1
				) AS `state_name`,
                                (
					SELECT c.`name`
					FROM `'._DB_PREFIX_.'carrier` c
					WHERE c.`id_carrier` = o.`id_carrier`
					LIMIT 1
				) AS `carrier_name`,
                                o.`date_add` AS `date_add`, o.`date_upd` AS `date_upd`
				FROM `'._DB_PREFIX_.'orders` o
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = o.`id_customer`)
                                LEFT JOIN `'._DB_PREFIX_.'address` ad ON (ad.`id_address` = o.`id_address_delivery`)
				WHERE 1
					'.Shop::addSqlRestriction(false, 'o').'
				ORDER BY o.`date_add` DESC
				'.((int)$limit ? 'LIMIT 0, '.(int)$limit : '');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}
