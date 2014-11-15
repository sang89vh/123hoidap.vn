<?php
namespace FAQ\FAQEntity;

use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\FAQCommon\Util;
/**
 * @ODM\Document
 * @todo Luu thong tin cap bac ma nguoi dung da dat duoc
 * @tutorial Type is Document
 */
class Rank extends Entity
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
     */
    private $status;

    
    public function __construct()
    {
    	parent::__construct();
    	$this->status = 1;
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

    	if ($this->date_created&&!$this->date_updated) {
    		$this->date_updated=Util::getCurrentTime();
    	}
    	if (!$this->date_created) {
    		$this->date_created=Util::getCurrentTime();
    	}

    }
}