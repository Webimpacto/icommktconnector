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

class IcommktconnectorMasterdataModuleFrontController extends ModuleFrontController
{

    public $php_self;

    public function __construct()
    {
        $this->php_self = 'masterdata';
        parent::__construct();
        $this->context = Context::getContext();
    }

    public function init()
    {
        $module = Module::getInstanceByName('icommktconnector');
        $module->authorizeRequest();
        
        $entity = Tools::getValue('entity_code');
        if ($entity && strtolower($entity) == 'cl') {
            $module->getClients();
        } else {
            die('entity not found');
        }
    }


}
