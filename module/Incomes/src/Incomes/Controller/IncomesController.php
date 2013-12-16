<?php

namespace Incomes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Incomes\Model\Incomes;
use Incomes\Form\IncomesForm;
use Zend\Http\Client;
use Zend\Http\Request;


class IncomesController extends AbstractActionController
{
    protected $incomesTable;
    protected $chartsGeneratorUrl = "http://localhost:8080/chart";

    public function indexAction()
    {   
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        return new ViewModel(array(
            'incomes' => $this->getIncomesTable()->fetchAll($userId),
        ));
    }

    public function addAction()
    {
        $this->checkAccess();

        $form = new IncomesForm($this->categoriesList());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $incomes = new Incomes();
            $form->setInputFilter($incomes->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $incomes->exchangeArray($form->getData());
                $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
                $this->getIncomesTable()->saveIncomes($incomes, $userId);

                // Redirect to list of incomes
                return $this->redirect()->toRoute('incomes');
            }
        }
        return array('form' => $form);

    }

    public function editAction()
    {
        $this->checkAccess();

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

        $form  = new IncomesForm($this->categoriesList());
        $form->bind($incomes);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($incomes->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
                $this->getIncomesTable()->saveIncomes($incomes, $userId);

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
        $this->checkAccess();

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

    public function chartsAction()
    {
        $this->checkAccess();

        return new ViewModel();
    }

    public function generateChartAction()
    {
        $this->checkAccess();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $startDate = $request->getPost('startDate');
            $endDate = $request->getPost('endDate');
            $type = $request->getPost('type');

            if ($type == "categories") {
                $data = $this->sumByCategories($startDate, $endDate);
                $title = "Przychody według kategorii";
            }
            elseif ($type == "days") {
                $data = $this->sumByDays($startDate, $endDate);
                $title = "Przychody według dni";
            }
        }

        $url = $this->getChartUrl($type, $data, $title);
        
        return new ViewModel(array(
            'url' => $url['url'],
            'title' => $title
        ));
    }

    public function categoriesList()
    {
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $incomes = $this->getIncomesTable()->fetchAll($userId);
        $cat = array();
        foreach ($incomes as $row) {
            $cat[$row->category] = $row->category;
        }
        $categories = array_values($cat); 

        return \Zend\Json\Json::encode($categories, true);      
    }

    public function sumByCategories($startDate = "", $endDate = "")
    {
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $incomes = $this->getIncomesTable()->fetchAll($userId);
        $sum = array();
        foreach ($incomes as $row) {
            if ($this->isInDataRange($row->date, $startDate, $endDate))
                $sum[$row->category] += $row->amount;
        }

        return \Zend\Json\Json::encode($sum, true);    
    }

    public function sumByDays($startDate = "", $endDate = "")
    {
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $incomes = $this->getIncomesTable()->fetchAll($userId);
        $sum = array();
        foreach ($incomes as $row) {
            if ($this->isInDataRange($row->date, $startDate, $endDate))
                $sum[$row->date] += $row->amount;
        }

        return \Zend\Json\Json::encode($sum, true);    
    }

    public function getIncomesTable()
    {
        if (!$this->incomesTable) {
            $sm = $this->getServiceLocator();
            $this->incomesTable = $sm->get('Incomes\Model\IncomesTable');
        }
        return $this->incomesTable;
    }

    private function isInDataRange($givenDate, $startDate, $endDate)
    {
        $givenDate = strtotime($givenDate);
        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        if (($startDate == "" || $startDate <= $givenDate) && ($endDate == "" || $endDate >= $givenDate))
            return true;
        else
            return false;
    }

    private function prepareRequest($type, $data, $title)
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setUri($this->chartsGeneratorUrl);
        $request->getHeaders()->addHeaders(array(
            'content-type' => 'application/x-www-form-urlencoded',
        ));
        $request->getPost()->set('type', $type);
        $request->getPost()->set('data', $data);
        $request->getPost()->set('title', $title);

        return $request;
    }

    private function getChartUrl($type, $data, $title) 
    {
        $newRequest = $this->prepareRequest($type, $data, $title);
        $client = new Client();
        $response = $client->dispatch($newRequest);

        $url = "";
        if ($response->isSuccess()) {
            $url = \Zend\Json\Json::decode($response->getBody(), \Zend\Json\Json::TYPE_ARRAY); 
        }

        return $url;
    }

    private function checkAccess() 
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login');
        }
    }
}

