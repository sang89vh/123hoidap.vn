<?php

namespace Web\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\Mapper\QuestionMapper;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;
use Doctrine\Tests\Common\Annotations\True;
use Exception;
use FAQ\FAQCommon\Authcfg;
use Zend\Validator\StringLength;
use FAQ\Mapper\AnswerMapper;
use Zend\View\Model\ViewModel;
use FAQ\Mapper\SubjectMapper;
use FAQ\FAQCommon\Appcfg;
use Zend\Json\Json;

class AnswerController extends FAQAbstractActionController {
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
			"create_by"
	);
	private $list_subject;
	public function __construct() {
		$subjectMapper = new SubjectMapper ();
		$this->list_subject = $subjectMapper->findSubject ( array (
				"id",
				"title"
		), null, null, null, FAQParaConfig::STATUS_ACTIVE, null, null, null, false );
	}
	public function indexAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$isFirstLoad = false;
		$orderBy = array (
				"date_updated" => "acs"
		);
		$from = 0;
		$to = 16;
		$subjectID = null;
		if ($this->getRequest ()->isPost ()) {
			$subjectID = $this->getRequest ()->getPost ( "subject" );
			$fromTemp = $this->getRequest ()->getPost ( 'from' );
			$toTemp = $this->getRequest ()->getPost ( 'to' );
			if (isset ( $fromTemp ) && isset ( $toTemp )) {
				$isFirstLoad = true;
				$from = $fromTemp;
				$to = $toTemp;
			}
			;
		}

		$answerMapper = new AnswerMapper ();
		$data = $answerMapper->getOverviewAnswer ( $this->select, null, $subjectID, $orderBy, $from, $to );

		if (! $isFirstLoad) {
			$this->setLayoutAnswer ();
			return array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject
			);
		} else {
			$this->setLayoutAjax ();
			$view = new ViewModel ( array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $list_subject
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function likeListAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$isFirstLoad = false;
		$orderBy = array (
				"date_updated" => "acs"
		);
		$from = 0;
		$to = 16;
		$subjectID = null;
		if ($this->getRequest ()->isPost ()) {
			$subjectID = $this->getRequest ()->getPost ( "subject" );
			$fromTemp = $this->getRequest ()->getPost ( 'from' );
			$toTemp = $this->getRequest ()->getPost ( 'to' );
			if (isset ( $fromTemp ) && isset ( $toTemp )) {
				$isFirstLoad = true;
				$from = $fromTemp;
				$to = $toTemp;
			}
			;
		}

		$answerMapper = new AnswerMapper ();
		$data = $answerMapper->getLikeAnswer ( $this->select, null, $subjectID, $orderBy, $from, $to );

		if (! $isFirstLoad) {
			$this->setLayoutAnswer ();
			return array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject
			);
		} else {
			$this->setLayoutAjax ();
			$view = new ViewModel ( array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function dislikeListAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$isFirstLoad = false;
		$orderBy = array (
				"date_updated" => "acs"
		);
		$from = 0;
		$to = 16;
		$subjectID = null;
		if ($this->getRequest ()->isPost ()) {
			$subjectID = $this->getRequest ()->getPost ( "subject" );
			$fromTemp = $this->getRequest ()->getPost ( 'from' );
			$toTemp = $this->getRequest ()->getPost ( 'to' );
			if (isset ( $fromTemp ) && isset ( $toTemp )) {
				$isFirstLoad = true;
				$from = $fromTemp;
				$to = $toTemp;
			}
			;
		}

		$answerMapper = new AnswerMapper ();
		$data = $answerMapper->getDislikeAnswer ( $this->select, null, $subjectID, $orderBy, $from, $to );

		if (! $isFirstLoad) {
			$this->setLayoutAnswer ();
			return array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject
			);
		} else {
			$this->setLayoutAjax ();
			$view = new ViewModel ( array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function bestListAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$isFirstLoad = false;
		$orderBy = array (
				"date_updated" => "acs"
		);
		$from = 0;
		$to = 16;
		$subjectID = null;
		if ($this->getRequest ()->isPost ()) {
			$subjectID = $this->getRequest ()->getPost ( "subject" );
			$fromTemp = $this->getRequest ()->getPost ( 'from' );
			$toTemp = $this->getRequest ()->getPost ( 'to' );
			if (isset ( $fromTemp ) && isset ( $toTemp )) {
				$isFirstLoad = true;
				$from = $fromTemp;
				$to = $toTemp;
			}
			;
		}

		$answerMapper = new AnswerMapper ();
		$data = $answerMapper->getBestAnswer ( $this->select, null, $subjectID, $orderBy, $from, $to );

		if (! $isFirstLoad) {
			$this->setLayoutAnswer ();
			return array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject
			);
		} else {
			$this->setLayoutAjax ();
			$view = new ViewModel ( array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	/**
	 * temp don't writting code
	 *
	 * public function prememberDislikeAction()
	 * {}
	 *
	 * public function prememberLikeAction()
	 * {}
	 *
	 * public function memberDislikeAction()
	 * {}
	 *
	 * public function memberLikeAction()
	 * {}
	 */
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
				$answerID = $this->params ()->fromPost ( "answer" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->dislikeAnswer ( $questionID, $answerID, Util::getCurrentUser (), $isEstablishedUser );
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

		// echo $data;
		return array (
				"data" => $statusAccess
		);
	}
	public function likeAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this, Authcfg::VOTE_UP );
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
				$answerID = $this->params ()->fromPost ( "answer" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->likeAnswer ( $questionID, $answerID, Util::getCurrentUser (), $isEstablishedUser );
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

		// echo $data;
		return array (
				"data" => $statusAccess
		);
	}
	public function bestAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 0;
		} else {
			if ($this->getRequest ()->isPost ()) {
				$questionID = $this->params ()->fromPost ( "question" );
				$answerID = $this->params ()->fromPost ( "answer" );

				try {
					$questionMapper = new QuestionMapper ();
					$statusAccess = $questionMapper->bestAnswer ( $questionID, $answerID, Util::getIDCurrentUser () );
				} catch ( Exception $e ) {
					$statusAccess = 0;
					Util::writeLog ( $e->getMessage () );
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
	public function answerAction() {
		$privilege = Util::isPrivilege ( $this, Authcfg::CREATE_POSTS );
		if (! $privilege ['isAllowed']) {
			Util::redirectLogin ();
			return $this->getResponse ();
		} elseif (! $privilege ['privilegeByPoint']) {
			Util::bootboxAlert ( "Số điểm câu hỏi của bạn không đủ để đặt câu hỏi" );
			return $this->getResponse ();
		} else {

			$this->setLayoutAjax ();
		}
		$error = "";
		$totalRankPointUser = $privilege ['totalRankPoint'];

		$isReply = false;
		if ($this->getRequest ()->isPost ()) {

			try {

				$dataComment = $this->getRequest ()->getPost ( "dataComment" );
				$content = trim ( $this->params ()->fromPost ( "content" ) );
				$isWikiPost = $this->params ()->fromPost ( "wikiPost" );
				// var_dump($isWikiPost);
				if ($privilege ['totalRankPoint'] >= Authcfg::CREATE_WIKI_POSTS && $isWikiPost == 'true') {
					$isWikiPost = true;
				} else {
					$isWikiPost = false;
				}
				if (strlen ( $content ) < 10) {
					throw new \Exception ( "length of the comment too short" );
				}
				$dataCommentArr = explode ( ",", $dataComment );
				$type = $dataCommentArr [0];
				if (! ($type == "ANSWER")) {
					$isReply = true;
				}
				// var_dump($type,"ANSWER",!($type=="ANSWER"));
				$parentCommentID = $dataCommentArr [1];

				$content = Util::html2txt ( $content, FAQParaConfig::TYPE_TRIP_SCRIPT * FAQParaConfig::TYPE_TRIP_STYLE );
				$questionID = $this->params ()->fromPost ( "question" );
				$createByID = Util::getIDCurrentUser ();

				$questionMapper = new QuestionMapper ();
				$reply = $questionMapper->addAnswer ( $type, $parentCommentID, $content, $questionID, $createByID, $totalRankPointUser, $isWikiPost );
			} catch ( \Exception $e ) {

				Util::writeLog ( $e->getTraceAsString () );
				$error = Util::bootstrapAlert ( $e->getMessage () );
			}
		}
		// var_dump($reply);

		return array (
				"reply" => $reply,
				"isReply" => $isReply,
				"error" => $error,
				"type" => $type
		);
	}
	public function crawlerAction() {
		$status = null;
		$message = "";
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$pass = $this->params ()->fromPost ( 'pass' );
		if ($pass != "fZ4N6HwUGpBV0MLb_MPPo8y1L0s") {
			$status = - 1;
			$message = "what is your name?";
			goto breakPoint;
		}

		$isReply = false;
		if ($this->getRequest ()->isPost ()) {

			try {

				$content = trim ( $this->params ()->fromPost ( "content" ) );
				$questionID = $this->params ()->fromPost ( "question" );
				$createByID = $this->params ()->fromPost ( "user_answer" );
				$isWikiPost = true;
				$isReply = true;

				if (strlen ( $content ) < 10) {
					$status = - 1;
					$message = "length of the comment too short!";
					goto breakPoint;
				}



				$content = Util::html2txt ( $content, FAQParaConfig::TYPE_TRIP_SCRIPT * FAQParaConfig::TYPE_TRIP_STYLE );


				$questionMapper = new QuestionMapper ();
				$reply = $questionMapper->crawlerAnswer (  $content, $questionID, $createByID, $isWikiPost );
				$status = 2;
				$message = "create new answer success!";
			} catch ( \Exception $e ) {

			$status = 3;
			$message = "loi he thong:".$e->getMessage();
			Util::writeLog ( $e->getTrace () );
			}
		}
		breakPoint:
		if(empty($reply)){
		$data = array (
				'status' => $status,
				'message' => $message
		);
		}else{
			$data = array (
					'status' => $status,
					'message' => $message,
					'answer'=>$reply->getId()
			);

		}
		echo Json::encode ( $data );
		return $this->getResponse ();
	}
	public function editWikistyleAction() {
		// check privilege
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập để đăng bài câu hỏi", 5000, "/user/login" );
		}

		$this->setLayoutBasic ();
		// edit
		$questionID = $this->getEvent ()->getRouteMatch ()->getParam ( "urlseo" );
		$answerID = $this->getEvent ()->getRouteMatch ()->getParam ( "answerID" );

		// ---------------------------------------
		$error = "";
		$content = "";

		$questionMapper = new QuestionMapper ();

		if (! empty ( $questionID )) {

			/* @var $question \FAQ\FAQEntity\Question */
			$question = $questionMapper->getOneQuestion ( $questionID );
			if (empty ( $question )) {

				return $this->toNoticeWarning ( "Câu hỏi không tồn tại!", 3000, "/" );
			} else {
				// $statusQuestion=$question->getStatus();
				$dataUpdateBest = $question->getDate_update_best ();
				if (! empty ( $dataUpdateBest ) || ! $question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_OPEN )) {

					return $this->toNoticeWarning ( "Không thể sửa câu hỏi đã đóng!", 3000, "/" );
				}
			}
			$answers = $question->getAnswer ();
			$answer = $answers->get ( $answerID );
			foreach ( $answers as $key => $answer ) {
				if ($answer->getId () == $answerID) {
					$content = $answer->getContent ();
					// var_dump($content);
				}
			}
		} else {
			return $this->toNoticeWarning ( "Câu hỏi không tồn tại!", 3000, "/" );
		}

		return array (
				'questionID' => $questionID,
				'answerID' => $answerID,
				'content' => $content,
				'error' => $error,
				"backUrl" => Appcfg::$domain . "/question/detail/" . $questionID . "/" . Util::convertUrlSeo ( $question->getTitle () )
		);
	}
	public function saveWikistyleAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type: application/json" );
		$statusAccess = 0;
		// check privilege
		$privilege = Util::isPrivilege ( $this, Authcfg::EDIT_QUESTIONS_AND_ANSWERS );
		if (! $privilege ['isAllowed']) {
			$statusAccess = 2;
			goto breakbusiness;
		}

		try {
			// status return equal 0 => unsuccess action

			if ($this->getRequest ()->isPost ()) {

				// edit wiki style

				$questionID = $this->params ()->fromPost ( 'question' );
				$answerID = $this->params ()->fromPost ( 'answer' );
				$newContent = $this->params ()->fromPost ( 'contentAnswer' );

				$noteEdit = $this->params ()->fromPost ( 'noteEdit' );
				//
				$questionMapper = new QuestionMapper ();
				$answerMapper = new AnswerMapper ();
				$question = $questionMapper->getOneQuestion ( $questionID );
				if (empty ( $question )) {
					// var_dump($question);
					$statusAccess = 3;
					goto breakbusiness;
				}
				$isEditWiki = false;
				if ($privilege ['privilegeByPoint']) {
					$isEditWiki = true;
				} elseif ($question->isContainStatus ( FAQParaConfig::QUESTION_STATUS_WIKI_POST )) {
					$isEditWiki = true;
				}
				$answerEdit = null;
				$answers = $question->getAnswer ();
				foreach ( $answers as $key => $answer ) {
					if ($answer->getId () == $answerID) {
						$answerEdit = $answer;
						$oldContent = $answer->getContent ();
						if ($answer->isContainStatus ( FAQParaConfig::QUESTION_STATUS_WIKI_POST )) {
							$isEditWiki = true;
						}
						break;
					}
				}
				if (empty ( $answerEdit )) {
					$statusAccess = 4;
					goto breakbusiness;
				} elseif ($isEditWiki == false) {
					// khong du diem tich luy,hoac cau tra loi khong phai wiki
					$statusAccess = 5;
					goto breakbusiness;
				}
				if ($oldContent == $newContent) {
					// khong thay doi j
					$statusAccess = 6;
					goto breakbusiness;
				}
				$statusAccess = $answerMapper->updateWikistyle ( $question, $answerEdit, $oldContent, $newContent, $noteEdit );
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
	public function revisionAction() {
		$privilege = Util::isPrivilege ( $this );
		if ($privilege ['role'] == Authcfg::GUEST) {
			$this->setLayoutGuest ();
		} else {
			$this->setLayoutBasic ();
		}

		// edit
		$questionID = $this->getEvent ()->getRouteMatch ()->getParam ( "urlseo" );
		$answerID = $this->getEvent ()->getRouteMatch ()->getParam ( "answerID" );
		$questionMapper = new QuestionMapper ();
		$question = $questionMapper->getOneQuestion ( $questionID );
		if (empty ( $question )) {
			return $this->toNoticeWarning ( "Câu hỏi không tồn tại!", 3000, "/" );
		}
		$historyAnswer = null;
		$answers = $question->getAnswer ();
		foreach ( $answers as $key => $answer ) {
			if ($answer->getId () == $answerID) {
				$historyAnswer = $answer->getHistoryContent ();

				break;
			}
		}
		$userCreateQuestionID = $question->getCreateBy ()->getId ();
		$titleQuestion = $question->getTitle ();
		$backUrl = "/question/detail/$questionID/" . Util::convertUrlSeo ( $titleQuestion );
		return array (
				"historyAnswer" => $historyAnswer,
				"userCreateQuestionID" => $userCreateQuestionID,
				"questionID" => $questionID,
				"answerID" => $answerID,
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
				$answerID = $this->params ()->fromPost ( "answer" );
				$contentHistoryID = $this->params ()->fromPost ( "contentHistory" );

				try {
					$answerMapper = new AnswerMapper ();
					$statusAccess = $answerMapper->setContentActive ( $questionID, $answerID, $contentHistoryID );
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
	public function formSpamAction() {
		$isFlagPosts = false;
		$privilege = Util::isPrivilege ( $this, Authcfg::FLAG_POSTS );
		$isAllowed = $privilege ['isAllowed'];
		$isFlagPosts = $privilege ['privilegeByPoint'];
		$this->setLayoutAjax ();
		// if ($this->getRequest ()->isPost ()) {
		$questionID = $this->params ()->fromPost ( "question" );
		$answerID = $this->params ()->fromPost ( "answer" );
		$questionMapper = new QuestionMapper ();
		$question = $questionMapper->getOneQuestion ( $questionID );
		$answers = $question->getAnswer ();
		$userSpams = null;
		foreach ( $answers as $key => $answer ) {

			if ($answer->getId () == $answerID) {
				$userSpams = $answer->getUserSpam ();
			}
		}
		// }
		return array (
				"isFlagPosts" => $isFlagPosts,
				"isAllowed" => $isAllowed,
				"questionID" => $questionID,
				"answerID" => $answerID,
				"userSpams" => $userSpams
		);
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
				$answerID = $this->params ()->fromPost ( "answer" );

				try {
					$answerMapper = new AnswerMapper ();
					$statusAccess = $answerMapper->reportSpam ( $questionID, $answerID, Util::getCurrentUser (), $typespam );
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
}