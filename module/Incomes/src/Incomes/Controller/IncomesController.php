<?php

namespace Incomes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IncomesController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }


}

