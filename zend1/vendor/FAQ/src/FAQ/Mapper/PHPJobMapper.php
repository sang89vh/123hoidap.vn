<?php

namespace FAQ\Mapper;

use FAQ\DB\Db;
use FAQ\FAQEntity\KeyWord;
use FAQ\FAQEntity\Subject;
use FAQ\FAQEntity\User;
use FAQ\FAQEntity\Question;
use FAQ\FAQEntity\Location;
use FAQ\FAQCommon\Util;
use MongoDate;
use DateTime;
use Exception;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQCommon\Authcfg;
use FAQ\FAQCommon\Usercfg;
use FAQ\FAQEntity\Skill;

/**
 *
 * @author sang
 *
 */
class PHPJobMapper extends Db {
	private $keyword;
	private $subject;
	private $location;
	private $user;
	private $question;
	private $skill;
	public function __construct() {
		parent::__construct ();

		$this->keyword = new KeyWord ();
		$this->subject = new Subject ();
		$this->location = new Location ();
		$this->user = new User ();
		$this->question = new Question ();
		$this->skill = new Skill ();
	}
	private function fnUpdateKeywordSubject($desc, $key, $arrayKey, $subject) {
		try {

			/* @var $keyword \FAQ\FAQEntity\KeyWord */
			$keyword = $this->keyword->findOneBy ( array (
					'desc' => $desc
			) );
			if (! isset ( $keyword )) {
				$keyword = new KeyWord ();
			} else {
				$keyword->setStatusUpdateRefere ();
			}
			if (! empty ( $subject )) {
				$keyword->setSubject ( $subject );
			}

			$keyword->setDesc ( $desc );
			// check contain key after?
			$keys = $keyword->getKey ();

			if (! empty ( $key )) {
				if (! in_array ( $key, $keys, true )) {
					$keyword->setKey ( $key );
				}
			}
			if (! empty ( $arrayKey )) {
				foreach ( $arrayKey as $keyarr => $arrkey ) {
					// check contain key after?
					$keys = $keyword->getKey ();
					$key = Util::covertUnicode ( $arrkey );

					if (! in_array ( $key, $keys, true )) {
						$keyword->setKey ( $key );
					}
				}
			}
			$keyword->setType ( "subject" );
			$keyword->setDateUpdated ( Util::getCurrentTime () );
			$keyword->insert ();
			$this->commit ();
		} catch ( Exception $e ) {
			Util::writeLog ( $e->getMessage (), \Zend\Log\Logger::ERR );
		}
	}
	private function fnUpdateKeywordSkill($desc, $key, $arrayKey, $skill) {
		try {

			/* @var $keyword \FAQ\FAQEntity\KeyWord */
			$keyword = $this->keyword->findOneBy ( array (
					'desc' => $desc
			) );
			if (! isset ( $keyword )) {
				$keyword = new KeyWord ();
			} else {
				$keyword->setStatusUpdateRefere ();
			}

			if (! empty ( $skill )) {
				$keyword->setSkill ( $skill );
			}

			$keyword->setDesc ( $desc );
			// check contain key after?
			$keys = $keyword->getKey ();

			if (! empty ( $key )) {
				if (! in_array ( $key, $keys, true )) {
					$keyword->setKey ( $key );
				}
			}
			if (! empty ( $arrayKey )) {
				foreach ( $arrayKey as $keyarr => $arrkey ) {
					// check contain key after?
					$keys = $keyword->getKey ();
					$key = Util::covertUnicode ( $arrkey );

					if (! in_array ( $key, $keys, true )) {
						$keyword->setKey ( $key );
					}
				}
			}
			$keyword->setType ( "skill" );
			$keyword->setDateUpdated ( Util::getCurrentTime () );
			$keyword->insert ();
			$this->commit ();
		} catch ( Exception $e ) {
			Util::writeLog ( $e->getMessage (), \Zend\Log\Logger::ERR );
		}
	}
	private function fnUpdateKeywordQuestion($desc, $key, $arrayKey, $question) {
		try {

			/* @var $keyword \FAQ\FAQEntity\KeyWord */
			$keyword = $this->keyword->findOneBy ( array (
					'desc' => $desc
			) );
			if (! isset ( $keyword )) {
				$keyword = new KeyWord ();
			} else {
				$keyword->setStatusUpdateRefere ();
			}

			if (! empty ( $question )) {
				$keyword->setQuestion ( $question );
				$keyword->setSubject ( $question->getSubject () );
			}

			$keyword->setDesc ( $desc );
			// check contain key after?
			$keys = $keyword->getKey ();

			if (! empty ( $key )) {
				if (! in_array ( $key, $keys, true )) {
					$keyword->setKey ( $key );
				}
			}
			if (! empty ( $arrayKey )) {
				foreach ( $arrayKey as $keyarr => $arrkey ) {
					// check contain key after?
					$keys = $keyword->getKey ();
					$key = Util::covertUnicode ( $arrkey );

					if (! in_array ( $key, $keys, true )) {
						$keyword->setKey ( $key );
					}
				}
			}
			$keyword->setType ( "question" );
			$keyword->setDateUpdated ( Util::getCurrentTime () );
			$keyword->insert ();
			$this->commit ();
		} catch ( Exception $e ) {
			Util::writeLog ( $e->getMessage (), \Zend\Log\Logger::ERR );
		}
	}
	/**
	 *
	 * @param unknown $desc
	 * @param unknown $key
	 * @param unknown $arrayKey
	 * @param \FAQ\FAQEntity\User $user
	 */
	private function fnUpdateKeywordUser($desc, $key, $arrayKey, $user) {
		try {

			/* @var $keyword \FAQ\FAQEntity\KeyWord */
			$keyword = $this->keyword->findOneBy ( array (
					'desc' => $desc
			) );
			if (! isset ( $keyword )) {
				$keyword = new KeyWord ();
			} else {
				$keyword->setStatusUpdateRefere ();
			}

			if (! empty ( $user )) {
				$keyword->setUser ( $user );
			}

			$keyword->setDesc ( $desc );
			// check contain key after?
			$keys = $keyword->getKey ();

			if (! empty ( $key )) {
				if (! in_array ( $key, $keys, true )) {
					$keyword->setKey ( $key );
				}
			}
			if (! empty ( $arrayKey )) {
				foreach ( $arrayKey as $keyarr => $arrkey ) {
					// check contain key after?
					$keys = $keyword->getKey ();
					$key = Util::covertUnicode ( $arrkey );

					if (! in_array ( $key, $keys, true )) {
						$keyword->setKey ( $key );

					}
				}
			}
			$keyword->setType ( "member" );
			$keyword->setDateUpdated ( Util::getCurrentTime () );
			$keyword->insert ();
			$this->commit ();
		} catch ( Exception $e ) {
			Util::writeLog ( $e->getMessage (), \Zend\Log\Logger::ERR );
		}
	}
	private function fnUpdateKeywordLocation($desc, $key, $arrayKey, $location) {
		try {

			/* @var $keyword \FAQ\FAQEntity\KeyWord */
			$keyword = $this->keyword->findOneBy ( array (
					'desc' => $desc
			) );
			if (! isset ( $keyword )) {
				$keyword = new KeyWord ();
			} else {
				$keyword->setStatusUpdateRefere ();
			}

			if (! empty ( $location )) {
				$keyword->setLocation ( $location );
			}

			$keyword->setDesc ( $desc );
			// check contain key after?
			$keys = $keyword->getKey ();

			if (! empty ( $key )) {
				if (! in_array ( $key, $keys, true )) {
					$keyword->setKey ( $key );
				}
			}
			if (! empty ( $arrayKey )) {
				foreach ( $arrayKey as $keyarr => $arrkey ) {
					// check contain key after?
					$keys = $keyword->getKey ();
					$key = Util::covertUnicode ( $arrkey );

					if (! in_array ( $key, $keys, true )) {
						$keyword->setKey ( $key );
					}
				}
			}
			$keyword->setType ( "location" );
			$keyword->setDateUpdated ( Util::getCurrentTime () );
			$keyword->insert ();
			$this->commit ();
		} catch ( Exception $e ) {
			Util::writeLog ( $e->getMessage (), \Zend\Log\Logger::ERR );
		}
	}
	public function initKeyWord() {
		Util::writeLog ( "======================> start job init key word <======================", \Zend\Log\Logger::INFO );
		$subjects = $this->subject->getQueryBuilder ()->field ( "status" )->equals ( FAQParaConfig::STATUS_ACTIVE )->getQuery ()->execute ();
		/* @var $subject \FAQ\FAQEntity\Subject */
		foreach ( $subjects as $keysub => $subject ) {

			$desc = Util::covertUnicode ( $subject->getTitle () );
			$key = Util::covertUnicode ( $subject->getTitle () );
			$subjectKeyWords = $subject->getKeyWord ();

			$this->fnUpdateKeywordSubject ( $desc, $key, $subjectKeyWords, $subject );
		}

		Util::writeLog ( "======================> end job init key word<======================", \Zend\Log\Logger::INFO );
	}
	public function updateKeyWord($start, $end, $type) {
		Util::writeLog ( "======================> start job update key word", \Zend\Log\Logger::INFO );
		if ($type == "USER") {
			$qb = $this->user->getQueryBuilder ();

			$qb->field ( "date_created" )->gte ( $start )->field ( "date_created" )->lte ( $end )->field ( "status" )->lte ( 10 );
			$users = $qb->getQuery ()->execute ();
			Util::writeLog ( "==> start job update key word==>update user", \Zend\Log\Logger::INFO );
			/* @var $user \FAQ\FAQEntity\User */
			foreach ( $users as $key => $user ) {
                $user->updateKeyword();
				$desc = Util::covertUnicode ( $user->getFirstName () . " " . $user->getLastName () );
				$key = $desc;
				$this->fnUpdateKeywordUser ( $desc, $key, $user );
			}
		} elseif ($type == "QUESTION") {
			Util::writeLog ( "==> start job update key word==>update question", \Zend\Log\Logger::INFO );
			// question extraction keyword
			$qb = $this->question->getQueryBuilder ();
			$qb->field ( "date_created" )->gte ( $start )->field ( "date_created" )->lte ( $end )->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );
			$questions = $qb->getQuery ()->execute ();
			/* @var $question \FAQ\FAQEntity\Question */
			foreach ( $questions as $key => $question ) {

				$desc = Util::covertUnicode ( $question->getTitle () );
				$key = $desc;
				$keyQuestions = $question->getKeyWord ();

				$this->fnUpdateKeywordQuestion ( $desc, $key, $keyQuestions, $question );
				// Util::writeLog("Question");
			}
		} elseif ($type == "LOCATION") {
			Util::writeLog ( "==> start job update key word==>update location", \Zend\Log\Logger::INFO );
			$locations = $this->location->getQueryBuilder ()->field ( "status" )->equals ( FAQParaConfig::STATUS_ACTIVE )->getQuery ()->execute ();
			/* @var $location \FAQ\FAQEntity\Location */
			foreach ( $locations as $keylo => $location ) {

				$desc = Util::covertUnicode ( $location->getName () );
				$key = Util::covertUnicode ( $location->getName () );
				$locationKeywords = $location->getKeyWord ();
				$this->fnUpdateKeywordLocation ( $desc, $key, $locationKeywords, $location );
			}
		} elseif ($type == "SKILL") {
			Util::writeLog ( "==> start job update key word==>update skill", \Zend\Log\Logger::INFO );
			$skills = $this->skill->getQueryBuilder ()->field ( "status" )->equals ( FAQParaConfig::STATUS_ACTIVE )->getQuery ()->execute ();
			/* @var $skill \FAQ\FAQEntity\Skill */
			foreach ( $skills as $keylo => $skill ) {

				$desc = Util::covertUnicode ( $skill->getName () );
				$key = Util::covertUnicode ( $skill->getName () );
				$skillKeywords = $skill->getKeyWord ();
				$this->fnUpdateKeywordSkill ( $desc, $key, $skillKeywords, $skill );
			}
		}
		Util::writeLog ( " end job update key word<======================", \Zend\Log\Logger::INFO );
	}
}

?>