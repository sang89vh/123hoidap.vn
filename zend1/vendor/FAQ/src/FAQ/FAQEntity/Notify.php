<?php
namespace FAQ\FAQEntity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\EntityEmbed;
use FAQ\FAQCommon\Util;

/**
 * @ODM\EmbeddedDocument
 *
 * @todo Cac thong bao tu he thong gui den nguoi dung
 */
class Notify extends EntityEmbed
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
     * @ODM\ReferenceOne(targetDocument="Question",cascade={"all"})
     */
    private $question;



    /**
     * @ODM\ReferenceOne(targetDocument="User",cascade={"all"})
     */
    private $user_create_notify;

    /**
     * @ODM\Int
     * @todo that is comments, follow, unfollow, report spam, unreport spam...
     * that is type os action for object
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
     * @return Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     *
     * @param Question $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
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
     * @return User
     */
    public function getUserCreateNotify()
    {
        return $this->user_create_notify;
    }

    /**
     *
     * @param User $user_create_notify
     */
    public function setUserCreateNotify($user_create_notify)
    {
        $this->user_create_notify = $user_create_notify;
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