<?php
namespace FAQ\FAQEntity;

use FAQ\FAQCommon\Util;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\DB\EntityEmbed;

/**
 * @ODM\EmbeddedDocument
 * @todo Comment cho bai viet
 */
class Comment extends EntityEmbed
{

    /**
     * @ODM\String
     */
    private $content;

    /**
     * @ODM\Date
     */
    private $date_created;

    /**
     * @ODM\Date
     * @ODM\AlsoLoad("date_created")
     */
    private $date_updated;

    /**
     * @ODM\ReferenceOne(targetDocument="User")
     */
    private $create_by;

    /**
     * @ODM\ReferenceMany(targetDocument="User")
     */
    private $like;

    /**
     * @ODM\ReferenceMany(targetDocument="User")
     */
    private $dislike;

    /**
     * @ODM\EmbedMany(targetDocument="Reply")
     * @ODM\Index(unique=false, order="asc")
     */
    private $reply;
    public function __construct(){

    	$this->like=new ArrayCollection();
    	$this->dislike=new ArrayCollection();
    	$this->reply=new ArrayCollection();
    }
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
     *
     * @return  Date
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
     * @return  Date
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
     * @return   User
     */
    public function getCreateBy()
    {
        return $this->create_by;
    }

    /**
     *
     * @param  User $create_by
     */
    public function setCreateBy($create_by)
    {
        $this->create_by = $create_by;
        return $this;
    }


    /**
     *
     * @return   ArrayCollection
     */
    public function getLike()
    {
        return $this->like;
    }

    /**
     *
     * @param  User $like
     */
    public function setLike($like)
    {
        $this->like[] = $like;
        return $this;
    }

    /**
     *
     * @return   ArrayCollection
     */
    public function getDislike()
    {
        return $this->dislike;
    }

    /**
     *
     * @param  User $dislike
     */
    public function setDislike($dislike)
    {
        $this->dislike[] = $dislike;
        return $this;
    }

    /**
     *
     * @return   ArrayCollection
     */
    public function getReply()
    {
        return $this->reply;
    }

    /**
     *
     * @param  Reply $reply
     */
    public function setReply($reply)
    {
        $this->reply[] = $reply;
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