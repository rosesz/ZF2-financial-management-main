<?php
namespace Incomes;

use Incomes\Model\Incomes;
use Incomes\Model\IncomesTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Incomes\Model\IncomesTable' =>  function($sm) {
                    $tableGateway = $sm->get('IncomesTableGateway');
                    $table = new IncomesTable($tableGateway);
                    return $table;
                },
                'IncomesTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Incomes());
                    return new TableGateway('incomes', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
