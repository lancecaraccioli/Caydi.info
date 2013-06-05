<?php

class User_UserController extends Zend_Controller_Action
{
    public static $_resultsPerPage = 25;

    public function init(){
        $this->request = $this->getRequest();
    }
    
	public function indexAction(){
		$this->_forward('login');
	}

    public function listAction(){
        $orderby = $this->getRequest()->getParam('orderby');
        $orderby = empty($orderby)?'u.last_name':$orderby;
        $currentPage = is_numeric($this->getRequest()->getParam('page'))?$this->getRequest()->getParam('page'):1;
        $resultsPerPage = self::$_resultsPerPage;
        $pager = new Doctrine_Pager(
            Doctrine_Query::create()
                ->from( 'User_Model_User u' )
                ->orderby($orderby),
            $currentPage,
            $resultsPerPage
        );
        //Zend_Debug::dump($pager->execute()->toArray());

        $this->view->users = $pager->execute();
        $this->view->currentPage = $currentPage;
        $this->view->totalPages = ceil($pager->getNumResults() / $resultsPerPage);
    }

    public function editAction(){
        $this->_forward('form');
    }
    public function createAction(){
        $this->_forward('form');
    }

    public function formAction(){
//        include ('forms/UserForm.php');
        $id=$this->getRequest()->getParam('id');
        $form = new User_Form_User();
        $user = Doctrine_Query::create()
                ->from('User_Model_User')
                ->where('id = ?', $id)
                ->fetchOne();
        if ($this->getRequest()->isPost() && $form->isValid($_POST)){
            $values = $form->getValues();

            $user = empty($user)?new User_Model_User:$user;
            $user->merge($values);
            $user->password_plaintext = $user->password;
            $user->password = md5($user->password);
            $user->save();

            $this->_helper->redirector('list');
        } else if (!empty($user)){
            if ($_POST) {
                $form->setDefaults($_POST);
            } else {
                $form->setDefaults($user->toArray());
            }
		}
        $this->view->form = $form;
    }

    public function deleteAction(){
        $id=$this->getRequest()->getParam('id');
        $user = Doctrine_Query::create()
            ->from('User_Model_User u')
            ->where ('u.id = ?', $id)
            ->execute();
        $user->delete();
        $this->_helper->redirector('list');
    }
    
	public function loginAction()
	{
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$this->view->identity = $auth->getIdentity();
		} else {
            $redirect_to = $this->getRequest()->getParam('redirect-to');

            if (isset($redirect_to)) {
                $_SESSION['LOGIN']['redirect'] = urldecode(urldecode($redirect_to));
            } else {
            	$_SESSION['LOGIN']['redirect'] = '/user/user/login';
            }

			$this->view->form=$form=$this->_getLoginForm();
			if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {//authentication occurs in the form validation
                if (isset($_SESSION['LOGIN']['redirect'])) {
                    $redirect_to = $_SESSION['LOGIN']['redirect'];
                    unset($_SESSION['LOGIN']['redirect']);
                    $this->_helper->redirector->gotoUrl($redirect_to);
                }
			}
		}

        $this->view->title = 'Member Login';
	}

	public function logoutAction(){
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$auth->clearIdentity();
		}
		$this->_helper->redirector('login');
	}

	public function registerAction(){
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$this->view->identity = $auth->getIdentity();
		} else {
			$this->view->form=$form=$this->_getRegisterForm();
			if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {//a check to see if this user exist occurs in the form validation
				//get the form data values
				$data = $form->getValues();
				//create the new user
				$user=new Users();
				$user->email = $data['email'];
				$user->password_plaintext=$data['password'];
				$user->password = md5($data['password']);
				$user->save();
				//login the new user
				$auth = Zend_Auth::getInstance();
				// Set up the authentication adapter
				$authAdapter = new Custom_Auth_Adapter_Doctrine($user->email, $user->password_plaintext, array('username'=>'email'));
				// Attempt authentication, saving the result
				$result = $auth->authenticate($authAdapter);
				if (!$result->isValid()) {
				    //you just created this user so if validation doesn't happen then there is serious problem with the system at this point
				    //throw an exception?
					$this->error_messages=$result->getMessages(); // the messages associated with authentication failure
				} else {
					$this->view->identity = $auth->getIdentity();
				}
			}
		}

        $this->view->title = 'Member Registration';
	}

    public function passwordReminderAction(){
        $this->view->form=$form=$this->_getPasswordReminderForm();
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {//a check to see if this user exist occurs in the form validation
            //get the form data values
            $data = $form->getValues();
            //see if a user exists
            $user= Doctrine_Query::create()
                ->from('User_Model_User')
                ->where('email = ?', $data['mail'])
                ->fetchOne();
            //iff the user exists
            if (!empty($user)){
                //send and email to the user
                $host = $this->request->getHttpHost();
                $mail = new Zend_Mail();
                $mail->setFrom('no-reply@'.$host, $host);
                $mail->addTo($user->email);
                $mail->setSubject('Your password reminder from ('. $host .')');
                $txtBody = "Your Password: ".$user->password_plaintext;
                $htmlBody = "<strong>Your Password:</strong> <span>".$user->password_plaintext."</span>";
                $mail->setBodyText($txtBody);
                $mail->setBodyHtml($htmlBody);
                $mail->send();
                $this->_forward('password-reminder-success');
            }
        }

        $this->view->title = 'Password Reminder';
    }

    public function passwordReminderSuccessAction(){}

    protected function _getPasswordReminderForm(){
		$form = new User_Form_PasswordReminder();
		return $form;
	}

	protected function _getLoginForm(){
		$form = new User_Form_Login();
		return $form;
	}

	protected function _getRegisterForm(){
		$form = new User_Form_Register();
		return $form;
	}
}
