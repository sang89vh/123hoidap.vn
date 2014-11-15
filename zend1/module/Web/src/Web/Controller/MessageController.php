<?php

namespace Web\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\Mapper\MessageMapper;
use FAQ\FAQCommon\Util;
use FAQ\Mapper\UserMapper;

class MessageController extends FAQAbstractActionController {
	public function inboxAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$this->setLayoutAjax ();

		$messageMapper = new MessageMapper ();
		$chatNotify = $messageMapper->getChatUnread ();
		/*
		 * $data = $messageMapper->getNotify(Util::getIDCurrentUser(), - 1); $currentUser = Util::getCurrentUser(); $currentUser->setTotalNewNotify(0); $userMapper = new UserMapper(); $userMapper->update($currentUser);
		 */
		return array (
				'chat_notify' => $chatNotify
		);
	}
	public function indexAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$this->setLayoutHome ();

		$messageMapper = new MessageMapper ();

		$data = $messageMapper->getNotify ( Util::getIDCurrentUser (), - 1 );
		// var_dump($notifies);
		$currentUser = Util::getCurrentUser ();
		$currentUser->setTotalNewNotify ( 0 );
		$userMapper = new UserMapper ();
		$userMapper->update ( $currentUser );

		return array (
				'data' => $data
		);
	}
	public function notifyAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$this->setLayoutAjax ();

		$messageMapper = new MessageMapper ();

		$data = $messageMapper->getNotify ( Util::getIDCurrentUser (), 5 );
		// var_dump($notifies);
		$currentUser = Util::getCurrentUser ();
		$currentUser->setTotalNewNotify ( 0 );
		$userMapper = new UserMapper ();
		$userMapper->update ( $currentUser );

		return array (
				'data' => $data
		);
	}

	/*
	 * @Long . comment de sua thanh chat ben duoi public function moreMessageAction() { $privilege = Util::isPrivilege($this); if (! $privilege['isAllowed']) { return $this->toNoticeWarning("Bạn cần đăng nhập trước!", 3000, "/user/login"); } $this->setLayoutHome(); $messageMapper = new MessageMapper(); $data = $messageMapper->getNotify(Util::getIDCurrentUser(), - 1); // var_dump($notifies); $currentUser = Util::getCurrentUser(); $currentUser->setTotalNewNotify(0); $userMapper = new UserMapper(); $userMapper->update($currentUser); return array( 'data' => $data ); }
	 */
	public function chatBoxAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$room_id = $this->getEvent ()->getRouteMatch ()->getParam ( "tab" );
		$this->setLayoutHome ();
		return array (
				"room_id" => $room_id
		);
	}
	public function sendAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$isAjax = $this->getRequest ()->getQuery ( "ajax" );
		if ($isAjax == true) {
			$this->setLayoutAjax ();
		} else {
			$this->setLayoutBasic ();
		}
	}
	public function detailAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
	}
}