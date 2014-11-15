<?php

namespace FAQ\Mapper;

use FAQ\FAQEntity\Location;
use FAQ\DB\Db;
use FAQ\FAQEntity\Skill;
use FAQ\FAQEntity\KeyWord;
use FAQ\FAQCommon\Util;
use FAQ\FAQEntity\Subject;
use FAQ\FAQEntity\User;
use FAQ\FAQCommon\ChromePhp;
use FAQ\FAQEntity\Tag;
use FAQ\FAQEntity\Question;

class SearchMapper extends Db {
	private $keyWord;
	private $subject;
	private $question;
	private $user;
	private $tag;
	public function __construct() {
		parent::__construct ();
		$this->keyWord = new KeyWord ();
		$this->subject = new Subject ();
		$this->question = new Question ();
		$this->user = new User ();
		$this->tag = new Tag ();
	}

	/**
	 *
	 * @author izzi
	 * @todo tim kiem location voi type>=type_start, type<type_end
	 * @param String $keyword
	 * @param int $from
	 * @param int $to
	 * @param int $type_start
	 * @param int $type_end
	 * @return unknown
	 */
	public function searchLocationByKeyword($keyword, $from = null, $to = null, $type_start = null, $type_end = null) {
		$location = new Location ();
		$qb = $location->getQueryBuilder ();
		$qb = $qb->distinct ( 'name' );
		$keyword = trim ( $keyword );
		$keyword = Util::covertUnicode ( $keyword );
		$regexObj = new \MongoRegex ( "/" . $keyword . "/i" );
		$qb = $qb->field ( 'key_word' )->equals ( $regexObj );
		if ($type_start !== null && $type_end) {
			$qb->field ( 'type' )->gte ( $type_start );
			$qb->field ( 'type' )->lte ( $type_end );
		} else if ($type_start !== null) {
			$qb->field ( 'type' )->equals ( $type_start );
		}
		if ($from)
			$qb = $qb->skip ( $from );
		if ($to)
			$qb = $qb->limit ( $to );
		$list_location = $qb->getQuery ()->execute ();
		return $list_location;
	}

	/**
	 * @auth izzi
	 *
	 * @todo tim kiem location voi mang cac type phan loai location. vd mang (0, 20, 40)
	 * @param String $keyword
	 * @param int $from
	 * @param int $to
	 * @param array $type
	 * @return unknown
	 */
	public function searchLocationByType($keyword, $from = null, $to = null, $type) {
		$location = new Location ();
		$qb = $location->getQueryBuilder ();
		$qb = $qb->distinct ( 'name' );
		$keyword = trim ( $keyword );
		$keyword = Util::covertUnicode ( $keyword );
		$regexObj = new \MongoRegex ( "/^" . $keyword . "/i" );
		$qb = $qb->field ( 'key_word' )->equals ( $regexObj );
		if ($type) {
			foreach ( $type as $t ) {
				$qb->field ( "type" )->gte ( $t );
				$qb->field ( "type" )->lt ( $t + 10 );
			}
		}

		if ($from)
			$qb = $qb->skip ( $from );
		if ($to)
			$qb = $qb->limit ( $to );
		$list_location = $qb->getQuery ()->execute ();
		return $list_location;
	}

	// izzi
	public function searchSkillByKeyword($keyword, $from = null, $to = null) {
		$skill = new Skill ();
		$qb = $skill->getQueryBuilder ();
		$qb = $qb->distinct ( 'name' );
		$keyword = trim ( $keyword );
		$keyword = Util::covertUnicode ( $keyword );
		$regexObj = new \MongoRegex ( "/^" . $keyword . "/i" );
		$qb = $qb->field ( 'key_word' )->equals ( $regexObj );
		if ($from)
			$qb = $qb->skip ( $from );
		if ($to)
			$qb = $qb->limit ( $to );
		$list_skill = $qb->getQuery ()->execute ();
		return $list_skill;
	}

	/**
	 *
	 * @param array $select
	 * @param array $keyWord
	 * @param array $orderBy
	 * @param Int $from
	 * @param Int $to
	 * @param Boolean $isHydrate
	 * @return array
	 */
	public function searchKeyWord($isOnlyQuestion, $isOnlySubject, $isOnlyUser, $isOnlyLocation, $isOnlySkill, $select, $query, $orderBy, $from = null, $to = null, $isHydrate = false) {
		$queryString = Util::covertUnicode ( $query );
		if (! empty ( $queryString )) {
			$keywords = explode ( ' ', $queryString );
			foreach ( $keywords as $key => $value ) {
				$regexObj = new \MongoRegex ( "/^" . $value . "/i" );
				$keyWord [$key] = $regexObj;
			}
		} else {
			$keyWord = null;
		}

		$qb = $this->keyWord->getQueryBuilder ();
		// select field on collection u want to use
		if (isset ( $isHydrate )) {
			$qb = $qb->hydrate ( $isHydrate );
		}
		if (! empty ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		// set where for query

		if ($isOnlyQuestion == "true") {
			$qb->addOr ( $qb->expr ()->field ( "question" )->notEqual ( null ) );
		} elseif ($isOnlyQuestion == "false") {
			$qb->addOr ( $qb->expr ()->field ( "question" )->equals ( null ) );
		}
		if ($isOnlySubject == "true") {

			$qb->addOr ( $qb->expr ()->field ( "subject" )->notEqual ( null ) );
		} elseif ($isOnlySubject == "false") {
			$qb->addOr ( $qb->expr ()->field ( "subject" )->equals ( null ) );
		}

		if ($isOnlyUser == "true") {
			$qb->addOr ( $qb->expr ()->field ( "user" )->notEqual ( null ) );
			// ChromePhp::info('isOnlyUser == "true"');
		} elseif ($isOnlyUser == "false") {
			$qb->addOr ( $qb->expr ()->field ( "user" )->equals ( null ) );
			// $qb->addOr($qb->expr()
			// ->field("user")
			// ->exists(false));
			// ChromePhp::info('isOnlyUser == "false"');
		}
		if ($isOnlyLocation == "true") {

			$qb->addOr ( $qb->expr ()->field ( "location" )->notEqual ( null ) );
		} elseif ($isOnlyLocation == "false") {
			$qb->addOr ( $qb->expr ()->field ( "location" )->equals ( null ) );
		}
		if ($isOnlySkill == "true") {

			$qb->addOr ( $qb->expr ()->field ( "skill" )->notEqual ( null ) );
		} elseif ($isOnlySkill == "false") {
			$qb->addOr ( $qb->expr ()->field ( "skill" )->equals ( null ) );
		}
		if ($keyWord) {
			$qb = $qb->field ( 'key' )->in ( $keyWord );
		}
		// set order
		if ($orderBy) {
			$qb = Util::addOrder ( $qb, $orderBy );
		}

		// set limit
		if (isset ( $from ) && isset ( $to )) {
			$qb = $qb->limit ( $to - $from )->skip ( $from );
		}
		$result = $qb->getQuery ()->execute ();
		return $result;
	}
	public function searchQuestion($query, $from = null, $to = null) {
		$queryString = Util::covertUnicode ( $query );
		if (! empty ( $queryString )) {
			$keywords = explode ( ' ', $queryString );
			foreach ( $keywords as $key => $value ) {
				$regexObj = new \MongoRegex ( "/^" . $value . "/i" );
				$keyWord [$key] = $regexObj;
			}
		} else {
			$keyWord = null;
		}

		$qb = $this->question->getQueryBuilder ();

		// set where for query

		if ($keyWord) {
			$qb = $qb->field ( 'key_word' )->in ( $keyWord );
		}

		// set limit
		if (isset ( $from ) && isset ( $to )) {
			$qb = $qb->limit ( $to - $from )->skip ( $from );
		}

		$result = $qb->getQuery ()->execute ();
		return $result;
	}
	/**
	 *
	 * @param array $select
	 * @param array $keyWord
	 * @param array $orderBy
	 * @param Int $from
	 * @param Int $to
	 * @param Boolean $isHydrate
	 * @return array
	 */
	public function searchKeyWordSubject($query, $orderBy, $from = null, $to = null, $isHydrate = false) {
		$queryString = Util::covertUnicode ( $query );
		if (! empty ( $queryString )) {
			$keywords = explode ( ' ', $queryString );
			foreach ( $keywords as $key => $value ) {
				$regexObj = new \MongoRegex ( "/^" . $value . "/i" );
				$keyWord [$key] = $regexObj;
			}
		} else {
			$keyWord = null;
		}

		$qb = $this->subject->getQueryBuilder ();
		// select field on collection u want to use
		if (isset ( $isHydrate )) {
			$qb = $qb->hydrate ( $isHydrate );
		}

		// set where for query

		if ($keyWord) {
			$qb = $qb->field ( 'key_word' )->in ( $keyWord );
		}
		// set order
		if ($orderBy) {
			$qb = Util::addOrder ( $qb, $orderBy );
		}

		// set limit
		if (isset ( $from ) && isset ( $to )) {
			$qb = $qb->limit ( $to - $from )->skip ( $from );
		}
		$subjects = $qb->getQuery ()->execute ();

		$result = array ();
		/* @var $subject \FAQ\FAQEntity\Subject */
		foreach ( $subjects as $key => $subject ) {
			// var_dump(count($subjects));
			$result = $result + ($subject ['key_word']);
		}
		return array_unique ( $result );
	}

	/**
	 *
	 * @param array $select
	 * @param array $keyWord
	 * @param array $orderBy
	 * @param Int $from
	 * @param Int $to
	 * @param Boolean $isHydrate
	 * @return array
	 */
	public function searchKeyWordMember($query, $orderBy, $from = null, $to = null, $isHydrate = false) {
		$queryString = Util::covertUnicode ( $query );
		if (! empty ( $queryString )) {
			$keywords = explode ( ' ', $queryString );
			foreach ( $keywords as $key => $value ) {
				$regexObj = new \MongoRegex ( "/^" . $value . "/i" );
				$keyWord [$key] = $regexObj;
			}
		} else {
			$keyWord = null;
		}

		$qb = $this->user->getQueryBuilder ();
		// select field on collection u want to use
		if (isset ( $isHydrate )) {
			$qb = $qb->hydrate ( $isHydrate );
		}

		// set where for query

		if ($keyWord) {
			$qb = $qb->field ( 'key_word' )->in ( $keyWord );
		}
		// set order
		if ($orderBy) {
			$qb = Util::addOrder ( $qb, $orderBy );
		}

		// set limit
		if (isset ( $from ) && isset ( $to )) {
			$qb = $qb->limit ( $to - $from )->skip ( $from );
		}
		$users = $qb->getQuery ()->execute ();

		$result = array ();
		/* @var $subject \FAQ\FAQEntity\Subject */
		foreach ( $users as $key => $user ) {
			// var_dump(count($subjects));
			$result = $result + ($user ['key_word']);
		}
		return array_unique ( $result );
	}
	public function searchTag($query, $type, $from, $to) {
		$qb = $this->tag->getQueryBuilder ();

		$queryString = Util::covertUnicode ( $query );
		if (! empty ( $queryString )) {
			$keywords = explode ( ' ', $queryString );
			foreach ( $keywords as $key => $value ) {
				$regexObj = new \MongoRegex ( "/^" . $value . "/i" );
				$keyWord [$key] = $regexObj;
			}
		} else {
			$keyWord = null;
		}
		// set where for query
		if ($keyWord) {
			$qb = $qb->field ( 'tag_name' )->in ( $keyWord );
		}
		if (! empty ( $type )) {
			$qb->field ( "type" )->equals ( $type );
		}
		// set limit
		if (isset ( $from ) && isset ( $to )) {
			$qb = $qb->limit ( $to - $from )->skip ( $from );
		}

		$tags = $qb->getQuery ()->execute ();
		return $tags;
	}
}

?>