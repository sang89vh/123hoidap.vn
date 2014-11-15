<?php
namespace FAQ\FAQEntity;

use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\FloatType;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Float;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @odm\Document
 *
 * @todo Luu cac anh trong he thong de index, khai pha du lieu
 */
class Media extends Entity
{

    /**
     * @ODM\String
     */
    private $name;
    /**
     * @ODM\String
     */
    private $filename;

    /**
     * @ODM\Int
     * @var unknown
     */
    private  $status;

    /**
     *  @ODM\ReferenceMany(targetDocument="Media", cascade={"persist"})
     */
    private $children;

    /**
     *  @ODM\ReferenceOne(targetDocument="Media", cascade={"persist"})
     */
    private $parent;

    /**
    * @ODM\String
    */
    private $link;

    /**
     * @ODM\File
     */
    private $file;

    /**
     * @ODM\String
     */
    private $contentType;

    /**
     * @ODM\String
     * @tutorial anh, video, link video, link anh....
     */
    private $type;

    /**
     * @ODM\Date
     */
    private $uploadDate;

    /**
     * @ODM\Float
     */
    private $length;

    /**
     * @ODM\String
     */
    private $chunkSize;

    /**
     * @ODM\String
     */
    private $md5;
    /**
     * @ODM\ReferenceOne(targetDocument="User",inversedBy="media",cascade={"detach","merge","refresh","persist"})
     */
    private $create_by;

    /**
	 * @return the $link
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * @param field_type $link
	 */
	public function setLink($link) {
		$this->link = $link;
	}

	public function __construct(){
        parent::__construct();
        $this->children =  new ArrayCollection();
    }

    /**
	 * @return the $status
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @return the $children
	 */
	public function getChildren() {
		return $this->children;
	}

	/**
	 * @return the $parent
	 */
	public function getParent() {
		return $this->parent;
	}

	/**
	 * @param \FAQ\FAQEntity\unknown $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * @param field_type $children
	 */
	public function setChildren($children) {
		$this->children[] = $children;
	}

	/**
	 * @param field_type $parent
	 */
	public function setParent($parent) {
		$this->parent = $parent;
	}

	/**
     *
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

	/**
     * @return String
     */
    public function getFilename() {
    	return $this->filename;
    }

    	/**
     * @param String $filename
     */
    public function setFilename($filename) {
    	$this->filename = $filename;
    	return $this;
    }


    /**
     *
     *
     * @tutorial $image = $dm->createQueryBuilder('Documents\Image')<br/>
     *           ->field('name')->equals('Test image')<br/>
     *           ->getQuery()<br/>
     *           ->getSingleResult();<br/>
     *
     *           header('Content-type: image/png;');<br/>
     *           echo $image->getFile()->getBytes();
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     *
     *
     * @tutorial $image = new Image(); <br/>
     *           $image->setName('Test image');<br/>
     *           $image->setFile('/path/to/image.png');<br/>
     *           $dm->persist($image);<br/>
     *           $dm->flush();
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     *
     * @param String $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
	 * @return Int
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param Int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
     *
     * @return Date
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }



    /**
     *
     * @return Float
     */
    public function getLength()
    {
        return $this->length;
    }



    /**
     *
     * @return String
     */
    public function getChunkSize()
    {
        return $this->chunkSize;
    }


    /**
     *
     * @return String
     */
    public function getMd5()
    {
        return $this->md5;
    }
	/**
	 * @return User
	 */
	public function getCreate_by() {
		return $this->create_by;
	}

	/**
	 * @param User $create_by
	 */
	public function setCreate_by($create_by) {
		$this->create_by = $create_by;
	}


	/**
	 * @odm\PreFlush
	 */
	public function setDefault(){
	    if($this->create_by==null){
	        $user = Util::getCurrentUser();
	        $this->setCreate_by($user);
	    }
	    if($this->status==null){
	        $this->setStatus(FAQParaConfig::MEDIA_STATUS_NORMAL);
	    }
	    if($this->uploadDate==null){
	        $this->uploadDate = Util::getCurrentTime();
	    }
	}

}