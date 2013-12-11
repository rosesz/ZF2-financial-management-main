<?php

namespace Outcomes\Model;

 use Zend\Db\TableGateway\TableGateway;

 class OutcomesTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }

     public function getOutcomes($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Could not find row $id");
         }
         return $row;
     }

     public function saveOutcomes(Outcomes $outcomes)
     {
         $data = array(
             'amount' => $outcomes->amount,
             'category'  => $outcomes->category,
             'date'  => $outcomes->date,
         );

         $id = (int) $outcomes->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getOutcomes($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Outcomes id does not exist');
             }
         }
     }

     public function deleteOutcomes($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }