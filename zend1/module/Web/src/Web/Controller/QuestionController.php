<?php

namespace Web\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\Mapper\SubjectMapper;
use Web\Forms\ContentQuestion;
use Web\Forms\FinishQuestion;
use FAQ\FAQCommon\Util;
use FAQ\Mapper\QuestionMapper;
use FAQ\Mapper\AuthMapper;
use FAQ\FAQCommon\Sessioncfg;
use FAQ\FAQEntity\Question;
use Exception;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQCommon\Appcfg;
use Zend\View\Model\ViewModel;
use FAQ\FAQCommon\Authcfg;
use FAQ\Mapper\UserMapper;
use Zend\Json\Json;
use FAQ\Mapper\MediaMapper;

class QuestionController extends FAQAbstractActionController {
	private $select = array (
			"id",
			"title",
			"content",
			"status",
			"subject",
			"date_created",
			"total_spam",
			"total_share",
			"total_view",
			"total_like",
			"total_dislike",
			"total_answer",
			"create_by",
			"bonus_point",
			"first_image",
			"short_content"
	);
	public function __construct() {
	}
	public function indexAction() {
		$this->setLayoutHome ();
		$subjectMapper = new SubjectMapper ();
		$list_subject = $subjectMapper->findSubject ( array (
				"id",
				"title"
		), null, null, null, FAQParaConfig::STATUS_ACTIVE, null, null, null, false );
		return array (
				"list_subject" => $list_subject
		);
	}
	public function overviewAction() {
		$isFirstLoad = true;
		$this->setLayoutAjax ();
		$subject_id = $this->getRequest ()->getPost ( 'subject' );
		$from = $this->getRequest ()->getPost ( 'from' );
		$to = $this->getRequest ()->getPost ( 'to' );
		if (! isset ( $from )) {
			$isFirstLoad = false;
			$from = 0;
		}
		if (! isset ( $to )) {
			$to = Appcfg::$question_paging_size;
		}
		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getOverview ( $this->getUserId (), $subject_id, $from, $to, $this->select );

		if (! $isFirstLoad) {
			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			);
		} else {
			$view = new ViewModel ( array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function openListAction() {
		$isFirstLoad = true;
		$this->setLayoutAjax ();
		$subject_id = $this->getRequest ()->getPost ( 'subject' );
		$from = $this->getRequest ()->getPost ( 'from' );
		$to = $this->getRequest ()->getPost ( 'to' );
		if (! isset ( $from )) {
			$isFirstLoad = false;
			$from = 0;
		}
		if (! isset ( $to )) {
			$to = Appcfg::$question_paging_size;
		}
		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getOpenList ( $this->getUserId (), $subject_id, $from, $to, $this->select );
		if (! $isFirstLoad) {
			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			);
		} else {
			$view = new ViewModel ( array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function draftAction() {
		$isFirstLoad = true;
		$this->setLayoutAjax ();
		$subject_id = $this->getRequest ()->getPost ( 'subject' );
		$from = $this->getRequest ()->getPost ( 'from' );
		$to = $this->getRequest ()->getPost ( 'to' );
		if (! isset ( $from )) {
			$isFirstLoad = false;
			$from = 0;
		}
		if (! isset ( $to )) {
			$to = Appcfg::$question_paging_size;
		}
		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getDraftList ( $this->getUserId (), $subject_id, $from, $to, $this->select );
		if (! $isFirstLoad) {
			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			);
		} else {
			$view = new ViewModel ( array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function closedListAction() {
		$isFirstLoad = true;
		$this->setLayoutAjax ();
		$subject_id = $this->getRequest ()->getPost ( 'subject' );
		$from = $this->getRequest ()->getPost ( 'from' );
		$to = $this->getRequest ()->getPost ( 'to' );
		if (! isset ( $from )) {
			$isFirstLoad = false;
			$from = 0;
		}
		if (! isset ( $to )) {
			$to = Appcfg::$question_paging_size;
		}
		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getClosedList ( $this->getUserId (), $subject_id, $from, $to, $this->select );
		if (! $isFirstLoad) {
			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			);
		} else {
			$view = new ViewModel ( array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function spamListAction() {
		$isFirstLoad = true;
		$this->setLayoutAjax ();
		$subject_id = $this->getRequest ()->getPost ( 'subject' );
		$from = $this->getRequest ()->getPost ( 'from' );
		$to = $this->getRequest ()->getPost ( 'to' );
		if (! isset ( $from )) {
			$isFirstLoad = false;
			$from = 0;
		}
		if (! isset ( $to )) {
			$to = Appcfg::$question_paging_size;
		}
		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getSpamList ( $this->getUserId (), $subject_id, $from, $to, $this->select );
		if (! $isFirstLoad) {
			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			);
		} else {
			$view = new ViewModel ( array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function followListAction() {
		$isFirstLoad = true;
		$this->setLayoutAjax ();
		$subject_id = $this->getRequest ()->getPost ( 'subject' );
		$from = $this->getRequest ()->getPost ( 'from' );
		$to = $this->getRequest ()->getPost ( 'to' );
		if (! isset ( $from )) {
			$isFirstLoad = false;
			$from = 0;
		}
		if (! isset ( $to )) {
			$to = Appcfg::$question_paging_size;
		}
		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getFollowList ( $this->getUserId (), $subject_id, $from, $to, $this->select );
		if (! $isFirstLoad) {
			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			);
		} else {
			$view = new ViewModel ( array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function askmeListAction() {
		$isFirstLoad = true;
		$this->setLayoutAjax ();
		$subject_id = $this->getRequest ()->getPost ( 'subject' );
		$from = $this->getRequest ()->getPost ( 'from' );
		$to = $this->getRequest ()->getPost ( 'to' );
		if (! isset ( $from )) {
			$isFirstLoad = false;
			$from = 0;
		}
		if (! isset ( $to )) {
			$to = Appcfg::$question_paging_size;
		}
		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getAskMeList ( $this->getUserId (), $subject_id, $from, $to, $this->select );
		if (! $isFirstLoad) {
			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			);
		} else {
			$view = new ViewModel ( array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	// public function createAction() {
	// $privilege = Util::isPrivilege ( $this, Authcfg::CREATE_POSTS );
	// if ($privilege ['role'] == Authcfg::GUEST) {
	// return $this->toNoticeWarning ( "Bạn cần đăng nhập để đặt câu hỏi", 5000, "/user/login" );
	// } elseif (! $privilege ['privilegeByPoint']) {
	// return $this->toNoticeWarning ( "Số điểm câu hỏi của bạn không đủ để đặt câu hỏi" );
	// }

	// $this->setLayoutBasic ();
	// $subjectMapper = new SubjectMapper ();
	// $list_subject = $subjectMapper->findSubject ( array (
	// "id",
	// "title",
	// "first_image"
	// ), null, null, null, FAQParaConfig::STATUS_ACTIVE, null, null, null, false );
	// return array (
	// 'list_subject' => $list_subject
	// );
	// }
	// public function selectSubjectAction() {
	// $isMetaSubject = true;
	// $privilege = Util::isPrivilege ( $this, Authcfg::PARTICIPATE_IN_META );
	// if ($privilege ['role'] == Authcfg::GUEST) {
	// return $this->toNoticeWarning ( "Bạn cần đăng nhập để đặt câu hỏi", 5000, "/user/login" );
	// } elseif (! $privilege ['privilegeByPoint']) {
	// $isMetaSubject = false;
	// }
	// $this->setLayoutAjax ();
	// $subjectMapper = new SubjectMapper ();
	// $subjectsFollow = $subjectMapper->findSubject ( null, null, Util::getIDCurrentUser (), true, FAQParaConfig::STATUS_ACTIVE, null, null, null, true, $isMetaSubject );
	// $subjectsUnfollow = $subjectMapper->findSubject ( null, null, Util::getIDCurrentUser (), false, FAQParaConfig::STATUS_ACTIVE, null, null, null, true, $isMetaSubject );
	// $subjects = $subjectsFollow->toArray () + $subjectsUnfollow->toArray ();
	// return array (
	// "subjects" => array_slice ( $subjects, 0, 12 ),
	// "totalSubject" => count ( $subjects ),
	// "actionName" => $this->params ( "action" )
	// );
	// }
	// public function contentQuestionAction() {
	// $privilege = Util::isPrivilege ( $this, Authcfg::PARTICIPATE_IN_META );
	// if ($privilege ['role'] == Authcfg::GUEST) {
	// return $this->toNoticeWarning ( "Bạn cần đăng nhập để đặt câu hỏi", 5000, "/user/login" );
	// }
	// $error = "";
	// $this->setLayoutAjax ();
	// $subjectID = $this->params ()->fromPost ( 'subject' );
	// if (! empty ( $subjectID )) {

	// $questionMapper = new QuestionMapper ();
	// $questionID = Util::getSessionParam ( Sessioncfg::DRAFT_QUESTION_ID );
	// $isCreateNew = false;
	// if (! empty ( $questionID )) {
	// $question = $questionMapper->getOneQuestion ( $questionID );
	// $oldSubject = $question->getSubject ();
	// $question->setStatusUpdateRefere ();
	// } else {
	// $isCreateNew = true;
	// /* @var $question \FAQ\FAQEntity\Question */
	// $question = new Question ();
	// $question->setTitle ( "Chưa có tiêu đề" );
	// $question->setCreateBy ( Util::getCurrentUser () );
	// $question->setStatus ( FAQParaConfig::QUESTION_STATUS_DRAFT );
	// }
	// $subjectMapper = new SubjectMapper ();
	// $subject = $subjectMapper->getOneStubject ( $subjectID );
	// $subjectStatus = $subject->getStatus ();
	// $subjectStatus = $subject->getStatus ();
	// if ($subjectStatus == FAQParaConfig::SUBJECT_META && ! $privilege ['privilegeByPoint']) {
	// return $this->toNoticeWarning ( "Điểm câu hỏi không đủ để tạo câu hỏi trong chủ đề đã chọn!", 100, "/" );
	// }

	// // var_dump($subject);
	// $question->setSubject ( $subject );

	// try {
	// if ($isCreateNew == true) {
	// $question = $questionMapper->create ( $question );
	// Util::setSessionParam ( Sessioncfg::DRAFT_QUESTION_ID, $question->getId () );
	// } else {
	// $questionMapper->update ( $question, false, $oldSubject );
	// }

	// // var_dump(Util::getSessionParam(Sessioncfg::DRAFT_QUESTION_ID));
	// } catch ( Exception $e ) {
	// $error = Util::bootstrapAlert ( $e->getMessage () );
	// }
	// } else {
	// $error = Util::bootstrapAlert ( "chưa chọn chủ đề cho câu hỏi" );
	// }
	// $form = new ContentQuestion ( 'content-question' );
	// return array (
	// 'form' => $form,
	// 'error' => $error,
	// 'contentQuestion' => $question->getContent (),
	// 'titleQuestion' => $question->getTitle ()
	// );
	// }
	// public function saveQuestionAction() {
	// $this->setLayoutAjax ();
	// header ( "Content-Type: application/json" );
	// $statusAccess = 0;
	// try {
	// // status return equal 0 => unsuccess action

	// if ($this->getRequest ()->isPost ()) {
	// $questionMapper = new QuestionMapper ();
	// $bonusPoint = $this->params ()->fromPost ( 'bonusPoint' );
	// $tags = $this->params ()->fromPost ( 'tags' );

	// // edit wiki style
	// $title = $this->params ()->fromPost ( 'title' );
	// $contentQuestion = $this->params ()->fromPost ( 'contentQuestion' );
	// $subjectID = $this->params ()->fromPost ( 'subject' );
	// // $questionMapper->
	// $questionMapper = new QuestionMapper ();
	// $question = $questionMapper->getOneQuestion ( Util::getSessionParam ( Sessioncfg::DRAFT_QUESTION_ID ) );
	// $question->setStatusUpdateRefere ();
	// if (! empty ( $bonusPoint )) {
	// $question->setOldBonusPoint ( $question->getBonusPoint () );
	// $question->setBonusPoint ( $bonusPoint );
	// }
	// if (! empty ( $title )) {
	// $question->setTitle ( $title );
	// }
	// if (! empty ( $contentQuestion )) {
	// $question->setContent ( $contentQuestion );
	// }
	// if (! empty ( $subjectID )) {
	// $subjectMapper = new SubjectMapper ();
	// $subject = $subjectMapper->getOneStubject ( $subjectID );
	// if (! empty ( $subject )) {
	// // var_dump($subject);
	// $question->setSubject ( $subject );
	// }
	// }
	// $isCreateNew = false;
	// if ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_DRAFT )) {
	// $isCreateNew = true;
	// }
	// $question->setStatus ( FAQParaConfig::QUESTION_STATUS_OPEN );
	// $keyWords = explode ( ",", $tags );
	// // var_dump($keyWords);
	// $question->removeAllKeyWord ();
	// foreach ( $keyWords as $key => $value ) {
	// if (trim ( $value ) != "") {
	// $question->setKeyWord ( trim ( $value ) );
	// }
	// }
	// $statusAccess = $questionMapper->update ( $question, $isCreateNew );
	// Util::clearSessionParam ( Sessioncfg::DRAFT_QUESTION_ID );
	// // var_dump(Util::getSessionParam(Sessioncfg::DRAFT_QUESTION_ID));
	// }
	// } catch ( Exception $e ) {
	// $statusAccess = 0;
	// Util::writeLog ( $e->getTraceAsString () );
	// }

	// $data = array (
	// "status" => $statusAccess
	// );
	// // echo $data;
	// return array (
	// "data" => $data
	// );
	// }
	public function saveWikistyleAction() {
		// check privilege
		$privilege = Util::isPrivilege ( $this, Authcfg::EDIT_COMMUNITY_WIKI );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 0;
			goto breakbusiness;
		}

		$this->setLayoutAjax ();
		header ( "Content-Type: application/json" );
		$statusAccess = 0;
		$oldSubject = null;
		$newSubject = null;
		try {
			// status return equal 0 => unsuccess action

			if ($this->getRequest ()->isPost ()) {
				$questionMapper = new QuestionMapper ();
				$tags = $this->params ()->fromPost ( 'tags' );
				// edit wiki style
				$newTitle = $this->params ()->fromPost ( 'title' );
				$questionID = $this->params ()->fromPost ( 'question' );
				$newContent = $this->params ()->fromPost ( 'contentQuestion' );
				$newSubjectID = $this->params ()->fromPost ( 'subject' );
				$newBonusPoint = $this->params ()->fromPost ( 'bonusPoint' );
				if (empty ( $newBonusPoint ) || $newBonusPoint < 1) {
					$newBonusPoint = 0;
				}

				$subjectMapper = new SubjectMapper ();
				$newSubject = $subjectMapper->getOneStubject ( $newSubjectID );
				$noteEdit = $this->params ()->fromPost ( 'noteEdit' );

				$question = $questionMapper->getOneQuestion ( $questionID );
				if (empty ( $question )) {
					// var_dump($question);
					$statusAccess = 0;
					goto breakbusiness;
				}

				if (! $privilege ['privilegeByPoint'] && $question->getCreateBy ()->getId () != Util::getIDCurrentUser ()) {
					$statusAccess = 0;
					goto breakbusiness;
				}
				$oldContent = $question->getContent ();
				$oldTitle = $question->getTitle ();
				$oldTag = $question->getKeyWord ();
				// var_dump("==================");
				// var_dump($oldTag);
				$oldSubject = $question->getSubject ();
				$oldSubjectID = $oldSubject->getId ();

				$newTag = array ();
				$keyWords = explode ( ",", $tags );
				foreach ( $keyWords as $key => $value ) {
					if (trim ( $value ) != "") {
						$newTag [] = trim ( $value );
					}
				}

				$unionTags = $oldTag + $newTag;

				if ($oldContent == $newContent && $oldTitle == $newTitle && $oldSubjectID == $newSubjectID && $newBonusPoint == 0 && count ( $unionTags ) == count ( $oldTag )) {
					// khong thay doi j
					$statusAccess = 3;
					goto breakbusiness;
				}
				$statusAccess = $questionMapper->updateWikistyle ( $question, $newBonusPoint, $newTitle, $newContent, $newSubject, $newTag, $noteEdit, $oldSubject, $oldContent, $oldTitle, $oldTag );
			}
		} catch ( Exception $e ) {
			$statusAccess = 0;
			Util::writeLog ( $e->getTraceAsString () );
		}
		breakbusiness:
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	public function bonusPointAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type: application/json" );
		$statusAccess = 0;
		$oldSubject = null;
		$newSubject = null;
		try {
			// status return equal 0 => unsuccess action

			if ($this->getRequest ()->isPost ()) {
				$questionMapper = new QuestionMapper ();

				// edit wiki style

				$newBonusPoint = $this->params ()->fromPost ( 'bonusPoint' );
				$questionID = $this->params ()->fromPost ( 'question' );
				$totalMoneyPointCurrentUser = Util::getCurrentUser ()->getTotalMoneyPoint ();
				if (empty ( $newBonusPoint ) || $newBonusPoint < 1 || $totalMoneyPointCurrentUser < $newBonusPoint) {
					$newBonusPoint = 0;
				}

				$noteEdit = $this->params ()->fromPost ( 'noteEdit' );
				// $questionMapper->
				$questionMapper = new QuestionMapper ();
				$question = $questionMapper->getOneQuestion ( $questionID );
				if (empty ( $question )) {
					var_dump ( $question );
					$statusAccess = 0;
					goto breakbusiness;
				}
				$oldContent = $question->getContent ();
				$oldTitle = $question->getTitle ();
				$oldTag = $question->getKeyWord ();
				// var_dump("==================");
				// var_dump($oldTag);
				$oldSubject = $question->getSubject ();
				$oldSubjectID = $oldSubject->getId ();

				// nochange
				$newTitle = $oldTitle;
				$newContent = $oldContent;
				$newSubject = $oldSubject;
				$newTag = $oldTag;

				if ($newBonusPoint == 0) {
					// khong thay doi j
					$statusAccess = 3;
					goto breakbusiness;
				}
				$statusAccess = $questionMapper->updateWikistyle ( $question, $newBonusPoint, $newTitle, $newContent, $newSubject, $newTag, $noteEdit, $oldSubject, $oldContent, $oldTitle, $oldTag );
			}
		} catch ( Exception $e ) {
			$statusAccess = 0;
			Util::writeLog ( $e->getTraceAsString () );
		}
		breakbusiness:
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	public function finishQuestionAction() {
		$error = "";
		$this->setLayoutAjax ();
		// FinishQuestion
		$form = new FinishQuestion ( 'finish-question' );
		if ($this->getRequest ()->isPost ()) {
			$questionMapper = new QuestionMapper ();
			$tags = $this->params ()->fromPost ( 'tags' );
			$title = $this->params ()->fromPost ( 'title' );
			$contentQuestion = $this->params ()->fromPost ( 'contentQuestion' );

			if (strlen ( trim ( $title ) ) < 20 || strlen ( trim ( $contentQuestion ) ) < 50 || strlen ( trim ( $tags ) ) < 1) {
				return $this->toNoticeWarning ( "Có lỗi xẩy ra", 2000, "/question/create" );
			}
			// var_dump($contentQuestion);
			$subjectID = $this->params ()->fromPost ( 'subject' );
			$subjectMapper = new SubjectMapper ();
			$questionID = Util::getSessionParam ( Sessioncfg::DRAFT_QUESTION_ID );
			$question = $questionMapper->getOneQuestion ( $questionID );
			$question->setStatusUpdateRefere ();
			$bonus_point = $question->getBonusPoint ();

			try {

				$question->setCreateBy ( Util::getCurrentUser () );
				$question->setTitle ( $title );
				$question->setContent ( $contentQuestion );
				$keyWords = explode ( ",", $tags );
				foreach ( $keyWords as $key => $value ) {
					if (trim ( $value ) != "") {
						$question->setKeyWord ( trim ( $value ) );
					}
				}

				$questionMapper->update ( $question );
			} catch ( Exception $e ) {
				$error = Util::bootstrapAlert ( $e->getMessage () );
			}
			$data = array (
					"subject_title" => $subjectMapper->getOneStubject ( $subjectID )->getTitle (),
					"title" => $title,
					"content_question" => $contentQuestion,
					"key_word" => $tags,
					'bonus_point' => $bonus_point
			);

			// get question reationship
			$questionReationship = $questionMapper->getQuestionRelationship ( $questionID, 0, 30 );

			// var_dump($data);
			return array (
					'form' => $form,
					"data" => $data,
					'error' => $error,
					'list_question' => $questionReationship
			);
		} else {
			$data = $this->getRequest ()->getMetadata ( "data" );
			return array (
					'form' => $form,
					"data" => $data,
					'error' => $error
			);
		}
	}
	public function detailAction() {
		$questionID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		$questionMapper = new QuestionMapper ();
		$question = $questionMapper->getOneQuestion ( $questionID );
		if (empty ( $question ) || $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_TEMP_DELETE ) || ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_DRAFT ) && $question->getCreateBy ()->getId () != Util::getIDCurrentUser ())) {

			return $this->toNoticeWarning ( "Câu hỏi hiện không có!", 3000, "/" );
		}

		$questionMapper->updateTotalView ( $question );
		$this->getRequest ()->setMetadata ( array (
				"questionID" => $questionID
		) );

		$privilege = Util::isPrivilege ( $this, Authcfg::PROTECT_QUESTIONS );
		$isAllowProtectQuestion = false;
		$isSetHighLight = false;
		$isEstablishedUser = false;
		$isCastCloseAndReopenVotes = false;
		$isGuest = $privilege ['role'] == Authcfg::GUEST;
		if ($isGuest) {
			$this->setLayoutQuestionGuest ();
		} else {
			$this->setLayoutQuestionDetail ();
			if ($privilege ['privilegeByPoint']) {
				$isAllowProtectQuestion = true;
			}
		}
		if ($privilege ['totalRankPoint'] >= Authcfg::ESTABLISHED_USER) {
			$isEstablishedUser = true;
		}
		if ($privilege ['totalRankPoint'] >= Authcfg::SET_HIGHLIGHT) {
			$isSetHighLight = true;
		}
		if ($privilege ['totalRankPoint'] >= Authcfg::CAST_CLOSE_AND_REOPEN_VOTES) {
			$isCastCloseAndReopenVotes = true;
		}
		$type_editor = "MARKDOWN";
		$isQuestionClosed = $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE );
		$isQuestionHighLight = $question->getIsHighlight () == FAQParaConfig::STATUS_ACTIVE;
		return array (
				"question" => $question,
				"type_editor" => $type_editor,
				"isAllowProtectQuestion" => $isAllowProtectQuestion,
				"isEstablishedUser" => $isEstablishedUser,
				"totalRankPoint" => $privilege ['totalRankPoint'],
				"isCastCloseAndReopenVotes" => $isCastCloseAndReopenVotes,
				"isQuestionClosed" => $isQuestionClosed,
				"isSetHighLight" => $isSetHighLight,
				"isQuestionHighLight" => $isQuestionHighLight,
				'isGuest' => $isGuest
		);
	}
	public function memberSpamAction() {
	}
	public function prememberSpamAction() {
		$this->setLayoutAjax ();
		if ($this->getRequest ()->isPost ()) {
			$questionID = $this->params ()->fromPost ( "question" );

			try {
				$questionMapper = new QuestionMapper ();

				$users = $questionMapper->getMemberSpam ( $questionID, 0, 4 );
			} catch ( Exception $e ) {

				Util::writeLog ( $e->getTraceAsString () );
			}
		}

		$view = new ViewModel ( array (
				"users" => $users
		) );
		$view->setTemplate ( 'web/question/premember.phtml' ); // path to phtml file under view folder
		return $view;
	}
	public function prememberAnswerAction() {
		$this->setLayoutAjax ();
		if ($this->getRequest ()->isPost ()) {
			$questionID = $this->params ()->fromPost ( "question" );

			try {
				$questionMapper = new QuestionMapper ();

				$users = $questionMapper->getMemberAnswer ( $questionID, 0, 4 );
			} catch ( Exception $e ) {

				Util::writeLog ( $e->getTraceAsString () );
			}
		}
		$view = new ViewModel ( array (
				"users" => $users
		) );
		$view->setTemplate ( 'web/question/premember.phtml' ); // path to phtml file under view folder
		return $view;
	}
	public function memberShareAction() {
	}
	public function prememberShareAction() {
		$this->setLayoutAjax ();
		if ($this->getRequest ()->isPost ()) {
			$questionID = $this->params ()->fromPost ( "question" );

			try {
				$questionMapper = new QuestionMapper ();

				$users = $questionMapper->getMemberShare ( $questionID, 0, 4 );
			} catch ( Exception $e ) {

				Util::writeLog ( $e->getTraceAsString () );
			}
		}

		$view = new ViewModel ( array (
				"users" => $users
		) );
		$view->setTemplate ( 'web/question/premember.phtml' ); // path to phtml file under view folder
		return $view;
	}
	public function memberFollowAction() {
	}
	public function prememberFollowAction() {
		$this->setLayoutAjax ();
		if ($this->getRequest ()->isPost ()) {
			$questionID = $this->params ()->fromPost ( "question" );

			try {
				$questionMapper = new QuestionMapper ();

				$users = $questionMapper->getMemberFollow ( $questionID, 0, 4 );
			} catch ( Exception $e ) {

				Util::writeLog ( $e->getTraceAsString () );
			}
		}

		$view = new ViewModel ( array (
				"users" => $users
		) );
		$view->setTemplate ( 'web/question/premember.phtml' ); // path to phtml file under view folder
		return $view;
	}
	public function spamAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		// check privilege
		$privilege = Util::isPrivilege ( $this, Authcfg::FLAG_POSTS );
		if ($privilege ['role'] == Authcfg::GUEST) {
			$statusAccess = 0;
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = 4;
		} else {
			// logic business
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );
				$typespam = $this->params ()->fromPost ( "typespam" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->reportSpam ( $questionID, Util::getCurrentUser (), $typespam );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		$data = array (
				"status" => $statusAccess
		);
		// echo $data;
		return array (
				"data" => $data
		);
	}
	/*
	 * public function unspamAction() { $this->setLayoutAjax (); header ( "Content-Type:application/json" ); // check privilege $privilege = Util::isPrivilege ( $this ); if ($privilege ['role'] == Authcfg::GUEST) { $statusAccess = 0; } else { if ($this->getRequest ()->isPost ()) { $questionID = $this->params ()->fromPost ( "question" ); try { $questionMapper = new QuestionMapper (); $questionMapper->unReportSpam ( $questionID, Util::getCurrentUser () ); $statusAccess = 1; } catch ( Exception $e ) { $statusAccess = 0; Util::writeLog ( $e->getTraceAsString () ); } } } $data = array ( "status" => $statusAccess ); // echo $data; return array ( "data" => $data ); }
	 */
	public function shareAction() {
		$this->setLayoutAjax ();
	}
	public function formSpamAction() {
		$isFlagPosts = false;
		$privilege = Util::isPrivilege ( $this, Authcfg::FLAG_POSTS );
		$isAllowed = $privilege ['isAllowed'];
		$isFlagPosts = $privilege ['privilegeByPoint'];
		$this->setLayoutAjax ();
		return array (
				"isFlagPosts" => $isFlagPosts,
				"isAllowed" => $isAllowed
		);
	}
	public function addShareAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		// check privilege
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 0;
		} else {
			// check privilege
			$privilege = Util::isPrivilege ( $this );
			if ($privilege ['role'] == Authcfg::GUEST) {
				$statusAccess = 0;
			} else {
				if ($this->getRequest ()->isPost ()) {
					$questionID = $this->params ()->fromPost ( "question" );

					try {
						$questionMapper = new QuestionMapper ();
						$questionMapper->shareQuestion ( $questionID, Util::getCurrentUser () );

						$statusAccess = 1;
					} catch ( Exception $e ) {
						$statusAccess = 0;
						Util::writeLog ( $e->getTraceAsString () );
					}
				}
			}
		}
		$data = array (
				"status" => $statusAccess
		);
		// echo $data;
		return array (
				"data" => $data
		);
	}
	public function followAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		// check privilege
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 0;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->followQuestion ( $questionID, Util::getCurrentUser () );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		$data = array (
				"status" => $statusAccess
		);
		// echo $data;
		return array (
				"data" => $data
		);
	}
	public function unfollowAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		// check privilege
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 0;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$questionMapper->unFollowQuestion ( $questionID, Util::getCurrentUser () );

					$statusAccess = 1;
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		$data = array (
				"status" => $statusAccess
		);
		// echo $data;
		return array (
				"data" => $data
		);
	}
	private function getUserId() {
		$authMapper = new AuthMapper ();
		return $authMapper->getSessionParam ( Sessioncfg::$user_id );
	}
	public function deleteAction() {
		$this->setLayoutAjax ();
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 0;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->getRequest ()->getPost ( 'question' );
				try {
					$questionMapper = new QuestionMapper ();
					$questionMapper->delete ( $questionID );
					Util::clearSessionParam ( Sessioncfg::DRAFT_QUESTION_ID );
					$statusAccess = 1;
				} catch ( Exception $e ) {
					$statusAccess = 0;
				}
			}
		}
		$data = array (
				"status" => $statusAccess
		);
		return array (
				"data" => $data
		);
	}
	public function closeAction() {
		$this->setLayoutAjax ();
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 0;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->getRequest ()->getPost ( 'question' );
				try {
					$questionMapper = new QuestionMapper ();
					$question = $questionMapper->getOneQuestion ( $questionID );
					$question->setStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE );
					$statusAccess = $questionMapper->update ( $question );
				} catch ( Exception $e ) {
					$statusAccess = 0;
				}
			}
		}
		$data = array (
				"status" => $statusAccess
		);
		return array (
				"data" => $data
		);
	}

	/**
	 *
	 * @todo use in layout
	 *       http://123hoidap.vn/member/question/52765e189df815f8260006bf
	 */
	public function chartQuestionAction() {
		$userID = $this->getRequest ()->getMetadata ( "userID" );
		$userMapper = new UserMapper ();

		if (empty ( $userID )) {
			$userID = Util::getIDCurrentUser ();
		}
		/* @var $currentUser \FAQ\FAQEntity\User */
		$currentUser = $userMapper->getOneUser ( $userID );
		$chartData = array ();
		if (! empty ( $currentUser )) {
			$chartData = array (
					"total_question" => $currentUser->getTotalQuestion (),
					"total_open_question" => $currentUser->getTotalOpenQuestion (),
					"total_close_question" => $currentUser->getTotalClosedQuestion (),
					"total_spam_question" => $currentUser->getTotalSpamQuestion ()
			);
		}
		$this->layout ()->chartData = $chartData;
	}
	public function editWikistyleAction() {
		// check privilege
		$privilege = Util::isPrivilege ( $this, Authcfg::EDIT_COMMUNITY_WIKI );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập để đăng bài câu hỏi", 5000, "/user/login" );
		}

		$this->setLayoutBasic ();
		// edit
		$questionID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );

		$subjectID = null;
		$tags = "";
		$titleQuestion = "";
		$contentQuestion = "";

		// ---------------------------------------
		$error = "";

		$questionMapper = new QuestionMapper ();

		if (! empty ( $questionID )) {

			/* @var $question \FAQ\FAQEntity\Question */
			$question = $questionMapper->getOneQuestion ( $questionID );
			if (empty ( $question )) {
				Util::clearSessionParam ( Sessioncfg::DRAFT_QUESTION_ID );
				return $this->toNoticeWarning ( "Câu hỏi không tồn tại!", 3000, "/" );
			} else {

				if (! $privilege ['privilegeByPoint'] && $question->getCreateBy ()->getId () != Util::getIDCurrentUser ()) {
					return $this->toNoticeWarning ( "Điểm câu hỏi yêu cầu " . Authcfg::EDIT_COMMUNITY_WIKI . "đ", 3000, "/" );
				}

				// $statusQuestion=$question->getStatus();
				$dataUpdateBest = $question->getDate_update_best ();
				if (! empty ( $dataUpdateBest ) || ! $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_OPEN )) {
					Util::clearSessionParam ( Sessioncfg::DRAFT_QUESTION_ID );
					return $this->toNoticeWarning ( "Không thể sửa câu hỏi đã đóng!", 3000, "/" );
				}
			}
			$subject = $question->getSubject ();
			if (isset ( $subject )) {
				$subjectID = $subject->getId ();
			}
			$keyWord = $question->getKeyWord ();

			foreach ( $keyWord as $key => $tag ) {
				$tags = $tags . "," . $tag;
			}
			$titleQuestion = $question->getTitle ();
			$contentQuestion = $question->getContent ();
		} else {
			return $this->toNoticeWarning ( "Câu hỏi không tồn tại!", 3000, "/" );
		}

		$form = new ContentQuestion ( 'content-question' );

		// select subject
		$subjectMapper = new SubjectMapper ();
		$subjectsFollow = $subjectMapper->findSubject ( null, null, Util::getIDCurrentUser (), true, FAQParaConfig::STATUS_ACTIVE, null, null, null, true, true );
		$subjectsUnfollow = $subjectMapper->findSubject ( null, null, Util::getIDCurrentUser (), false, FAQParaConfig::STATUS_ACTIVE, null, null, null, true, true );
		$listSubject = $subjectsFollow->toArray () + $subjectsUnfollow->toArray ();

		return array (
				'questionID' => $questionID,
				'subjectID' => $subjectID,
				'tags' => $tags,
				'titleQuestion' => $titleQuestion,
				'contentQuestion' => $contentQuestion,
				"list_subject" => $listSubject,
				'form' => $form,
				'error' => $error,
				"backUrl" => Appcfg::$domain . "/question/detail/" . $questionID . "/" . Util::convertUrlSeo ( $titleQuestion )
		);
	}
	public function dislikeAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this, Authcfg::VOTE_DOWN );
		$isEstablishedUser = false;
		if (! $privilege ['isAllowed']) {
			$statusAccess = array (
					"status" => 0,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = array (
					"status" => 3,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		} else {
			if ($privilege ['totalRankPoint'] >= Authcfg::ESTABLISHED_USER) {
				$isEstablishedUser = true;
			}
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->dislikeQuestion ( $questionID, Util::getCurrentUser (), $isEstablishedUser );
				} catch ( Exception $e ) {
					$statusAccess = array (
							"status" => 0,
							"toatlLike" => null,
							"totalDislike" => null,
							"totalPoint" => null
					);
					Util::writeLog ( $e->getMessage () );
				}
			}
		}
		echo Json::encode ( $statusAccess );
		return $this->getResponse ();
	}
	public function likeAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );

		$userLike = Util::getCurrentUser ();

		$privilege = Util::isPrivilege ( $this, Authcfg::VOTE_UP );
		$isEstablishedUser = false;
		if (! $privilege ['isAllowed']) {
			$statusAccess = array (
					"status" => 2,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = array (
					"status" => 3,
					"toatlLike" => null,
					"totalDislike" => null,
					"totalPoint" => null
			);
		} else {
			if ($privilege ['totalRankPoint'] >= Authcfg::ESTABLISHED_USER) {
				$isEstablishedUser = true;
			}
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();

					$statusAccess = $questionMapper->likeQuestion ( $questionID, $userLike, $isEstablishedUser );
				} catch ( Exception $e ) {
					$statusAccess = array (
							"status" => 0,
							"toatlLike" => null,
							"totalDislike" => null,
							"totalPoint" => null
					);
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}

		echo Json::encode ( $statusAccess );
		return $this->getResponse ();
	}
	public function revisionAction() {
		$privilege = Util::isPrivilege ( $this );
		if ($privilege ['role'] == Authcfg::GUEST) {
			$this->setLayoutGuest ();
		} else {
			$this->setLayoutBasic ();
		}

		$questionID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		$questionMapper = new QuestionMapper ();
		$question = $questionMapper->getOneQuestion ( $questionID );
		if (empty ( $question )) {
			return $this->toNoticeWarning ( "Câu hỏi không tồn tại!", 3000, "/" );
		}

		$historyQuestion = $question->getHistoryContent ();
		$userCreateQuestionID = $question->getCreateBy ()->getId ();
		$titleQuestion = $question->getTitle ();
		$backUrl = "/question/detail/$questionID/" . Util::convertUrlSeo ( $titleQuestion );
		return array (
				"historyQuestion" => $historyQuestion,
				"userCreateQuestionID" => $userCreateQuestionID,
				"questionID" => $questionID,
				"backUrl" => $backUrl
		);
	}
	public function activeVersionAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 2;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );
				$contentHistoryID = $this->params ()->fromPost ( "contentHistory" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->setContentActive ( $questionID, $contentHistoryID );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	public function protectQuestionAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this, Authcfg::PROTECT_QUESTIONS );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 2;
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = 3;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->protectQuestion ( $questionID );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	/**
	 *
	 * @return <br> 0 loi
	 *         <br> 1 thanh cong
	 *         <br> 2 khong du quyen
	 *         <br> 3 khong du diem
	 *         <br> 4 khong la nguoi cho vao protect =>khong the bo ra
	 */
	public function unprotectQuestionAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this, Authcfg::PROTECT_QUESTIONS );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 2;
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = 3;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->unprotectQuestion ( $questionID );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	public function closeQuestionAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this, Authcfg::CAST_CLOSE_AND_REOPEN_VOTES );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 2;
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = 3;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->closeQuestion ( $questionID );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	public function reopenQuestionAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this, Authcfg::CAST_CLOSE_AND_REOPEN_VOTES );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 2;
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = 3;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->reopenQuestion ( $questionID );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	public function highlightQuestionAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this, Authcfg::SET_HIGHLIGHT );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 2;
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = 3;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->highlightQuestion ( $questionID );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	public function unhighlightQuestionAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this, Authcfg::SET_HIGHLIGHT );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 2;
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = 3;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->unhighlightQuestion ( $questionID );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	public function topQuestionAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this, Authcfg::SET_HIGHLIGHT );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 2;
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = 3;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->topQuestion ( $questionID );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	public function untopQuestionAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this, Authcfg::SET_HIGHLIGHT );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 2;
		} elseif (! $privilege ['privilegeByPoint']) {
			$statusAccess = 3;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->untopQuestion ( $questionID );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getTraceAsString () );
				}
			}
		}
		echo Json::encode ( array (
				"status" => $statusAccess
		) );
		return $this->getResponse ();
	}
	public function saveAskNowAction() {
		$privilege = Util::isPrivilege ( $this, Authcfg::CREATE_POSTS );
		if ($privilege ['role'] == Authcfg::GUEST) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập để đặt câu hỏi", 5000, "/user/login" );
		} elseif (! $privilege ['privilegeByPoint']) {
			return $this->toNoticeWarning ( "Số điểm câu hỏi của bạn không đủ để đặt câu hỏi" );
		}
		$this->setLayoutAjax ();
		$title = trim ( $this->params ()->fromPost ( 'title' ) );
		$listImg = trim ( $this->params ()->fromPost ( 'listImg' ) );
		$contentQuestion = trim ( $this->params ()->fromPost ( 'content' ) );
		$subjectID = trim ( $this->params ()->fromPost ( 'subject' ) );
		$listTag = trim ( $this->params ()->fromPost ( 'listTag' ) );
		$bonusPoint = intval ( trim ( $this->params ()->fromPost ( 'bonus' ) ) );
		if (strlen ( $title ) < 8 || strlen ( $contentQuestion ) < 16 || empty ( $subjectID ) || empty ( $listTag ) || $bonusPoint < 0) {
			echo Util::bootstrapAlert ( "Câu hỏi không hợp lệ!" );
			goto breakPoint;
		}
		if (! empty ( $listImg )) {
			$arrayImg = explode ( ",", $listImg );
			$mediaMapper = new MediaMapper ();
			foreach ( $arrayImg as $key => $imgId ) {
				if (! empty ( $imgId )) {
					$media = $mediaMapper->findMediaByID ( $imgId );
					if (! empty ( $media )) {
						$name = $media->getName ();
						$fileName = $name;
						$contentQuestion = $contentQuestion . '<p><img style="-webkit-user-select: none;" title="' . $name . '" src="/media/get-media-image/images/' . $imgId . '/' . $fileName . '" alt="' . $name . '" width="auto" height="auto"></p>';
					}
				}
			}
		}

		$questionMapper = new QuestionMapper ();
		/* @var $question \FAQ\FAQEntity\Question */
		$question = new Question ();
		$question->setCreateBy ( Util::getCurrentUser () );

		$subjectMapper = new SubjectMapper ();
		$subject = $subjectMapper->getOneStubject ( $subjectID );
		$subjectStatus = $subject->getStatus ();
		if ($subjectStatus == FAQParaConfig::SUBJECT_META && ! $privilege ['privilegeByPoint']) {
			echo Util::bootstrapAlert ( "Điểm câu hỏi không đủ để tạo câu hỏi trong chủ đề đã chọn!" );
			goto breakPoint;
		}

		// var_dump($subject);
		$question->setSubject ( $subject );

		if (! empty ( $bonusPoint )) {
			$question->setBonusPoint ( $bonusPoint );
		}
		if (! empty ( $title )) {
			$question->setTitle ( $title );
		}
		if (! empty ( $contentQuestion )) {
			$question->setContent ( $contentQuestion );
		}
		try {
			if (! empty ( $subjectID )) {
				$subjectMapper = new SubjectMapper ();
				$subject = $subjectMapper->getOneStubject ( $subjectID );
				if (! empty ( $subject )) {
					// var_dump($subject);
					$question->setSubject ( $subject );
				} else {
					echo Util::bootstrapAlert ( "Không tồn tại chủ đề!" );
					goto breakPoint;
				}
			}

			$question->setStatus ( FAQParaConfig::QUESTION_STATUS_OPEN );
			$keyWords = array_unique ( explode ( ",", $listTag ) );
			foreach ( $keyWords as $key => $value ) {
				if (trim ( $value ) != "") {
					$question->setKeyWord ( trim ( $value ) );
				}
			}

			$question = $questionMapper->create ( $question );
		} catch ( Exception $e ) {
			echo Util::bootstrapAlert ( "Có lỗi xẩy ra!" );
			Util::writeLog ( $e->getTrace () );
		}
		breakPoint:
		return array (
				"question" => $question
		);
	}
	public function crawlerAction() {
		$status = null;
		$message = "";
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$pass = $this->params ()->fromPost ( 'pass' ) ;
		if($pass!="fZ4N6HwUGpBV0MLb_MPPo8y1L0s"){
			$status = -1;
			$message = "what is your name?";
			goto breakPoint;
		}


		$title = trim ( $this->params ()->fromPost ( 'title' ) );
		$userQuestion = trim ( $this->params ()->fromPost ( 'user_question' ) );
		$contentQuestion = trim ( $this->params ()->fromPost ( 'content' ) );
		$subjectID = trim ( $this->params ()->fromPost ( 'subject' ) );
		$listTag = trim ( $this->params ()->fromPost ( 'listTag' ) );
		$bonusPoint = intval ( trim ( $this->params ()->fromPost ( 'bonus' ) ) );
		if (strlen ( $title ) < 8 || strlen ( $contentQuestion ) < 16 || empty ( $subjectID ) || empty ( $listTag ) || $bonusPoint < 0) {
			$status = 0;
			$message = "Tieu de cau hoi >8 ky tu, noi dung cau hoi lon hon 16 ky tu, 1 tag, diem thuong >0!";
			goto breakPoint;
		}

		$questionMapper = new QuestionMapper ();
		$userMapper = new UserMapper ();
		if(empty($userQuestion)){
			$userQuestion="536d9fbf7eebacc81500000a";
		}
		$defaultUser = $userMapper->getOneUser ( $userQuestion);
		/* @var $question \FAQ\FAQEntity\Question */
		$question = new Question ();
		if(empty($defaultUser)){
			$status = 4;
			$message = "khong tim thay user tao cau hoi:".$userQuestion;
			goto breakPoint;
		}else{
		$question->setCreateBy ( $defaultUser );
		}

		$subjectMapper = new SubjectMapper ();
		$subject = $subjectMapper->getOneStubject ( $subjectID );
		if (empty ( $subject )) {
			$status = 1;
			$message = "khong tim thay chu de subject id:" . $subjectID;
			goto breakPoint;
		}

		// var_dump($subject);
		$question->setSubject ( $subject );

		$question->setBonusPoint ( $bonusPoint );

		$question->setTitle ( $title );

		$question->setContent ( $contentQuestion );

		$question->setSubject ( $subject );
		try {

			$question->setStatus ( FAQParaConfig::QUESTION_STATUS_OPEN );
			$keyWords = array_unique ( explode ( ",", $listTag ) );
			foreach ( $keyWords as $key => $value ) {
				if (trim ( $value ) != "") {
					$question->setKeyWord ( trim ( $value ) );
				}
			}

			$question = $questionMapper->crawler ( $question );
			$status = 2;
			$message = "tao moi cau hoi thanh cong!";
		} catch ( Exception $e ) {

			$status = 3;
			$message = "loi he thong:".$e->getMessage();
			Util::writeLog ( $e->getTrace () );
		}
		breakPoint:
		if(empty($question)){
		$data = array (
				'status' => $status,
				'message' => $message
		);
		}else{
			$data = array (
					'status' => $status,
					'message' => $message,
					'question'=>$question->getId()
			);

		}
		echo Json::encode ( $data );
		return $this->getResponse ();
	}
}
