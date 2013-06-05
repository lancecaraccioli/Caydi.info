<?php

class UserRegisterForm extends Custom_Form_Front
{
	public function init() {
        parent::init();

		parent::init();
        $this->submit->setLabel('Register');

        $mail = new Zend_Form_Element_Text('email');
        $mail->setLabel('E-Mail');
        $mail->setDescription('Your email address that you would like associated to this account.');
        $mail->setRequired(true);
        $mail->addValidator('EmailAddress', true);
        $mail->addValidator(new Custom_Validate_Auth_Exists(array('username'=>'email')));

        $mailRepeat = new Zend_Form_Element_Text('mailRepeat');
        $mailRepeat->setLabel('E-Mail (repeat)');
        $mailRepeat->setDescription('Please confirm your email address');
        $mailRepeat->setRequired(true);
        $mailRepeat->addValidator('EmailAddress', true);
        $mailRepeat->addValidator(new Custom_Validate_Match(array('email')));

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password');
        $password->setDescription('Your password. Must be between 8 and 20 characters.');
        $password->addErrorMessage('Your password. Must be between 8 and 20 characters.');
        $password->setRequired(true);
        $password->addValidator('StringLength', true, array(8, 20));

        $this->password->removeDecorator('HtmlTag');
        $this->password->addDecorator('Description');
        $this->password->addDecorator(array('li'=>'HtmlTag'), array('tag'=>'li'));

        $passwordRepeat = new Zend_Form_Element_Password('passwordRepeat');
        $passwordRepeat->setLabel('Password (repeat)');
        $passwordRepeat->setDescription('Please confirm your password');
        $passwordRepeat->addErrorMessage('Your password confirmation did not match your password.  Please enter your password again.');
        $passwordRepeat->setRequired(true);
        $passwordRepeat->addValidator(new Custom_Validate_Match(array('password')));

        $this->addElements(array($mail, $mailRepeat, $password, $passwordRepeat));
     }

}
