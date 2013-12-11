<?php
namespace Outcomes;

use Outcomes\Model\Outcomes;
use Outcomes\Model\OutcomesTable;
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
                 'Outcomes\Model\OutcomesTable' =>  function($sm) {
                     $tableGateway = $sm->get('OutcomesTableGateway');
                     $table = new OutcomesTable($tableGateway);
                     return $table;
                 },
                 'OutcomesTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new Outcomes());
                     return new TableGateway('outcomes', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
    }

}
