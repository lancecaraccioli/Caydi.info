<?php

class MediaGallery_Bootstrap extends Zend_Application_Module_Bootstrap
{

    protected function _initRegistry(){
        $registry = Zend_Registry::getInstance();
        $registry->set('MEDIA_GALLERY_PATH',        $registry->get('USER_MEDIA_PATH').'/media-gallery');
        $registry->set('MEDIA_GALLERY_URL',         $registry->get('USER_MEDIA_URL').'/media-gallery');
        $registry->set('MEDIA_GALLERY_MEDIA_PATH',  $registry->get('USER_MEDIA_PATH').'/media-gallery/media/media');
        $registry->set('MEDIA_GALLERY_MEDIA_URL',   $registry->get('USER_MEDIA_URL').'/media-gallery/media/media');
        $registry->set('MEDIA_GALLERY_THUMB_PATH',  $registry->get('USER_MEDIA_PATH').'/media-gallery/media/thumbnails');
        $registry->set('MEDIA_GALLERY_THUMB_URL',   $registry->get('USER_MEDIA_URL').'/media-gallery/media/thumbnails');

        $registry->set('MEDIA_GALLERY_THUMB_DEFAULTS_PATH',  $registry->get('MEDIA_GALLERY_THUMB_PATH').'/_defaults');
        
    }

}


