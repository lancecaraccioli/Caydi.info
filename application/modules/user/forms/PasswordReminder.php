<?php
class User_Form_PasswordReminder extends Custom_Form_Front
{

	public function init() {
        $submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Send me a password reminder');
		
        $mail = new Zend_Form_Element_Text('mail');
        $mail->setLabel('E-Mail');
        $mail->setDescription('The email address of the account for which you would like to have a password reminder.');
        $mail->setRequired(true);
        $mail->addValidator('EmailAddress', true);
        $mail->addValidator(new Custom_Validate_Auth_DoesNotExists(array('username'=>'mail')));

        $this->addElements(array($mail, $submit));

	}

}
