<?php

class MediaGallery_PhotoGalleryController extends Zend_Controller_Action
{
	private $_state;

	public function init()
	{
	    //switch layout to photo gallery layout
	    $this->_helper->layout->setLayout('photo-gallery-layout');
	    //setup context switching
		$contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('view', 'xml')
                      ->initContext();
		
		$this->_state = new Zend_Session_Namespace('PhotoGalleryController');
		//1.check the request for the album number
		//2.check the persistant memory for album number
		//3.if neither exist get the first album number from the database
		      if ($album = $this->getRequest()->getParam('album')){
		} elseif ($album = $this->_state->currentAlbum){
		} elseif ($album = Doctrine_Query::create()->from('MediaGallery_Model_Album ma')->addWhere('ma.Media.type=?','photo')->fetchOne()->id){//assuming one record in DB for code simplicity
		}
		
		//1.check the request for the page number
		//2.check the persistant memory for the page number
		//3.if neither exist set page number to 1
		      if ($page = $this->getRequest()->getParam('page')){
		} elseif (isset($this->_state->persitantMemory[$album]['page']) && $page = $this->_state->persitantMemory[$album]['page']){
		} else   {$page = 1;
		}
		
		//reset persitant memory
		$this->_state->currentAlbum = $album;
		$this->_state->currentPage = $this->_state->persitantMemory[$album]['page'] = $page;
		$this->view->state = $this->_state;
	}
	
    public function indexAction()
    {
        $this->_forward('view');
    }
    
    public function listAction()
    {
		$this->_forward('view');//the view template has a navigation which list out the galleries
    }
    
    public function viewAction(){
    	$contextSwitch = $this->_helper->getHelper('contextSwitch');
    	$context = $contextSwitch->getCurrentContext();
    	if (empty($context)){
    		$query = Doctrine_Query::create()
				->select('a.*')
				->from('MediaGallery_Model_Album a')
				->addWhere('a.Media.type=?','photo')//if there aren't any media with type 'photo' associated with a media album then that media album won't be selected by this query.
				;
			
			$this->view->albums = $query->execute();
		
    	} else {
	    	$MediaGallery_Model_Album = Doctrine_Query::create()
				->from('MediaGallery_Model_Album a')
				->addWhere('a.id = ?', $this->_state->currentAlbum)
				->fetchOne()
				;
				
			$this->view->album = $MediaGallery_Model_Album;    
    	}

    	$query = Doctrine_Query::create()
            ->from('MediaGallery_Model_Media m')
	        ->addWhere('m.Album.id = ?', $this->_state->currentAlbum)
	        ->addWhere('m.type=?','photo')
	        ;
            
	    $paginator = new Zend_Paginator(new Custom_Paginator_Adapter_Doctrine($query));
	    $paginator
	    	->setCurrentPageNumber($this->_state->currentPage)
	        ->setItemCountPerPage(12);
	    
	    //assign view vars
	    
		$this->view->paginator = $paginator;
    }
}
