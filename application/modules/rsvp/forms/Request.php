<?php
class Rsvp_Form_Request extends Custom_Form_Front
{

	public function init() {
		parent::init();

        $first_name		= new Zend_Form_Element_Text('first_name');
        $last_name		= new Zend_Form_Element_Text('last_name');
    	$email			= new Zend_Form_Element_Text('email');
    	$confirm_email	= new Zend_Form_Element_Text('confirm_email');
		$number_of_people	= new Zend_Form_Element_Select('number_of_people');
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
    	$email		->setLabel('Email')
                    ->addValidator('EmailAddress')
                    ->setDescription('An email address that you can be reached.')
                    ->setRequired(true);
		$numberOptions=array(''=>'choose...');
		for($i=1;$i<11;$i++){
			$numberOptions[$i]=$i;
		}
		
    	$number_of_people->setLabel('How many people are in your party (including you)?')
                    ->setDescription('How many people including rsvp-er.')
                    ->setRequired(true)
					->setMultiOptions($numberOptions);
    	$confirm_email
    				->setLabel('Email (Repeat)')
                    ->addValidator(new Custom_Validate_Match(array('email')))
                    ->setDescription('Please repeate your email address here.')
                    ->setRequired(true);
        
    	$comments	->setLabel('Comments/Questions')
                    ->setDescription('Go ahead tell us how pumped you are to be coming to Caydi\'s party.');

		$submit
			->setLabel('Send')
			;
			

        
        
		
        $this->addElements(array(
        	$first_name,
        	$last_name,
        	$email,
        	$confirm_email,
			$number_of_people,
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
