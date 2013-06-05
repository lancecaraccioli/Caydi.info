<?php
class User_Form_User extends Zend_Form
{
	public function init() {

        $id = new Zend_Form_Element_Hidden('id');
        $first_name	        = new Zend_Form_Element_Text('first_name');
        $last_name	        = new Zend_Form_Element_Text('last_name');
        $email	            = new Zend_Form_Element_Text('email');
        $confirm_email      = new Zend_Form_Element_Text('confirm_email');
        $password           = new Zend_Form_Element_Password('password');
        $confirm_password   = new Zend_Form_Element_Password('confirm_password');
        $is_admin           = new Zend_Form_Element_Checkbox('is_admin');
        $submit         = new Zend_Form_Element_Submit('submit');

        $first_name	->setLabel('First Name')
                ->addValidator(new Zend_Validate_Alnum(true))
                ->setDescription('The users first name.')
                ->setRequired(true);
                
        $last_name	->setLabel('Last Name')
                ->addValidator(new Zend_Validate_Alnum(true))
                ->setDescription('The users last name.')
                ->setRequired(true);

        $email	->setLabel('Email')
                ->addValidator(new Zend_Validate_EmailAddress())
                ->setDescription('The users email address.')
                ->setRequired(true);
                
        $confirm_email	->setLabel('Email (confirm)')
                ->addValidator(new Zend_Validate_EmailAddress())
                ->addValidator(new Custom_Validate_Match(array('email')))
                ->setDescription('The users email address.')
                ->setRequired(true);
                
        $password	->setLabel('Password')
                ->addValidator(new Zend_Validate_Alnum(false))
                ->setDescription('The users password.')
                ->setRequired(true);
                
        $confirm_password	->setLabel('Password (confirm)')
                ->addValidator(new Zend_Validate_Alnum(false))
                ->addValidator(new Custom_Validate_Match(array('password')))
                ->setDescription('The users password.')
                ->setRequired(true);

        $is_admin->setLabel('CMS Administrator')
                ->setCheckedValue('1')
                ->setUncheckedValue('0');
                    
        $submit->setLabel('Save')
                ->setDescription('Click here to save your changes.');

        $this->addElements(array(
        	$id,
        	$first_name,
        	$last_name,
            $email,
            $confirm_email,
            $password,
            $confirm_password,
            $is_admin,
            $submit,
        ));
	}
}
