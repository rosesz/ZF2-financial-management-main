<?php

namespace Outcomes\Form;

use Zend\Form\Form;

class OutcomesForm extends Form
{
    public function __construct($categories, $name = null)
    {
        parent::__construct('outcomes');
        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'amount',
            'type' => 'Text',
            'options' => array(
                'label' => 'Kwota',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'autocomplete' => 'off',
            ),
        ));
        $this->add(array(
            'name' => 'category',
            'type' => 'Text',
            'options' => array(
                'label' => 'Kategoria',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'category-select',
                'data-source' => $categories,
                'autocomplete' => 'off',
            ),
        ));
        $this->add(array(
            'name' => 'date',
            'type' => 'Text',
            'options' => array(
                'label' => 'Data',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'id' => 'datepicker',
                'autocomplete' => 'off',
            ),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
                'class' => 'btn btn-default',
            ),
        ));
    }
}