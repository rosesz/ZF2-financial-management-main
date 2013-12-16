<?php

namespace Outcomes\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;

class OutcomesTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($userId)
    {
        $where = new Where();    
        $where->equalTo('user_id', $userId);
        $resultSet = $this->tableGateway->select($where);
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

    public function saveOutcomes(Outcomes $outcomes, $userId)
    {
        $data = array(
            'amount' => $outcomes->amount,
            'category'  => $outcomes->category,
            'date'  => $outcomes->date,
            'user_id'  => $userId,
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