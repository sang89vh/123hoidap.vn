<?php
namespace FAQ\FAQEntity;

use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Types\FloatType;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Float;

/**
 * @odm\Document
 *
 * @todo Luu cac anh trong he thong de index, khai pha du lieu
 */
class Image extends Entity
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
     * @ODM\File
     */
    private $file;

    /**
     * @ODM\String
     */
    private $contentType;

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
     * @ODM\ReferenceOne(targetDocument="User",inversedBy="image",cascade={"detach","merge","refresh","persist"})
     */
    private $create_by;


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
	 * @odm\PrePersist
	 */
	public function autoSetDefaultAttibue()
	{
		if(empty($this->contentType)){
		    //default from facebook avatar
		    $this->contentType="image/jpeg";
		}
	}


}