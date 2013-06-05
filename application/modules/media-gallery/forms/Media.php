<?php
class MediaGallery_Form_Media extends Zend_Form
{
	public function init() {
	    //die(Zend_Registry::get('MEDIA_GALLERY_THUMB_PATH'));
		$this->setAttrib('enctype', 'multipart/form-data');
		$this->setMethod('post');
		$this->addElement('hidden','id');
        $mediaAlbums=Doctrine::getTable('MediaGallery_Model_Album')->findAll();
        $mediaAlbumOptions=array(''=>"choose an album");
        foreach($mediaAlbums as $key=>$mediaAlbum){
            $mediaAlbumOptions[$mediaAlbum->id]=$mediaAlbum->name;
        }
		$this->addElement('select','media_album_id')
            ->getElement('media_album_id')
            ->addMultiOptions($mediaAlbumOptions)
            ->setIsArray(false)
            ->setValue('')
            ->setRequired(true)
            ->setLabel('Media Album:');
		$this->addElement('text','name',array('label' => 'Media Name','required' => true));
		//custom thumbnail
        $element = new Zend_Form_Element_File('thumbnail');
		$element->setLabel('Custom thumbnail (jpg):')
			->setDescription('If you would like a custom thumbnail to be used for listings of this media file please upload it here.  Otherwise a thumbnail will be generated for you when you upload a media file.')
		    ->setDestination(Zend_Registry::get('MEDIA_GALLERY_THUMB_PATH'))
		    ->addValidator('Count', false, 1)     // ensure only 1 file
		    ->addValidator('Size', false, 3000000) // limit to 3M
		    ->addValidator('Extension', false, 'png,gif,jpg,jpeg'); // only JPEG, PNG, GIFs, Flash Video, or Mp3's allowed
		$this->addElement($element);
		$this->addElement('hidden','temp_thumbnail');//this is used by the flash uploader to tell the controller which temporary file to use for the media file

        $this->addElement('submit','submit',array('label' => 'Send'));
        
	}
}
