<?php
namespace FAQ\Mapper;

use FAQ\DB\Db;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQEntity\News;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQCommon\ChromePhp;

/**
 *
 * @author sang
 *
 */
class NewMapper extends Db
{

    private $news;

    public function __construct()
    {
        parent::__construct();
        $this->news = new News();
    }

    /**
     *
     * @param \FAQ\FAQEntity\News $news
     */
    public function createNews($news)
    {
        $news->insert();
        $this->commit();
    }

    /**
     *
     * @param String $newID
     */
    public function delete($newID)
    {
        $qb = $this->news->getQueryBuilder()
            ->findAndUpdate()
            ->field("id")
            ->equals($newID)
            ->field("status")
            ->set(FAQParaConfig::STATUS_DEACTIVE);
        $qb->getQuery()->execute();
    }
    /**
     *
     * @param String $newID
     */
    public function reopen($newID)
    {
        $qb = $this->news->getQueryBuilder()
            ->findAndUpdate()
            ->field("id")
            ->equals($newID)
            ->field("status")
            ->set(FAQParaConfig::STATUS_ACTIVE);
        $qb->getQuery()->execute();
    }

    /**
     *
     * @param Int $newID
     */
    public function getOneNew($newID)
    {
        return $this->news->find($newID, true);
    }

    /**
     *
     * @param Int $type
     * @param Int $status
     * @param Int $from
     * @param Int $to
     * @return ArrayCollection
     */
    public function getNews($type, $status, $from, $to,$orderBy)
    {
        $qb = $this->news->getQueryBuilder();

        if (! empty($type)) {
            $qb->field('type')->equals($type);
        }
        if (! empty($status)) {
            $qb->field('status')->equals($status);
        }
        if (! empty($orderBy)) {
            $qb=Util::addOrder($qb, $orderBy);
        }
        // set limit
        if (isset($from) && isset($to)) {
            $qb->limit($to - $from)->skip($from);
        }
        $q = $qb->getQuery();
        $news = $q->execute();
        return $news;
    }
}

?>