<?php
namespace FAQ\FAQEntity;


use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\Entity;
use FAQ\FAQCommon\Util;
/**
 * @odm\Document
 * @todo cac message ma gui dung gui cho nhau
 */
class Message extends Entity
{

    /**
     * @ODM\String
     */
    private $content;

    /**
     * @ODM\Date
     */
    private $date_updated;

    /**
     * @ODM\ReferenceOne(targetDocument="User",inversedBy="message",cascade={"detach","merge","refresh","persist"})
     */
    private $from_user;
    /**
     * @ODM\ReferenceOne(targetDocument="User",inversedBy="to_message",cascade={"detach","merge","refresh","persist"})
     */
    private $to_user;

    /**
     * @ODM\ReferenceOne(targetDocument="Question",inversedBy="chat_help",cascade={"detach","merge","refresh","persist"})
     */
    private $question;



    /**
     * @ODM\Int
     * @todo phan loai messsage theo giup do
     */
    private $type;

    /**
     * @ODM\Int
     * @todo luu trang thai cua message
     * @tutorial Domain MESSAGE co cac gia tri
     * 1: Chua doc
     * 2: Da doc
     */
    private $status;

    /**
     *
     * @return  String
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
     * @return  Date
     */
    public function getDateUpdated() {
    	return $this->date_updated;
    }

    /**
     * @param Date $date_updated
     */
    public function setDateUpdated($date_updated) {
    	$this->date_updated = $date_updated;
    	return $this;
    }

    /**
     *
     * @return   User
     */
    public function getToUser()
    {
        return $this->to_user;
    }

    /**
     *
     * @param  User $to_user
     */
    public function setToUser($to_user)
    {
//         $to_user->setToMessage($this);
        $this->to_user = $to_user;
        return $this;
    }

	/**
 * @return User
 */
public function getFromUser() {
	return $this->from_user;
}

	/**
 * @param User $from_user
 */
public function setFromUser($from_user) {
	$this->from_user = $from_user;
	return $this;
}


    /**
     *
     * @return   Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     *
     * @param  Question $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
        return $this;
    }




    /**
     *
     * @return   Int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param  Int $type
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     *
     * @return   Int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param  Int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /** @odm\PrePersist*/
    public function autoSetDateChange(){

    	if (!$this->date_updated) {
    		$this->date_updated=Util::getCurrentTime();
    	}


    }



}