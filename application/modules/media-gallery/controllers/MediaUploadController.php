<?php

class MediaGallery_MediaUploadController extends Zend_Controller_Action
{

	public $destination;
	public $thumbnail_partial_path;
	public $thumbnail_url;
	public $thumbnail_filename;
	public $file;
	
	public function init(){
	    $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('attach-media-from-upload', 'json')
                      ->initContext();
                      
		$this->_helper->viewRenderer->setNoRender(true);
		$fileKey = $this->_getParam('file_key');
		$fileKey = $fileKey?$fileKey:'Filedata';
		$this->mediaDestination=Zend_Registry::get('MEDIA_GALLERY_MEDIA_PATH');
		$this->mediaUrl = Zend_Registry::get('MEDIA_GALLERY_MEDIA_URL');
        $this->thumbnailDestination = Zend_Registry::get('MEDIA_GALLERY_THUMB_PATH');
        $this->thumbnailUrl = Zend_Registry::get('MEDIA_GALLERY_THUMB_URL');
        $this->defaultThumbnailPath = Zend_Registry::get('MEDIA_GALLERY_THUMB_DEFAULTS_PATH');
        $this->file = $_FILES[$fileKey];
        if ($this->file){
        	$this->file['ext'] = $ext = substr($this->file['name'],strrpos($this->file['name'], ".")+1);
        	//die($this->file['ext']);
        } else {
        	die('failure');//the file wasn't uploaded, and since that's all this controller does is handle file uploads we know there is a problem
        }	
        $this->mediaType = MediaGallery_Model_Media::getMediaTypeForFileName($this->file['name']);
	}
	public function testUploadScriptAction(){
		//debugging
			$debugData = array(
				'REQUEST'=>$this->getRequest()->getParams(),
				'$_FILES'=>$_FILES,
			);
			$mail = new Zend_Mail;
			$mail->setFrom ('debugging@mrburly.com', 'Debugging');
			$mail->addTo('caraccioli@cox.net');
			$mail->setSubject ('Testing Upload Script');
			$mail->setBodyHTML(print_r($debugData,1));
			$mail->send();
	        $this->_forward('attach-media-from-upload');
	}
	
	public function attachMediaFromUploadAction(){
		
		if (is_file($this->file['tmp_name'])){//a file was uploaded... 
			$media_album_id = $this->_getParam('media_album_id');
			$media_id = $this->_getParam('media_id');
			//die($media_id);
			$this->media = $media_album_id?new MediaGallery_Model_Media:Doctrine_Query::create()->from('MediaGallery_Model_Media m')->where('m.id =?',$media_id)->fetchOne();
			//delete current media files
			$publicPath = Zend_Registry::get('PUBLIC_PATH');
			if (is_file($oldFile = $publicPath . $this->media->media_file)){
			    unlink($publicPath . $this->media->media_file);
			}
			if (is_file($publicPath . $this->media->thumbnail_file)){
			    unlink($publicPath . $this->media->thumbnail_file);
			}
			$this->media->media_album_id = $media_album_id?$media_album_id:$this->media->media_album_id;
			$this->media->type = $this->mediaType;
			$this->media->orig_file_name = $this->file['name'];
			//move the media to the media folder with a unique name
			$uniqName=md5($this->file['name'].date('Y-m-d H:i:s'));
			$newFileName = $this->mediaDestination.'/'.$uniqName.'.'.$this->file['ext'];
			move_uploaded_file($this->file['tmp_name'], $newFileName);
			$this->media->media_file = $this->mediaUrl.'/'.$uniqName.'.'.$this->file['ext'];
			$this->filter($newFileName, $this->media->type);
			$fileToThumbnail = ($this->media->type == 'photo')?$newFileName:$this->defaultThumbnailPath.'/'.$this->media->type.'.jpg';
			$fileNameOfThumbnail =  $this->thumbnailDestination.'/'.$uniqName.'.jpg'; 
			$thumbnailDimensions = ($this->media->type == 'video')?array('x'=>96, 'y'=>48):array('x'=>76, 'y'=>66);

			$this->_makeThumbnail($fileToThumbnail, $fileNameOfThumbnail, $thumbnailDimensions['x'], $thumbnailDimensions['y']);
			$this->media->name = $this->media->name?$this->media->name:$this->file['name'];
			$this->media->thumbnail_file = $this->thumbnailUrl.'/'.$uniqName.'.jpg';
			$this->media->save();
			//JSON response
			$this->view->thumbnail = $this->media->thumbnail_file;
			$this->view->media = $this->media->media_file;
			$this->view->mediaName= $this->media->orig_file_name;
			$this->view->oldFile = $oldFile;
			$this->view->random = rand();
		} else {
		    //should not reach the following die if there is a valid file and it gets moved
		    $this->view->error = "The temporary file name is not a file.  See line 63 of ". __FILE__;   
		}		
	}
	
	public function testThumbnailAction(){
		$this->_makeThumbnail($this->mediaDestination.'/0a446fa0e0d1c9bcbaf6381440d95970.jpg', $this->mediaDestination.'test_'.date('His').'.jpg', 50,50);
		die();
	}

	
	public function filter($file, $mediaType){
		if ($mediaType=='photo'){
			$this->_resizeFullSizeImage($file, 1280);
		}
	}
	
	private function _makeThumbnail($fileToThumbnail, $saveFileName, $width=77, $height=66){
        /**wideImage magic**/ 
        //die($fileToThumbnail);
        require_once ('WideImage/lib/WideImage.inc.php');
        // image paths
        $wi = wiImage::load($fileToThumbnail)
                ->resize($width, $height, 'outside')
                ->crop(0,0,$width,$height)
                ->saveToFile($saveFileName);
         /* end wideImage */

         return true;
	}
	
	private function _resizeFullSizeImage($file_name, $width = 1280, $height = 720){
        /**wideImage magic**/
        require_once ('WideImage/lib/WideImage.inc.php');
        // image path
        $wi = wiImage::load($file_name)
                ->resize($width, $height, 'outside')
                ->crop(0,0,$width,$height)
                ->saveToFile($file_name);
        return true;
	}
}
