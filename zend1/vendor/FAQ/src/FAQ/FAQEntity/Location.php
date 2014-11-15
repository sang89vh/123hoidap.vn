<?php
namespace FAQ\FAQEntity;

use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Distance;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;

/**
 * @ODM\Document
 * @ODM\Index(keys={"coordinates"="2d"})
 * @todo Luu danh sach cac vi tri dia ly, cong ty, truong hoc
 * cac dia danh theo ten, mo ta, vi tri theo toa do dia ly
 */
class Location extends Entity
{

    /**
     * @ODM\String
     */
    private $name;

    /**
     * @ODM\String
     */
    private $desc;

    /**
     *
     * @todo find location in this field
     *       @ODM\Collection
     *       @ODM\Index
     */
    private $key_word=array();

    /**
     * @ODM\EmbedOne(targetDocument="Coordinates")
     */
    private $coordinates;

    /**
     * @ODM\Distance
     */
    private $distance;

    /**
     * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
     */
    private $create_by;

    /**
     * @ODM\Date
     */
    private $date_created;

    /**
     * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
     */
    private $update_by;

    /**
     * @ODM\Date
     */
    private $date_updated;

    /**
     * @ODM\Int
     * @todo Phan loai location thanh nhieu loai khac nhau
     * 1: Dia danh hanh chinh
     * 2: cong ty
     * 3: Ban bo, nganh, so
     * 4: Truong cap 1
     * 5: Truong cap 2
     * 6: Truong cap 3
     * 7: Truong trung cap,Truong cao dang,Truong dai hoc
     *
     */
    private $type;

    /**
     * @ODM\Int
     */
    private $status;



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
     *
     * @return String
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     *
     * @param String $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getKeyWord()
    {
        return $this->key_word;
    }

    /**
     *
     * @param String $key_word
     */
    public function setKeyWord($key_word)
    {
        $this->key_word[] = $key_word;
        return $this;
    }

	/**
 * @return Coordinates
 */
public function getCoordinates() {
	return $this->coordinates;
}

	/**
 * @param Coordinates $coordinates
 */
public function setCoordinates($coordinates) {
	$this->coordinates = $coordinates;
	return $this;
}

	/**
 * @return Distance
 */
public function getDistance() {
	return $this->distance;
}

	/**
 * @param Distance $distance
 */
public function setDistance($distance) {
	$this->distance = $distance;
	return $this;
}


    /**
     *
     * @return User
     */
    public function getCreateBy()
    {
        return $this->create_by;
    }

    /**
     *
     * @param User $create_by
     */
    public function setCreateBy($create_by)
    {
        $this->create_by = $create_by;
        return $this;
    }

    /**
     *
     * @return Date
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     *
     * @param Date $date_created
     */
    public function setDateCreated($date_created)
    {
        $this->date_created = $date_created;
        return $this;
    }

    /**
     *
     * @return User
     */
    public function getUpdateBy()
    {
        return $this->update_by;
    }

    /**
     *
     * @param User $update_by
     */
    public function setUpdateBy($update_by)
    {
        $this->update_by = $update_by;
        return $this;
    }

    /**
     *
     * @return Date
     */
    public function getDateUpdated()
    {
        return $this->date_updated;
    }

    /**
     *
     * @param Date $date_updated
     */
    public function setDateUpdated($date_updated)
    {
        $this->date_updated = $date_updated;
        return $this;
    }

    /**
     *
     * @return Int
     * @todo Phan loai location thanh nhieu loai khac nhau
     * @tutorial Domain la LOCATION
     * 1: Dia danh hanh chinh
     * 2: cong ty
     * 3: Ban bo, nganh, so
     * 4: Truong cap 1
     * 5: Truong cap 2
     * 6: Truong cap 3
     * 7: Truong trung cap, Truong cao dang,Truong dai hoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param Int $type
     * @todo Phan loai location thanh nhieu loai khac nhau
     * @tutorial Domain la LOCATION
     * 1: Dia danh hanh chinh
     * 2: cong ty
     * 3: Ban bo, nganh, so
     * 4: Truong cap 1
     * 5: Truong cap 2
     * 6: Truong cap 3
     * 7: Truong trung cap,Truong cao dang,Truong dai hoc
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * @return Int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param Int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    /** @odm\PrePersist*/
    public function autoSetDateChange(){


    	if (!$this->date_created) {
    		$this->date_created=Util::getCurrentTime();
    	}
    	if (!$this->status) {
    		$this->status=FAQParaConfig::STATUS_ACTIVE;
    	}
    	if (!$this->key_word) {
    		$this->key_word[]=strtolower( Util::covertUnicode($this->getName()));

    	}

    }
}