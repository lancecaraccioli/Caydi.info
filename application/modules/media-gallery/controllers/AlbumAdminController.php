<?php

class MediaGallery_AlbumAdminController extends Zend_Controller_Action
{
    public function indexAction()
    {
		$this->_forward('list');
	}
	public function editAction(){
		$this->_forward('form');
	}
	public function createAction(){
		$this->_forward('form');
	}
    public function formAction(){
        $id=$this->getRequest()->getParam('id');
        $mediaAlbum = !empty($id)?Doctrine_Query::create()->from('MediaGallery_Model_Album')->where('id = ?', $id)->fetchOne():new MediaGallery_Model_Album;
        $form = new MediaGallery_Form_Album;
        $form->setPhotoOptions($id);
        if ($this->getRequest()->isPost()){
        	if ($form->isValid($_POST)){ //post params get set as form defaults here
	            $values = $form->getValues();
	            $mediaAlbum->merge($values);
	            $mediaAlbum->save();
	            $this->_helper->redirector->gotoRouteAndExit(array('action'=>'edit', 'id'=>$mediaAlbum->id));
			}
        } else if (!empty($id)){//if no post but id is requested then we are updateing a record so set the form defaults to the current records values
			$form->setDefaults($mediaAlbum->toArray());
		}
		$this->view->mediaAlbum=$mediaAlbum;
        $this->view->form = $form;
    }
	
	public function listAction()
    {
		if ($id = $this->getRequest()->getParam('id')){//a record was updated
			$this->view->last_updated=$id;
		}
		
		$this->view->media_albums = Doctrine_Query::create()->from('MediaGallery_Model_Album')->orderby('name')->execute();
    }
    
    public function uploadAction(){
    	$id=$this->getRequest()->getParam('id');
    	$media = new Media;
    	Custom_Debug::dumpDie($id);
		$fileName = $_FILES['Filedata']['name'];
		$tmpfilePath = $_FILES['Filedata']['tmp_name'];
		$newFilePath= Zend_Registry::get('USER_MEDIA_PATH').'media/original/temp/'.session_id().$fileName;
		//debugging
			/*$debugData = array(
				'tmpfilePath'=>$tmpfilePath,
				'fileName'=>$fileName,
				'newFilePath'=>$newFilePath,
				'toInit'=>true,
				'REQUEST'=>$this->getRequest()->getParams(),
				'$_FILES'=>$_FILES,
			);
			$mail = new Zend_Mail;
			$mail->setFrom ('debugging@kfx2.com', 'Debugging');
			$mail->addTo('lance@kfx2.com');
			$mail->setSubject ('Flash Upload');
			$mail->setBodyHTML(print_r($debugData,1));
			$mail->send();*/
		if (is_file($tmpfilePath)){
			//TODO: check that the file upload actually works
			if (move_uploaded_file($tmpfilePath, $newFilePath)) die('success');
		}
		//should not reach the following die if there is a valid file and it gets moved
		die('failure');
	}
	
	public function deleteAction()
    {
		if ($id = $this->getRequest()->getParam('id')){//record id to delete
			$query = Doctrine_Query::create();
			$mediaAlbum = $query->from('MediaGallery_Model_Album')->where('id = ?', $id)->fetchOne();
			$mediaAlbum->delete();
    	}
    	$this->_forward('list');
    }
	
	public function flickrAction()
	{
		// to do:
		//1.  creat form
		//2. check to see if there was a post and make sure its valid
		//3. if form valid ...........
		//	a.  get values from form
		//	b.  make flickr request, if valid ....
		//		i. process flickr results
		//			A.  for each result as photo store in DB
		//	c. if not valid.....
		//		i. error message
		//	
		//4. if form is not valed ................
		
	}
} 
