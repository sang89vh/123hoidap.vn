<?php
namespace FAQ\FAQEntity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\Entity;
use FAQ\FAQCommon\Util;

/**
 * @ODM\Document
 *
 * @todo Danh sach cac tu khoa cua 1 thanh vien
 */
class Key extends Entity
{

    /**
     * @ODM\ReferenceOne(targetDocument="User", inversedBy="key",cascade={"detach","merge","refresh","persist"})
     */
    private $user;

    /**
     * @ODM\Collection
     * @ODM\Index
     * @ODM\AlsoLoad("hashtag")
     */
    private $key_search = array();

    /**
     * @ODM\Collection
     * @ODM\Index
     */
    private $hashtag = array();

    /**
     * @ODM\Date
     */
    private $date_updated;

    /**
     *
     * @return array
     */
    public function getKeySearch()
    {
        return $this->key_search;
    }

    /**
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     *
     * @param String $key_search
     */
    public function setKeySearch($key_search)
    {
        $this->key_search[] = $key_search;
        return $this;
    }

    /**
     *
     * @return array
     */
    public function getHashtag()
    {
        return $this->hashtag;
    }

    /**
     *
     * @param String $hashtag
     */
    public function setHashtag($hashtag)
    {
        $this->hashtag[] = $hashtag;
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