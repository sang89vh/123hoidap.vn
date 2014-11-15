<?php
namespace FAQ\FAQEntity;

use FAQ\FAQCommon\Util;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\EntityEmbed;

/**
 * @ODM\EmbeddedDocument
 * @todo Lien he voi support cua he thong 123hoidap.vn
 */
class ContactUs extends EntityEmbed
{

    /**
     * @ODM\String
     */
    private $title;

    /**
     * @ODM\String
     */
    private $guest_name;

    /**
     * @ODM\String
     */
    private $guest_email;

    /**
     * @ODM\String
     */
    private $message_to;

    /**
     * @ODM\Date
     */
    private $date_to;

    /**
     * @ODM\String
     */
    private $message_reply;

    /**
     * @ODM\Date
     */
    private $date_reply;

    /**
     * @ODM\Int
     */
    private $status;

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
    public function getGuestName()
    {
        return $this->guest_name;
    }

    /**
     *
     * @param String $guest_name
     */
    public function setGuestName($guest_name)
    {
        $this->guest_name = $guest_name;
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getGuestEmail()
    {
        return $this->guest_email;
    }

    /**
     *
     * @param String $guest_email
     */
    public function setGuestEmail($guest_email)
    {
        $this->guest_email = $guest_email;
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getMessageTo()
    {
        return $this->message_to;
    }

    /**
     *
     * @param String $message_to
     */
    public function setMessageTo($message_to)
    {
        $this->message_to = $message_to;
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getDateTo()
    {
        return $this->date_to;
    }

    /**
     *
     * @param Date $date_to
     */
    public function setDateTo($date_to)
    {
        $this->date_to = $date_to;
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getMessageReply()
    {
        return $this->message_reply;
    }

    /**
     *
     * @param String $message_reply
     */
    public function setMessageReply($message_reply)
    {
        $this->message_reply = $message_reply;
        return $this;
    }

    /**
     *
     * @return Date
     */
    public function getDateReply()
    {
        return $this->date_reply;
    }

    /**
     *
     * @param Date $date_reply
     */
    public function setDateReply($date_reply)
    {
        $this->date_reply = $date_reply;
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

    	if (!$this->date_to) {
    		$this->date_to=Util::getCurrentTime();
    	}


    }

}