<?php

namespace Outcomes\Form;

 use Zend\Form\Form;

 class OutcomesForm extends Form
 {
     public function __construct($name = null)
     {
         // we want to ignore the name passed
         parent::__construct('outcomes');

         $this->add(array(
             'name' => 'id',
             'type' => 'Hidden',
         ));
         $this->add(array(
             'name' => 'amount',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Amount',
             ),
         ));
         $this->add(array(
             'name' => 'category',
             'type' => 'Text',
             'options' => array(
                 'label' => 'Category',
             ),
         ));
         $this->add(array(
             'name' => 'date',
             'type' => 'Date',
             'options' => array(
                 'label' => 'Date',
             ),
         ));
         $this->add(array(
             'name' => 'submit',
             'type' => 'Submit',
             'attributes' => array(
                 'value' => 'Go',
                 'id' => 'submitbutton',
             ),
         ));
     }
 }