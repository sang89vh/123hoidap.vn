<?php

namespace FAQ\Mapper;

use FAQ\DB\Db;
use FAQ\FAQEntity\Question;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQEntity\User;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQEntity\Answer;
use FAQ\FAQEntity\Reply;
use FAQ\FAQEntity\Notify;
use FAQ\FAQEntity\Hashtag;
use FAQ\FAQCommon\Authcfg;
use FAQ\FAQEntity\HistoryContent;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Int;
use FAQ\FAQEntity\UserSpam;
use MongoDate;

/**
 *
 * @author izzi
 *
 */
class QuestionMapper extends Db {
	private $question;
	private $user;
	private $hashtag;
	public function __construct() {
		parent::__construct ();
		$this->question = new Question ();
		$this->user = new User ();
		$this->hashtag = new Hashtag ();
		// qapolo
	}

	/**
	 *
	 * @author izzi,sang
	 * @todo get list question by { type of question, subject, user)
	 * @param String $type_question
	 *        	(overview-list, open-list, close-list, spam-list, follow-list, askme-list)
	 * @param String $subject_id
	 * @param String $user_id
	 * @return \Doctrine\MongoDB\Cursor
	 */
	private function getListQuestion($type_question, $subject_id, $user_id, $from = null, $to = null, $select = null, $type = 0) {
		$question = $this->question;
		$qb = $question->getQueryBuilder ();
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		// status >0 is not delete
		$qb->field ( "status" )->gte ( 0 );
		if ($type_question == 'overview-list') {
			if (! empty ( $user_id )) {
				$qb = $qb->addOr ( array (
						"user_follow.id" => $user_id
				) )->addOr ( array (
						"create_by.id" => $user_id
				) );
				$messageMapper = new MessageMapper ();
				$messageIdArr = $messageMapper->getMessageToUserArray ( $user_id );
				foreach ( $messageIdArr as $msgid ) {
					$qb = $qb->addOr ( array (
							"chat_help.id" => $msgid
					) );
				}
			}
		}

		if ($type_question == 'open-list') {
			if (! empty ( $user_id )) {
				$qb = $qb->field ( "create_by.id" )->equals ( $user_id );
			}
			$qb = $qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );
		}
		if ($type_question == 'draft-list') {
			if (! empty ( $user_id )) {
				$qb = $qb->field ( "create_by.id" )->equals ( $user_id );
			}
			$qb = $qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_DRAFT );
		}

		if ($type_question == 'close-list') {
			if (! empty ( $user_id )) {
				$qb = $qb->field ( "create_by.id" )->equals ( $user_id );
			}
			$qb = $qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_CLOSE );
		}
		// used in /member/question-asking
		if ($type_question == 'overview-list-publish') {
			if (! empty ( $user_id )) {
				$qb = $qb->field ( "create_by.id" )->equals ( $user_id );
			}
			$qb = $qb->field ( "status" )->notEqual ( FAQParaConfig::QUESTION_STATUS_DRAFT );
		}
		if ($type_question == 'follow-list') {
			$qb = $qb->field ( "user_follow.id" )->equals ( $user_id );
		}
		if ($type_question == 'spam-list') {
			if (! empty ( $user_id )) {
				$qb = $qb->field ( "create_by.id" )->equals ( $user_id );
			}
			$qb = $qb->field ( "total_spam" )->gte ( FAQParaConfig::QUESTION_MAX_SPAM );
		}
		if ($type_question == 'askme-list') {
			if (! empty ( $user_id )) {
				$messageMapper = new MessageMapper ();
				$messageIdArr = $messageMapper->getMessageToUserArray ( $user_id );
				$qb = $qb->field ( "chat_help.id" )->in ( $messageIdArr );
			}
		}

		if (! empty ( $subject_id ) && $subject_id != "-1") {
			$qb = $qb->field ( "subject.id" )->equals ( $subject_id );
		}

		if ($type == 0) {
			$orderBy = array (
					"date_updated" => "desc",
					"date_created" => "desc"
			);
		} elseif ($type == 1) {
			$orderBy = array (
					"bonus_point" => "desc",
					"date_created" => "desc",
					"total_answer" => "asc"
			);
		} elseif ($type == 2) {
			$orderBy = array (

					"date_created" => "desc"
			);
			$qb->field ( "total_answer" )->equals ( 0 );
		}
		// count total question here;
		$totalDocument = $qb->getQuery ()->count ();

		$qb = Util::addOrder ( $qb, $orderBy );
		if (isset ( $from )) {
			$qb = $qb->skip ( $from );
		}
		if (isset ( $to )) {
			$qb = $qb->limit ( $to - $from );
		}

		$listQuestionCursor = $qb->getQuery ()->execute ();
		$listQuestionCursor->totalDocument = $totalDocument;
		return $listQuestionCursor;
	}

	/**
	 *
	 * @todo get Question object, not insert
	 * @return \FAQ\FAQEntity\Question
	 */
	public function getQuestion() {
		return $this->question;
	}

	/**
	 *
	 * @author sang
	 * @todo : create a Question and insert it, return this question
	 * @param $question \FAQ\FAQEntity\Question
	 * @return \FAQ\FAQEntity\Question
	 * @throws \Exception
	 */
	public function create($question) {
		$currentUser = Util::getCurrentUser ();
		if ($currentUser->getTotalMoneyPoint () < $question->getBonusPoint ()) {
			throw new \Exception ( "Không đủ điểm thưởng" );
		}
		$totalQuestion = $currentUser->getTotalQuestion ();
		if (empty ( $totalQuestion )) {
			$totalQuestion = 0;
		}
		$currentUser->setTotalQuestion ( $totalQuestion + 1 );
		$subject = $question->getSubject ();
		$subject->setTotalQuestion ( 1 + $subject->getTotalQuestion () );
		$currentUser->setTotalMoneyPoint ( $currentUser->getTotalMoneyPoint () - $question->getBonusPoint () );

		// send notify user follow subject of question
		$content = $question->getTitle ();
		$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
		$contentNotify = "đã đăng 1 câu hỏi: " . $subContent . "...";
		$this->addNotify ( $question->getCreateBy (), $contentNotify, FAQParaConfig::TYPE_NOTIFY_NEW_QUESTION, $question );

		$question->insert ();

		$this->commit ();
		return $question;
	}
	/**
	 *
	 * @param \FAQ\FAQEntity\Question $question
	 */
	public function crawler($question) {
		$currentUser = $question->getCreateBy ();
		$totalQuestion = $currentUser->getTotalQuestion ();
		if (empty ( $totalQuestion )) {
			$totalQuestion = 0;
		}

		$currentUser->setTotalQuestion ( $totalQuestion + 1 );
		$subject = $question->getSubject ();
		$subject->setTotalQuestion ( 1 + $subject->getTotalQuestion () );

		$question->insert ();

		$this->commit ();
		return $question;
	}
	/**
	 *
	 * @author sang
	 * @param \FAQ\FAQEntity\Question $question
	 */
	public function updateTotalView($question) {
		$question->setTotalView ( 1 + $question->getTotalView () );
		$this->commit ();
	}
	/**
	 *
	 * @author izzi, sang
	 * @param \FAQ\FAQEntity\Question $question
	 * @todo note this error data of subject then don't update status
	 */
	public function update($question, $isCreateNew = false, $oldSubject = null) {
		$dateUpdateBest = $question->getDate_update_best ();
		if (! empty ( $dateUpdateBest )) {
			return 2;
		}
		if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_OPEN )) {
			// update total point
			// Find the user and update total money point
			// $this->user->getQueryBuilder()
			// ->findAndUpdate()
			// ->field('id')
			// ->equals(Util::getIDCurrentUser())
			// ->field('total_money_point')
			// ->set(Util::getCurrentUser()->getTotalMoneyPoint() - $question->getBonusPoint())
			// ->getQuery()
			// ->execute();
			$createBy = $question->getCreateBy ();
			$createBy->setTotalMoneyPoint ( $createBy->getTotalMoneyPoint () - $question->getBonusPoint () + $question->getOldBonusPoint () );
			// send notify user follow subject of question
			$content = $question->getTitle ();
			$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
			$contentNotify = "đã đăng 1 câu hỏi: " . $subContent . "...";
			$this->addNotify ( $createBy, $contentNotify, FAQParaConfig::TYPE_NOTIFY_NEW_QUESTION, $question );
		}
		// increment total question
		if ($isCreateNew == true && FAQParaConfig::QUESTION_STATUS_OPEN) {
			$subject = $question->getSubject ();
			$subject->setTotalQuestion ( 1 + $subject->getTotalQuestion () );

			// izzi: update point - tam thoi su dung, phai dat o cho cho phu hop vi no co the bi lap nhieu lan khi sua bai viet
			// $userMapper = new UserMapper ();
			// $userMapper->updatePointByQuestionBeCreated ( Util::getCurrentUser (), null, $question );
		} elseif ($isCreateNew == false && $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE )) {
			$subject = $question->getSubject ();
			$subject->setTotalQuestion ( $subject->getTotalQuestion () - 1 );

			// // izzi: update point -
			// $userMapper = new UserMapper ();
			// $userMapper->updatePointByQuestionBeClosed ( Util::getCurrentUser (), null, $question );
		}
		if ($isCreateNew == false && $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_OPEN )) {
			$oldSubjectID = null;
			if (! empty ( $oldSubject )) {
				$oldSubjectID = $oldSubject->getId ();
			}
			$subject = $question->getSubject ();
			// update seubject
			if ($oldSubjectID != $subject->getId () && ! empty ( $oldSubject )) {
				$subject->setTotalQuestion ( $subject->getTotalQuestion () + 1 );
				$oldSubject->setTotalQuestion ( $oldSubject->getTotalQuestion () - 1 );
			}
		}
		// Util::writeLog("getOldBonusPoint=>" . $question->getOldBonusPoint());
		// Util::writeLog("getBonusPoint=>" . $question->getBonusPoint());
		$this->commit ();
		return 1;
	}
	/**
	 *
	 * @author izzi, sang
	 * @todo note this error data of subject then don't update status
	 * @param \FAQ\FAQEntity\Question $question
	 * @param Int $newBonusPoint
	 * @param String $newTitle
	 * @param String $newContent
	 * @param \FAQ\FAQEntity\Subject $newSubject
	 * @param String $newTag
	 * @param String $noteEdit
	 * @param \FAQ\FAQEntity\Subject $oldSubject
	 * @param String $oldContent
	 * @param String $oldTitle
	 * @param String $oldTag
	 */
	public function updateWikistyle($question, $newBonusPoint, $newTitle, $newContent, $newSubject, $newTag, $noteEdit, $oldSubject, $oldContent, $oldTitle, $oldTag) {
		if (strlen ( $newTitle ) < 8 || strlen ( $newContent ) < 15 || empty ( $noteEdit ) || empty ( $newTag )) {
			return 0;
		}

		$dateUpdateBest = $question->getDate_update_best ();
		if (! empty ( $dateUpdateBest )) {
			return 2;
		}

		$userEdit = Util::getCurrentUser ();
		$userCreateQuestion = $question->getCreateBy ();
		$userCreateQuestionID = $userCreateQuestion->getId ();
		if (count ( $question->getHistoryContent () ) == 0) {
			// save history
			$historyContent = new HistoryContent ();
			$historyContent->setContent ( $oldContent );
			$historyContent->setTitle ( $oldTitle );
			$historyContent->setDateCreated ( $question->getDateCreated () );
			$historyContent->setCreateBy ( $userCreateQuestion );
			$historyContent->setKeyWord ( $oldTag );
			$historyContent->setSubject ( $oldSubject );
			$historyContent->setIsActive ( FAQParaConfig::STATUS_ACTIVE );
			$historyContent->setBonusPoint ( $question->getBonusPoint () );
			$question->setHistoryContent ( $historyContent );
		}

		// save curent content
		$currentContent = new HistoryContent ();
		$currentContent->setContent ( $newContent );
		$currentContent->setTitle ( $newTitle );
		$currentContent->setDateCreated ( Util::getCurrentTime () );
		$currentContent->setCreateBy ( $userEdit );
		$currentContent->setKeyWord ( $newTag );
		$currentContent->setSubject ( $newSubject );
		$currentContent->setNoteEdit ( $noteEdit );
		$currentContent->setBonusPoint ( $newBonusPoint );

		$roleUserEdit = $userEdit->getRoleCode ();
		$rankPointUserEdit = $userEdit->getTotalRankPoint ();
		if ($userCreateQuestionID == Util::getIDCurrentUser () || $roleUserEdit == Authcfg::ADMIN || $rankPointUserEdit >= Authcfg::EDIT_QUESTIONS_AND_ANSWERS) {
			$historyContents = $question->getHistoryContent ();
			$newSubjectID = $newSubject->getId ();
			$currentSubjectID = $oldSubject->getId ();
			/* @var $historyContent /FAQ/FAQEntity/HistoryContent */
			foreach ( $historyContents as $key => $historyContent ) {
				$historyContent->setIsActive ( FAQParaConfig::STATUS_DEACTIVE );
			}
			// coppy to question
			if (! empty ( $newTitle )) {
				$question->setTitle ( $newTitle );
			}
			if (! empty ( $newContent )) {
				$question->setContent ( $newContent );
			}
			if ($newSubjectID != $currentSubjectID) {
				$newSubject->setTotalQuestion ( $newSubject->getTotalQuestion () + 1 );
				$oldSubject->setTotalQuestion ( $oldSubject->getTotalQuestion () - 1 );
				$question->setSubject ( $newSubject );
			}
			// var_dump($keyWords);
			$question->removeAllKeyWord ();
			$question->replaceKeyWord ( $newTag );

			$currentContent->setIsActive ( FAQParaConfig::STATUS_ACTIVE );
			// set status control edit
			$question->setIsApproveEditQuestion ( FAQParaConfig::IS_APPROVE_EDIT_QUESTION_ACCESS );
			$question->setDateupdatedApproveEditQuestion ( Util::getCurrentTime () );
		} else {
			$question->setIsApproveEditQuestion ( FAQParaConfig::IS_APPROVE_EDIT_QUESTION_NOTACCESS );
			$question->setDateupdatedApproveEditQuestion ( Util::getCurrentTime () );
			$currentContent->setIsActive ( FAQParaConfig::STATUS_DEACTIVE );
		}
		$question->setHistoryContent ( $currentContent );

		if ($userCreateQuestionID != Util::getIDCurrentUser ()) {

			// send notify to user's created question
			$content = Util::html2txt ( $question->getContent (), FAQParaConfig::TYPE_TRIP_HTML );

			if ($newBonusPoint == 0) {
				$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
				$contentNotify = "đã sửa câu hỏi: " . $subContent . "...";
			} else {
				$subContent = mb_substr ( $content, 0, 50, 'UTF-8' );
				$contentNotify = "đã sửa, tặng thêm " . $newBonusPoint . " điểm cho câu hỏi: " . $subContent . "...";
			}
			$notify = new Notify ();
			$notify->setQuestion ( $question );
			$notify->setContent ( $contentNotify );
			$notify->setDateUpdated ( Util::getCurrentTime () );
			$notify->setStatus ( FAQParaConfig::TYPE_NOTIFY_STATUS );
			$notify->setType ( FAQParaConfig::TYPE_NOTIFY_EDIT_WIKISTYLE_QUESTION );
			$notify->setUserCreateNotify ( $userEdit );
			$userCreateQuestion->setNotify ( $notify );
			$userCreateQuestion->setTotalNewNotify ( 1 + $userCreateQuestion->getTotalNewNotify () );
		}
		$userEdit->setTotalMoneyPoint ( $userEdit->getTotalMoneyPoint () - $newBonusPoint );
		$question->setBonusPoint ( $question->getBonusPoint () + $newBonusPoint );
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
	public function setContentActive($questionID, $activeContentID) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 3;
		}

		$currentUser = Util::getCurrentUser ();
		$rankPointCurrentUser = $currentUser->getTotalRankPoint ();
		$curentRoleCode = $currentUser->getRoleCode ();
		$userCreateQuestionID = $question->getCreateBy ()->getId ();
		if ($userCreateQuestionID != Util::getIDCurrentUser () && $curentRoleCode != Authcfg::ADMIN && $rankPointCurrentUser < Authcfg::EDIT_QUESTIONS_AND_ANSWERS) {
			return 4;
		}

		$oldSubject = $question->getSubject ();
		$oldSubjectID = $oldSubject->getId ();
		$historyContents = $question->getHistoryContent ();
		$newTitle = null;
		$newContent = null;
		$newSubject = null;
		$newSubjectID = null;
		$newTag = null;
		$userCreateNewContent = null;
		$userCreateNewContentID = "";
		$isExist = false;
		/* @var $historyContent /FAQ/FAQEntity/HistoryContent */
		foreach ( $historyContents as $key => $historyContent ) {
			$myHistoryContentID = $historyContent->getId ();
			if ($activeContentID == $myHistoryContentID) {
				$historyContent->setIsActive ( FAQParaConfig::STATUS_ACTIVE );
				$newTitle = $historyContent->getTitle ();
				$newContent = $historyContent->getContent ();
				$newSubject = $historyContent->getSubject ();
				$newSubjectID = $newSubject->getId ();
				$newTag = $historyContent->getKeyWord ();
				$userCreateNewContent = $historyContent->getCreateBy ();
				$userCreateNewContentID = $userCreateNewContent->getId ();
				$isExist = true;
			} else {
				$historyContent->setIsActive ( FAQParaConfig::STATUS_DEACTIVE );
			}
		}
		// coppy to question
		$currentSubjectID = $question->getSubject ()->getId ();
		if ($isExist) {
			if (! empty ( $newTitle )) {
				$question->setTitle ( $newTitle );
			}
			if (! empty ( $newContent )) {
				$question->setContent ( $newContent );
			}
			if ($newSubjectID != $oldSubjectID) {

				$newSubject->setTotalQuestion ( $newSubject->getTotalQuestion () + 1 );
				$oldSubject->setTotalQuestion ( $oldSubject->getTotalQuestion () - 1 );

				$question->setSubject ( $newSubject );
			}
			// var_dump($keyWords);
			$question->removeAllKeyWord ();
			$question->replaceKeyWord ( $newTag );

			// send notify to user edit
			if ($userCreateNewContentID != Util::getIDCurrentUser () && $isExist) {
				// send notify to user's created question
				$content = Util::html2txt ( $newContent, FAQParaConfig::TYPE_TRIP_HTML );
				$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
				$contentNotify = "cập nhật sửa đổi câu hỏi: " . $subContent . "...";
				$notify = new Notify ();
				$notify->setQuestion ( $question );
				$notify->setContent ( $contentNotify );
				$notify->setDateUpdated ( Util::getCurrentTime () );
				$notify->setStatus ( FAQParaConfig::TYPE_NOTIFY_STATUS );
				$notify->setType ( FAQParaConfig::TYPE_NOTIFY_ACTIVE_WIKISTYLE_QUESTION );
				$notify->setUserCreateNotify ( $currentUser );
				$userCreateNewContent->setNotify ( $notify );
				$userCreateNewContent->setTotalNewNotify ( 1 + $userCreateNewContent->getTotalNewNotify () );
			}
			// control edit
			$question->setIsApproveEditQuestion ( FAQParaConfig::IS_APPROVE_EDIT_QUESTION_ACCESS );
			$question->setDateupdatedApproveEditQuestion ( Util::getCurrentTime () );
			$this->commit ();
		} else {
			return 0;
		}
		return 1;
	}
	/**
	 *
	 * @author izzi,sang
	 * @todo this is do not delete the question permanent
	 * @param String $questionId
	 * @return 0 false, 1 success
	 */
	public function delete($questionId) {
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionId, true );
		if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_DRAFT )) {
			if ($this->deletePermanent ( $questionId )) {
				return 1;
			} else {
				return 0;
			}
		}
		$currentUser = Util::getCurrentUser ();
		$totalQuestion = $currentUser->getTotalQuestion ();
		if (empty ( $totalQuestion )) {
			$totalQuestion = 0;
		}

		$subject = $question->getSubject ();
		if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_OPEN )) {
			$currentUser->setTotalQuestion ( $totalQuestion - 1 );
			$subject->setTotalQuestion ( $subject->getTotalQuestion () - 1 );

			// // izzi: update point - khi nguoi dung xoa cau hoi cua minh
			// $userMapper = new UserMapper ();
			// $userMapper->updatePointByQuestionBeDeletedByYou ( Util::getCurrentUser (), null, $question );
		}
		// check owner data
		if ($question->getCreateBy ()->getId () == $currentUser->getId () || $currentUser->getRoleCode () == Authcfg::ADMIN) {

			$question->setStatus ( FAQParaConfig::QUESTION_STATUS_TEMP_DELETE );
		}
		$this->commit ();
		return 1;
	}

	/**
	 *
	 * @author izzi,sang
	 * @todo delete this question permanent
	 * @param String $questionId
	 * @return boolean
	 */
	public function deletePermanent($questionId) {
		$statusAccess = false;

		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionId, true );
		$currentUser = Util::getCurrentUser ();
		if (! empty ( $question )) {

			if (! $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_DRAFT )) {
				// delete some data relationship

				$totalQuesion = $currentUser->getTotalQuestion ();
				if (empty ( $totalQuesion )) {
					$totalQuesion = 0;
				}

				$subject = $question->getSubject ();
				if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_OPEN )) {
					$currentUser->setTotalQuestion ( $totalQuesion - 1 );
					$subject->setTotalQuestion ( $subject->getTotalQuestion () - 1 );
				}
			}
			// check owner data
			if ($question->getCreateBy ()->getId () == $currentUser->getId () || $currentUser->getRoleCode () == Authcfg::ADMIN) {

				// delete data
				$statusAccess = $this->question->remove ( $questionId );
			}
			$this->commit ();
		}
		return $statusAccess;
		// $this->question->getQueryBuilder()
		// ->findAndRemove()
		// ->field("id")
		// ->equals($questionId)
		// ->field("create_by.id")
		// ->equals(Util::getIDCurrentUser())
		// ->getQuery()
		// ->execute();
		// $question = new Question();
		// /* @var $question \FAQ\FAQEntity\Question */
		// $question = $question->find($questionId, true);
		// $isOwner = Util::checkOwnerData($question);

		// if (! $question || ! $isOwner)
		// return false;
		// $this->getDm()->remove($question);
		// return true;
	}

	/**
	 *
	 * @author izzi, sang
	 * @param String $userID
	 * @param String $subjectID
	 */
	public function getOverview($userID, $subjectID, $from = null, $to = null, $select) {
		return $this->getListQuestion ( "overview-list", $subjectID, $userID, $from, $to, $select );
	}

	/**
	 *
	 * @author izzi
	 * @param String $userID
	 * @param String $subjectID
	 */
	public function getOpenList($userID, $subjectID, $from = null, $to = null, $select = null, $type = 0) {
		return $this->getListQuestion ( "open-list", $subjectID, $userID, $from, $to, $select, $type );
	}

	/**
	 *
	 * @author sang
	 * @param String $userID
	 * @param String $subjectID
	 */
	public function getOverviewPublish($userID, $subjectID, $from = null, $to = null, $select) {
		return $this->getListQuestion ( "overview-list-publish", $subjectID, $userID, $from, $to, $select );
	}

	/**
	 *
	 * @author sang
	 * @param String $userID
	 * @param String $subjectID
	 */
	public function getDraftList($userID, $subjectID, $from = null, $to = null, $select) {
		return $this->getListQuestion ( "draft-list", $subjectID, $userID, $from, $to, $select );
	}

	/**
	 *
	 * @author izzi
	 * @param String $userID
	 * @param String $subjectID
	 */
	public function getClosedList($userID, $subjectID, $from = null, $to = null, $select) {
		return $this->getListQuestion ( "close-list", $subjectID, $userID, $from, $to, $select );
	}

	/**
	 *
	 * @author izzi
	 * @param String $userID
	 * @param String $subjectID
	 */
	public function getSpamList($userID, $subjectID, $from = null, $to = null, $select) {
		return $this->getListQuestion ( "spam-list", $subjectID, $userID, $from, $to, $select );
	}

	/**
	 *
	 * @author izzi
	 * @param String $userID
	 * @param String $subjectID
	 */
	public function getFollowList($userID, $subjectID, $from = null, $to = null, $select) {
		return $this->getListQuestion ( "follow-list", $subjectID, $userID, $select );
	}

	/**
	 *
	 * @author izzi
	 * @param String $userID
	 * @param String $subjectID
	 */
	public function getAskMeList($userID, $subjectID, $from = null, $to = null, $select) {
		return $this->getListQuestion ( "askme-list", $subjectID, $userID, $from, $to, $select );
	}

	/**
	 *
	 * @author izzi
	 * @todo get a question by questionID
	 * @param String $questionID
	 * @return Question
	 */
	public function getOneQuestion($questionID) {
		$question = $this->question->find ( $questionID, true );
		return $question;
	}

	/**
	 *
	 * @author izzi
	 * @todo get list question by status
	 * @param String $userID
	 * @param String $status
	 * @param String $subjectID
	 * @return Ambigous <\Doctrine\ODM\MongoDB\Query\mixed, \Doctrine\MongoDB\EagerCursor, \Doctrine\MongoDB\Cursor, Cursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
	 */
	public function getQuestionByStatus($userID, $status, $subjectID) {
		$qb = $this->question->getQueryBuilder ();
		if ($userID) {
			$qb = $qb->field ( "create_by.id" )->equals ( $userID );
		}
		if ($subjectID) {
			$qb = $qb->field ( "subject.id" )->equals ( $subjectID );
		}
		if ($status) {
			$qb = $qb->field ( "status" )->equals ( $status );
		}
		$list_question = $qb->getQuery ()->execute ();
		return $list_question;
	}

	/**
	 *
	 * @author izzi
	 * @todo get list member mark question as spam
	 * @param String $questionID
	 * @param int $from
	 * @param int $to
	 * @return \Doctrine\MongoDB\EagerCursor:
	 */
	public function getMemberSpam($questionID, $from, $to) {
		/* @var $question Question */
		$question = $this->question->find ( $questionID, true );
		/* @var $list_member \Doctrine\ODM\MongoDB\PersistentCollection */
		if (! $question)
			return array ();
		$list_member = $question->getSpam ();
		return $list_member->slice ( $from, $to );
	}

	/**
	 *
	 * @author sang
	 * @todo get list member answer
	 * @param String $questionID
	 * @param int $from
	 * @param int $to
	 * @return \Doctrine\MongoDB\EagerCursor
	 */
	public function getMemberAnswer($questionID, $from, $to) {

		/* @var $question Question */
		$question = $this->question->find ( $questionID, true );
		/* @var $list_member \Doctrine\ODM\MongoDB\PersistentCollection */
		if (! $question)
			return array ();
		$answers = $question->getAnswer ();
		$users = new ArrayCollection ();
		foreach ( $answers as $key => $answer ) {
			$answerCreateBy = $answer->getCreateBy ();
			if (! ($users->contains ( $answerCreateBy ))) {
				$users->add ( $answerCreateBy );
			}
		}
		return $users->slice ( $from, $to );
	}

	/**
	 *
	 * @author izzi,sang
	 * @todo get list member share a question
	 * @param String $questionID
	 * @param int $from
	 * @param int $to
	 * @return \Doctrine\MongoDB\EagerCursor
	 */
	public function getMemberShare($questionID, $from, $to) {
		$qb = $this->user->getQueryBuilder ()->field ( "share_question.id" )->equals ( $questionID );

		// set limit
		if (isset ( $from ) && isset ( $to )) {
			$qb = $qb->limit ( $to - $from )->skip ( $from );
		}

		$q = $qb->getQuery ();

		$users = $q->execute ();

		return $users;
	}

	/**
	 *
	 * @author izzi
	 * @todo get list member is following a question
	 * @param String $questionID
	 * @param String $from
	 * @param String $to
	 * @return \Doctrine\MongoDB\EagerCursor
	 */
	public function getMemberFollow($questionID, $from, $to) {
		$user = new User ();
		$qb = $user->getQueryBuilder ();
		$qb = $qb->field ( "follow_question.id" )->equals ( $questionID );
		if (isset ( $from )) {
			$qb = $qb->skip ( $from );
		}
		if (isset ( $to )) {
			$qb = $qb->limit ( $to - $from );
		}
		$listMemberFollow = $qb->getQuery ()->execute ();
		return $listMemberFollow;
	}

	/**
	 *
	 * @author izzi
	 * @todo check a question be marked as spam by a member. return true if marked, else return false
	 * @param String $questionID
	 * @param String $userID
	 * @return boolean
	 */
	private function isQuestionSpamByMember($questionID, $userID) {
		$user = new User ();
		$qb = $user->getQueryBuilder ();
		$qb = $qb->field ( "id" )->equals ( $userID );
		$qb = $qb->field ( "spam_question.id" )->equals ( $questionID );
		$spam_num = $qb->getQuery ()->count ();
		if ($spam_num)
			return false;
		return true;
	}

	/**
	 *
	 * @author izzi,sang
	 * @todo report question as spam. return true if DB be update, else return false
	 * @param String $questionID
	 * @param User $userSpam
	 * @throws \Exception
	 */
	public function reportSpam($questionID, $userSpam, $typespam = null) {
		$currentTotalRankPoint = $userSpam->getTotalRankPoint ();
		$isCastOpenAndReopen = ($currentTotalRankPoint >= Authcfg::CAST_CLOSE_AND_REOPEN_VOTES);
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
		if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE )) {
			return 0;
		}
		$question->setStatusUpdateRefere ();
		$question->setSpam ( $userSpam );
		$question->incTotalSpam ( $userSpam, $isCastOpenAndReopen );
		// izzi update point
		// $userMapper = new UserMapper ();
		// $userMapper->updatePointByMarkQuestionAsSpam ( Util::getCurrentUser (), $question->getCreateBy (), $question );
		// send notify user follow subject of question
		// send notify to user edit

		// send notify to user's created question
		$content = Util::html2txt ( $question->getContent (), FAQParaConfig::TYPE_TRIP_HTML );
		$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
		$contentNotify = "Báo câu hỏi vi phạm: " . $subContent . "...";
		$notify = new Notify ();
		$notify->setQuestion ( $question );
		$notify->setContent ( $contentNotify );
		$notify->setDateUpdated ( Util::getCurrentTime () );
		$notify->setStatus ( FAQParaConfig::TYPE_NOTIFY_STATUS );
		$notify->setType ( FAQParaConfig::TYPE_NOTIFY_REPORT_QUESTION );
		$notify->setUserCreateNotify ( Util::getCurrentUser () );
		$userSpam->setNotify ( $notify );
		$userSpam->setTotalNewNotify ( 1 + $userSpam->getTotalNewNotify () );

		// set user spam for mod tool
		if (! empty ( $typespam )) {
			$uSpam = new UserSpam ();
			$uSpam->setCreateBy ( $userSpam );
			$uSpam->setDateUpdated ( Util::getCurrentTime () );
			$uSpam->setType ( $typespam );
			$question->setUserSpam ( $uSpam );
		}
		if ($isCastOpenAndReopen) {
			$this->closeQuestion ( $questionID, false );
		}
		$this->commit ();

		return 1;
	}

	/**
	 *
	 * @author izzi,sang
	 * @todo unmark question as spam. return true if DB be update, else return false
	 * @param String $questionID
	 * @param \FAQ\FAQEntity\User $userSpam
	 */
	public function unReportSpam($questionID, $userSpam) {
		$currentTotalRankPoint = $userSpam->getTotalRankPoint ();
		$isCastOpenAndReopen = ($currentTotalRankPoint >= Authcfg::CAST_CLOSE_AND_REOPEN_VOTES);
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );
		$question->setStatusUpdateRefere ();
		$question->getSpam ()->removeElement ( $userSpam );
		$question->descTotalSpam ( $userSpam, $isCastOpenAndReopen );
		$userSpam->getSpamQuestion ()->removeElement ( $question );
		// izzi update point
		// $userMapper = new UserMapper ();
		// $userMapper->updatePointByMarkQuestionAsUnspam ( Util::getCurrentUser (), $question->getCreateBy (), $question );
		// send notify user follow subject of question
		$contentNotify = "rút lại báo câu hỏi của bạn vi phạm quy định của hệ thống";
		$this->addNotify ( $userSpam, $contentNotify, FAQParaConfig::TYPE_NOTIFY_UNREPORT_QUESTION, $question );

		// $userSpam->setStatusUpdateRefere();
		if ($isCastOpenAndReopen) {
			$this->reopenQuestion ( $questionID, false );
		}
		$this->commit ();
	}

	/**
	 *
	 * @author izzi
	 * @todo check a question is shared by a member. return true if shared, else return false
	 * @param String $questionID
	 * @param String $memberID
	 * @return boolean
	 */
	private function isQuestionSharedByMember($questionID, $memberID) {
		$question = $this->question;
		$qb = $question->getQueryBuilder ();
		$qb = $qb->field ( "id" )->equals ( $questionID );
		$qb = $qb->field ( "share.create_by.id" )->equals ( $userID );
		$share_num = $qb->getQuery ()->count ();
		if (! $share_num)
			return false;
		return true;
	}

	/**
	 *
	 * @author izzi, sang
	 * @todo share a question. return true if DB is update, else return false;<br/>
	 *       if user has shared question then the question is sharing more
	 * @param String $questionID
	 * @return boolean
	 * @throws \Exception
	 */
	public function shareQuestion($questionID, $userShare) {
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );
		$question->setStatusUpdateRefere ();
		$question->setShare ( $userShare );
		$question->incTotalShare ();
		// $userShare->setStatusUpdateRefere();

		$this->commit ();
		return true;
	}

	/**
	 *
	 * @author izzi
	 * @todo check question is following by a member. return if followed, else return false
	 * @param String $questionID
	 * @param String $userID
	 * @return boolean
	 */
	private function isQuestionFollowedByMember($questionID, $userID) {
		$user = new User ();
		$qb = $user->getQueryBuilder ();
		$qb = $qb->field ( "id" )->equals ( $userID );
		$qb = $qb->field ( "follow_question.id" )->equals ( $questionID );
		$follow_num = $qb->getQuery ()->count ();
		if (! $follow_num)
			return false;
		return true;
	}

	/**
	 *
	 * @author izzi,sang
	 * @todo member do follow a question. return true if DB is update, else return false;
	 * @param String $questionID
	 * @param User $userFollow
	 * @todo add new member follow question
	 * @throws \Exception
	 */
	public function followQuestion($questionID, $userFollow) {
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );

		if (empty ( $question )) {
			return 0;
		}
		if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE )) {
			return 0;
		}
		$question->setStatusUpdateRefere ();
		$question->setUserFollow ( $userFollow );
		$question->incTotalFollow ();
		// $userFollow->setStatusUpdateRefere();

		// // izzi update point
		// $userMapper = new UserMapper ();
		// $userMapper->updatePointByFollowQuestion ( Util::getCurrentUser (), $question->getCreateBy (), $question );
		// $userFollow->setStatusUpdateRefere ();
		// $question->setStatusUpdateRefere();
		$this->commit ();
		return 1;
	}

	/**
	 *
	 * @author izzi
	 * @todo member skip follow a question. return true if DB is update, else return false.
	 * @param String $questionID
	 * @param User $userFollow
	 * @todo add new member follow question
	 * @throws \Exception
	 */
	public function unFollowQuestion($questionID, $userFollow) {
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );
		$question->setStatusUpdateRefere ();
		$question->getUserFollow ()->removeElement ( $userFollow );
		$userFollow->getFollowQuestion ()->removeElement ( $question );
		$question->descTotalFollow ();

		// $userFollow->setStatusUpdateRefere();
		// // izzi update point
		// $userMapper = new UserMapper ();
		// $userMapper->updatePointByUnFollowQuestion ( Util::getCurrentUser (), $question->getCreateBy (), $question );
		// $question->setStatusUpdateRefere();
		$userFollow->setStatusUpdateRefere ();
		$this->commit ();
	}
	public function getMultiAnswer($questionID, $orderBy, $from, $to) {
	}

	/**
	 *
	 * @author sang
	 * @param String $type
	 * @param String $parentAnswerID
	 * @param String $content
	 * @param String $questionID
	 * @param String $createByID
	 * @throws \Exception
	 * @todo add new Answer for question
	 * @return \FAQ\FAQEntity\Answer \FAQ\FAQEntity\Reply
	 */
	public function addAnswer($type, $parentAnswerID, $content, $questionID, $createByID, $totalRankPointUser = null, $isWikiPost = false) {
		$userEntity = new User ();
		/* @var $createBy \FAQ\FAQEntity\User */
		$createBy = $userEntity->find ( $createByID, true );

		// increment total answer
		$createBy->setTotalAnswer ( 1 + $createBy->getTotalAnswer () );
		// find question
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );

		$isProtectQuestion = $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_PROTECT );
		if ($isProtectQuestion && (empty ( $totalRankPointUser ) || $totalRankPointUser < Authcfg::REMOVE_NEW_USER_RESTRICTIONS)) {
			throw new \Exception ( "Điểm câu hỏi không đủ để trả lời câu hỏi được [Bảo vệ]" );
		}
		$question->setStatusUpdateRefere ();

		// send notify to Author's, who's answer question

		$subContent = mb_substr ( strip_tags ( $content ), 0, 70, 'UTF-8' );
		$contentNotify = "đã trả lời: " . $subContent . "...";
		$this->addNotify ( $createBy, $contentNotify, FAQParaConfig::TYPE_NOTIFY_ANSWER_QUESTION, $question );
		// update total Answer
		$question->setTotalAnswer ( $question->getTotalAnswer () + 1 );
		// because answer can be bad or good, so not add point, point will be caculate when another like or dislike.
		// new Answer
		if ($type == "ANSWER" && empty ( $parentAnswerID )) {
			$answer = new Answer ();
			$answer->setTotalDislike ( 0 );
			$answer->setTotalLike ( 0 );
			$answer->setIsBest ( false );
			$answer->setContent ( $content );
			$answer->setCreateBy ( $createBy );
			$answer->setDateCreated ( Util::getCurrentTime () );
			$answer->addStatus ( FAQParaConfig::QUESTION_STATUS_OPEN );
			// var_dump($isWikiPost);
			if ($isWikiPost || $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_WIKI_POST )) {
				$answer->addStatus ( FAQParaConfig::QUESTION_STATUS_WIKI_POST );
			}
			$question->setAnswer ( $answer );
			// var_dump($parentAnswerID);
			// $createBy->setStatusUpdateRefere();
			// $question->setStatusUpdateRefere();
			$this->commit ();
			// var_dump($answer);
			// var_dump($createBy);
			return $answer;
			// reply for Answer
		} else if ($type == "COMMENT" && ! empty ( $parentAnswerID )) {
			$reply = new Reply ();
			$reply->setContent ( $content );
			$reply->setCreateBy ( $createBy );
			$reply->setDateUpdated ( Util::getCurrentTime () );

			$answers = $question->getAnswer ();

			foreach ( $answers as $key => $value ) {
				/* @var $value \FAQ\FAQEntity\Answer */
				if ($value->getId () == $parentAnswerID) {
					$value->setReply ( $reply );
					break;
				}
			}

			$this->commit ();
			return $reply;
		}

		else {
			// reply1, reply2.....for COMMENT

			$childrenReply = new Reply ();
			$childrenReply->setContent ( $content );
			$childrenReply->setCreateBy ( $createBy );
			$childrenReply->setDateUpdated ( Util::getCurrentTime () );

			$answers = $question->getAnswer ();

			foreach ( $answers as $key => $value ) {
				/* @var $value \FAQ\FAQEntity\Answer */
				$replies = $value->getReply ();
				foreach ( $replies as $key => $reply ) {
					/* @var $reply \FAQ\FAQEntity\Reply */
					if ($reply->getId () == $parentAnswerID) {
						$reply->setChildren ( $childrenReply );
						goto label;
					}
				}
			}
			label:

			// $question->setStatusUpdateRefere();
			$this->commit ();
			return $childrenReply;
		}
	}
	public function crawlerAnswer($content, $questionID, $createByID, $isWikiPost) {
		$userEntity = new User ();
		/* @var $createBy \FAQ\FAQEntity\User */
		$createBy = $userEntity->find ( $createByID, true );
		if(empty($createBy)){
			throw new \Exception("not found create by user id:".$createByID);
		}

		// increment total answer
		$createBy->setTotalAnswer ( 1 + $createBy->getTotalAnswer () );
		// find question
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );
		if(empty($question)){
			throw new \Exception("not found question by question_id:".$questionID);
		}
		// update total Answer
		$question->setTotalAnswer ( $question->getTotalAnswer () + 1 );
		// because answer can be bad or good, so not add point, point will be caculate when another like or dislike.
		// new Answer

		$answer = new Answer ();
		$answer->setTotalDislike ( 0 );
		$answer->setTotalLike ( 0 );
		$answer->setIsBest ( false );
		$answer->setContent ( $content );
		$answer->setCreateBy ( $createBy );
		$answer->setDateCreated ( Util::getCurrentTime () );
		$answer->addStatus ( FAQParaConfig::QUESTION_STATUS_OPEN );
		// var_dump($isWikiPost);
		if ($isWikiPost || $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_WIKI_POST )) {
			$answer->addStatus ( FAQParaConfig::QUESTION_STATUS_WIKI_POST );
		}
		$question->setAnswer ( $answer );
		// var_dump($parentAnswerID);
		// $createBy->setStatusUpdateRefere();
		// $question->setStatusUpdateRefere();
		$this->commit ();
		// var_dump($answer);
		// var_dump($createBy);
		return $answer;
		// reply for Answer
	}
	private function addNotify($createBy, $conentNotify, $type, $question) {
		$notify = new Notify ();
		$array_to_user = new ArrayCollection ();

		/* @var $question \FAQ\FAQEntity\Question */
		$notify->setQuestion ( $question );

		$this->addUniqueUser ( $array_to_user, $question->getCreateBy () );
		// report spam Question
		if ($type == FAQParaConfig::TYPE_NOTIFY_REPORT_QUESTION) {
			goto label;
		}

		// add new question
		if ($type == FAQParaConfig::TYPE_NOTIFY_NEW_QUESTION) {
			$subject = $question->getSubject ();
			$userFollowSubjects = $subject->getUserFollow ();
			foreach ( $userFollowSubjects as $key => $userFollowSubject ) {

				$this->addUniqueUser ( $array_to_user, $userFollowSubject );
			}
			goto label;
		}
		$answers = $question->getAnswer ();
		/* @var $answer \FAQ\FAQEntity\Answer */
		foreach ( $answers as $key => $answer ) {

			$this->addUniqueUser ( $array_to_user, $answer->getCreateBy () );
			$likeAnswers = $answer->getLike ();
			foreach ( $likeAnswers as $key => $likeAnswer ) {

				$this->addUniqueUser ( $array_to_user, $likeAnswer );
			}
			$disLikeAnswer = $answer->getDislike ();
			foreach ( $disLikeAnswer as $key => $disLikeAnswer ) {

				$this->addUniqueUser ( $array_to_user, $disLikeAnswer );
			}
			$replies = $answer->getReply ();
			foreach ( $replies as $key => $reply ) {

				$this->addUniqueUser ( $array_to_user, $reply->getCreateBy () );

				$subReplies = $reply->getChildren ();
				foreach ( $subReplies as $key => $subReply ) {

					$this->addUniqueUser ( $array_to_user, $subReply->getCreateBy () );
				}
			}
		}
		$spams = $question->getSpam ();
		foreach ( $spams as $key => $spam ) {

			$this->addUniqueUser ( $array_to_user, $spam );
		}
		$follows = $question->getUserFollow ();
		foreach ( $follows as $key => $follow ) {

			$this->addUniqueUser ( $array_to_user, $follow );
		}

		label:
		foreach ( $array_to_user as $key => $toUser ) {
			$this->setNotifyToUser ( $notify, $toUser );
		}
		$notify->setType ( $type );

		$notify->setContent ( $conentNotify );
		$notify->setStatus ( FAQParaConfig::TYPE_NOTIFY_STATUS );
		$notify->setUserCreateNotify ( $createBy );

		// $notify->insert();
	}

	/**
	 *
	 * @param User $user
	 * @param \Doctrine\Common\Collections\ArrayCollection $uniqueArray
	 */
	private function addUniqueUser($uniqueArray, $user) {
		if ($user)
			if (! $uniqueArray->contains ( $user ) && $user->getId () != Util::getIDCurrentUser ()) {
				$uniqueArray->add ( $user );
			}
	}
	private function setNotifyToUser($notify, $toUser) {
		// set to user
		$toUser->setStatusUpdateRefere ();
		$toUser->setNotify ( $notify );
		// $toUser->setTotalNewNotify(1 + $toUser->getTotalNewNotify());

		// Find the user and update
		$this->user->getQueryBuilder ()->findAndUpdate ()->field ( 'id' )->equals ( $toUser->getId () )->field ( 'total_new_notify' )->set ( 1 + $toUser->getTotalNewNotify () )->getQuery ()->execute ();
	}

	/**
	 *
	 * @param String $questionID
	 * @param String $answerID
	 * @param \FAQ\FAQEntity\User $userLike
	 * @return 1 success, 4 dont like for yourselft
	 */
	public function likeAnswer($questionID, $answerID, $userLike, $isEstablishedUser = false) {
		// check total vote per one day
		$totalLikeOneDay = $userLike->getTotalVoteOneDay ();
		$lastDayLike = $userLike->getVoteDay ();
		$currentDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
		// var_dump("currentdate",$currentDate);
		// var_dump("last date stamp: ",$lastDayLike->getTimestamp ());
		// var_dump("last date like: ",$lastDayLike);
		// past 1 days
		if (! empty ( $lastDayLike )) {
			if (($totalLikeOneDay >= FAQParaConfig::VOTE_MAX_TOTAL_PER_ONE_DAY) && (($currentDate) - ($lastDayLike->getTimestamp ()) < 1 * 24 * 60 * 60)) {
				return array (
						"status" => 5,
						"toatlLike" => null,
						"totalDislike" => null,
						"totalPoint" => null
				);
			} elseif ((($currentDate) - ($lastDayLike->getTimestamp ()) > 1 * 24 * 60 * 60)) {
				$userLike->setTotalVoteOneDay ( 0 );
			}
		}
		// update total vote perday
		$userLike->setTotalVoteUp ( $userLike->getTotalVoteUp () + 1 );
		$userLike->setTotalVoteOneDay ( $userLike->getTotalVoteOneDay () + 1 );
		$today = getdate ();
		$userLike->setVoteDay ( Util::createDate ( $today ["mday"], $today ["mon"], $today ["year"] ) );
		// var_dump($questionID,$answerID);
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE )) {
			return array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		$question->setStatusUpdateRefere ();
		$answers = $question->getAnswer ();
		$userMapper = new UserMapper ();
		$answerUpdate = null;
		foreach ( $answers as $key => $answer ) {
			$answerCreateBy = $answer->getCreateBy ();
			/* @var $answer \FAQ\FAQEntity\Answer */
			if ($answer->getId () == $answerID) {
				$answerUpdate = $answer;
				if ($userLike->getId () == $answerCreateBy->getId ()) {
					return array (
							"status" => 4,
							"toatlLike" => null,
							"totalDislike" => null,
							"totalPoint" => null
					);
				}
				if ($answer->getLike ()->contains ( $userLike )) {
					throw new \Exception ( "user has liked before" );
				}
				$answer->setLike ( $userLike );
				if ($answer->getDislike ()->contains ( $userLike )) {
					$answer->setTotalDislike ( $answer->setTotalDislike () - 1 );
					$answer->getDislike ()->removeElement ( $userLike );

					$userMapper->updatePointByQuestionAnswerBeLike ( $userLike, $answerCreateBy, $question, "MINUS" );
				}

				$answer->setTotalLike ( $answer->getTotalLike () + 1 );
				// izzi update point

				$userMapper->updatePointByQuestionAnswerBeLike ( $userLike, $answer->getCreateBy (), $question, "PLUS" );
				$this->getDm ()->persist ( $answer );
				// send notify user follow subject of question
				$content = Util::html2txt ( $answer->getContent (), FAQParaConfig::TYPE_TRIP_HTML );
				$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
				$contentNotify = "đã thích câu trả lời: " . $subContent . "...";
				$this->addNotify ( $userLike, $contentNotify, FAQParaConfig::TYPE_NOTIFY_LIKE_ANSWER_QUESTION, $question );

				// Find the user and update total like increment
				$this->user->getQueryBuilder ()->findAndUpdate ()->field ( 'id' )->equals ( $answer->getCreateBy ()->getId () )->field ( 'total_answer_like' )->set ( 1 + $answer->getCreateBy ()->getTotalAnswerLike () )->getQuery ()->execute ();

				goto label;
			}
		}
		label:

		$this->commit ();

		$totalLike = $answerUpdate->getTotalLike ();
		$totalDislike = $answerUpdate->getTotalDislike ();
		if ($isEstablishedUser) {
			return array (
					"status" => 1,
					"toatlLike" => $totalLike,
					"totalDislike" => $totalDislike,
					"totalPoint" => null
			);
		} else {
			return array (
					"status" => 1,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => $totalLike - $totalDislike
			);
		}
	}
	/**
	 *
	 * @author sang
	 * @param String $questionID
	 * @param User $userLike
	 */
	public function likeQuestion($questionID, $userLike, $isEstablishedUser = false) {
		// check total vote per one day
		$totalLikeOneDay = $userLike->getTotalVoteOneDay ();
		$lastDayLike = $userLike->getVoteDay ();
		$currentDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
		// var_dump("currentdate",$currentDate);
		// var_dump("last date stamp: ",$lastDayLike->getTimestamp ());
		// var_dump("last date like: ",$lastDayLike);
		// past 1 days
		if (! empty ( $lastDayLike )) {
			if (($totalLikeOneDay >= FAQParaConfig::VOTE_MAX_TOTAL_PER_ONE_DAY) && (($currentDate) - ($lastDayLike->getTimestamp ()) < 1 * 24 * 60 * 60)) {
				return array (
						"status" => 5,
						"toatlLike" => null,
						"totalDislike" => null,
						"totalPoint" => null
				);
			} elseif ((($currentDate) - ($lastDayLike->getTimestamp ()) > 1 * 24 * 60 * 60)) {
				$userLike->setTotalVoteOneDay ( 0 );
			}
		}
		// update total vote perday
		$userLike->setTotalVoteUp ( $userLike->getTotalVoteUp () + 1 );
		$userLike->setTotalVoteOneDay ( $userLike->getTotalVoteOneDay () + 1 );
		$today = getdate ();
		$userLike->setVoteDay ( Util::createDate ( $today ["mday"], $today ["mon"], $today ["year"] ) );
		// var_dump($questionID,$answerID);
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );
		// var_dump($question);
		if (empty ( $question )) {
			return array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE )) {
			return array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		$userCreateQuestion = $question->getCreateBy ();
		if ($userLike->getId () == $userCreateQuestion->getId ()) {
			return array (
					"status" => 4,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		$isLikeBefor = $question->getLike ()->contains ( $userLike );
		if ($isLikeBefor) {
			return array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		$userMapper = new UserMapper ();
		$isDislikeBefor = $question->getDislike ()->contains ( $userLike );
		if ($isDislikeBefor) {
			$question->getDislike ()->removeElement ( $userLike );
			$userMapper->updatePointByQuestionBeLike ( $userLike, $userCreateQuestion, $question, "MINUS" );
			$question->descDislike ();
		}
		// set user like
		$question->setLike ( $userLike );
		// increment total like
		$question->incLike ();

		// sang update point
		// Upvotes on a question give the asker +5 reputation.
		// Upvotes on an answer give the answerer +10 reputation.
		// You can vote 30 times per UTC day, plus 10 more times on questions only.

		$userMapper->updatePointByQuestionBeLike ( $userLike, $userCreateQuestion, $question, "PLUS" );
		// send notify to user's created question
		$content = Util::html2txt ( $question->getContent (), FAQParaConfig::TYPE_TRIP_HTML );
		$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
		$contentNotify = "đã thích câu hỏi: " . $subContent . "...";
		$notify = new Notify ();
		$notify->setQuestion ( $question );
		$notify->setContent ( $contentNotify );
		$notify->setDateUpdated ( Util::getCurrentTime () );
		$notify->setStatus ( FAQParaConfig::TYPE_NOTIFY_STATUS );
		$notify->setType ( FAQParaConfig::TYPE_NOTIFY_LIKE_QUESTION );
		$notify->setUserCreateNotify ( $userLike );

		$userCreateQuestion->setNotify ( $notify );
		$userCreateQuestion->setTotalNewNotify ( 1 + $userCreateQuestion->getTotalNewNotify () );

		$this->commit ();
		$totalLike = $question->getTotalLike ();
		$totalDislike = $question->getTotalDislike ();
		if ($isEstablishedUser) {
			return array (
					"status" => 1,
					"toatlLike" => $totalLike,
					"totalDislike" => $totalDislike,
					"totalPoint" => null
			);
		} else {
			return array (
					"status" => 1,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => $totalLike - $totalDislike
			);
		}
	}
	/**
	 *
	 * @author sang
	 * @param String $questionID
	 * @param User $userLike
	 */
	public function dislikeQuestion($questionID, $userDislike, $isEstablishedUser = false) {
		// check total vote per one day
		$totalLikeOneDay = $userDislike->getTotalVoteOneDay ();
		$lastDayLike = $userDislike->getVoteDay ();
		$currentDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
		// var_dump("currentdate",$currentDate);
		// var_dump("last date stamp: ",$lastDayLike->getTimestamp ());
		// var_dump("last date like: ",$lastDayLike);
		// past 1 days
		if (! empty ( $lastDayLike )) {
			if (($totalLikeOneDay >= FAQParaConfig::VOTE_MAX_TOTAL_PER_ONE_DAY) && (($currentDate) - ($lastDayLike->getTimestamp ()) < 1 * 24 * 60 * 60)) {
				return array (
						"status" => 5,
						"toatlLike" => null,
						"totalDislike" => null,
						"totalPoint" => null
				);
			} elseif ((($currentDate) - ($lastDayLike->getTimestamp ()) > 1 * 24 * 60 * 60)) {
				$userDislike->setTotalVoteOneDay ( 0 );
			}
		}
		// update total vote perday
		$userDislike->setTotalVoteDown ( $userDislike->getTotalVoteDown () + 1 );
		$userDislike->setTotalVoteOneDay ( $userDislike->getTotalVoteOneDay () + 1 );
		$today = getdate ();
		$userDislike->setVoteDay ( Util::createDate ( $today ["mday"], $today ["mon"], $today ["year"] ) );
		// var_dump($questionID,$answerID);
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );
		// var_dump($question);
		if (empty ( $question )) {
			return array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE )) {
			return array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		$userMapper = new UserMapper ();
		$userCreateQuestion = $question->getCreateBy ();
		if ($userDislike->getId () == $userCreateQuestion->getId ()) {
			return array (
					"status" => 4,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		$isDislikeBefor = $question->getDislike ()->contains ( $userDislike );
		if ($isDislikeBefor) {
			return array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}

		$isLikeBefor = $question->getLike ()->contains ( $userDislike );
		if ($isLikeBefor) {
			$question->getLike ()->removeElement ( $userDislike );
			$question->descLike ();
			$userMapper->updatePointByQuestionBeDislike ( $userDislike, $userCreateQuestion, $question, "MINUS" );
		}

		// set user like
		$question->setDislike ( $userDislike );
		// increment total like
		$question->incDislike ();

		// sang update point

		$userMapper->updatePointByQuestionBeDislike ( $userDislike, $userCreateQuestion, $question, "PLUS" );
		// send notify to user's created question
		$content = Util::html2txt ( $question->getContent (), FAQParaConfig::TYPE_TRIP_HTML );
		$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
		$contentNotify = "đã không thích câu hỏi: " . $subContent . "...";
		$notify = new Notify ();
		$notify->setQuestion ( $question );
		$notify->setContent ( $contentNotify );
		$notify->setDateUpdated ( Util::getCurrentTime () );
		$notify->setStatus ( FAQParaConfig::TYPE_NOTIFY_STATUS );
		$notify->setType ( FAQParaConfig::TYPE_NOTIFY_DISLIKE_QUESTION );
		$notify->setUserCreateNotify ( $userDislike );

		$userCreateQuestion->setNotify ( $notify );
		$userCreateQuestion->setTotalNewNotify ( 1 + $userCreateQuestion->getTotalNewNotify () );

		$this->commit ();
		$totalLike = $question->getTotalLike ();
		$totalDislike = $question->getTotalDislike ();
		if ($isEstablishedUser) {
			return array (
					"status" => 1,
					"toatlLike" => $totalLike,
					"totalDislike" => $totalDislike,
					"totalPoint" => null
			);
		} else {
			return array (
					"status" => 1,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => $totalLike - $totalDislike
			);
		}
	}
	/**
	 *
	 * @param String $questionID
	 * @param String $answerID
	 * @param \FAQ\FAQEntity\User $userDislike
	 */
	public function dislikeAnswer($questionID, $answerID, $userDislike, $isEstablishedUser) {
		// check total vote per one day
		$totalLikeOneDay = $userDislike->getTotalVoteOneDay ();
		$lastDayLike = $userDislike->getVoteDay ();
		$currentDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
		// var_dump("currentdate",$currentDate);
		// var_dump("last date stamp: ",$lastDayLike->getTimestamp ());
		// var_dump("last date like: ",$lastDayLike);
		// past 1 days
		if (! empty ( $lastDayLike )) {
			if (($totalLikeOneDay >= FAQParaConfig::VOTE_MAX_TOTAL_PER_ONE_DAY) && (($currentDate) - ($lastDayLike->getTimestamp ()) < 1 * 24 * 60 * 60)) {
				return array (
						"status" => 5,
						"toatlLike" => null,
						"totalDislike" => null,
						"totalPoint" => null
				);
			} elseif ((($currentDate) - ($lastDayLike->getTimestamp ()) > 1 * 24 * 60 * 60)) {
				$userDislike->setTotalVoteOneDay ( 0 );
			}
		}
		// update total vote perday
		$userDislike->setTotalVoteDown ( $userDislike->getTotalVoteDown () + 1 );
		$userDislike->setTotalVoteOneDay ( $userDislike->getTotalVoteOneDay () + 1 );
		$today = getdate ();
		$userDislike->setVoteDay ( Util::createDate ( $today ["mday"], $today ["mon"], $today ["year"] ) );
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE )) {
			return array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		}
		$question->setStatusUpdateRefere ();
		$answers = $question->getAnswer ();
		$answerUpdate = null;
		foreach ( $answers as $key => $answer ) {
			$answerCreateBy = $answer->getCreateBy ();
			/* @var $answer \FAQ\FAQEntity\Answer */
			if ($answer->getId () == $answerID) {
				$answerUpdate = $answer;
				if ($userDislike->getId () == $answerCreateBy->getId ()) {
					return array (
							"status" => 4,
							"toatlLike" => null,
							"totalDislike" => null,
							"totalPoint" => null
					);
				}
				if ($answer->getDislike ()->contains ( $userDislike )) {

					throw new \Exception ( "user has liked before" );
				}
				$userMapper = new UserMapper ();
				$answer->setDislike ( $userDislike );
				if ($answer->getLike ()->contains ( $userDislike )) {
					$answer->setTotalLike ( $answer->getTotalLike () - 1 );
					$answer->getLike ()->removeElement ( $userDislike );
					$userMapper->updatePointByQuestionAnswerBeDislike ( $userDislike, $answer->getCreateBy (), $question, "MINUS" );
				}

				$answer->setTotalDislike ( $answer->getTotalDislike () + 1 );
				// izzi update point

				$userMapper->updatePointByQuestionAnswerBeDislike ( $userDislike, $answer->getCreateBy (), $question, "PLUS" );

				// send notify user follow subject of question
				$content = Util::html2txt ( $answer->getContent (), FAQParaConfig::TYPE_TRIP_HTML );
				$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
				$contentNotify = "không thích câu trả lời: " . $subContent . "...";
				$this->addNotify ( $userDislike, $contentNotify, FAQParaConfig::TYPE_NOTIFY_DISLIKE_ANSWER_QUESTION, $question );

				// Find the user and update total like increment
				$this->user->getQueryBuilder ()->findAndUpdate ()->field ( 'id' )->equals ( $answer->getCreateBy ()->getId () )->field ( 'total_answer_dislike' )->set ( 1 + $answer->getCreateBy ()->getTotalAnswerDislike () )->getQuery ()->execute ();

				goto label;
			}
		}
		label:

		$this->commit ();
		$totalLike = $answerUpdate->getTotalLike ();
		$totalDislike = $answerUpdate->getTotalDislike ();
		if ($isEstablishedUser) {
			return array (
					"status" => 1,
					"toatlLike" => $totalLike,
					"totalDislike" => $totalDislike,
					"totalPoint" => null
			);
		} else {
			return array (
					"status" => 1,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => $totalLike - $totalDislike
			);
		}
	}

	/**
	 *
	 * @param String $questionID
	 * @param String $answerID
	 * @param User $userDislike
	 */
	public function bestAnswer($questionID, $answerID, $userSetBestAnswerID) {
		$userMapper = new UserMapper ();
		$currentUser = Util::getCurrentUser ();
		/* @var $question \FAQ\FAQEntity\Question */
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 0;
		}
		if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE )) {
			return 0;
		}
		$question->setStatusUpdateRefere ();
		$currentDate = Util::getCurrentTime ();
		$questionDateUpdateBest = $question->getDate_update_best ();
		// past 10 days
		if (! empty ( $questionDateUpdateBest )) {
			if (($currentDate->sec) - ($questionDateUpdateBest->getTimestamp ()) > 15 * 24 * 60 * 60) {
				return 4;
			}
		}

		if ($userSetBestAnswerID == $question->getCreateBy ()->getId ()) {
			$answers = $question->getAnswer ();
			$userCreateAnswerBestBefore = null;
			foreach ( $answers as $key => $answer ) {

				if ($answer->getId () != $answerID && $answer->getIsBest () == true) {
					// set best is false for answer was bested before
					$answer->setIsBest ( false );
					// plus bonus point
					$userCreateAnswerBestBefore = $answer->getCreateBy ();
				} elseif ($answer->getId () == $answerID && $answer->getIsBest () == true) {
					// it is best before
					return 3;
				}

				/* @var $answer \FAQ\FAQEntity\Answer */
				if ($answer->getId () == $answerID && $userSetBestAnswerID != $answer->getCreateBy ()->getId ()) {
					$answer->setIsBest ( true );
					// update date set best answer for question
					$question->setDate_update_best ( Util::getCurrentTime () );
					// plus bonus point
					$userCreateAnswer = $answer->getCreateBy ();
					// $userCreateAnswer->setStatusUpdateRefere();

					// send notify user follow subject of question
					$notify = new Notify ();
					$content = Util::html2txt ( $answer->getContent (), FAQParaConfig::TYPE_TRIP_HTML );
					$subContent = mb_substr ( $content, 0, 70, 'UTF-8' );
					$contentNotify = "bình chọn câu trả lời hay nhất: " . $subContent . "...";
					// $this->setNotifyToUser($notify,$userCreateAnswer );
					$notify->setType ( FAQParaConfig::TYPE_NOTIFY_BEST_ANSWER_QUESTION );
					$notify->setQuestion ( $question );
					$notify->setContent ( $contentNotify );
					$notify->setStatus ( FAQParaConfig::TYPE_NOTIFY_STATUS );
					$notify->setUserCreateNotify ( $this->user->find ( $userSetBestAnswerID, true ) );
					$this->setNotifyToUser ( $notify, $userCreateAnswer );

					if (! empty ( $userCreateAnswerBestBefore ) && $userCreateAnswerBestBefore->getId () != $userCreateAnswer->getId ()) {
						// $userCreateAnswer->setTotalMoneyPoint($question->getBonusPoint() + $userCreateAnswer->getTotalMoneyPoint());
						// Find the user and update
						$userCreateAnswer->setTotalMoneyPoint ( $question->getBonusPoint () + $userCreateAnswer->getTotalMoneyPoint () );
						$userCreateAnswer->setTotalAnswerBest ( 1 + $userCreateAnswer->getTotalAnswerBest () );

						$userCreateAnswerBestBefore->setTotalMoneyPoint ( $userCreateAnswerBestBefore->getTotalMoneyPoint () - $question->getBonusPoint () );
						$userCreateAnswerBestBefore->setTotalAnswerBest ( $userCreateAnswerBestBefore->getTotalAnswerBest () - 1 );

						$userMapper->updatePointByQuestionBeVoteBestAnswer ( $currentUser, $userCreateAnswerBestBefore, $question, "MINUS" );
						// $this->user->getQueryBuilder ()->findAndUpdate ()->field ( 'id' )->equals ( $userCreateAnswer->getId () )->field ( 'total_money_point' )->set ( $question->getBonusPoint () + $userCreateAnswer->getTotalMoneyPoint () )->getQuery ()->execute ();

						// // $userCreateAnswer->setTotalMoneyPoint($userCreateAnswer->getTotalMoneyPoint() - $question->getBonusPoint());
						// $this->user->getQueryBuilder ()->findAndUpdate ()->field ( 'id' )->equals ( $userCreateAnswerBestBefore->getId () )->field ( 'total_money_point' )->set ( $userCreateAnswerBestBefore->getTotalMoneyPoint () - $question->getBonusPoint () )->getQuery ()->execute ();

						// // Find the user and update total best answer increment
						// $this->user->getQueryBuilder ()->findAndUpdate ()->field ( 'id' )->equals ( $userCreateAnswer->getId () )->field ( 'total_answer_best' )->set ( 1 + $userCreateAnswer->getTotalAnswerBest () )->getQuery ()->execute ();
					}
				} elseif ($answer->getId () == $answerID && $userSetBestAnswerID == $answer->getCreateBy ()->getId ()) {
					// vote best for myselft
					return 2;
				}
			}
			// sang update point

			$userMapper->updatePointByQuestionBeVoteBestAnswer ( $currentUser, $userCreateAnswer, $question, "PLUS" );
			// close question
			$question->setStatus ( FAQParaConfig::QUESTION_STATUS_EXIST_BEST );
			$this->commit ();
			return 1; // return success
		} else {
			throw new \Exception ( "Bạn không là không là người tạo câu hỏi!" );
		}
	}

	/**
	 *
	 * @author sang
	 * @param String $userID
	 * @param Int $from
	 * @param Int $to
	 * @param array $select
	 */
	public function getQuestionHome($select, $subjectID, $from = null, $to = null, $type = 0) {
		$orderBy = array ();

		$qb = $this->question->getQueryBuilder ();
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		if (! empty ( $subjectID )) {
			$qb->field ( "subject.id" )->equals ( $subjectID );
		}
		// select document ispublish
		$qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );
		if ($type == 0) {
			$orderBy = array (
					"date_created" => "desc"
			);
			// question create by user, my follow
			// current user login
			$userID = Util::getIDCurrentUser ();
			if (! empty ( $userID )) {
				$currentUser = Util::getCurrentUser ();
				// my_follow user
				$myFollowUser = $currentUser->getMyFollow ();
				// get question create by my follow
				foreach ( $myFollowUser as $key => $userMyFollow ) {
					$qb->addOr ( array (
							"create_by.id" => $userMyFollow->getId ()
					) );
				}

				// follow_subject
				$subjectFollow = $currentUser->getFollowSubject ();
				foreach ( $subjectFollow as $key => $subject ) {
					$qb->addOr ( array (
							"subject.id" => $subject->getId ()
					) );
				}
				// follow_question
				$questionFollows = $currentUser->getFollowQuestion ();
				foreach ( $questionFollows as $key => $questionFollow ) {

					$qb->addOr ( $qb->expr ()->field ( 'id' )->equals ( $questionFollow->getId () ) );
				}

				// skill-------------------------------------------------------????????????????????????????????????????????
				$currentUserSkills = $currentUser->getSkill ();
				/* @var $currentUserSkill \FAQ\FAQEntity\Skill */
				foreach ( $currentUserSkills as $key => $currentUserSkill ) {
					$keywords = $currentUserSkill->getKeyWord ();
					foreach ( $keywords as $keyw => $keyword ) {
						$qb->addOr ( $qb->expr ()->field ( 'key_word' )->in ( explode ( ' ', $keyword ) ) );
					}
				}
				// like_answer
				$questionLikeAnswers = $currentUser->getLikeAnswer ();
				foreach ( $questionLikeAnswers as $key => $questionLikeAnswer ) {

					$qb->addOr ( $qb->expr ()->field ( 'id' )->equals ( $questionLikeAnswer->getId () ) );
				}

				// dislike_answer
				$questionDislikeAnswers = $currentUser->getDisikeAnswer ();
				foreach ( $questionDislikeAnswers as $key => $questionDislikeAnswer ) {

					$qb->addOr ( $qb->expr ()->field ( 'id' )->equals ( $questionDislikeAnswer->getId () ) );
				}

				// // spam_question
				// $questionSpam = $currentUser->getSpamQuestion()->toArray();

				// share_question
				$questionShares = $currentUser->getShareQuestion ();
				foreach ( $questionShares as $key => $questionShare ) {

					$qb->addOr ( $qb->expr ()->field ( 'id' )->equals ( $questionShare->getId () ) );
				}

				// chat help
				$messageMapper = new MessageMapper ();
				$messageIdArr = $messageMapper->getMessageToUserArray ( $userID );
				foreach ( $messageIdArr as $msgid ) {
					$qb = $qb->addOr ( array (
							"chat_help.id" => $msgid
					) );
				}
				// answer
				$qb = $qb->addOr ( array (
						"answer.create_by.id" => $userID
				) );
				// reply1
				$qb = $qb->addOr ( array (
						"answer.reply.create_by.id" => $userID
				) );
				// reply2
				$qb = $qb->addOr ( array (
						"answer.reply.reply.create_by.id" => $userID
				) );
			}
		} elseif ($type == 1) {
			$orderBy = array (
					"bonus_point" => "desc",
					"date_created" => "desc",
					"total_answer" => "asc"
			);
		} elseif ($type == 2) {
			$orderBy = array (

					"date_created" => "desc"
			);
			$qb->field ( "total_answer" )->equals ( 0 );
		} elseif ($type == 3) {
			$orderBy = array (

					"total_like" => "desc",
					"total_dislike" => "asc"
			);
		}
		$totalDocument = $qb->getQuery ()->count ();

		$qb = Util::addOrder ( $qb, $orderBy );
		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}
		$listQuestion = $qb->getQuery ()->execute ();
		if ($totalDocument < 10 && empty ( $subjectID ) && $type == 0) {
			$topQuestion = $this->getTopQuestion ( $select, $from, $to );
			$data = array (
					"totalDocument" => $totalDocument + $topQuestion ['totalDocument'],
					'listQuestion' => $listQuestion->toArray () + $topQuestion ['listQuestion']->toArray ()
			);
		} else {
			$data = array (
					"totalDocument" => $totalDocument,
					'listQuestion' => $listQuestion
			);
		}
		return $data;
	}
	public function searchQuestion($queryString, $select, $from, $to) {
		if (! empty ( $queryString )) {
			$keywords = explode ( ' ', $queryString );
			foreach ( $keywords as $key => $value ) {
				$regexObj = new \MongoRegex ( "/^" . $value . "/i" );
				$keySearchs [$key] = $regexObj;
			}
		} else {
			$keySearchs = null;
		}

		$qb = $this->question->getQueryBuilder ();
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		if (! empty ( $keySearchs )) {

			$qb->addOr ( $qb->expr ()->field ( 'key_word' )->in ( $keySearchs ) );
		}
		;
		// select document ispublish
		$qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );

		$totalDocument = $qb->getQuery ()->count ();
		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}
		$listQuestion = $qb->getQuery ()->execute ();

		$data = array (
				"totalDocument" => $totalDocument,
				'listQuestion' => $listQuestion
		);

		return $data;
	}

	/**
	 *
	 * @author sang
	 * @todo top question in the my system
	 * @param array $select
	 * @param Int $from
	 * @param Int $to
	 * @param array $listSubjectID
	 * @return multitype:number Ambigous <\Doctrine\ODM\MongoDB\Query\mixed, \Doctrine\MongoDB\EagerCursor, \Doctrine\MongoDB\Cursor, Cursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
	 */
	public function getTopQuestion($select, $from, $to) {
		$orderBy = array (
				"total_comment" => "desc",
				"total_share" => "desc",
				"total_spam" => "desc",
				"total_follow" => "desc",
				"vote" => "desc"
		);

		$qb = $this->question->getQueryBuilder ();
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		$userID = Util::getIDCurrentUser ();
		if (! empty ( $userID )) {
			$qb->field ( "create_by.id" )->notEqual ( $userID );
		}
		// select document is publish
		$qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );

		// select document is not spam
		$qb->field ( "total_spam" )->lte ( FAQParaConfig::QUESTION_MAX_SPAM );

		$totalDocument = $qb->getQuery ()->count ();

		$qb = Util::addOrder ( $qb, $orderBy );
		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}

		$listQuestion = $qb->getQuery ()->execute ();

		$data = array (
				"totalDocument" => $totalDocument,
				'listQuestion' => $listQuestion
		);

		return $data;
	}
	/**
	 *
	 * @author sang
	 * @todo highlight question in the my system AND pint this to the top
	 * @param array $select
	 * @param Int $from
	 * @param Int $to
	 * @param array $listSubjectID
	 * @return multitype:number Ambigous <\Doctrine\ODM\MongoDB\Query\mixed, \Doctrine\MongoDB\EagerCursor, \Doctrine\MongoDB\Cursor, Cursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
	 */
	public function getHighlightQuestion($select, $from, $to, $listSubjectID = null, $isHighlight = null) {
		$qb = $this->question->getQueryBuilder ()->hydrate ( false );
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}

		// select document is publish
		$qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );
		// select document is publish
		if (! empty ( $listSubjectID )) {
			$qb->field ( "subject.id" )->in ( $listSubjectID );
		}
		// select document is not spam
		$qb->field ( "total_spam" )->lte ( FAQParaConfig::QUESTION_MAX_SPAM );
		// select document is hot
		if (! empty ( $isHighlight )) {
			$qb->field ( "is_highlight" )->equals ( FAQParaConfig::QUESTION_HIGHLIGHT );
		}

		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}
		$data = $qb->getQuery ()->execute ();

		return $data;
	}
	/**
	 *
	 * @author sang
	 * @todo hot question in the my system AND pint this to the top
	 * @param array $select
	 * @param Int $from
	 * @param Int $to
	 * @param array $listSubjectID
	 * @return multitype:number Ambigous <\Doctrine\ODM\MongoDB\Query\mixed, \Doctrine\MongoDB\EagerCursor, \Doctrine\MongoDB\Cursor, Cursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
	 */
	public function getHotQuestion($select, $from, $to, $listSubjectID = null, $isTop = null) {
		$qb = $this->question->getQueryBuilder ()->hydrate ( false );
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}

		// select document is publish
		$qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );
		// select document is publish
		if (! empty ( $listSubjectID )) {
			$qb->field ( "subject.id" )->in ( $listSubjectID );
		}
		// select document is not spam
		$qb->field ( "total_spam" )->lte ( FAQParaConfig::QUESTION_MAX_SPAM );
		// select document is hot
		if (! empty ( $isTop )) {
			$qb->field ( "is_top" )->equals ( FAQParaConfig::QUESTION_HOT );
		}

		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}

		$listQuestion = $qb->getQuery ()->execute ();

		return $listQuestion;
	}
	public function getHashtagRelationship($questionID, $from, $to) {
		$hashtag = array ();
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return null;
		}
		$questionHashtag = $question->getHashtag ();
		if (! empty ( $questionHashtag )) {
			$hashtag = $hashtag + $questionHashtag;
		}
		$questionKeyWord = $question->getKeyWord ();

		if (! empty ( $questionKeyWord )) {
			$hashtag = $hashtag + $questionKeyWord;
		}
		$subject = $question->getSubject ();

		$qb = $this->hashtag->getQueryBuilder ()->select ( "tag" );
		$qb->field ( "subject.id" )->equals ( $subject->getId () );
		$orderBy = array (
				"recomment" => "asc",
				"total_amount" => "acs"
		);
		$qb = Util::addOrder ( $qb, $orderBy );

		$subjectHashtag = $qb->getQuery ()->execute ();

		if (! empty ( $subjectHashtag )) {
			$hashtag = $hashtag + $subjectHashtag->toArray ();
		}
		$hashtag = array_slice ( $hashtag, $from, $to - $from );
		return $hashtag;
	}
	public function getQuestionRelationship($questionID, $from, $to) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return null;
		}
		$keywords = $question->getKeyWord ();
		foreach ( $keywords as $key => $value ) {
			$regexObj = new \MongoRegex ( "/^" . $value . "/i" );
			$keyWord [$key] = $regexObj;
		}
		$subject = $question->getSubject ();
		$qb = $this->question->getQueryBuilder ()->field ( "id" )->notEqual ( $questionID )->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );
		if (! empty ( $keyWord )) {

			$qb->addOr ( $qb->expr ()->field ( 'key_word' )->in ( $keyWord ) );
		}
		;
		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}
		$qb->field ( "subject.id" )->equals ( $subject->getId () );

		$questions = $qb->getQuery ()->execute ();

		return $questions;
	}
	public function getQuestionManager($select, $orderBy, $from, $to) {
		$qb = $this->question->getQueryBuilder ();
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}

		$totalDocument = $qb->getQuery ()->count ();
		if (isset ( $orderBy )) {
			$qb = Util::addOrder ( $qb, $orderBy );
		}
		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}
		$listQuestion = $qb->getQuery ()->execute ();

		$data = array (
				"totalDocument" => $totalDocument,
				'listQuestion' => $listQuestion
		);
		return $data;
	}
	public function reportChartPerDay() {
		$qb = $this->question->getQueryBuilder ();
		$orderBy = array (
				"date_created" => "desc"
		);
		$qb = Util::addOrder ( $qb, $orderBy );
		$qb->map ( 'function() { emit(this.date_created.toDateString(), 1); }' )->reduce ( 'function(k, vals) {

            return Array.sum(vals);
    }' );
		$q = $qb->getQuery ();
		$reportQuestion = $q->execute ();
		return $reportQuestion;
	}
	public function reportChartAll() {
		$qb = $this->question->getQueryBuilder ();
		$qb->map ( 'function() { emit(this.status,1); }' )->reduce ( 'function(k, vals) {
            return Array.sum(vals);
    }' );
		$q = $qb->getQuery ();
		$reportQuestion = $q->execute ();
		return $reportQuestion;
	}
	/**
	 *
	 * @author sang
	 * @param String $questionID
	 * @return Int 0 fail
	 *         1 success
	 */
	public function protectQuestion($questionID) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 0;
		}

		$question->addStatus ( FAQParaConfig::QUESTION_STATUS_PROTECT );
		$question->setProtectBy ( Util::getCurrentUser () );
		$this->commit ();
		return 1;
	}
	/**
	 *
	 * @author sang
	 * @param String $questionID
	 * @return Int 0 fail
	 *         1 success
	 */
	public function unprotectQuestion($questionID) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 0;
		}
		$protectBy = $question->getProtectBy ();
		$protectByID = $protectBy->getId ();
		if ($protectByID != Util::getIDCurrentUser ()) {
			return 4;
		}
		$question->removeStatus ( FAQParaConfig::QUESTION_STATUS_PROTECT );
		$question->setProtectBy ( null );
		$this->commit ();
		return 1;
	}
	/**
	 *
	 * @author sang
	 * @param String $questionID
	 * @return Int 0 fail
	 *         1 success
	 */
	public function closeQuestion($questionID, $isCommit = true) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 0;
		}
		$currentUser = Util::getCurrentUser ();
		$closeBy = $question->getCloseBy ();
		if (! empty ( $closeBy )) {
			$protectByID = $closeBy->getId ();

			if ($protectByID == $currentUser->getId ()) {
				return 4;
			}
		}
		$question->addStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE );
		$question->removeStatus ( FAQParaConfig::QUESTION_STATUS_OPEN );
		$question->setCloseBy ( $currentUser );

		$userCreateQuestion = $question->getCreateBy ();
		$userMapper = new UserMapper ();

		$userMapper->updatePointByQuestionBeDeletedByAdmin ( $userCreateQuestion, $question );
		if ($isCommit) {
			$this->commit ();
		}
		return 1;
	}
	/**
	 *
	 * @author sang
	 * @param String $questionID
	 * @return Int 0 fail
	 *         1 success
	 */
	public function reopenQuestion($questionID, $isCommit = true) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 0;
		}
		$closeBy = $question->getCloseBy ();
		if (empty ( $closeBy ) && ! ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE ))) {

			return 0;
		}
		$currentUser = Util::getCurrentUser ();
		$question->removeStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE );
		$question->addStatus ( FAQParaConfig::QUESTION_STATUS_OPEN );
		$question->setCloseBy ( $currentUser );

		$userCreateQuestion = $question->getCreateBy ();
		$userMapper = new UserMapper ();
		$userMapper->updatePointByQuestionBeUndeletedByAdmin ( $userCreateQuestion, $question );
		if ($isCommit) {
			$this->commit ();
		}
		return 1;
	}
	/**
	 *
	 * @author sang
	 * @param String $questionID
	 * @return Int 0 fail
	 *         1 success
	 */
	public function highlightQuestion($questionID) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 0;
		}

		if (! $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_OPEN )) {

			return 4;
		}
		$question->setIsHighlight ( FAQParaConfig::QUESTION_HIGHLIGHT );

		$this->commit ();
		return 1;
	}
	/**
	 *
	 * @author sang
	 * @param String $questionID
	 * @return Int 0 fail
	 *         1 success
	 */
	public function unhighlightQuestion($questionID) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 0;
		}

		// if (!$question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_OPEN)) {

		// return 4;
		// }
		$question->setIsHighlight ( FAQParaConfig::QUESTION_NOT_HIGHLIGHT );

		$this->commit ();
		return 1;
	}
	/**
	 *
	 * @author sang
	 * @param String $questionID
	 * @return Int 0 fail
	 *         1 success
	 */
	public function untopQuestion($questionID) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 0;
		}

		// if (!$question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_OPEN)) {

		// return 4;
		// }
		$question->setIsTop ( FAQParaConfig::QUESTION_NOT_HOT );

		$this->commit ();
		return 1;
	}

	/**
	 *
	 * @author sang
	 * @param String $questionID
	 * @return Int 0 fail
	 *         1 success
	 */
	public function topQuestion($questionID) {
		$question = $this->question->find ( $questionID, true );
		if (empty ( $question )) {
			return 0;
		}

		if (! $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_OPEN )) {

			return 4;
		}
		$question->setIsTop ( FAQParaConfig::QUESTION_HOT );

		$this->commit ();
		return 1;
	}
	/**
	 *
	 * @author sang
	 * @return multitype:string
	 */
	public function reviewQuestion() {
		$total_spam_need_access = $this->spamNeedAccessQuestion ();
		$total_spam_today_access = $this->spamTodayAccessQuestion ();
		$total_spam_recent_access = $this->spamRecentAccessQuestion ();
		$total_unspam_need_access = $this->unspamNeedAccessQuestion ();
		$total_unspam_today_access = $this->unspamTodayAccessQuestion ();
		$total_unspam_recent_access = $this->unspamRecentAccessQuestion ();

		$total_edit_question_need_access = $this->editQuestionNeedAccess ();
		$total_edit_question_today_access = $this->editQuestionTodayAccess ();
		$total_edit_question_recent_access = $this->editQuestionRecentAccess ();

		$total_edit_answer_need_access = $this->editAnswerNeedAccess ();
		$total_edit_answer_today_access = $this->editAnswerTodayAccess ();
		$total_edit_answer_recent_access = $this->editAnswerRecentAccess ();
		return array (
				"total_spam_need_access" => $total_spam_need_access,
				"total_spam_today_access" => $total_spam_today_access,
				"total_spam_recent_access" => $total_spam_recent_access,
				"total_unspam_need_access" => $total_unspam_need_access,
				"total_unspam_today_access" => $total_unspam_today_access,
				"total_unspam_recent_access" => $total_unspam_recent_access,
				"total_edit_question_need_access" => $total_edit_question_need_access,
				"total_edit_question_today_access" => $total_edit_question_today_access,
				"total_edit_question_recent_access" => $total_edit_question_recent_access,
				"total_edit_answer_need_access" => $total_edit_answer_need_access,
				"total_edit_answer_today_access" => $total_edit_answer_today_access,
				"total_edit_answer_recent_access" => $total_edit_answer_recent_access
		);
	}
	/**
	 *
	 * @return Int
	 */
	private function editQuestionNeedAccess() {
		$qb = $this->question->getQueryBuilder ();
		$qb->field ( "is_approve_edit_question" )->equals ( FAQParaConfig::IS_APPROVE_EDIT_QUESTION_NOTACCESS );
		$total_edit_question_need_access = $qb->getQuery ()->count ();
		return $total_edit_question_need_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function editQuestionTodayAccess() {
		$qb = $this->question->getQueryBuilder ();
		$currentDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
		$tomorowDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) );
		$start = new MongoDate ( $currentDate );
		$end = new MongoDate ( $tomorowDate );
		// var_dump($start);
		// var_dump($end);
		$qb->field ( "is_approve_edit_question" )->equals ( FAQParaConfig::IS_APPROVE_EDIT_QUESTION_ACCESS );
		$qb->field ( "dateupdated_approve_edit_question" )->gte ( $start );
		$qb->field ( "dateupdated_approve_edit_question" )->lte ( $end );

		$total_edit_question_today_access = $qb->getQuery ()->count ();
		return $total_edit_question_today_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function editQuestionRecentAccess() {
		$qb = $this->question->getQueryBuilder ();
		$tomorowDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) );

		$end = new MongoDate ( $tomorowDate );
		// var_dump($start);
		// var_dump($end);
		$qb->field ( "is_approve_edit_question" )->equals ( FAQParaConfig::IS_APPROVE_EDIT_QUESTION_ACCESS );
		$qb->field ( "dateupdated_approve_edit_question" )->gte ( $end );

		$total_edit_question_recent_access = $qb->getQuery ()->count ();
		return $total_edit_question_recent_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function editAnswerNeedAccess() {
		$qb = $this->question->getQueryBuilder ();
		$qb->field ( "is_approve_edit_answer" )->equals ( FAQParaConfig::IS_APPROVE_EDIT_ANSWER_NOTACCESS );
		$total_edit_answer_need_access = $qb->getQuery ()->count ();
		return $total_edit_answer_need_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function editAnswerTodayAccess() {
		$qb = $this->question->getQueryBuilder ();
		$currentDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
		$tomorowDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) );
		$start = new MongoDate ( $currentDate );
		$end = new MongoDate ( $tomorowDate );
		// var_dump($start);
		// var_dump($end);
		$qb->field ( "is_approve_edit_answer" )->equals ( FAQParaConfig::IS_APPROVE_EDIT_ANSWER_ACCESS );
		$qb->field ( "dateupdated_approve_edit_answer" )->gte ( $start );
		$qb->field ( "dateupdated_approve_edit_answer" )->lte ( $end );

		$total_edit_answer_today_access = $qb->getQuery ()->count ();
		return $total_edit_answer_today_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function editAnswerRecentAccess() {
		$qb = $this->question->getQueryBuilder ();
		$tomorowDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) );

		$end = new MongoDate ( $tomorowDate );
		// var_dump($start);
		// var_dump($end);
		$qb->field ( "is_approve_edit_answer" )->equals ( FAQParaConfig::IS_APPROVE_EDIT_ANSWER_ACCESS );
		$qb->field ( "dateupdated_approve_edit_answer" )->gte ( $end );

		$total_edit_answer_recent_access = $qb->getQuery ()->count ();
		return $total_edit_answer_recent_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function spamNeedAccessQuestion() {
		$qb = $this->question->getQueryBuilder ();
		$qb->field ( "total_spam" )->gt ( 0 );
		$qb->field ( "is_admin_spam" )->equals ( FAQParaConfig::IS_ADMIN_SPAM_STATUS_NOTACCESS );
		$total_spam_need_access = $qb->getQuery ()->count ();
		return $total_spam_need_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function unspamNeedAccessQuestion() {
		$qb = $this->question->getQueryBuilder ();
		$qb->field ( "total_spam" )->lte ( 0 );
		$qb->field ( "is_admin_spam" )->equals ( FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_SPAM );
		$total_unspam_need_access = $qb->getQuery ()->count ();
		return $total_unspam_need_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function unspamTodayAccessQuestion() {
		$qb = $this->question->getQueryBuilder ();
		$currentDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
		$tomorowDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) );
		$start = new MongoDate ( $currentDate );
		$end = new MongoDate ( $tomorowDate );
		// var_dump($start);
		// var_dump($end);
		$qb->field ( "is_admin_spam" )->equals ( FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_NOTSPAM );
		$qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );
		$qb->field ( "dateupdated_admin_spam" )->gte ( $start );
		$qb->field ( "dateupdated_admin_spam" )->lte ( $end );

		$total_unspam_today_access = $qb->getQuery ()->count ();
		return $total_unspam_today_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function spamTodayAccessQuestion() {
		$qb = $this->question->getQueryBuilder ();
		$currentDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ), date ( "Y" ) );
		$tomorowDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) );
		$start = new MongoDate ( $currentDate );
		$end = new MongoDate ( $tomorowDate );
		// var_dump($start);
		// var_dump($end);
		$qb->field ( "is_admin_spam" )->equals ( FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_SPAM );
		$qb->field ( "dateupdated_admin_spam" )->gte ( $start );
		$qb->field ( "dateupdated_admin_spam" )->lte ( $end );

		$total_spam_today_access = $qb->getQuery ()->count ();
		return $total_spam_today_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function spamRecentAccessQuestion() {
		$qb = $this->question->getQueryBuilder ();
		$qb->field ( "is_admin_spam" )->equals ( FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_SPAM );
		$tomorowDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) );
		$end = new MongoDate ( $tomorowDate );
		$qb->field ( "dateupdated_admin_spam" )->gte ( $end );
		$total_spam_recent_access = $qb->getQuery ()->count ();
		return $total_spam_recent_access;
	}
	/**
	 *
	 * @return Int
	 */
	private function unspamRecentAccessQuestion() {
		$qb = $this->question->getQueryBuilder ();
		$qb->field ( "is_admin_spam" )->equals ( FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_NOTSPAM );
		$tomorowDate = mktime ( 0, 0, 0, date ( "m" ), date ( "d" ) + 1, date ( "Y" ) );
		$end = new MongoDate ( $tomorowDate );
		$qb->field ( "dateupdated_admin_spam" )->gte ( $end );
		$total_unspam_recent_access = $qb->getQuery ()->count ();
		return $total_unspam_recent_access;
	}
	public function getQuestionReviewSpam($select, $subjectID, $from = null, $to = null, $type = 1) {
		$orderBy = array ();

		$qb = $this->question->getQueryBuilder ();
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		if (! empty ( $subjectID ) && $subjectID != - 1) {
			$qb->field ( "subject.id" )->equals ( $subjectID );
		}
		if ($type == 1) {
			// select document ispublish
			$qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );
			$qb->field ( "is_admin_spam" )->equals ( FAQParaConfig::IS_ADMIN_SPAM_STATUS_NOTACCESS );
			$qb->field ( "total_spam" )->gt ( 0 );
		} elseif ($type == 2) {
			// select document ispublish
			$qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_CLOSE );
			$qb->field ( "is_admin_spam" )->equals ( FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_SPAM );
		}

		$totalDocument = $qb->getQuery ()->count ();
		$orderBy = array (
				"total_spam" => "desc",
				"date_created" => "acs"
		);
		$qb = Util::addOrder ( $qb, $orderBy );
		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}
		$listQuestion = $qb->getQuery ()->execute ();
		if ($totalDocument < 10 && empty ( $subjectID ) && $type == 0) {
			$topQuestion = $this->getTopQuestion ( $select, $from, $to );
			$data = array (
					"totalDocument" => $totalDocument + $topQuestion ['totalDocument'],
					'listQuestion' => $listQuestion->toArray () + $topQuestion ['listQuestion']->toArray ()
			);
		} else {
			$data = array (
					"totalDocument" => $totalDocument,
					'listQuestion' => $listQuestion
			);
		}
		return $data;
	}
	public function getQuestionReviewUnspam($select, $subjectID, $from = null, $to = null, $type = 1) {
		$orderBy = array ();

		$qb = $this->question->getQueryBuilder ();
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		if (! empty ( $subjectID ) && $subjectID != - 1) {
			$qb->field ( "subject.id" )->equals ( $subjectID );
		}
		if ($type == 1) {
			// select document ispublish
			$qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_CLOSE );
			$qb->field ( "is_admin_spam" )->equals ( FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_SPAM );
			$qb->field ( "total_spam" )->lte ( 0 );
		} elseif ($type == 2) {
			// select document ispublish
			$qb->field ( "status" )->equals ( FAQParaConfig::QUESTION_STATUS_OPEN );
			$qb->field ( "is_admin_spam" )->equals ( FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_NOTSPAM );
		}

		$totalDocument = $qb->getQuery ()->count ();
		$orderBy = array (
				"total_spam" => "asc",
				"date_created" => "acs"
		);
		$qb = Util::addOrder ( $qb, $orderBy );
		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}
		$listQuestion = $qb->getQuery ()->execute ();
		if ($totalDocument < 10 && empty ( $subjectID ) && $type == 0) {
			$topQuestion = $this->getTopQuestion ( $select, $from, $to );
			$data = array (
					"totalDocument" => $totalDocument + $topQuestion ['totalDocument'],
					'listQuestion' => $listQuestion->toArray () + $topQuestion ['listQuestion']->toArray ()
			);
		} else {
			$data = array (
					"totalDocument" => $totalDocument,
					'listQuestion' => $listQuestion
			);
		}
		return $data;
	}
	public function getQuestionReviewEditQuestion($select, $subjectID, $from = null, $to = null, $type = 1) {
		$qb = $this->question->getQueryBuilder ();
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		if (! empty ( $subjectID ) && $subjectID != - 1) {
			$qb->field ( "subject.id" )->equals ( $subjectID );
		}
		if ($type == 1) {
			// select document ispublish

			$qb->field ( "is_approve_edit_question" )->equals ( FAQParaConfig::IS_APPROVE_EDIT_QUESTION_NOTACCESS );
		} elseif ($type == 2) {
			// select document ispublish

			$qb->field ( "is_approve_edit_question" )->equals ( FAQParaConfig::IS_APPROVE_EDIT_QUESTION_ACCESS );
		}

		$totalDocument = $qb->getQuery ()->count ();
		$orderBy = array (
				"dateupdated_approve_edit_question" => "asc",
				"date_created" => "acs"
		);
		$qb = Util::addOrder ( $qb, $orderBy );
		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}
		$listQuestion = $qb->getQuery ()->execute ();
		if ($totalDocument < 10 && empty ( $subjectID ) && $type == 0) {
			$topQuestion = $this->getTopQuestion ( $select, $from, $to );
			$data = array (
					"totalDocument" => $totalDocument + $topQuestion ['totalDocument'],
					'listQuestion' => $listQuestion->toArray () + $topQuestion ['listQuestion']->toArray ()
			);
		} else {
			$data = array (
					"totalDocument" => $totalDocument,
					'listQuestion' => $listQuestion
			);
		}
		return $data;
	}
	public function getQuestionReviewEditAnswer($select, $subjectID, $from = null, $to = null, $type = 1) {
		$qb = $this->question->getQueryBuilder ();
		// select field
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		if (! empty ( $subjectID ) && $subjectID != - 1) {
			$qb->field ( "subject.id" )->equals ( $subjectID );
		}
		if ($type == 1) {
			// select document ispublish

			$qb->field ( "is_approve_edit_answer" )->equals ( FAQParaConfig::IS_APPROVE_EDIT_ANSWER_NOTACCESS );
		} elseif ($type == 2) {
			// select document ispublish

			$qb->field ( "is_approve_edit_answer" )->equals ( FAQParaConfig::IS_APPROVE_EDIT_ANSWER_ACCESS );
		}

		$totalDocument = $qb->getQuery ()->count ();
		$orderBy = array (
				"dateupdated_approve_edit_answer" => "asc",
				"date_created" => "acs"
		);
		$qb = Util::addOrder ( $qb, $orderBy );
		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}
		$listQuestion = $qb->getQuery ()->execute ();
		if ($totalDocument < 10 && empty ( $subjectID ) && $type == 0) {
			$topQuestion = $this->getTopQuestion ( $select, $from, $to );
			$data = array (
					"totalDocument" => $totalDocument + $topQuestion ['totalDocument'],
					'listQuestion' => $listQuestion->toArray () + $topQuestion ['listQuestion']->toArray ()
			);
		} else {
			$data = array (
					"totalDocument" => $totalDocument,
					'listQuestion' => $listQuestion
			);
		}
		return $data;
	}
}

?>