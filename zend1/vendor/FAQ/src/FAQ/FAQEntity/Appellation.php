<?php
namespace FAQ\FAQEntity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\EntityEmbed;
use FAQ\FAQCommon\Util;

/**
 * @ODM\EmbeddedDocument
 *
 * @todo danh hieu
 */
class Appellation extends EntityEmbed
{

    /**
     * @ODM\String
     */
    private $rank;

    /**
     * @ODM\ReferenceOne(targetDocument="Subject")
     */
    private $subject;

    /**
     * @ODM\Int
     * @todo stored total money point for user in the subject
     */
    private $total_money_point;

    /**
     * @ODM\Int
     * @todo stored total rank point for user in the subject
     */
    private $total_rank_point;

    /**
     * @ODM\String
     */
    private $desc;

    /**
     * @ODM\Date
     */
    private $date_updated;

    /**
     *
     * @return String
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     *
     * @param String $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
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
        $this->subject = $subject;
        return $this;
    }

	/**
 * @return the unknown_type
 */
public function getTotalMoneyPoint() {
	return $this->total_money_point;
}

	/**
 * @param unknown_type $total_money_point
 */
public function setTotalMoneyPoint($total_money_point) {
	$this->total_money_point = $total_money_point;
	return $this;
}

	/**
 * @return the Int
 */
public function getTotalRankPoint() {
	return $this->total_rank_point;
}

	/**
 * @param Int $total_rank_point
 */
public function setTotalRankPoint($total_rank_point) {
	$this->total_rank_point = $total_rank_point;
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
     * @odm\PrePersist
     */
    public function autoSetDateChange()
    {
        if (! $this->date_updated) {
            $this->date_updated = Util::getCurrentTime();
        }
    }
}