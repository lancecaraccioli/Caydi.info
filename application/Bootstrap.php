<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function run()
    {
        $this->bootstrap('frontController');
        $this->frontController->dispatch();
    }
    
    protected function _initDebug($showMe = 0){

        if ($showMe){
            $debugInfo = $environment_config = $this->getApplication()->getOptions();
            Zend_Debug::dump($debugInfo);
            die();        
        }
    }
    
    protected function _initAutoLoader(){
        $this->getApplication()
            ->getAutoLoader()
            ->registerNamespace('Custom')
            ->registerNamespace('Doctrine')
            ->registerNamespace('Site')
            ;
    }

    protected function _initLayout(){
        $environment_config = $this->getApplication()->getOptions();
        Zend_Layout::startMvc($environment_config['layout']);
    }
    
    protected function _initView(){
        $view = Zend_Layout::getMvcInstance()->getView();
        $view->doctype('XHTML1_STRICT');
        $view->addHelperPath('Custom/View/Helper/', 'Custom_View_Helper');
    }
    
    protected function _initDb(){
        // We will be using Doctrine as the database interface
        // DATABASE ADAPTER - Setup the database adapter
        $environment_config = $this->getApplication()->getOptions();
        $db = $environment_config['database'];
        $dsn = $db['type'] . '://' . $db['username'] . ':' . $db['password'] . '@' . $db['host'] . '/' . $db['dbname'];
        $manager=Doctrine_Manager::connection($dsn);
        $manager->setAttribute(Doctrine::ATTR_USE_DQL_CALLBACKS, true);
    }
    
    protected function _initEmailTransport(){
        $tr = new Zend_Mail_Transport_Smtp('relay-hosting.secureserver.net');
        Zend_Mail::setDefaultTransport($tr);
    }
    
    protected function _initFrontControllerPlugins(){
        Zend_Controller_Front::getInstance()->registerPlugin(new Site_Controller_Plugin_AclCheck());
        
    }
    
    protected function _initRegistry(){
        $registry = Zend_Registry::getInstance();
        $registry->set('PUBLIC_PATH', realpath(dirname(__FILE__).'/../public'));
        $registry->set('USER_MEDIA_PATH', realpath(dirname(__FILE__).'/../public/user'));
        $registry->set('USER_MEDIA_URL', '/user');
        $registry->set('acl', new Site_Acl);

    }
    
    protected function _initSession(){
        isset($_REQUEST['s_ID']) && session_id($_REQUEST['s_ID']);
        Zend_Session::start();
    }
    

}

