<?php

class Contact_AdminController extends Zend_Controller_Action
{

    public function init(){
	    $this->_helper->layout->setLayout('admin-layout');
	}

    public function indexAction()
    {
        $this->_forward('submission-list');    
    }
    
    public function submissionListAction(){
        $orderBy = 'cr.'.$this->_getParam('orderby', 'last_name');
        $this->view->contactRequests = Doctrine_Query::create()
            ->from('Contact_Model_ContactRequest cr')
            ->orderBy($orderBy)
            ->execute()
            ;
    }
    
    public function deleteAction(){
        $id = $this->_getParam('id');
        $submission = Doctrine_Query::create()
            ->from('Contact_Model_ContactRequest')
            ->where('id=?',$id)
            ->fetchOne();
        !empty($submission) && $submission->delete();
        $this->_helper->redirector('index');
    }

}

