<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        return $this->_helper->redirector('index','party');
    }

    public function indexAction()
    {
        // action body
    }


}

