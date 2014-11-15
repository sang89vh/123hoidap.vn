<?php
namespace FAQ\FAQEntity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\EntityEmbed;
use FAQ\FAQCommon\Util;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\EmbeddedDocument
 *
 * @todo Luu cac phan hoi cua nguoi dung cho cho cac comment, Answer
 *       giong nhu sub-comment
 */
class Reply extends EntityEmbed
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
     * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
     */
    private $create_by;


    /**
     * @ODM\EmbedMany(targetDocument="Reply")
     */
    private $children;

    public function __construct()
    {
        parent::__construct();
        $this->children = new ArrayCollection();
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
     * @return User
     */
    public function getCreateBy()
    {
        return $this->create_by;
    }

    /**
     *
     * @param User $user
     */
    public function setCreateBy($create_by)
    {
        $this->create_by = $create_by;
        return $this;
    }

    /**
     *
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     *
     * @param Reply $children
     */
    public function setChildren($children)
    {
        $this->children[] = $children;
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