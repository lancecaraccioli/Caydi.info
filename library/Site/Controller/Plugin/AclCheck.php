<?php
//USAGE:  this acl checker is for chris sirney's site only
class Site_Controller_Plugin_AclCheck extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch()
    {
    	$request = $this->getRequest();
    	//Custom_Debug::dumpDie($request->getParams());
    	$module = $request->getParam('module');
		$controller = $request->getParam('controller');
		$action = $request->getParam('action');
	    $auth = Zend_Auth::getInstance();
	    $acl  = new Site_Acl;
	    $role = $auth->hasIdentity()?$auth->getIdentity():'guest';
	    $role = $acl->hasRole($role)?$role:'guest';
	    $resource = $module.'_'.$controller;
	    //Custom_Debug::dumpDie($resource);
    	$resource = $acl->has($resource)?$resource:'restricted';
	    $privilege = $action;
	    //var_dump(array($role,$resource,$privilege));die();
    	if (!$acl->isAllowed($role, $resource, $privilege)){
    		
    		$flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger'); 
    		$flashMessenger->clearMessages();
    		$flashMessenger->addMessage("'$role' does not have sufficient privileges to perform the requested action '$action' on the requested resource '$module _ $controller'.");
    		$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector'); 
    		$redirector ->gotoSimple('login','user','user');
    	}	}
}

