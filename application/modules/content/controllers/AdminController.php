<?php

class Content_AdminController extends Zend_Controller_Action
{
	public function init(){
	    $this->_helper->layout->setLayout('admin-layout');
	}
	
    public function indexAction()
    {
    	$this->view->contentPages = $contentPages = Doctrine_Query::create()
    		->from('Content_Model_Content c')
	    	->orderBy('c.title')
	    	->execute();
	}
	
	public function listAction(){
	    $this->_forward('index');
	}
	
    public function formAction()
    {
        $request = $this->getRequest();
        $form    = $this->_getForm();
        // check to see if this action has been POST'ed to
        if ($this->getRequest()->isPost()) {

            // now check to see if the form submitted exists, and
            // if the values passed in are valid for this form
            if ($form->isValid($request->getPost())) {
            	
                // since we now know the form validated, we can now
                // start integrating that data submitted via the form
                // into our model
                $values = $form->getValues();
                $Content = $values['Content']['id']? Doctrine::getTable('Content_Model_Content')->find($values['Content']['id']) : new Content_Model_Content();
				$Content->fromArray($values['Content']);
				$Content->save();
				
				//
                // now that we have saved our model, lets url redirect
                // to a new location
                // this is also considered a "redirect after post"
                // @see http://en.wikipedia.org/wiki/Post/Redirect/Get
                return $this->_helper->redirector('index');
            }
        } elseif(!empty($request->id)){
        	$Content = Doctrine::getTable('Content_Model_Content')->find($request->id);
        	$this->view->Content = $Content;
		   	$form->setDefaults(array('Content' => $Content->toArray()));
        }

        $this->view->form = $form;
    }
	
    public function createAction(){
    	$this->view->placeholder('title')->set('Create Content');
    	$this->_forward('form');
    }

    public function editAction(){
    	$this->view->placeholder('title')->set('Edit Content');
    	$this->_forward('form');
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $Content = Doctrine::getTable('Content_Model_Content')->find($request->id);
        if(!empty($Content)) {
            $Content->delete();
        }
        return $this->_helper->redirector('index');
    }
    protected function _getForm()
    {
        $form = new Content_Form_CreateUpdate();
        return $form;
    }
}
