<?php

class User_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected function _initTest(){
        /*set_include_path(get_include_path().':'
            .dirname(__FILE__).'/models/generated:'
            .dirname(__FILE__).'/models');
        Zend_Debug::dump(get_include_path());*/
    }

}


