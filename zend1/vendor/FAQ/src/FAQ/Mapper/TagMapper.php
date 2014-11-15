<?php

namespace FAQ\Mapper;

use FAQ\DB\Db;
use FAQ\FAQEntity\KeyWord;
use FAQ\FAQCommon\Util;
use MongoDate;
use DateTime;
use Exception;
use FAQ\FAQEntity\Tag;

/**
 *
 * @author sang
 *
 */
class TagMapper extends Db {
	private $keyword;
	private $tag;
	public function __construct() {
		parent::__construct ();

		$this->keyword = new KeyWord ();
		$this->tag = new Tag ();
	}

	/**
	 *
	 * @author sang
	 * @todo get top tag for home page
	 * @param array $orderBy
	 * @param Int $from
	 * @param Int $to
	 * @return Ambigous <\Doctrine\ODM\MongoDB\Query\mixed, \Doctrine\MongoDB\EagerCursor, \Doctrine\MongoDB\Cursor, Cursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
	 */
	public function getTagHome($orderBy, $from, $to, $isQuestion) {
		$qb = $this->keyword->getQueryBuilder ();
		$qb->field ( "question" )->exists ( $isQuestion );
		if (isset ( $orderBy )) {
			$qb = Util::addOrder ( $qb, $orderBy );
		}
		// set limit
		if (isset ( $from ) && isset ( $to )) {
			$qb = $qb->limit ( $to - $from )->skip ( $from );
		}

		$q = $qb->getQuery ();

		$tags = $q->execute ();

		return $tags;
	}

	/**
	 *
	 * @author sang
	 * @todo get top tag for home page
	 * @param array $orderBy
	 * @param Int $from
	 * @param Int $to
	 * @param String $subjectID
	 * @return Ambigous <\Doctrine\ODM\MongoDB\Query\mixed, \Doctrine\MongoDB\EagerCursor, \Doctrine\MongoDB\Cursor, Cursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
	 */
	public function getTagSubject($subjectID, $orderBy, $from, $to, $isQuestion = true) {
		$qb = $this->keyword->getQueryBuilder ();
		$qb->field ( "question" )->exists ( $isQuestion );
		if (! empty ( $subjectID )) {
			$qb->field ( "subject.id" )->equals ( $subjectID );
		}
		$qb->field ( "subject" )->exists ( true );
		if (isset ( $orderBy )) {
			$qb = Util::addOrder ( $qb, $orderBy );
		}
		// set limit
		if (isset ( $from ) && isset ( $to )) {
			$qb = $qb->limit ( $to - $from )->skip ( $from );
		}

		$q = $qb->getQuery ();

		$tags = $q->execute ();

		return $tags;
	}
	public function getSystemTag($query,$type, $from, $to, $orderBy = null,$status=1) {
// 		var_dump($query,$type,$from,$to,$orderBy);
		if($type == "popular"){
			$type = null;
		}
		$qb = $this->tag->getQueryBuilder ()->hydrate ( true );
		$queryString = Util::covertUnicode($query);
		if (! empty($queryString)) {
			$keywords = explode(' ', $queryString);
			foreach ($keywords as $key => $value) {
				$regexObj = new \MongoRegex("/^" . $value . "/i");
				$keyWord[$key] = $regexObj;
			}
		} else {
			$keyWord = null;
		}
		// set where for query
		if (! empty($keyWord)) {
			$qb = $qb->field('tag_name')->in($keyWord);
		}
		if (! empty ( $type )) {
			$qb->field ( "type" )->equals ( $type );
		}
		if (! empty ( $status )) {
			$qb->field ( "status" )->equals ( $status );
		}
		if (! empty ( $orderBy )) {
			$qb = Util::addOrder ( $qb, $orderBy );
		}
		$totalDocument = $qb->getQuery ()->count ();
		if($from>$totalDocument){
			return array (
					"tags" => array(),
					"totalDocument" => $totalDocument
			);
		}
		if (isset ( $from ) && isset ( $to )) {
			$qb = $qb->limit ( $to - $from )->skip ( $from );
		}

		$q = $qb->getQuery ();

		$tags = $q->execute ();
// var_dump($totalDocument);
		return array (
				"tags" => $tags,
				"totalDocument" => $totalDocument
		);
	}
	/**
	 *
	 * @param Tag $tag
	 * @return Tag
	 */
	public function createTag($tag){
		$tag->insert();
		$this->commit();
		return $tag;
	}
	public function deleteTag($tagID){
		$qb=$this->tag->getQueryBuilder()->findAndRemove()->field("id")->equals($tagID);
		$isRemove=$qb->getQuery()->execute();
		if($isRemove){
			return 1;
		}else {
			return 0;
		}
	}
	public function updateTag($tag){
		$this->commit();
		return $tag;
	}
	public function getOneTag($tagID){
		return $this->tag->find($tagID, true);
	}
}

?>