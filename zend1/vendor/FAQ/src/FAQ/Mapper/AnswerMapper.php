<?php

namespace FAQ\Mapper;

use FAQ\DB\Db;
use FAQ\FAQEntity\Question;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQEntity\HistoryAnswer;
use FAQ\FAQEntity\Answer;
use FAQ\FAQCommon\Authcfg;
use FAQ\FAQEntity\Notify;
use FAQ\FAQEntity\UserSpam;

/**
 *
 * @author sang
 *
 */
class AnswerMapper extends Db {
	private $question;
	public function __construct() {
		parent::__construct ();
		$this->question = new Question ();
	}

	/**
	 *
	 * @author sang
	 * @param Answer $answer
	 */
	public function getOverviewAnswer($select, $userID, $subjectID, $orderBy, $from, $to, $isHydrate = true) {
		return $this->getAnswer ( $select, $userID, $subjectID, FAQParaConfig::QUESTION_STATUS_OPEN, $orderBy, $from, $to, 'OVERVIEW' );
	}

	/**
	 *
	 * @author sang
	 * @param Answer $answer
	 */
	public function getLikeAnswer($select, $userID, $subjectID, $orderBy, $from, $to, $isHydrate = true) {
		return $this->getAnswer ( $select, $userID, $subjectID, FAQParaConfig::QUESTION_STATUS_OPEN, $orderBy, $from, $to, 'LIKE' );
	}

	/**
	 *
	 * @author sang
	 * @param Answer $answer
	 */
	public function getDislikeAnswer($select, $userID, $subjectID, $orderBy, $from, $to, $isHydrate = true) {
		return $this->getAnswer ( $select, $userID, $subjectID, FAQParaConfig::QUESTION_STATUS_OPEN, $orderBy, $from, $to, 'DISLIKE' );
	}

	/**
	 *
	 * @author sang
	 * @param Answer $answer
	 */
	public function getBestAnswer($select, $userID, $subjectID, $orderBy, $from, $to, $isHydrate = true) {
		return $this->getAnswer ( $select, $userID, $subjectID, FAQParaConfig::QUESTION_STATUS_OPEN, $orderBy, $from, $to, 'BEST' );
	}
	private function getAnswer($select, $userID, $subjectID, $status, $orderBy, $from, $to, $type, $isHydrate = true) {
		// get query builder
		$qb = $this->question->getQueryBuilder ();
		// select field on collection u want to use
		if (isset ( $isHydrate )) {
			$qb = $qb->hydrate ( $isHydrate );
		}
		if (! empty ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		// set where for query
		if (empty ( $userID )) {
			$userID = Util::getIDCurrentUser ();
		}

		// $qb->field("answer.create_by.id")->equals($userID);
		$qb->field ( "answer.create_by.\$id" )->equals ( new \MongoId ( $userID ) );
		// $qb->where("function() { return this.answer.create_by.\$id == ObjectId('52765e189df815f8260006bf'); }");

		if (isset ( $status )) {
			$qb->field ( "status" )->equals ( $status );
		}

		// var_dump($subjectID);
		if (! empty ( $subjectID ) && $subjectID != "-1") {
			$qb->field ( 'subject.id' )->equals ( $subjectID );
		}

		if ($type == 'LIKE') {
			$qb->field ( "answer" )->elemMatch ( $qb->expr ()->where ( "function() { return this.total_like > this.total_dislike; }" ) );
			// $qb->field("answer.total_like")->gt(0);
		} elseif ($type == 'DISLIKE') {

			// $qb->field("answer.total_dislike")->gt(0);
			$qb->field ( "answer" )->elemMatch ( $qb->expr ()->where ( "function() { return this.total_like < this.total_dislike; }" ) );
		} elseif ($type == 'BEST') {
			$qb->field ( "answer.is_best" )->equals ( true );
		}

		$totalDocument = $qb->getQuery ()->count ();

		Util::writeLog ( $totalDocument );

		// set order
		if ($orderBy) {
			$qb = Util::addOrder ( $qb, $orderBy );
		}

		// set limit
		if (isset ( $from ) && isset ( $to )) {
			$qb = $qb->limit ( $to - $from )->skip ( $from );
		}

		$questions = $qb->getQuery ()->execute ();

		$data = array (
				"totalDocument" => $totalDocument,
				"questions" => $questions
		);

		return $data;
	}
	/**
	 *
	 * @param Question $question
	 * @param Answer $answerEdit
	 * @param String $oldContent
	 * @param String $newContent
	 * @param String $noteEdit
	 */
	public function updateWikistyle($question, $answerEdit, $oldContent, $newContent, $noteEdit) {
		/* @var $answerEdit /FAQ/FAQEntity/Answer */
		$userEdit = Util::getCurrentUser ();
		$userCreateAnswer = $answerEdit->getCreateBy ();
		$userCreateAnswerID = $userCreateAnswer->getId ();

		if (count ( $answerEdit->getHistoryContent () ) == 0) {
			// save history
			$historyAnswer = new HistoryAnswer ();
			$historyAnswer->setContent ( $oldContent );
			$historyAnswer->setCreateBy ( $userCreateAnswer );
			$historyAnswer->setDateCreated ( $answerEdit->getDateCreated () );
			$historyAnswer->setIsActive ( FAQParaConfig::STATUS_ACTIVE );
			$answerEdit->setHistoryContent ( $historyAnswer );
		}

		// save curent content
		$currentAnswer = new HistoryAnswer ();
		$currentAnswer->setContent ( $newContent );
		$currentAnswer->setCreateBy ( $userEdit );
		$currentAnswer->setDateCreated ( Util::getCurrentTime () );

		$roleUserEdit = $userEdit->getRoleCode ();
		$rankPointUserEdit = $userEdit->getTotalRankPoint ();
		if ($userCreateAnswerID == $userEdit->getId () || $roleUserEdit == Authcfg::ADMIN || $rankPointUserEdit >= Authcfg::EDIT_QUESTIONS_AND_ANSWERS) {
			$historyContents = $answerEdit->getHistoryContent ();

			/* @var $historyContent /FAQ/FAQEntity/HistoryAnswer */
			foreach ( $historyContents as $key => $historyContent ) {
				$historyContent->setIsActive ( FAQParaConfig::STATUS_DEACTIVE );
			}
			// coppy to question
			if (! empty ( $newContent )) {
				$answerEdit->setContent ( $newContent );
			}
			$question->setIsApproveEditAnswer(FAQParaConfig::IS_APPROVE_EDIT_ANSWER_ACCESS);
			$question->setDateupdatedApproveEditAnswer(Util::getCurrentTime());
			$currentAnswer->setIsActive ( FAQParaConfig::STATUS_ACTIVE );
		} else {
			$question->setIsApproveEditAnswer(FAQParaConfig::IS_APPROVE_EDIT_ANSWER_NOTACCESS);
			$question->setDateupdatedApproveEditAnswer(Util::getCurrentTime());
			$currentAnswer->setIsActive ( FAQParaConfig::STATUS_DEACTIVE );
		}
		$answerEdit->setHistoryContent ( $currentAnswer );

		if ($userCreateAnswerID != $userEdit->getId ()) {

			// send notify to user's created question
			$content = Util::html2txt ( $answerEdit->getContent (), FAQParaConfig::TYPE_TRIP_HTML );

			$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
			$contentNotify = "đã sửa câu trả lời: " . $subContent . "...";

			$notify = new Notify ();
			$notify->setQuestion ( $question );
			$notify->setContent ( $contentNotify );
			$notify->setDateUpdated ( Util::getCurrentTime () );
			$notify->setStatus ( FAQParaConfig::TYPE_NOTIFY_STATUS );
			$notify->setType ( FAQParaConfig::TYPE_NOTIFY_EDIT_WIKISTYLE_QUESTION );
			$notify->setUserCreateNotify ( $userEdit );
			$userCreateAnswer->setNotify ( $notify );
			$userCreateAnswer->setTotalNewNotify ( 1 + $userCreateAnswer->getTotalNewNotify () );
		}

		$this->commit ();
		return 1;
	}
	/**
	 *
	 * @param Question $question
	 * @param String $noteEdit
	 * @param String $newTitle
	 * @param String $newContent
	 * @param Subject $newSubject
	 * @param array $newTag
	 */
	public function setContentActive($questionID, $answerID, $activeContentID) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 3;
		}

		$currentUser = Util::getCurrentUser ();
		$rankPointCurrentUser = $currentUser->getTotalRankPoint ();
		$curentRoleCode = $currentUser->getRoleCode ();

		$historyAnswer = null;
		$answers = $question->getAnswer ();
		foreach ( $answers as $key => $answer ) {
			// var_dump($answerID);
			// var_dump($answer->getId ());
			if ($answer->getId () == $answerID) {
				$historyAnswer = $answer;

				break;
			}
		}
		if (empty ( $historyAnswer )) {
			return 4;
		}

		$userCreateAnswerID = $historyAnswer->getCreateBy ()->getId ();
		if ($userCreateAnswerID != Util::getIDCurrentUser () && $curentRoleCode != Authcfg::ADMIN && $rankPointCurrentUser < Authcfg::EDIT_QUESTIONS_AND_ANSWERS) {
			return 5;
		}

		$historyContents = $historyAnswer->getHistoryContent ();
		$newContent = null;
		$userCreateNewContent = null;
		$userCreateNewContentID = "";
		$isExist = false;
		/* @var $historyContent /FAQ/FAQEntity/HistoryAnswer */
		foreach ( $historyContents as $key => $historyContent ) {
			$myHistoryContentID = $historyContent->getId ();
			if ($activeContentID == $myHistoryContentID) {
				$historyContent->setIsActive ( FAQParaConfig::STATUS_ACTIVE );

				$newContent = $historyContent->getContent ();

				$userCreateNewContent = $historyContent->getCreateBy ();
				$userCreateNewContentID = $userCreateNewContent->getId ();
				$isExist = true;
			} else {
				$historyContent->setIsActive ( FAQParaConfig::STATUS_DEACTIVE );
			}
		}
		// coppy to question

		if ($isExist) {

			if (! empty ( $newContent )) {
				$historyAnswer->setContent ( $newContent );
			}

			// send notify to user edit
			if ($userCreateNewContentID != Util::getIDCurrentUser () && $isExist) {
				// send notify to user's created question
				$content = Util::html2txt ( $newContent, FAQParaConfig::TYPE_TRIP_HTML );
				$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
				$contentNotify = "cập nhật sửa đổi câu trả lời: " . $subContent . "...";
				$notify = new Notify ();
				$notify->setQuestion ( $question );
				$notify->setContent ( $contentNotify );
				$notify->setDateUpdated ( Util::getCurrentTime () );
				$notify->setStatus ( FAQParaConfig::TYPE_NOTIFY_STATUS );
				$notify->setType ( FAQParaConfig::TYPE_NOTIFY_ACTIVE_WIKISTYLE_ANSWER );
				$notify->setUserCreateNotify ( $currentUser );
				$userCreateNewContent->setNotify ( $notify );
				$userCreateNewContent->setTotalNewNotify ( 1 + $userCreateNewContent->getTotalNewNotify () );
			}
			//CONTROL EDIT WIKI
			$question->setIsApproveEditAnswer(FAQParaConfig::IS_APPROVE_EDIT_ANSWER_ACCESS);
			$question->setDateupdatedApproveEditAnswer(Util::getCurrentTime());
			$this->commit ();
		} else {
			return 0;
		}
		return 1;
	}
	/**
	 *
	 * @author izzi,sang
	 * @todo report question as spam. return true if DB be update, else return false
	 * @param String $questionID
	 * @param User $userSpam
	 * @throws \Exception
	 */
	public function reportSpam($questionID, $answerID, $userSpam, $typespam) {
		// check total vote per one day
		$totalFlagOneDay = $userSpam->getTotalFlagOneDay ();
		$lastDayFlag = $userSpam->getFlagDay ();
		$currentDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
		// var_dump("currentdate",$currentDate);
		// var_dump("last date stamp: ",$lastDayFlag->getTimestamp ());
		// var_dump("last date like: ",$lastDayFlag);
		// past 1 days
		if (! empty ( $lastDayFlag )) {
			if (($totalFlagOneDay >= FAQParaConfig::FLAG_MAX_TOTAL_PER_ONE_DAY) && (($currentDate) - ($lastDayFlag->getTimestamp ()) < 1 * 24 * 60 * 60)) {
				return 5;
			} elseif ((($currentDate) - ($lastDayFlag->getTimestamp ()) > 1 * 24 * 60 * 60)) {
				$userSpam->setTotalFlagOneDay ( 0 );
			}
		}
		// update total vote perday
		$userSpam->setTotalFlag ( $userSpam->getTotalFlag () + 1 );
		$userSpam->setTotalFlagOneDay ( $userSpam->getTotalFlagOneDay () + 1 );
		$today = getdate ();
		$userSpam->setFlagDay ( Util::createDate ( $today ["mday"], $today ["mon"], $today ["year"] ) );
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 0;
		}

		if ($question->isContainStatus(FAQParaConfig::QUESTION_STATUS_CLOSE)) {
			return 0;
		}
		$answers = $question->getAnswer ();
		$answerVoteSpam = null;
		foreach ( $answers as $key => $answer ) {
			$myAnswerID = $answer->getId ();
			if ($answerID == $myAnswerID) {
				$answerVoteSpam = $answer;
				break;
			}
		}
		if (empty ( $answerVoteSpam )) {
			return 2;
		}
		$userSpams = $answerVoteSpam->getUserSpam ();
		foreach ( $userSpams as $key => $us ) {
			$usCreateByID = $us->getCreateBy ()->getId ();
			if ($userSpam->getId () == $usCreateByID) {
				// reported
				return 3;
			}
		}
		$userSpamObject = new UserSpam ();
		$userSpamObject->setCreateBy ( $userSpam );
		$userSpamObject->setDateUpdated ( Util::getCurrentTime () );
		$userSpamObject->setType ( $typespam );
		$answerVoteSpam->setUserSpam ( $userSpamObject );

		// izzi update point
		// $userMapper = new UserMapper ();
		// $userMapper->updatePointByMarkQuestionAsSpam ( Util::getCurrentUser (), $question->getCreateBy (), $question );
		// send notify user follow subject of question
		// send notify to user's created question
		$content = Util::html2txt ( $answerVoteSpam->getContent (), FAQParaConfig::TYPE_TRIP_HTML );
		$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
		$contentNotify = "Báo câu trả lời vi phạm: " . $subContent . "...";
		$notify = new Notify ();
		$notify->setQuestion ( $question );
		$notify->setContent ( $contentNotify );
		$notify->setDateUpdated ( Util::getCurrentTime () );
		$notify->setStatus ( FAQParaConfig::TYPE_NOTIFY_STATUS );
		$notify->setType ( FAQParaConfig::TYPE_NOTIFY_REPORT_ANSWER );
		$notify->setUserCreateNotify ( Util::getCurrentUser () );
		$userSpam->setNotify ( $notify );
		$userSpam->setTotalNewNotify ( 1 + $userSpam->getTotalNewNotify () );

		$this->commit ();

		return 1;
	}
}

?>