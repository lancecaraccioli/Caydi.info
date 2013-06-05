<?php

class MediaGallery_MediaAdminController extends Zend_Controller_Action
{
	public $media;
	public $form;
	
	public function init(){
		$this->_helper->layout->setLayout('admin-layout');
	}
	
    public function indexAction()
    {
		$this->_redirect('/media-gallery/album-admin/list');
	}
	public function editAction(){
		$this->_forward('form');
	}
	public function createAction(){
		$this->_forward('form');
	}
    public function testUploadScriptFormAction(){
        //just render form
    }
    public function formAction(){
        $this->form = new MediaGallery_Form_Media();
        //$this->form->setAction('/media-gallery/media-admin/form');//isn't the default the same action it's rendered in?
        $id = $this->getRequest()->getParam('id');
        $this->media = $id?Doctrine_Query::create()->from('MediaGallery_Model_Media')->where('id = ?', $id)->fetchOne():new MediaGallery_Model_Media;
        if (!$id){
        	$this->form->removeElement('thumbnail');
        } elseif (!$this->media->type){
        	$this->form->removeElement('thumbnail');//we don't know the media type yet, so let's wait for a media file to be uploaded before giving option to upload custom thumbnail
        }
        if ($this->getRequest()->isPost()){//a form has been submitted... (sets form defaults to Post Values)
        	if($this->form->isValid($_POST)){//if the form validates
	            $values = $this->form->getValues();
	            $this->media->merge($values);
	            $this->media->save();//save the normal form data before processing the uploades to ensure that the data is preserved in event of an error
	            //this gets called with JS is disabled and the file is uploaded through a normal file upload element
	            /*$viewRendered = 'unset';
				$viewRendered = $this->view->action('attach-upload-to-media','media-upload', array('fileKey'=>'media'));
				Custom_Debug::dumpDie($viewRendered);*/
	            if (!empty($this->form->thumbnail) && $this->form->thumbnail->isUploaded() && $this->form->thumbnail->receive()){
	            	$tmpFileName = $this->form->thumbnail->getFileName();
	            	//Custom_Debug::dumpDie($tmpFileName);
            		$width=100;
            		$height=125;

					$uniqIdentifier=md5($this->media->name.date('Y-m-d H:i:s'));
			        /**wideImage magic - consider branching wide image to rename classes based on PEAR and Zend autoloading standards**/
			        require_once ('WideImage/lib/WideImage.inc.php');
			        // image paths
			        $newRelFileName = "{$uniqIdentifier}.jpg";
			        $newAbsFileName = Zend_Registry::get('MEDIA_GALLERY_THUMB_PATH') . "/$newRelFileName";
			        $wi = wiImage::load($tmpFileName)
			                ->resize($width, $height, 'outside')
			                ->crop(0,0,$width,$height)
			                ->saveToFile($newAbsFileName);
			        unlink($tmpFileName);
			         /* end wideImage */
			        //delete old files
			        $deletable_file=Zend_Registry::get('MEDIA_GALLERY_THUMB_PATH')."/".$this->media->thumbnail_file;
			        if (is_file($deletable_file)){	unlink($deletable_file); }
			        //save path to new thumbnail file
			        $this->media->thumbnail_file = $newRelFileName;
			        $this->media->save();
			    }
	            
	            $_POST = array();
	            $this->_redirect('/media-gallery/media-admin/edit/id/'.$this->media->id);
	        }
        } elseif ($id){//no $_POST, but a particular id is requested for editing
			$this->media = Doctrine_Query::create()->from('MediaGallery_Model_Media m')->leftJoin('m.Album')->where('m.id = ?', $id)->fetchOne();
			$this->form->setDefaults($this->media->toArray());
		} elseif ($media_album_id = $this->getRequest()->getParam('media_album_id')){//request to create a new media for a particular media album
            $this->media = new MediaGallery_Model_Media;
            $this->media->Album = Doctrine_Query::create()->from('MediaGallery_Model_Album a')->where('a.id = ?', $media_album_id)->fetchOne();
            $this->form->setDefaults(array('media_album_id'=>$this->media->Album->id));
        } else {//no id and no media_album_id... return to media album list so we can either create another or edit a current media
            $this->_redirect('/media-gallery/album-admin/list');
        }
        $this->view->media=$this->media;
        $this->view->form = $this->form;
    }
	
	public function listAction()
    {
        $media_album_id = $this->getRequest()->getParam('media_album_id');
        if (empty($media_album_id)){
            $this->_redirect('/media-gallery/album-admin/list');
        }
        $query = Doctrine_Query::create();
        $media_album=$query->from('MediaGallery_Model_Album')->where('id = ?', $media_album_id)->orderby('name')->fetchOne();
		$this->view->media_album = $media_album;
    }
	
	public function deleteAction()
    {
		if ($id = $this->getRequest()->getParam('id')){//record id to delete
			$query = Doctrine_Query::create();
			$media = $query->from('MediaGallery_Model_Media')->where('id = ?', $id)->fetchOne();
            //delete associated files
            $deletable_file=Zend_Registry::get('USER_MEDIA_PATH').$media->thumbnail_file;
            if (is_file($deletable_file)){	unlink($deletable_file); }
            $deletable_file=Zend_Registry::get('USER_MEDIA_PATH').$media->media_file;
            if (is_file($deletable_file)){	unlink($deletable_file); }
            //delete db record
            $media->delete();
    	}
        $this->_redirect('/media-gallery/media-admin/list/media_album_id/'.$media->media_album_id);
    }

}
