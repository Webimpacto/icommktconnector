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
                'keywords' => array(
                    'id_order' => array('regexp' => '[0-9]+', 'param' => 'id_order'),
                ),
                'rule' =>        'icommkt/oms/pvt/orders{/:id_order}',
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
    
    public function getSingleOrder($id_order){
        $order = $this->getOrderInformation($id_order);
        $cart_rules = $this->getCartRules($id_order);


        if(!$order)
            exit('order not found');

        $data = array(
            'orderId' => $order['id_order'],
            'sequence' => $order['id_order'],
            'marketPlaceOrderId' => $order['id_order'],            
            'marketplaceServicesEndpoint' => null,
            'sellerOrderId' => $order['id_order'],
            'origin' => null,
            'affiliateId' => null,
            'salesChannel' => 1,
            'merchantName' => null,
            'status' => $order['state_name'],
            'statusDescription' => $order['state_name'],
            'value' => $order['total_paid'],
            'creationDate' => gmdate("c", $order['date_add']),
            'lastChange' => gmdate("c", $order['date_upd']),
            'orderGroup' => null,
            'totals' => array(
                array(
                    'id' => 'Items',
                    'name' => 'Total de los items',
                    'values' => $order['total_products_wt'],
                ),
                array(
                    'id' => 'Discounts',
                    'name' => 'Total de descuentos',
                    'values' => $order['total_discounts_tax_incl'],
                ),
                array(
                    'id' => 'Shipping',
                    'name' => 'Costo total del envío',
                    'values' => $order['total_shipping_tax_incl'],
                ),
            ),
            'items' => $this->formatProductList($order['id_order'], true),
            'marketplaceItems' => array(),
            'clientProfileData' => array(
                'id' => $order['id_customer'],
                'email' => $order['email'],
                'firstName' => $order['firstname'],
                'lastName' => $order['lastname'],
                'documentType' => 'nif',
                'document' => $order['dni'],
                'phone' => ($order['phone'] != '' ? $order['phone'] : $order['phone_mobile']),
                'corporateName' => $order['company'],
                'tradeName' => null,
                'corporateDocument' => null,
                'stateInscription' => null,
                'corporatePhone' => null,
                'isCorporate' => false,
                'userProfileId' => $order['id_customer'],
                'customerClass' => null,

            ),
            'giftRegistryData' => null,
            'marketingData' => array(
                'id' => 'marketingData',
                'utmSource' => $cart_rules[0]['name'],
                'utmPartner' => null,
                'utmMedium' => '',
                'utmCampaign' => '',
                'coupon' => $cart_rules[0]['code'],
                'utmiCampaign' => '',
                'utmipage' => '',
                'utmiPart' => '',
                'marketingTags' => array(),
            ),
            'ratesAndBenefitsData' => array(
                'id' => 'ratesAndBenefitsData',
                'rateAndBenefitsIdentifiers' => array(),
            ),
            'shippingData' => array(
                'id' => 'shippingData',
                'address' => array(
                    'addressType' => "residential",
                    'receiverName' => $order['firstname'].' '.$order['lastname'],
                    'addressId' => $order['id_address'],
                    'postalCode' => $order['postcode'],
                    'city' => $order['city'],
                    'state' => State::getNameById($order['id_state']),
                    'country' => Country::getNameById($order['id_lang'], $order['id_state']),
                    'street' => $order['address1'],
                    'number' => null,
                    'neighborhood' => null,
                    'complement' => $order['address2'],
                    'reference' => null,
                    'geoCoordinates' => array(),
                ),
                'logisticsInfo' => $this->getLogisticInfo($id_order),
                'trackingHints' => null,
                'selectedAddresses' => array(
                    array(
                        'addressId' => $order['id_address_delivery'],
                        'addressType' => "residential",
                        'receiverName' => $order['firstname'].' '.$order['lastname'],
                        'street' => $order['address1'],
                        'number' => null,
                        'complement' => $order['address2'],
                        'neighborhood' => null,
                        'postalCode' => $order['postcode'],
                        'city' => $order['city'],
                        'state' => State::getNameById($order['id_state']),
                        'country' => Country::getNameById($order['id_lang'], $order['id_state']),
                        'reference' => null,
                        'geoCoordinates' => array(),
                    ),
                ),
            ),
            'paymentData' => array(
                'transactions' => array(
                    array(
                        'isActive' => true,
                        'transactionId' => "409E420B030E4E33945A1B8E02DF9BB9",
                        'merchantName' => "PATPRIMO",
                        'payments' => $this->getDataPayment($order['reference']),
                    ),
                ),
            ),
            'packageAttachment' => array(
                'packages' => array(),
            ),
            'sellers' => array(
                array(
                    'id' => "1",
                    'name' => "PatPrimo",
                    'logo' => "",
                ),
            ),
            'callCenterOperatorData' => null,
            'followUpEmail' => "cead99a91b104f5c871e888f28a1e07e@ct.vtex.com.br",
            'lastMessage' => null,
            'hostname' => "patprimo",
            'changesAttachment' => null,
            'openTextField' => null,
            'roundingError' => 0,
            'orderFormId' => "0b0f14fad79d428d934a82c5a1ccab2e",
            'commercialConditionData' => null,
            'isCompleted' => true,
            'customData' => null,
            'storePreferencesData' => array(
                'countryCode' => "COL",
                'currencyCode' => "COP",
                'currencyFormatInfo' => array(
                    'CurrencyDecimalDigits' => 2,
                    'CurrencyDecimalSeparator' => ",",
                    'CurrencyGroupSeparator' => ".",
                    'CurrencyGroupSize' => 3,
                    'StartsWithCurrencySymbol' => true,
                ),
                'currencyLocale' => 9226,
                'currencySymbol' => "$",
                'timeZone' => "SA Pacific Standard Time",
            ),
            'allowCancellation' => true,
            'allowEdition' => false,
            'isCheckedIn' => false,
            'marketplace' => array(
                'baseURL' => "http:\/\/portal.vtexcommerce.com.br\/api\/oms?an=patprimo",
                'isCertified' => null,
                'name' => "patprimo",
            ),
        );
        exit(json_encode($data));

    }

    public function getOrders(){
        $orderField = null;
        $orderType = null;
        $limit = null;
        $page = null;

        if($orderBy = Tools::getValue('orderBy')){
            $field = explode(',', $orderBy)[0];            

            switch ($field) {
                case 'orderId':
                    $orderField = 'id_order';
                    break;
                case 'totalValue':
                    $orderField = 'total_paid';
                    break;
                case 'creationDate':
                    $orderField = 'date_add';
                    break;
                default:
                    $orderField = null;
                    break;
            }

            $type = explode(',', $orderBy)[1];
            if($type == 'asc' || $type == 'desc')
                $orderType = $type;
        }


        $orders = $this->getOrdersWithInformations(limit, page, $orderField, $orderType);
        $ordersFormatVtex = array();
        foreach($orders as &$order){
            $ordersFormatVtex[] = $this->formatListOrder($order);
        }
        
        exit(json_encode($ordersFormatVtex));
        ddd($orders[0]);
    }
    

    public function formatListOrder($order){
        $data = array(
            'orderId' => $order['id_order'],
            'creationDate' => gmdate("c", $order['date_add']),
            'clientName' => $order['firstname'].' '.$order['lastname'],
            'totalValue' => $order['total_paid'],
            'paymentNames' => $order['payment'],
            'status' => $order['state_name'],
            'statusDescription' => $order['state_name'],
            'marketPlaceOrderId' => $order['id_order'],
            'sequence' => $order['id_order'],
            'salesChannel' => 1,
            'affiliateId' => null,
            'origin' => null,
            'workflowInErrorState' => null,
            'workflowInRetry' => null,
            'lastMessageUnread' => null,
            'ShippingEstimatedDate' => 'undefined',
            'orderIsComplete' => ($order['valid'] ? true : false),
            'listId' => null,
            'listType' => null,
            'authorizedDate' => gmdate("c", $order['date_add']),
            'callCenterOperatorName' => 'undefined',
            'items' => $this->formatProductList($order['id_order']),
        );
        
        return $data;
                
    }
    
    public function getOrdersWithInformations($limit = null, $page = null, $orderField = null, $orderType = null, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        if(!$page)
            $n=0;
        else
            $n=((int)$page-1)*(int)$limit;

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
                ORDER BY o.'.($orderField ? $orderField : 'id_order').' '.($orderType ? $orderType : 'DESC').'
				'.((int)$limit ? 'LIMIT '.(int)$n.', '.(int)$limit : '');
       return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getOrderInformation($id_order, Context $context = null)
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
                    '.Shop::addSqlRestriction(false, 'o').' AND id_order = '.$id_order.'
                ORDER BY o.`date_add` DESC
                '.((int)$limit ? 'LIMIT 0, '.(int)$limit : '');
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function formatProductList($id_order, $extend = false, Context $context = null){

        if (!$context) {
            $context = Context::getContext();
        }

        $products = array();

        foreach (OrderDetail::getList($id_order) as $item) {
            $data = array(
                'seller' => null,
                'quantity' => $item['product_quantity'],
                'description' => $item['product_name'],
                'ean' => $item['product_ean13'],
                'refId' => $item['product_reference'],
                'id' => $item['product_id'],
                'productId' => $item['product_id'],
                'sellingPrice' => round($item['unit_price_tax_incl'], 2),
                'price' => round($item['total_price_tax_incl'], 2),
            );

            if($extend){
                $product = new Product($item['product_id'], false, (int)$context->language->id);

                $data_extend = array(
                    'uniqueId' => $item['product_id'],
                    'description' => $item['product_name'],
                    'listPrice' => '???',
                    'manualPrice' => null,
                    'priceTags' => array(),
                    'imageUrl' => $context->link->getImageLink($product->link_rewrite, Image::getCover($item['product_id'])['id_image']),
                    'detailUrl' => $context->link->getProductLink($item['product_id']),
                    'components' => array(),
                    'bundleItems' => array(),
                    'params' => array(),
                    'offerings' => array(),
                    'sellerSku' => $item['product_attribute_id'],
                    'priceValidUntil' => null,
                    'commission' => 0,
                    'tax' => $item['tax_rate'],
                    'preSaleDate' => null,
                    'additionalInfo' => array(
                        'brandName' => $product->manufacturer_name,
                        'brandId' => $product->id_manufacturer,
                        'categoriesIds' => implode(',', $product->getCategories()),
                        'productClusterId' => '',
                        'commercialConditionId' => "1",
                        'dimension' => array(
                            'cubicweight' => 1,
                            'height' => 1,
                            'length' => 1,
                            'weight' => 1,
                            'width' => 1
                        ),
                        'offeringInfo' => null,
                        'offeringType' => null,
                        'offeringTypeId' => null,
                    ),
                    'measurementUnit' => "un",
                    'unitMultiplier' => 1,
                    'isGift' => false,
                    'shippingPrice' => $item['total_shipping_price_tax_incl'],
                    'rewardValue' => 0,
                    'freightCommission' => 0,
                );

                $data = array_merge($data, $data_extend);
            }

            $products[] = $data;
        }

        return $products;
    }

    public function getLogisticInfo($id_order){
        $data = array();

        foreach (OrderDetail::getList($id_order) as $key => $item) {
            $data[] = array(
                'itemIndex' => $key,
                'selectedSla' => "Normal",
                'lockTTL' => "12d",
                'price' => round($item['unit_price_tax_incl'], 2),
                'listPrice' => round($item['original_product_price'], 2),
                'sellingPrice' => round($item['unit_price_tax_incl'], 2),
                'deliveryWindow' => null,
                'deliveryCompany' => "Servientrega",
                'shippingEstimate' => "5bd",
                'shippingEstimateDate' => "",
                'slas' => array(
                    array(
                        'id' => "Normal",
                        'name' => "Normal",
                        'shippingEstimate' => "5bd",
                        'deliveryWindow' => null,
                        'price' => 297600,
                        'deliveryChannel' => "delivery",
                        'pickupStoreInfo' => array(
                            'additionalInfo' => null,
                            'address' => null,
                            'dockId' => null,
                            'friendlyName' => null,
                            'isPickupStore' => false,
                        ),
                    ),
                ),
                'shipsTo' => array('COL'),
                'deliveryIds' => array(
                    array(
                        'courierId' => "1",
                        'courierName' => "Servientrega",
                        'dockId' => "1",
                        'quantity' => 1,
                        'warehouseId' => "1_1",
                    ),
                ),
                'deliveryChannel' => 'delivery',
                'pickupStoreInfo' => array(
                    'additionalInfo' => null,
                    'address' => null,
                    'dockId' => null,
                    'friendlyName' => null,
                    'isPickupStore' => false,
                ),
                'addressId' => "-1530076290589"
            );
        }
    }

    public function getCartRules($id_order){
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT *
        FROM `'._DB_PREFIX_.'order_cart_rule` ocr
        LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON ocr.id_cart_rule = cr.id_cart_rule
        WHERE ocr.`id_order` = '.(int)$id_order);
    }

    public static function getDataPayment($order_reference){
        $payments = Db::getInstance()->executeS('
            SELECT *
            FROM `'._DB_PREFIX_.'order_payment`
            WHERE `order_reference` = '.(int)$order_reference);

        $data = array();

        foreach ($payments as $value) {
            $data[] = array(
                'id' => $value['transaction_id'],
                'paymentSystem' => "",
                'paymentSystemName' => $value['card_brand'],
                'value' => $value['amount'],
                'installments' => 3,
                'referenceValue' => $value['transaction_id'],
                'cardHolder' => $value['card_holder'],
                'cardNumber' => $value['card_number'],
                'firstDigits' => "",
                'lastDigits' => "",
                'cvv2' => null,
                'expireMonth' => ($value['card_expiration'] ? substr($value['card_expiration'], 2) : null),
                'expireYear' => ($value['card_expiration'] ? substr($value['card_expiration'], -2) : null),
                'url' => null,
                'giftCardId' => null,
                'giftCardName' => null,
                'giftCardCaption' => null,
                'redemptionCode' => null,
                'group' => "",
                'tid' => "",
                'dueDate' => null,
                'connectorResponses' => array(),
            );
        }

        return $data;
    }
}
