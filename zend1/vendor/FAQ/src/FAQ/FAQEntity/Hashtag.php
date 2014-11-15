<?php
namespace FAQ\FAQEntity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\Entity;

/**
 * @ODM\Document
 *
 * @todo Luu danh muc cac hashtag trong he thong
 */
class Hashtag extends Entity
{

    /**
     * @ODM\String
     */
    private $tag;

    /**
     * @ODM\ReferenceOne(targetDocument="Subject",cascade={"detach","merge","refresh","persist"})
     */
    private $subject;

    /**
     * @ODM\Int
     */
    private $total_amount;

    /**
     * @ODM\Int
     */
    private $recomment;

    /**
     *
     * @return String
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     *
     * @param String $tag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     *
     * @return Int
     */
    public function getTotalAmount()
    {
        return $this->total_amount;
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
     * @return \FAQ\FAQEntity\Hashtag
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     *
     * @param Int $total_amount
     */
    public function setTotalAmount($total_amount)
    {
        $this->total_amount = $total_amount;
        return $this;
    }

    /**
     *
     * @return Int
     */
    public function getRecomment()
    {
        return $this->recomment;
    }

    /**
     *
     * @param Int $recomment
     */
    public function setRecomment($recomment)
    {
        $this->recomment = $recomment;
        return $this;
    }
}