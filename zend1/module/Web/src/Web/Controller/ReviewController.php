<?php

namespace Web\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\Authcfg;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\Mapper\QuestionMapper;
use FAQ\Mapper\SubjectMapper;
use Zend\View\Model\ViewModel;

class ReviewController extends FAQAbstractActionController {
	private $select = array (
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
	public function indexAction() {
		$this->setLayoutBasic ();
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/review" );
		}
		$questionMapper = new QuestionMapper ();
		$data = $questionMapper->reviewQuestion ();
		return $data;
	}
	public function spamQuestionAction() {

		$privilege = Util::isPrivilege ( $this ,Authcfg::CAST_CLOSE_AND_REOPEN_VOTES);
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/review" );
		}
		if (! $privilege ['privilegeByPoint']) {
			return $this->toNoticeWarning ( "Điểm câu hỏi yêu cầu ".Authcfg::CAST_CLOSE_AND_REOPEN_VOTES." đ", 3000, "/review" );
		}


		$isFirstLoad = false;

		$from = 0;
		$to = 16;
		$subjectID = null;

		$type = $this->getRequest ()->getQuery ( 'type' );
		if(empty($type)){
			$type=1;
		}
		if ($this->getRequest ()->isPost ()) {

			$this->setLayoutAjax ();
			$fromTemp = $this->getRequest ()->getPost ( 'from' );
			$toTemp = $this->getRequest ()->getPost ( 'to' );
			$subjectID = $this->getRequest ()->getPost ( 'subject' );

			if (isset ( $fromTemp ) && isset ( $toTemp )) {
				$isFirstLoad = true;
				$from = $fromTemp;
				$to = $toTemp;
			}
			;
		} else {
			$this->setLayoutBasic ();

		}

		$questionMapper = new QuestionMapper ();
		$data = $questionMapper->getQuestionReviewSpam ( $this->select, $subjectID, $from, $to, $type );
		if (! $isFirstLoad) {

			$listSubjectID = null;
			if (! empty ( $subjectID )) {
				$listSubjectID = array (
						$subjectID
				);
			}


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
					"hot_questions" => $hot_questions,
					"highlight_questions" => $highlight_questions,
					"type"=>$type
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
	public function unspamQuestionAction() {

		$privilege = Util::isPrivilege ( $this ,Authcfg::CAST_CLOSE_AND_REOPEN_VOTES);
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/review" );
		}
		if (! $privilege ['privilegeByPoint']) {
			return $this->toNoticeWarning ( "Điểm câu hỏi yêu cầu ".Authcfg::CAST_CLOSE_AND_REOPEN_VOTES." đ", 3000, "/review" );
		}


		$isFirstLoad = false;

		$from = 0;
		$to = 16;
		$subjectID = null;

		$type = $this->getRequest ()->getQuery ( 'type' );
		if(empty($type)){
			$type=1;
		}
		if ($this->getRequest ()->isPost ()) {

			$this->setLayoutAjax ();
			$fromTemp = $this->getRequest ()->getPost ( 'from' );
			$toTemp = $this->getRequest ()->getPost ( 'to' );
			$subjectID = $this->getRequest ()->getPost ( 'subject' );

			if (isset ( $fromTemp ) && isset ( $toTemp )) {
				$isFirstLoad = true;
				$from = $fromTemp;
				$to = $toTemp;
			}
			;
		} else {
			$this->setLayoutBasic ();

		}

		$questionMapper = new QuestionMapper ();
		$data = $questionMapper->getQuestionReviewUnspam( $this->select, $subjectID, $from, $to, $type );
		if (! $isFirstLoad) {

			$listSubjectID = null;
			if (! empty ( $subjectID )) {
				$listSubjectID = array (
						$subjectID
				);
			}


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
					"hot_questions" => $hot_questions,
					"highlight_questions" => $highlight_questions,
					"type"=>$type
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
	public function editQuestionAction() {

		$privilege = Util::isPrivilege ( $this ,Authcfg::EDIT_QUESTIONS_AND_ANSWERS);
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/review" );
		}
		if (! $privilege ['privilegeByPoint']) {
			return $this->toNoticeWarning ( "Điểm câu hỏi yêu cầu ".Authcfg::EDIT_QUESTIONS_AND_ANSWERS." đ", 3000, "/review" );
		}


		$isFirstLoad = false;

		$from = 0;
		$to = 16;
		$subjectID = null;

		$type = $this->getRequest ()->getQuery ( 'type' );
		if(empty($type)){
			$type=1;
		}
		if ($this->getRequest ()->isPost ()) {

			$this->setLayoutAjax ();
			$fromTemp = $this->getRequest ()->getPost ( 'from' );
			$toTemp = $this->getRequest ()->getPost ( 'to' );
			$subjectID = $this->getRequest ()->getPost ( 'subject' );

			if (isset ( $fromTemp ) && isset ( $toTemp )) {
				$isFirstLoad = true;
				$from = $fromTemp;
				$to = $toTemp;
			}
			;
		} else {
			$this->setLayoutBasic ();

		}

		$questionMapper = new QuestionMapper ();
		$data = $questionMapper->getQuestionReviewEditQuestion ( $this->select, $subjectID, $from, $to, $type );
		if (! $isFirstLoad) {

			$listSubjectID = null;
			if (! empty ( $subjectID )) {
				$listSubjectID = array (
						$subjectID
				);
			}


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
					"hot_questions" => $hot_questions,
					"highlight_questions" => $highlight_questions,
					"type"=>$type
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
	public function editAnswerAction() {

		$privilege = Util::isPrivilege ( $this ,Authcfg::EDIT_QUESTIONS_AND_ANSWERS);
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/review" );
		}
		if (! $privilege ['privilegeByPoint']) {
			return $this->toNoticeWarning ( "Điểm câu hỏi yêu cầu ".Authcfg::EDIT_QUESTIONS_AND_ANSWERS." đ", 3000, "/review" );
		}


		$isFirstLoad = false;

		$from = 0;
		$to = 16;
		$subjectID = null;

		$type = $this->getRequest ()->getQuery ( 'type' );
		if(empty($type)){
			$type=1;
		}
		if ($this->getRequest ()->isPost ()) {

			$this->setLayoutAjax ();
			$fromTemp = $this->getRequest ()->getPost ( 'from' );
			$toTemp = $this->getRequest ()->getPost ( 'to' );
			$subjectID = $this->getRequest ()->getPost ( 'subject' );

			if (isset ( $fromTemp ) && isset ( $toTemp )) {
				$isFirstLoad = true;
				$from = $fromTemp;
				$to = $toTemp;
			}
			;
		} else {
			$this->setLayoutBasic ();

		}

		$questionMapper = new QuestionMapper ();
		$data = $questionMapper->getQuestionReviewEditAnswer ( $this->select, $subjectID, $from, $to, $type );
		if (! $isFirstLoad) {

			$listSubjectID = null;
			if (! empty ( $subjectID )) {
				$listSubjectID = array (
						$subjectID
				);
			}


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
					"hot_questions" => $hot_questions,
					"highlight_questions" => $highlight_questions,
					"type"=>$type
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
}

