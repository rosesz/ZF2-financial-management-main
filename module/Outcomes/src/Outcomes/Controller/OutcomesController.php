<?php

namespace Outcomes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Outcomes\Model\Outcomes;
use Outcomes\Form\OutcomesForm;
use Zend\Http\Client;
use Zend\Http\Request;

class OutcomesController extends AbstractActionController
{
    protected $outcomesTable;
    protected $chartsGeneratorUrl = "http://localhost:8080/chart";

    public function indexAction()
    {   
        return new ViewModel(array(
            'outcomes' => $this->getOutcomesTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
        $form = new OutcomesForm($this->categoriesList());
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $outcomes = new Outcomes();
            $form->setInputFilter($outcomes->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $outcomes->exchangeArray($form->getData());
                $this->getOutcomesTable()->saveOutcomes($outcomes);

                // Redirect to list of outcomes
                return $this->redirect()->toRoute('outcomes');
            }
        }
        return array('form' => $form);

    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('outcomes', array(
                'action' => 'add'
            ));
        }

        // Get the Outcome with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $outcomes = $this->getOutcomesTable()->getOutcomes($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('outcomes', array(
                'action' => 'index'
            ));
        }

        $form  = new OutcomesForm($this->categoriesList());
        $form->bind($outcomes);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($outcomes->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getOutcomesTable()->saveOutcomes($outcomes);

                // Redirect to list of outcomes
                return $this->redirect()->toRoute('outcomes');
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
            return $this->redirect()->toRoute('outcomes');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getOutcomesTable()->deleteOutcomes($id);
            }

            // Redirect to list of outcomes
            return $this->redirect()->toRoute('outcomes');
        }

        return array(
            'id'    => $id,
            'outcomes' => $this->getOutcomesTable()->getOutcomes($id)
        );
    }

    public function chartsAction()
    {
        return new ViewModel();
    }

    public function generateChartAction()
    {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $startDate = $request->getPost('startDate');
            $endDate = $request->getPost('endDate');
            $type = $request->getPost('type');

            if ($type == "categories") {
                $data = $this->sumByCategories($startDate, $endDate);
                $title = "Wydatki według kategorii";
            }
            elseif ($type == "days") {
                $data = $this->sumByDays($startDate, $endDate);
                $title = "Wydatki według dni";
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
        $outcomes = $this->getOutcomesTable()->fetchAll();
        $cat = array();
        foreach ($outcomes as $row) {
            $cat[$row->category] = $row->category;
        }
        $categories = array_values($cat); 

        return \Zend\Json\Json::encode($categories, true);      
    }

    public function sumByCategories($startDate = "", $endDate = "")
    {
        $outcomes = $this->getOutcomesTable()->fetchAll();
        $sum = array();
        foreach ($outcomes as $row) {
            if ($this->isInDataRange($row->date, $startDate, $endDate))
                $sum[$row->category] += $row->amount;
        }

        return \Zend\Json\Json::encode($sum, true);    
    }

    public function sumByDays($startDate = "", $endDate = "")
    {
        $outcomes = $this->getOutcomesTable()->fetchAll();
        $sum = array();
        foreach ($outcomes as $row) {
            if ($this->isInDataRange($row->date, $startDate, $endDate))
                $sum[$row->date] += $row->amount;
        }

        return \Zend\Json\Json::encode($sum, true);    
    }

    public function getOutcomesTable()
    {
        if (!$this->outcomesTable) {
            $sm = $this->getServiceLocator();
            $this->outcomesTable = $sm->get('Outcomes\Model\OutcomesTable');
        }
        return $this->outcomesTable;
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
}

