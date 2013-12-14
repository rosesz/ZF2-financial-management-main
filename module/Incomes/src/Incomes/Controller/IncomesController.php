<?php

namespace Incomes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Incomes\Model\Incomes;
use Incomes\Form\IncomesForm;


class IncomesController extends AbstractActionController
{
    protected $incomesTable;

    public function indexAction()
    {   
        return new ViewModel(array(
            'incomes' => $this->getIncomesTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
        $form = new IncomesForm($this->categoriesList());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $incomes = new Incomes();
            $form->setInputFilter($incomes->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $incomes->exchangeArray($form->getData());
                $this->getIncomesTable()->saveIncomes($incomes);

                // Redirect to list of incomes
                return $this->redirect()->toRoute('incomes');
            }
        }
        return array('form' => $form);

    }

    public function editAction()
    {
        
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('incomes', array(
                'action' => 'add'
            ));
        }

        // Get the Income with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $incomes = $this->getIncomesTable()->getIncomes($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('incomes', array(
                'action' => 'index'
            ));
        }

        $form  = new IncomesForm();
        $form->bind($incomes);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($incomes->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getIncomesTable()->saveIncomes($incomes);

                // Redirect to list of incomes
                return $this->redirect()->toRoute('incomes');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('incomes');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getIncomesTable()->deleteIncomes($id);
            }

            // Redirect to list of incomes
            return $this->redirect()->toRoute('incomes');
        }

        return array(
            'id'    => $id,
            'incomes' => $this->getIncomesTable()->getIncomes($id)
        );
    }

    public function categoriesList()
    {
        $incomes = $this->getIncomesTable()->fetchAll();
        $cat = array();
        foreach ($incomes as $row) {
            $cat[$row->category] = $row->category;
        }
        $categories = array_values($cat); 

        return \Zend\Json\Json::encode($categories, true);      
    }

    public function getIncomesTable()
    {
        if (!$this->incomesTable) {
            $sm = $this->getServiceLocator();
            $this->incomesTable = $sm->get('Incomes\Model\IncomesTable');
        }
        return $this->incomesTable;
    }
}

