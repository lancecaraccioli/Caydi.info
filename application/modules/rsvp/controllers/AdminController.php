<?php

class Rsvp_AdminController extends Zend_Controller_Action
{

    public function init(){
	    $this->_helper->layout->setLayout('admin-layout');
	}

    public function indexAction()
    {
        $this->_forward('submission-list');    
    }
    
    public function submissionListAction(){
        $orderBy = 'r.'.$this->_getParam('orderby', 'last_name');
        $this->view->rsvps = Doctrine_Query::create()
            ->from('Rsvp_Model_Rsvp r')
            ->orderBy($orderBy)
            ->execute()
            ;
    }
    
    public function deleteAction(){
        $id = $this->_getParam('id');
        $submission = Doctrine_Query::create()
            ->from('Rsvp_Model_Rsvp')
            ->where('id=?',$id)
            ->fetchOne();
        !empty($submission) && $submission->delete();
        $this->_helper->redirector('index');
    }

}

