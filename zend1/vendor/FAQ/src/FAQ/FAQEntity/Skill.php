<?php
namespace FAQ\FAQEntity;

use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;
/**
 * @ODM\Document
 *
 * @todo Luu danh sach cac ky nang trong cac chu de, ky nang cua nguoi dung
 */
class Skill extends Entity
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
    /**
     *
     * @todo find skill in this field
     *       @ODM\Collection
     *       @ODM\Index
     */
    private $key_word=array();
    /**
     * @ODM\ReferenceMany(targetDocument="Subject",cascade={"detach","merge","refresh","persist"})
     */
    private $subject;

    public function __construct()
    {
        $this->subject = new ArrayCollection();
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
     *
     * @return Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     *
     * @param Subject $subject
     */
    public function setSubject($subject)
    {
        $this->subject[] = $subject;
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
    	if (!$this->key_word) {
    		$this->key_word[]=strtolower( Util::covertUnicode($this->getName()));

    	}
    	if (!$this->status) {
    		$this->status=FAQParaConfig::STATUS_ACTIVE;

    	}

    }
}

?>