<?php

namespace Outcomes\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Outcomes
{
    public $id;
    public $amount;
    public $category;
    public $date;
    public $userId;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id       = (!empty($data['id'])) ? $data['id'] : null;
        $this->amount   = (!empty($data['amount'])) ? $data['amount'] : null;
        $this->category = (!empty($data['category'])) ? $data['category'] : null;
        $this->date     = (!empty($data['date'])) ? $data['date'] : null;
        $this->userId   = (!empty($data['user_id'])) ? $data['user_id'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'amount',
                'required' => true,
            ));

            $inputFilter->add(array(
                'name'     => 'category',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            ));

            $inputFilter->add(array(
                'name'     => 'date',
                'required' => true,
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}