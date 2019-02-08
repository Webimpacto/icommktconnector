<?php
/*
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

class IcommktconnectorOMSModuleFrontController extends ModuleFrontController
{

    public $php_self;

    public function __construct()
    {
        $this->php_self = 'oms';
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function init()
    {

        $module = Module::getInstanceByName('icommktconnector');
        $module->authorizeRequest();
        
        if (strpos($_SERVER['REQUEST_URI'], 'status_list') !== false) {
            $module->getStatusList();
        } else {
            if ($id_order = Tools::getValue('id_order')) {
                $module->getSingleOrder($id_order);
            } else {
                $module->getOrders();
            }
        }
        
    }


}
