<?php

class Rsvp_RequestController extends Zend_Controller_Action
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
				
				//create the new rsvp record
				$rsvp = new Rsvp_Model_Rsvp;
				$rsvp->merge($data);
				$rsvp->save();
				try{
					$rsvp->sendAlert();
					$this->_sendTotals();
				} catch(Exception $e){
					//swallow errors
				}
				//redirect after post
				$this->_helper->redirector->gotoSimple('success');
			}
		} 
		
	}
	
	public function successAction(){
	    
	}
	
	private function _getForm(){
	    return new Rsvp_Form_Request();
	}

	public function testEmailAction(){
		$rsvp = new Rsvp_Model_Rsvp;
		$rsvp->first_name = 'Test First Name';
		$rsvp->first_name = 'Test Last Name';
		$rsvp->email = 'example@example.com';
		$rsvp->sendAlert();
		die('check email');
	}
	
	private function _sendTotals(){
		//send total rsvps
		$rsvps = Doctrine_Query::create()
			->from('Rsvp_Model_Rsvp')
			->execute();
		$txtBody=$this->view->partial('request/totals-email.phtml',array('rsvps'=>$rsvps));
		Custom_Debug::dumpDie($txtBody);
		$host = 'caydi.info';
		$mail = new Zend_Mail();
		$mail->setFrom('noreply@'.$host, $host);
		$to[] = $this->email;
		$to[] = 'ncaraccioli@cox.net';
		$to[] = 'caraccioli@cox.net';
		foreach($to as $recipient) {
			$mail->addTo($recipient);
		}
		$subject = 'Rsvp Received';
		$mail->setSubject($subject);
		
        $mail->setBodyText($txtBody);
        $mail->send();
    }

}

