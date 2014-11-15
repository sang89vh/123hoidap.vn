<?php
namespace FAQ\FAQEntity;


use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\EntityEmbed;
use FAQ\FAQCommon\Util;
/**
 * @ODM\EmbeddedDocument
 * @todo Luu thong tin cap bac ma nguoi dung dat duoc
 */
class UserRank extends EntityEmbed
{

    /**
     * @ODM\String
     */
    private $name;

    /**
     * @ODM\Date
     */
    private $date_updated;

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

    	if (!$this->date_updated) {
    		$this->date_updated=Util::getCurrentTime();
    	}


    }
}