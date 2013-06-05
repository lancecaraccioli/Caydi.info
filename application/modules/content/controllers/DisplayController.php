<?php

class Content_DisplayController extends Zend_Controller_Action
{
	public function init(){
	}
	
    public function indexAction()
    {
        $this->_forward('show');
	}
	
    public function showAction(){
    	$content = Doctrine_Query::create()->from('Content_Model_Content')->where('slug = ?', $this->getRequest()->slug)->fetchOne();
		if (empty($content)) {
			$content = Doctrine_Query::create()->from('Content_Model_Content')->where('slug = ?', 'home')->fetchOne();
		}
		$this->view->content = $content;
    }
}
