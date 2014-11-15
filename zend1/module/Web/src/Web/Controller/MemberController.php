<?php

namespace Web\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use Zend\View\Model\ViewModel;
use FAQ\Mapper\UserMapper;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\Authcfg;
use FAQ\FAQEntity\User;
use FAQ\FAQCommon\Appcfg;
use FAQ\Mapper\QuestionMapper;
use FAQ\Mapper\AnswerMapper;
use FAQ\Mapper\SubjectMapper;
use FAQ\FAQCommon\Usercfg;

class MemberController extends FAQAbstractActionController {

	private $select_question = array (
			"id",
			"title",
			"content",
			"status",
			"subject",
			"short_content",
			"date_created",
			"total_spam",
			"total_share","total_view",
			"total_follow",
			"total_comment",
			"total_answer",
			"create_by",
			"bonus_point", "first_image", "short_content"
	);
	private $list_subject;
	public function __construct() {
		$subjectMapper = new SubjectMapper ();
		$this->list_subject = $subjectMapper->findSubject ( array (
				"id",
				"title"
		), null, null, null, FAQParaConfig::STATUS_ACTIVE, null, null, null, false );
	}

	/**
	 *
	 * @author sang
	 * @todo view all member in the system
	 *
	 */
	public function indexAction() {
		$privilege = Util::isPrivilege ( $this );
		if ($privilege ['role'] == Authcfg::GUEST) {
			$this->setLayoutGuest ();
		} else {
			$this->setLayoutBasic ();
		}
		$orderBy = array (
				"total_rank_point" => "desc",
				"total_money_point" => "desc",
				"total_answer" => "desc"
		);
		$userMapper = new UserMapper ();
		// $data = array(
		// "totalRow" => $totalRow,
		// "users" => users
		// );

		$data = $userMapper->findUser ( null, null, $orderBy, 0, 12, Usercfg::USER_STATUS_CURRENT_ACTIVE, true );
		$list_member = $data ['users'];
		// var_dump(count($list_member));
		return array (
				"total_member" => $data ['totalRow'],
				"list_member" => $list_member
		);
	}
	public function listMemberAction() {
		$this->setLayoutAjax ();
		$orderBy = array (
				"total_rank_point" => "desc",
				"total_money_point" => "desc",
				"total_answer" => "desc"
		);
		$queryString = null;
		$from = null;
		$to = null;
		if ($this->getRequest ()->isPost ()) {

			$queryString = trim ( $this->params ()->fromPost ( 'keyword' ) );
			$from = trim ( $this->params ()->fromPost ( 'from' ) );
			$to = trim ( $this->params ()->fromPost ( 'to' ) );
			// var_dump($from."==".$to);
		}
		$userMapper = new UserMapper ();
		$data = $userMapper->findUser ( null, $queryString, $orderBy, $from, $to, Usercfg::USER_STATUS_CURRENT_ACTIVE, true );
		$list_member = $data ['users'];
		// var_dump(count($list_member));
		return array (
				"total_member" => $data ['totalRow'],
				"list_member" => $list_member
		);
	}


	/**
	 *
	 * @author izzi,sang
	 * @todo question page of member
	 * @return \Zend\View\Model\ViewModel multitype:unknown
	 */
	public function questionAction() {
		$isAjax = false;
		$questionFrom = 0;
		$questionTo = Appcfg::$question_paging_size;
		$subjectID = null;
		$orderBy = array (
				"date_updated" => "acs"
		);

		$tempFrom = $this->params ()->fromPost ( 'from' );
		if (isset ( $tempFrom )) {
			$isAjax = true;
			$questionFrom = $tempFrom;
		}
		$tempTo = $this->params ()->fromPost ( 'to' );
		if (isset ( $tempTo )) {
			$questionTo = $tempTo;
		}

		$subjectID = $this->params ()->fromPost ( 'subject' );
		$ajaxUserID = $this->params ()->fromPost ( 'user' );
		$userID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );

		$this->getRequest ()->setMetadata ( "userID", $userID );
		if (empty ( $userID )) {
			$userID = $ajaxUserID;
		}
		if (empty ( $userID )) {
			return $this->toNoticeError ( "Không tìm thấy service phục vụ cho request của bạn", 3000, "/" );
		}
		// privilege
		$privilege = Util::isPrivilege ( $this );
		if ($isAjax) {
			$this->setLayoutAjax ();
		} elseif ($privilege ['role'] == Authcfg::GUEST && $isAjax != true) {
			$this->setLayoutMemberGuest ();

		} elseif ($privilege ['role'] != Authcfg::GUEST && $isAjax != true) {
			$this->setLayoutMember ();
		}

		$user = new User ();
		$user = $user->find ( $userID, true );

		if (! isset ( $user )) {
			// todo: error user, redirect to user.
			// return $this->redirect()->toRoute("member");
		}

		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getOverviewPublish ( $userID, $subjectID, $questionFrom, $questionTo, $this->select_question );

		if (! $isAjax) {

			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument,
					'action' => $this->params ( "action" ),
					'user' => $user,
					'list_subject' => $this->list_subject
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

	/**
	 *
	 * @author izzi
	 * @todo question page of member
	 * @return \Zend\View\Model\ViewModel multitype:unknown
	 */
	public function questionAskingAction() {
		$isAjax = false;
		$questionFrom = 0;
		$questionTo = Appcfg::$question_paging_size;
		$subjectID = null;
		$orderBy = array (
				"date_updated" => "acs"
		);

		$tempFrom = $this->params ()->fromPost ( 'from' );
		if (isset ( $tempFrom )) {
			$isAjax = true;
			$questionFrom = $tempFrom;
		}
		$tempTo = $this->params ()->fromPost ( 'to' );
		if (isset ( $tempTo )) {
			$questionTo = $tempTo;
		}

		$subjectID = $this->params ()->fromPost ( 'subject' );
		$ajaxUserID = $this->params ()->fromPost ( 'user' );
		$userID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		$this->getRequest ()->setMetadata ( "userID", $userID );
		// privilege
		$privilege = Util::isPrivilege ( $this );
		if ($isAjax) {
			$this->setLayoutAjax ();
		} elseif ($privilege ['role'] == Authcfg::GUEST && $isAjax != true) {
			$this->setLayoutMemberGuest ();

		} elseif ($privilege ['role'] != Authcfg::GUEST && $isAjax != true) {
			$this->setLayoutMember ();
		}

		if (empty ( $userID )) {
			$userID = $ajaxUserID;
		}

		$user = new User ();
		$user = $user->find ( $userID, true );

		if (! isset ( $user )) {
			// todo: error user, redirect to user.
			// return $this->redirect()->toRoute("member");
		}

		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getOpenList ( $userID, $subjectID, $questionFrom, $questionTo, $this->select_question );

		if (! $isAjax) {

			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument,
					'action' => $this->params ( "action" ),
					'user' => $user,
					'list_subject' => $this->list_subject
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

	/**
	 *
	 * @author izzi
	 * @todo question page of member
	 * @return \Zend\View\Model\ViewModel multitype:unknown
	 */
	public function questionClosedAction() {
		$isAjax = false;
		$questionFrom = 0;
		$questionTo = Appcfg::$question_paging_size;
		$subjectID = null;
		$orderBy = array (
				"date_updated" => "acs"
		);

		$tempFrom = $this->params ()->fromPost ( 'from' );
		if (isset ( $tempFrom )) {
			$isAjax = true;
			$questionFrom = $tempFrom;
		}
		$tempTo = $this->params ()->fromPost ( 'to' );
		if (isset ( $tempTo )) {
			$questionTo = $tempTo;
		}

		$subjectID = $this->params ()->fromPost ( 'subject' );
		$ajaxUserID = $this->params ()->fromPost ( 'user' );
		$userID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		$this->getRequest ()->setMetadata ( "userID", $userID );
		// privilege
		$privilege = Util::isPrivilege ( $this );
		if ($isAjax) {
			$this->setLayoutAjax ();
		} elseif ($privilege ['role'] == Authcfg::GUEST && $isAjax != true) {
			$this->setLayoutMemberGuest ();

		} elseif ($privilege ['role'] != Authcfg::GUEST && $isAjax != true) {
			$this->setLayoutMember ();
		}

		if (empty ( $userID )) {
			$userID = $ajaxUserID;
		}

		$user = new User ();
		$user = $user->find ( $userID, true );

		if (! isset ( $user )) {
			// todo: error user, redirect to user.
			// return $this->redirect()->toRoute("member");
		}

		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getClosedList ( $userID, $subjectID, $questionFrom, $questionTo, $this->select_question );

		if (! $isAjax) {

			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument,
					'action' => $this->params ( "action" ),
					'user' => $user,
					'list_subject' => $this->list_subject
			)
			;
		} else {
			$view = new ViewModel ( array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}

	/**
	 *
	 * @author izzi
	 * @todo question page of member
	 * @return \Zend\View\Model\ViewModel multitype:unknown
	 */
	public function questionSpamAction() {
		$isAjax = false;
		$questionFrom = 0;
		$questionTo = Appcfg::$question_paging_size;
		$subjectID = null;
		$orderBy = array (
				"date_updated" => "acs"
		);

		$tempFrom = $this->params ()->fromPost ( 'from' );
		if (isset ( $tempFrom )) {
			$isAjax = true;
			$questionFrom = $tempFrom;
		}
		$tempTo = $this->params ()->fromPost ( 'to' );
		if (isset ( $tempTo )) {
			$questionTo = $tempTo;
		}

		$subjectID = $this->params ()->fromPost ( 'subject' );
		$ajaxUserID = $this->params ()->fromPost ( 'user' );
		$userID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		$this->getRequest ()->setMetadata ( "userID", $userID );
		// privilege
		$privilege = Util::isPrivilege ( $this );
		if ($isAjax) {
			$this->setLayoutAjax ();
		} elseif ($privilege ['role'] == Authcfg::GUEST && $isAjax != true) {
			$this->setLayoutMemberGuest ();
		} elseif ($privilege ['role'] != Authcfg::GUEST && $isAjax != true) {
			$this->setLayoutMember ();
		}

		if (empty ( $userID )) {
			$userID = $ajaxUserID;
		}

		$user = new User ();
		$user = $user->find ( $userID, true );

		if (! isset ( $user )) {
			// todo: error user, redirect to user.
			// return $this->redirect()->toRoute("member");
		}

		$questionMapper = new QuestionMapper ();
		$list_question = $questionMapper->getSpamList ( $userID, $subjectID, $questionFrom, $questionTo, $this->select_question );

		if (! $isAjax) {

			return array (
					'list_question' => $list_question,
					'totalDocument' => $list_question->totalDocument,
					'action' => $this->params ( "action" ),
					'user' => $user,
					"list_subject" => $this->list_subject
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

	/**
	 *
	 * @author izzi,sang
	 * @todo answer page of member
	 * @return \Zend\View\Model\ViewModel multitype:unknown
	 */
	public function answerAction() {
		$userID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		if(empty($userID)){
		    $userID = $this->getRequest ()->getPost ( "user" );
		}

		$this->getRequest ()->setMetadata ( "userID", $userID );
		$this->forward ()->dispatch ( "Web\Controller\Home", array (
				'action' => 'chart-answer',
				'controller' => 'Home'
		) );

		$user = new User ();
		$user = $user->find ( $userID, true );
		if (! $user) {
			// todo: no member
			return $this->redirect ()->toRoute ( "member" );
		}

		$isFirstLoad = false;
		$orderBy = array (
				"date_updated" => "acs"
		);
		$from = 0;
		$to = 16;
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
		$data = $answerMapper->getOverviewAnswer ( $this->select_question, $userID, $subjectID, $orderBy, $from, $to );

		if (! $isFirstLoad) {
			$privilege = Util::isPrivilege ( $this );
			if ($privilege ['role'] == Authcfg::GUEST) {
				$this->setLayoutAnswerGuest ();
				// return $this->toNoticeWarning("Bạn phải đăng nhập để xem các câu trả lời của thành viên", 3000, "/user/login");
			} else {
				$this->setLayoutAnswer ();
			}

			return array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject,
					'user' => $user,
					'action' => $this->params ( "action" )
			);
		} else {
			$this->setLayoutAjax ();
			$view = new ViewModel ( array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					'user' => $user,
					'action' => $this->params ( "action" )
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}

	/**
	 *
	 * @author izzi
	 * @todo answer page of member
	 * @return \Zend\View\Model\ViewModel multitype:unknown
	 */
	public function answerLikeAction() {
		$userID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		$user = new User ();
		$user = $user->find ( $userID, true );
		$this->getRequest ()->setMetadata ( "userID", $userID );
		$this->forward ()->dispatch ( "Web\Controller\Home", array (
				'action' => 'chart-answer',
				'controller' => 'Home'
		) );
		if (! $user) {
			// todo: no member
			return $this->redirect ()->toRoute ( "member" );
		}

		$isFirstLoad = false;
		$orderBy = array (
				"date_updated" => "acs"
		);
		$from = 0;
		$to = 16;
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
		$data = $answerMapper->getLikeAnswer ( $this->select_question, $userID, $subjectID, $orderBy, $from, $to );

		if (! $isFirstLoad) {
			$privilege = Util::isPrivilege ( $this );
			if ($privilege ['role'] == Authcfg::GUEST) {
				$this->setLayoutAnswerGuest ();
				// return $this->toNoticeWarning("Bạn phải đăng nhập để xem các câu trả lời của thành viên", 3000, "/user/login");
			} else {
				$this->setLayoutAnswer ();
			}

			return array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject,
					'user' => $user,
					'action' => $this->params ( "action" )
			);
		} else {
			$this->setLayoutAjax ();
			$view = new ViewModel ( array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],

					'user' => $user,
					'action' => $this->params ( "action" )
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}

	/**
	 *
	 * @author izzi
	 * @todo answer page of member
	 * @return \Zend\View\Model\ViewModel multitype:unknown
	 */
	public function answerDislikeAction() {
		$userID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );

		$this->getRequest ()->setMetadata ( "userID", $userID );
		$this->forward ()->dispatch ( "Web\Controller\Home", array (
				'action' => 'chart-answer',
				'controller' => 'Home'
		) );

		$user = new User ();
		$user = $user->find ( $userID, true );
		if (! $user) {
			// todo: no member
			return $this->redirect ()->toRoute ( "member" );
		}

		$isFirstLoad = false;
		$orderBy = array (
				"date_updated" => "acs"
		);
		$from = 0;
		$to = 16;
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
		$data = $answerMapper->getDislikeAnswer ( $this->select_question, $userID, $subjectID, $orderBy, $from, $to );

		if (! $isFirstLoad) {
			$privilege = Util::isPrivilege ( $this );
			if ($privilege ['role'] == Authcfg::GUEST) {
				$this->setLayoutAnswerGuest ();
				// return $this->toNoticeWarning("Bạn phải đăng nhập để xem các câu trả lời của thành viên", 3000, "/user/login");
			} else {
				$this->setLayoutAnswer ();
			}

			return array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject,
					'user' => $user,
					'action' => $this->params ( "action" )
			);
		} else {
			$this->setLayoutAjax ();
			$view = new ViewModel ( array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],

					'user' => $user,
					'action' => $this->params ( "action" )
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}

	/**
	 *
	 * @author izzi,sang
	 * @todo answer page of member
	 * @return \Zend\View\Model\ViewModel multitype:unknown
	 */
	public function answerBestAction() {
		$userID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );

		$this->getRequest ()->setMetadata ( "userID", $userID );
		$this->forward ()->dispatch ( "Web\Controller\Home", array (
				'action' => 'chart-answer',
				'controller' => 'Home'
		) );

		$user = new User ();
		$user = $user->find ( $userID, true );
		if (! $user) {
			// todo: no member
			return $this->redirect ()->toRoute ( "member" );
		}

		$isFirstLoad = false;
		$orderBy = array (
				"date_updated" => "acs"
		);
		$from = 0;
		$to = 16;
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
		$data = $answerMapper->getBestAnswer ( $this->select_question, $userID, $subjectID, $orderBy, $from, $to );

		if (! $isFirstLoad) {
			$privilege = Util::isPrivilege ( $this );
			if ($privilege ['role'] == Authcfg::GUEST) {
				$this->setLayoutAnswerGuest ();
				// return $this->toNoticeWarning("Bạn phải đăng nhập để xem các câu trả lời của thành viên", 3000, "/user/login");
			} else {
				$this->setLayoutAnswer ();
			}
			return array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],
					"list_subject" => $this->list_subject,
					'user' => $user,
					'action' => $this->params ( "action" )
			);
		} else {
			$this->setLayoutAjax ();
			$view = new ViewModel ( array (
					'list_question' => $data ['questions'],
					'totalDocument' => $data ['totalDocument'],

					'user' => $user,
					'action' => $this->params ( "action" )
			) );
			$view->setTemplate ( 'web/question/question-list.phtml' ); // path to phtml file under view folder
			return $view;
		}
	}

	/**
	 *
	 * @author izzi
	 * @todo profile page of member
	 * @return \Zend\View\Model\ViewModel multitype:unknown
	 */
	public function profileAction() {
		$privilege = Util::isPrivilege ( $this );
		if ($privilege ['role'] == Authcfg::GUEST) {
			$this->setLayoutGuest ();
			// return $this->toNoticeWarning("Bạn phải đăng nhập để xem thông tin của thành viên", 3000, "/user/login");
		} else {
			$this->setLayoutBasic ();
		}
		$userID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		$user = new User ();
		$user = $user->find ( $userID, true );
		if (! $user) {
			// todo: no member
			return $this->redirect ()->toRoute ( "member" );
		}

		return array (
				'user' => $user,
				'action' => $this->params ( "action" )
		);
	}

	/**
	 *
	 * @author sangnv
	 *
	 * @todo change following status
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function actionMemberAction() {
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
				$userMapper = new UserMapper ();
				$userID = $this->params ()->fromPost ( 'user' );
				$actionCode = $this->params ()->fromPost ( 'action' );
				$currentUserID = Util::getCurrentUser ()->getId ();
				// follow action
				if ($actionCode == "1") {
					$statusAccess = $userMapper->followMember ( $userID, $currentUserID );
					// unfollow action
				} else if ($actionCode == "2") {
					$statusAccess = $userMapper->unFollowMember ( $userID, $currentUserID );
				}
			}
		}
		$data = array (
				"status" => $statusAccess
		);
		echo json_encode ( $data );
		return $this->getResponse ();
	}

	/**
	 *
	 * @author sang
	 * @todo overview some infomation of member with ajax tooltip
	 */
	public function overviewAction() {
		$this->setLayoutAjax ();
		$rank = "";
		$appellation = "";
		$imageID = "";
		$name = "";
		$totalUserFollow = 0;

		$userID = $this->params ()->fromQuery ( 'user' );
		$userMapper = new UserMapper ();
		/* @var $user \FAQ\FAQEntity\User */
		$user = $userMapper->getOneUser ( $userID );
		if (! $user) {
			return $this->getResponse ();
		}
		$name = Util::getUserName ( $user );
		$nameSeo = Util::convertUrlSeo ( $name );
		if ($user->getAvatar ()) {
			$avatar = $user->getAvatar ();
			$imageID = $avatar->getId ();

			$contentType = $avatar->getContentType ();
			$extentionFile = Util::getTypeFile ( $contentType );
			$titleFileSeo = Util::convertUrlFileName ( $name, $extentionFile );
		}

		$rank = Util::getUserRankText ( $user );
		$totalUserFollow = $user->getTotalUserFollow ();

		$appellation = Util::getUserAppellationText ( $user );

		$statusFollowCode = 1;
		$currentStatusFollow = FAQParaConfig::statusUnfollow;
		/* @var $userFollows \Doctrine\Common\Collections\ArrayCollection */
		$userFollows = $user->getFollowMe ();

		if ($userFollows->contains ( Util::getCurrentUser () )) {
			$statusFollowCode = 2;
		}
		if ($statusFollowCode == 1) {
			$statusAction = FAQParaConfig::actionFollow;
		} else {
			$statusAction = FAQParaConfig::actionUnfollow;
			$currentStatusFollow = FAQParaConfig::statusFollow;
		}

		$overviewArr = array (
				"userID" => $userID,
				"imageID" => $imageID,
				"name" => $name,
				"nameSeo" => $nameSeo,
				"rank" => $rank,
				"appellation" => $appellation,
				"currentStatusFollow" => $currentStatusFollow,
				"statusAction" => $statusAction,
				'statusFollowCode' => $statusFollowCode,
				'totalUserFollow' => $totalUserFollow,
				'titleFileSeo' => $titleFileSeo
		);
		return $overviewArr;
	}
}