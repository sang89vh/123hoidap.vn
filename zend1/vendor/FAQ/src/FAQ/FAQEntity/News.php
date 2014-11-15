<?php
namespace FAQ\FAQEntity;


use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\Entity;
use FAQ\FAQCommon\Util;
/**
 * @ODM\Document
 * @todo Cac dieu khoan, nguyen tac cong dong, tin tuc cua he thong 123hoidap.vn
 * Thong bao...
 */
class News extends Entity
{

    /**
     * @ODM\String
     */
    private $title;

    /**
     * @ODM\String
     */
    private $content;

    /**
     * @ODM\Int
     */
    private $status;

    /**
     * @ODM\Int
     */
    private $type;

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
     *
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param String $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     *
     * @param String $content
     */
    public function setContent($content)
    {
        $this->content = $content;
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

    /**
     *
     * @return Int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param Int $type
     */
    public function setType($type)
    {
        $this->type = $type;
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