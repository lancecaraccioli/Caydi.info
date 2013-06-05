<?php

class Contact_RequestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_forward('form');
    }
    
    public function formAction(){
		$this->view->form=$form=$this->_getForm();
		if ($this->getRequest()->isPost()){
			if($form->isValid($this->getRequest()->getPost())) {
				//get the form data values
				$data = $form->getValues();
				//Custom_Debug::dumpDie($data);
				//create the new contact request record
				$contactRequest = new Contact_Model_ContactRequest();
				$contactRequest->merge($data);
				$contactRequest->save();
				//redirect after post
				$this->_helper->redirector->gotoSimple('success');
			}
		} 
		
	}
	
	public function successAction(){
	    $this->_helper->redirector->gotoUrl('/content/display/show/slug/contact-request-accepted');
	}
	
	private function _getForm(){
	    return new Contact_Form_Request();
	}


}

