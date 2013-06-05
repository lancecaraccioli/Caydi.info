<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class User_Model_Base_User extends Doctrine_Record
{
  public function setTableDefinition()
  {
    $this->setTableName('user');
    $this->hasColumn('id', 'integer', 4, array('type' => 'integer', 'length' => 4, 'primary' => true, 'autoincrement' => true));
    $this->hasColumn('first_name', 'string', 255, array('type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true));
    $this->hasColumn('last_name', 'string', 255, array('type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true));
    $this->hasColumn('email', 'string', 255, array('type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true));
    $this->hasColumn('password', 'string', 255, array('type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true));
    $this->hasColumn('password_plaintext', 'string', 255, array('type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true));
    $this->hasColumn('is_admin', 'integer', 1, array('type' => 'integer', 'length' => 1, 'default' => 0));
    $this->hasColumn('created', 'timestamp', null, array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00', 'notnull' => true));
    $this->hasColumn('updated', 'timestamp', null, array('type' => 'timestamp', 'default' => '0000-00-00 00:00:00', 'notnull' => true));
  }

  public function setUp(){
	    $timestampable0 = new Doctrine_Template_Timestampable(array('created' => array('name' => 'created'), 'updated' => array('name' => 'updated')));
	    $this->actAs($timestampable0);
	    $this->actAs('SoftDelete');
	}

}