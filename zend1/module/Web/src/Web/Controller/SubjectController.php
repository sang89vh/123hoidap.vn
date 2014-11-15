<?php

namespace Web\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\Mapper\SubjectMapper;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\Mapper\QuestionMapper;
use FAQ\FAQCommon\Authcfg;
use FAQ\FAQCommon\Appcfg;
use FAQ\Mapper\TagMapper;

class SubjectController extends FAQAbstractActionController {
	public function indexAction() {

		// $this->setLayoutAjax();
		$privilege = Util::isPrivilege ( $this );
		if ($privilege ['role'] == Authcfg::GUEST) {
			$this->setLayoutGuest ();
		} else {
			$this->setLayoutBasic ();
		}
		$subjectMapper = new SubjectMapper ();
		$subjects = $subjectMapper->getAllSubject();
		return array (
				"subjects" => array_slice ( $subjects, 0, 48 ),
				"totalSubject" => count ( $subjects ),
				"actionName" => $this->params ( "action" )
		);
	}
	public function detailAction() {
		$privilege = Util::isPrivilege ( $this );
		if ($privilege ['role'] == Authcfg::GUEST) {
			$this->setLayoutSubjectGuest ();
		} else {
			$this->setLayoutSubject ();
		}

		$subjectID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		$tab = $this->getEvent ()->getRouteMatch ()->getParam ( "tab" );

		$subjectMapper = new SubjectMapper ();
		$subject = $subjectMapper->getOneStubject ( $subjectID );
		if (empty ( $subject )) {
			return $this->toNoticeError ( "Chủ đề hiện không có!", 3000, "/subject/" );
		}

		$titleSubject = $subject->getTitle ();
		$this->layout ()->subject = $subject;

		$tagMapper = new TagMapper ();

		// find subject, categories =>view

		$from = 0;
		$to = 16;

		// top tags
		$tagMapper = new TagMapper ();
		$tags = $tagMapper->getTagSubject ( $subjectID, $orderBy, 0, 30 );
		$this->layout ()->tags = $tags;

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
		$list_question = $questionMapper->getOpenList ( null, $subjectID, $from, $to, $select );
		// set meta data
		$tags = $tagMapper->getTagSubject ( $subjectID, array (
				"date_updated" => "desc"
		), 0, 30, false, true );
		$headMetaDesc = "";
		/* @var $tag \FAQ\FAQEntity\KeyWord */
		foreach ( $tags as $keytag => $tag ) {
			$keys = $tag->getKey ();
			foreach ( $keys as $keyw => $key ) {

				$headMetaDesc = $headMetaDesc . ", " . substr ( $key, 0, 50 );
			}
		}
		$this->layout ()->headMetaDesc = $headMetaDesc;

		$listSubjectID = array (
				$subjectID
		);
		$quesionMapper = new QuestionMapper ();
		$hot_questions = $quesionMapper->getHotQuestion ( array (
				"id",
				"title",
				"first_image"
		), 0, 7, $listSubjectID, FAQParaConfig::QUESTION_HOT );
		$highlight_questions = $quesionMapper->getHighlightQuestion ( array (
				"id",
				"title",
				"first_image"
		), 0, 5, $listSubjectID, FAQParaConfig::QUESTION_HIGHLIGHT );

		return array (
				'list_question' => $list_question,
				'totalDocument' => $list_question->totalDocument,
				"tab" => $tab,
				'subjectID' => $subjectID,
				'titleSubject' => $titleSubject,
				'highlight_questions' => $highlight_questions,
				'hot_questions' => $hot_questions
		);
	}

	public function questionAction() {
		$this->setLayoutAjax ();
		$subjectID = $this->getRequest ()->getMetadata ( "subjectID" );

		if ($this->getRequest ()->isPost ()) {

			$subjectID = $this->getRequest ()->getPost ( 'subject' );
			$type = $this->getRequest ()->getPost ( 'type' );
			$from = $this->getRequest ()->getPost ( 'from' );
			$to = $this->getRequest ()->getPost ( 'to' );
			if (! $from) {
				$from = 0;
			}
			if (! $to) {
				$to = Appcfg::$question_paging_size;
			}
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
		$list_question = $questionMapper->getOpenList ( null, $subjectID, $from, $to, $select, $type );
		return array (
				'list_question' => $list_question,
				'totalDocument' => $list_question->totalDocument,
				"tab" => $tab,
				'subjectID' => $subjectID
		);
	}
	public function overviewAction() {
		$this->setLayoutAjax ();
		$subjectID = $this->params ()->fromQuery ( 'subject' );
		$subjectMapper = new SubjectMapper ();
		$subject = $subjectMapper->getOneStubject ( $subjectID );

		// var_dump($subjectID);
		return array (
				"subject" => $subject
		);
	}
	public function actionSubjectAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type: application/json" );
		// status return equal 0 => unsuccess action
		$statusAccess = "0";
		// check privilege
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			$statusAccess = "0";
		} else {
			if ($this->getRequest ()->isPost ()) {
				$subjectMapper = new SubjectMapper ();
				$subjectID = $this->params ()->fromPost ( 'subject' );
				$actionCode = $this->params ()->fromPost ( 'action' );
				$userID = Util::getCurrentUser ()->getId ();
				// follow action
				if ($actionCode == "1") {
					$statusAccess = $subjectMapper->followSubject ( $subjectID, $userID );
					// unfollow action
				} else if ($actionCode == "2") {
					$statusAccess = $subjectMapper->unFollowSubject ( $subjectID, $userID );
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
	public function listSubjectAction() {
		$isMetaSubject = true;
		$privilege = Util::isPrivilege ( $this, Authcfg::PARTICIPATE_IN_META );

		$this->setLayoutAjax ();
		$keySearchs = array ();
		$subjectMapper = new SubjectMapper ();
		if ($this->getRequest ()->isPost ()) {

			$queryString = trim ( $this->params ()->fromPost ( 'keyword' ) );
			$from = trim ( $this->params ()->fromPost ( 'from' ) );
			$to = trim ( $this->params ()->fromPost ( 'to' ) );
			$actionRequest = trim ( $this->params ()->fromPost ( 'actionRequest' ) );
			if ($actionRequest == "select-subject" && ! $privilege ['privilegeByPoint']) {
				$isMetaSubject = false;
			}
			// var_dump($from."==".$to);
			if (! empty ( $queryString )) {
				$keywords = explode ( ' ', $queryString );
				foreach ( $keywords as $key => $value ) {
					$regexObj = new \MongoRegex ( "/^" . $value . "/i" );
					$keySearchs [$key] = $regexObj;
				}
			} else {
				$keySearchs = null;
			}
		}
		$subjects = $subjectMapper->getAllSubject();
		return array (
				"subjects" => array_slice ( $subjects, $from, $to - $from ),
				"totalSubject" => count ( $subjects )
		);
	}
}