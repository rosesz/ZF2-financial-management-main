<?php

namespace Outcomes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Outcomes\Model\Outcomes;
use Outcomes\Form\OutcomesForm;


class OutcomesController extends AbstractActionController
{
    protected $outcomesTable;

    public function indexAction()
    {
         return new ViewModel(array(
             'outcomes' => $this->getOutcomesTable()->fetchAll(),
         ));
    }

    public function addAction()
    {
         $form = new OutcomesForm();
         $form->get('submit')->setValue('Add');

         $request = $this->getRequest();
         if ($request->isPost()) {
             $outcomes = new Outcomes();
             $form->setInputFilter($outcomes->getInputFilter());
             $form->setData($request->getPost());

             if ($form->isValid()) {
                 $outcomes->exchangeArray($form->getData());
                 $this->getOutcomesTable()->saveOutcomes($outcomes);

                 // Redirect to list of outcomess
                 return $this->redirect()->toRoute('outcomes');
             }
         }
         return array('form' => $form);

    }

    public function editAction()
    {
        return new ViewModel();
    }

    public function deleteAction()
    {
        return new ViewModel();
    }

    public function getOutcomesTable()
    {
         if (!$this->outcomesTable) {
             $sm = $this->getServiceLocator();
             $this->outcomesTable = $sm->get('Outcomes\Model\OutcomesTable');
         }
         return $this->outcomesTable;
    }
}

