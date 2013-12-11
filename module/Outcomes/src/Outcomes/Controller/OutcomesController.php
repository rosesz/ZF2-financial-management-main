<?php

namespace Outcomes\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class OutcomesController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }


}

