<?php
namespace FAQ\FAQEntity;


use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\EntityEmbed;
use FAQ\FAQCommon\Util;
/**
 * @ODM\EmbeddedDocument
 * @todo Luu diem so ma nguo dung dat duoc theo cong thuc tinh
 */
class UserFunctionPoint extends EntityEmbed
{

    /**
     * @ODM\String
     */
    private $desc;

    /**
     * @ODM\Int
     */
    private $money_point_bonus;

    /**
     * @ODM\Int
     */
    private $rank_point_bonus;

    /**
     * @ODM\Date
     */
    private $date_updated;
    /**
     * @ODM\ReferenceOne(targetDocument="Question",cascade={"detach","merge","refresh","persist"})
     */
    private $question;
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
     * @return String
     */
    public function getMoneyPointBonus()
    {
        return $this->money_point_bonus;
    }

    /**
     *
     * @param String $money_point_bonus
     */
    public function setMoneyPointBonus($money_point_bonus)
    {
        $this->money_point_bonus = $money_point_bonus;
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getRankPointBonus()
    {
        return $this->rank_point_bonus;
    }

    /**
     *
     * @param String $rank_point_bonus
     */
    public function setRankPointBonus($rank_point_bonus)
    {
        $this->rank_point_bonus = $rank_point_bonus;
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
	 * @return the Question
	 */
	public function getQuestion() {
		return $this->question;
	}

	/**
	 * @param Question $question
	 */
	public function setQuestion($question) {
		$this->question = $question;
		return $this;
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

    	if (!$this->date_updated) {
    		$this->date_updated=Util::getCurrentTime();
    	}


    }
}