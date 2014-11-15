<?php
namespace FAQ\FAQEntity;


use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\EntityEmbed;
use FAQ\FAQCommon\Util;
/**
 * @ODM\EmbeddedDocument
 * @todo Luu thong thong tin Vote cho bai viet
 */
class UserVote extends EntityEmbed
{

    /**
     * @ODM\Int
     */
    private $vote;

    /**
     * @ODM\Date
     */
    private $date_updated;

    /**
     * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
     */
    private $create_by;

    /**
     *
     * @return Int
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     *
     * @param Int $vote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;
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
     * @todo is person vote it
     *
     * @return User
     */
    public function getCreateBy()
    {
        return $this->create_by;
    }

    /**
     * @todo is persion vote it
     *
     * @param User
     */
    public function setCreateBy($create_by)
    {
        $this->create_by = $create_by;
        return $this;
    }

    /** @odm\PrePersist*/
    public function autoSetDateChange(){

    	if (!$this->date_updated) {
    		$this->date_updated=Util::getCurrentTime();
    	}


    }
}