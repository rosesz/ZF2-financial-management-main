<?php

namespace Incomes\Model;

use Zend\Db\TableGateway\TableGateway;

class IncomesTable
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

    public function getIncomes($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveIncomes(Incomes $incomes)
    {
        $data = array(
            'amount' => $incomes->amount,
            'category'  => $incomes->category,
            'date'  => $incomes->date,
        );

        $id = (int) $incomes->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getIncomes($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Incomes id does not exist');
            }
        }
    }

    public function deleteIncomes($id)
    {
         $this->tableGateway->delete(array('id' => (int) $id));
    }
}