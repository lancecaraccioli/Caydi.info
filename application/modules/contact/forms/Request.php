<?php
class Contact_Form_Request extends Custom_Form_Front
{

	public function init() {
		parent::init();

        $first_name		= new Zend_Form_Element_Text('first_name');
        $last_name		= new Zend_Form_Element_Text('last_name');
        /*$address_1		= new Zend_Form_Element_Text('address_line_1');
        $address_2		= new Zend_Form_Element_Text('address_line_2');
        $city			= new Zend_Form_Element_Text('city');
        $state			= new Custom_Form_Element_USAState('state');
        $zip			= new Zend_Form_Element_Text('zip', array('maxlength'=>5));
        $country		= new Custom_Form_Element_Country('country');*/
    	$phone			= new Custom_Form_Element_Phone('phone');
    	$email			= new Zend_Form_Element_Text('email');
    	$confirm_email	= new Zend_Form_Element_Text('confirm_email');
    	$comments		= new Zend_Form_Element_Textarea('comments');
    	
    	$submit 		= new Zend_Form_Element_Submit('submit',array('class'=>'png'));

        $first_name	->setLabel('First Name')
                    ->addValidator('Alpha')
                    ->setDescription('Your first name.')
                    ->setRequired(true);
        $last_name	->setLabel('Last Name')
                    ->addValidator('Alpha')
                    ->setDescription('Your last name.')
                    ->setRequired(true);
		/*$address_1	->setLabel('Address')
                    ->addValidator(new Zend_Validate_Regex('/^[a-zA-Z0-9. ]+$/i'))
                    ->setDescription('The first line of your street address.');
        $address_2	->setLabel('Address (cont.)')
                    ->addValidator(new Zend_Validate_Regex('/^[a-zA-Z0-9. ]+$/i'))
                    ->setDescription('The second line of your street address.');
        $city		->setLabel('City')
                    ->addValidator(new Zend_Validate_Alpha($allowWhiteSpace = true))
                    ->setDescription('Your city.');
        $state		->setLabel('State')
                    ->setDescription('Your state.');
        $zip		->setLabel('Zip')
                    ->addValidator('Digits')
                    ->addValidator(new Zend_Validate_StringLength(5,5))
                    ->setDescription('Your zip code.');
        $country	->setLabel('Country')
                    ->setDescription('Your country.');*/
    	$phone		->setLabel('Phone')
                    ->setDescription('A phone number that you can be reached.')
	                ->setRequired(false);
    	$email		->setLabel('Email')
                    ->addValidator('EmailAddress')
                    ->setDescription('An email address that you can be reached.')
                    ->setRequired(true);
    	$confirm_email
    				->setLabel('Email (Repeat)')
                    ->addValidator(new Custom_Validate_Match(array('email')))
                    ->setDescription('Please repeate your email address here.')
                    ->setRequired(true);
        
    	$comments	->setLabel('Comments/Questions')
                    ->setDescription('Please include some comments.');

		$submit
			->setLabel('Send')
			;
			

        
        
		
        $this->addElements(array(
        	$first_name,
        	$last_name,
        	/*$address_1,
        	$address_2,
        	$city,
        	$state,
        	$zip,
        	$country,*/
        	$phone,
        	$email,
        	$confirm_email,
        	$comments,
        	$submit
        ));
	}
	
	public function loadDefaultDecorators(){
		parent::loadDefaultDecorators();
		$this->submit->clearDecorators();
        $this->submit->addDecorator('ViewHelper');
        $this->submit->addDecorator(array('div'=>'HtmlTag'), array('tag'=>'div', 'class'=>'button-wrapper'));
		$this->submit->addDecorator(array('li'=>'HtmlTag'), array('tag'=>'li'));   

	}

}
