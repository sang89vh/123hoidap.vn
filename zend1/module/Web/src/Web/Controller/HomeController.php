<?php

namespace Web\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use Web\Forms\SearchBar;
use FAQ\Mapper\UserMapper;
use Zend\Session\SessionManager;
use FAQ\Mapper\SubjectMapper;
use FAQ\FAQCommon\Util;
use FAQ\Mapper\QuestionMapper;
use FAQ\FAQCommon\Authcfg;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\Mapper\TagMapper;
use Zend\View\Model\ViewModel;
use FAQ\FAQCommon\Usercfg;

class HomeController extends FAQAbstractActionController {
	public function questionAction() {
		$this->getRequest ()->setMetadata ( "isOnlyQuestionTag", true );
		$isFirstLoad = false;
		$select = array (
				"id",
				"title",
				"content",
				"status",
				"subject",
				"date_created",
				"total_spam",
				"total_share","total_view",
				"total_like",
				"total_dislike",
				"total_answer",
				"create_by",
				"bonus_point",
				"first_image",
				"short_content"
		);
		$from = 0;
		$to = 16;
		$subjectID = null;
		$type = null;
		$totalRankPoint = 0;
		if ($this->getRequest ()->isPost ()) {

			$this->setLayoutAjax ();
			$fromTemp = $this->getRequest ()->getPost ( 'from' );
			$toTemp = $this->getRequest ()->getPost ( 'to' );
			$subjectID = $this->getRequest ()->getPost ( 'subject' );
			$type = $this->getRequest ()->getPost ( 'type' );
			if (isset ( $fromTemp ) && isset ( $toTemp )) {
				$isFirstLoad = true;
				$from = $fromTemp;
				$to = $toTemp;
			}
			;
		} else {

			$privilege = Util::isPrivilege ( $this,Authcfg::CREATE_POSTS );
			if ($privilege ['role'] == Authcfg::GUEST) {
				$this->setLayoutHomeGuest ();
			} else {
				$this->setLayoutHome ();
				$totalRankPoint = $privilege ['totalRankPoint'];
			}
		}

		$questionMapper = new QuestionMapper ();
		$data = $questionMapper->getQuestionHome ( $select, $subjectID, $from, $to, $type );
		if (! $isFirstLoad) {

			$listSubjectID = null;
			if (! empty ( $subjectID )) {
				$listSubjectID = array (
						$subjectID
				);
			}

			$hot_questions = $questionMapper->getHotQuestion ( array (
					"id",
					"title",
					"first_image"
			), 0, 7, $listSubjectID, FAQParaConfig::QUESTION_HOT );
			$highlight_questions = $questionMapper->getHighlightQuestion ( array (
					"id",
					"title",
					"first_image"
			), 0, 5, $listSubjectID, FAQParaConfig::QUESTION_HIGHLIGHT );
			$subjectMapper = new SubjectMapper ();
			$list_subject = $subjectMapper->findSubject ( array (
					"id",
					"title",
					"first_image"
			), null, null, null, FAQParaConfig::STATUS_ACTIVE, null, null, null, false );
			return array (
					'totalDocument' => $data ['totalDocument'],
					'list_question' => $data ['listQuestion'],
					'list_subject' => $list_subject,
					'totalRankPoint' => $totalRankPoint,
					"hot_questions" => $hot_questions,
					"highlight_questions" => $highlight_questions,
					"privilegeByPoint"=>$privilege['privilegeByPoint'],
					"role"=>$privilege ['role']
			);
		} else {
			$view = new ViewModel ( array (
					'totalDocument' => $data ['totalDocument'],
					'list_question' => $data ['listQuestion']
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function topHashtagAction() {
		$isQuestion = true;

		$isOnlyQuestionTag = $this->getRequest ()->getMetadata ( "isOnlyQuestionTag" );

		$tagMapper = new TagMapper ();
		$tags = $tagMapper->getTagHome ( array (
				"date_updated" => "desc"
		), 0, 30, $isQuestion );
		$this->layout ()->tags = $tags;

		$headMetaDesc = "";
		/* @var $tag \FAQ\FAQEntity\KeyWord */
		foreach ( $tags as $keytag => $tag ) {
			$keys = $tag->getKey ();
			foreach ( $keys as $keyw => $key ) {

				$headMetaDesc = $headMetaDesc . ", " . mb_substr ( $key, 0, 50, 'UTF-8' );
			}
		}
		$this->layout ()->headMetaDesc = $headMetaDesc;
	}
	public function topSubjectAction() {
		$subjectMapper = new SubjectMapper ();
		$orderBy = array (
				'total_question' => 'desc'
		);
		$subjects = $subjectMapper->findSubject ( null, null, null, null, 1, $orderBy, 0, 12 );
		$this->layout ()->subjects = $subjects;
	}
	public function topMemberAction() {
		// $this->setLayoutAjax();
		$userMapper = new UserMapper ();
		$orderBy = array (
				'total_rank_point' => 'desc'
		);
		$users = $userMapper->findUser ( array (
				"id",
				"avatar",
				"first_name",
				"last_name"
		), null, $orderBy, 0, 12, Usercfg::USER_STATUS_CURRENT_ACTIVE );
		$this->layout ()->users = $users;
	}
	public function searchAction() {
		$queryString = $this->request->getQuery ( 'q' );
		$from = 0;
		$to = 16;
		if ($this->getRequest ()->isPost ()) {


			$fromTemp = $this->getRequest ()->getPost ( 'from' );
			$toTemp = $this->getRequest ()->getPost ( 'to' );
			if (isset ( $fromTemp ) && isset ( $toTemp )) {
				$isFirstLoad = true;
				$from = $fromTemp;
				$to = $toTemp;
			};

			// question
			$isFirstLoad = false;
		}
		$select = array (
				"id",
				"title",
				"content",
				"status",
				"subject",
				"date_created",
				"total_spam",
				"total_share","total_view",
				"total_like",
				"total_dislike",
				"total_answer",
				"create_by",
				"bonus_point",
				"first_image",
				"short_content"
		);

		$questionMapper = new QuestionMapper ();
		$data = $questionMapper->searchQuestion ( $queryString, $select, $from, $to );
		if (! $isFirstLoad) {

			$privilege = Util::isPrivilege ( $this );
			if ($privilege ['role'] == Authcfg::GUEST) {
				$this->setLayoutGuest ();
			} else {
				$this->setLayoutBasic ();
			}

			// get user
			$userMapper = new UserMapper ();

			$list_member = $userMapper->findUser ( null, $queryString, null, 0, 19, Usercfg::USER_STATUS_CURRENT_ACTIVE );
			// get subject
			if (! empty ( $queryString )) {
				$keywords = explode ( ' ', $queryString );
				foreach ( $keywords as $key => $value ) {
					$regexObj = new \MongoRegex ( "/^" . $value . "/i" );
					$keySearchs [$key] = $regexObj;
				}
			} else {
				$keySearchs = null;
			}

			$subjectMapper = new SubjectMapper ();
			$subjects = $subjectMapper->findSubject ( null, $keySearchs, null, null, FAQParaConfig::STATUS_ACTIVE, null, 0, - 1 );
			return array (
					"list_member" => $list_member,
					"subjects" => $subjects,
					'totalDocument' => $data ['totalDocument'],
					'list_question' => $data ['listQuestion'],
					'queryString'=>$queryString
			);
		} else {
			$this->setLayoutAjax ();
			$view = new ViewModel ( array (
					'totalDocument' => $data ['totalDocument'],
					'list_question' => $data ['listQuestion']
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}
	public function searchFormAction() {

		// check privilege
		$privilege = Util::isPrivilege ( $this );
		$isAllowed = ($privilege ['role'] != Authcfg::GUEST);
		$this->layout ()->isAllowed = $isAllowed;
		$this->layout ()->role = $privilege ['role'];
		$totalRankPoint = $privilege ['totalRankPoint'];
		$this->layout ()->totalRankPoint = $totalRankPoint;

		// $this->setLayoutAjax();
		// $form = new SearchBar();
		// $request = $this->getRequest();
		// if ($request->isPost()) {

		// $form->setData($request->getPost());

		// if ($form->isValid()) {
		// $data = $form->getData();
		// // var_dump($data);
		// // return null;
		// }
		// }

		// $this->layout()->form = $form;
		$currentUser = Util::getCurrentUser ( array (
				'total_new_notify',
				'total_new_message'
		), false );
		// var_dump($currentUser);
		$sumNotify = $currentUser ['total_new_notify'];
		if ($sumNotify > 99) {
			$sumNotify = "99+";
		}
		$sumMessage = $currentUser ['total_new_message'];
		if ($sumMessage > 99) {
			$sumMessage = "99+";
		}
		$this->layout ()->sumNotify = $sumNotify;
		$this->layout ()->sumMessage = $sumMessage;
		// $selectProjection = array (
		// "_id",
		// "title",
		// "avatar",
		// "total_question",
		// "total_user_follow"
		// );
		$selectProjection = null;
		$subjectMapper = new SubjectMapper ();
		$allSubject = $subjectMapper->getAllSubject ();
		$this->layout ()->allSubject = $allSubject;

		$sm = new SessionManager ();
		$sm->start ();
		$email = $sm->getStorage ()->getMetadata ( "email" );
		return array (
				'isAllowed' => $isAllowed,
				'email' => $email,
				"totalRankPoint" => $totalRankPoint
		);
	}
	// right question detail
	public function topQuestionHashtagAction() {
		$questionID = $this->getRequest ()->getMetadata ( "questionID" );
		$questionMapper = new QuestionMapper ();
		$hashtag = $questionMapper->getHashtagRelationship ( $questionID, 0, 15 );
		// $this->setLayoutAjax();
		$this->layout ()->hashtag = $hashtag;
		$headMetaDesc = "";
		foreach ( $hashtag as $key => $value ) {
			if (! empty ( $value ['tag'] )) {
				$ht = $value ['tag'];
			} else {
				$ht = $value;
			}
			$headMetaDesc = $headMetaDesc . ", " . $ht;
		}
		$this->layout ()->headMetaDesc = $headMetaDesc;
	}
	public function topQuestionRelationshipAction() {
		$questionID = $this->getRequest ()->getMetadata ( "questionID" );
		$quesionMapper = new QuestionMapper ();
		$questions = $quesionMapper->getQuestionRelationship ( $questionID, 0, 30 );
		$this->layout ()->questions = $questions;
	}
	public function chartAnswerAction() {
		$userID = $this->getRequest ()->getMetadata ( "userID" );
		$userMapper = new UserMapper ();

		if (empty ( $userID )) {
			$userID = Util::getIDCurrentUser ();
		}
		$currentUser = $userMapper->getOneUser ( $userID );
		$chartData = array ();
		if (! empty ( $currentUser )) {
			$chartData = array (
					"total_answer" => $currentUser->getTotalAnswer (),
					"total_answer_like" => $currentUser->getTotalAnswerLike (),
					"total_answer_dislike" => $currentUser->getTotalAnswerDislike (),
					"total_answer_best" => $currentUser->getTotalAnswerBest ()
			);
		}
		$this->layout ()->charAnswer = $chartData;
	}
}