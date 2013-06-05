<?php
class User_Form_Login extends Custom_Form_Front
{

	public function init() {
		parent::init();
		$submit	= new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Login');
		
        $mail = new Zend_Form_Element_Text('mail');
        $mail->setLabel('E-Mail');
        $mail->setDescription('Your email address');
        $mail->setRequired(true);
        $mail->addValidator('EmailAddress', true);

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password');
        $password->setDescription('Your password. Must be between 4 and 20 characters.');
        $password->setRequired(true);
        $password->addValidator('StringLength', true, array(4, 20));


		$hidden = new Zend_Form_Element_Hidden('authenticate');
		$hidden->setValue('1');//if there is no post value is won't try to validate this field for some reason
		$hidden->setRequired(true);
		$hidden->addValidator(new Custom_Validate_Auth(array('username'=>'mail')));

        $this->addElements(array($mail, $password, $hidden, $submit));

	}
	
	public function loadDefaultDecorators(){
		parent::loadDefaultDecorators();

		$this->submit->clearDecorators();
        $this->submit->addDecorator('ViewHelper');
        $this->submit->addDecorator(array('div'=>'HtmlTag'), array('tag'=>'div', 'class'=>'button-wrapper'));
		$this->submit->addDecorator(array('li'=>'HtmlTag'), array('tag'=>'li'));   	
	}

}
