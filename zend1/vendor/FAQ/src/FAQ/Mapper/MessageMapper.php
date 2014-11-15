<?php

namespace FAQ\Mapper;

use FAQ\DB\Db;
use FAQ\FAQEntity\Message;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQEntity\User;
use FAQ\FAQCommon\Util;
use FAQ\FAQEntity\Notify;
use FAQ\FAQEntity\ChatMessage;
use FAQ\FAQEntity\ChatUserUnread;
use FAQ\FAQEntity\ChatRoom;

/**
 *
 * @author izzi
 *
 */
class MessageMapper extends Db {
	private $message;
	private $user;
	private $notify;
	private $chat_message;
	private $chat_user_unread;
	private $chat_room;
	public function __construct() {
		parent::__construct ();
		$this->message = new Message ();
		$this->user = new User ();
		$this->notify = new Notify ();
		$this->chat_message = new ChatMessage ();
		$this->chat_user_unread = new ChatUserUnread ();
		$this->chat_room = new ChatRoom ();
	}

	/**
	 *
	 * @return \FAQ\FAQEntity\Message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 *
	 * @return \FAQ\FAQEntity\Message
	 */
	public function createMessage() {
		$message = new Message ();
		$message->insert ();
		return $message;
	}

	/**
	 *
	 * @todo get list message (object) by User_to
	 * @return \Doctrine\ODM\MongoDB\LoggableCursor
	 * @param String $user_id
	 */
	public function getMessageToUser($user_id) {
		$message = $this->createMessage ();
		$qb = $message->getQueryBuilder ();
		$qb->field ( "to_user.id" )->equals ( $user_id );
		return $qb->getQuery ()->execute ();
	}

	/**
	 *
	 * @todo get list id message by user_to
	 * @param String $user_id
	 * @return array
	 */
	public function getMessageToUserArray($user_id) {
		$messageIdArr = array ();
		$listMessage = $this->getMessageToUser ( $user_id );
		foreach ( $listMessage as $message ) {
			$messageIdArr [] = $message->getId ();
		}
		return $messageIdArr;
	}

	/**
	 *
	 * @param String $userID
	 * @param Int $from
	 * @param Int $to
	 * @return array:
	 */
	public function getNotify($userID, $numberDocument) {
		$qb = $this->user->getQueryBuilder ()->select ( "notify" );
		$qb->field ( "id" )->equals ( $userID );
		$q = $qb->getQuery ();
		/* @var $user \FAQ\FAQEntity\User */
		$user = $q->getSingleResult ();
		// if dont detach user then current user is same user=>conflick
		$this->getDm ()->detach ( $user );
		// $user = Util::getCurrentUser();
		$notifies = $user->getNotify ();
		$totalDocument = count ( $notifies );

		// if $numberDocument<0 then get all notify
		if ($numberDocument > 0) {
			$offset = $totalDocument - $numberDocument;
			$length = $numberDocument;
			// var_dump($totalDocument,$offset,$length);
			$notifies = $notifies->slice ( $offset, $length );
		} else {
			$offset = 0;
			$length = $totalDocument;
		}
		$data = array (
				"offset" => $offset,
				'length' => $length,
				'notifies' => $notifies
		);
		return $data;
	}
	/**
	 *
	 * @todo get list room by user_id.
	 * @param $user string
	 */
	private function getArrRoomIdByUser($user_id) {
		$arrUser = array ();
		$qb = $this->chat_room->getQueryBuilder ();
		$qb->field ( "users" )->equals ( $user_id );
		$arrRoomCur = $qb->getQuery ()->execute ();
		foreach ( $arrRoomCur as $room ) {
			$r = null;
			$r->room_id = $room->getRoom_id ();
			$r->room_name = $room->getName ();
			$r->list_message = array ();
			$r->num_message = 0;
			$arrUser [] = $r;
		}
		return $arrUser;
	}

	/**
	 *
	 * @todo . get list chat Notify.
	 * @return array
	 */
	public function getChatUnread() {
		$user_id = Util::getIDCurrentUser ();
		$list_room = $this->getArrRoomIdByUser ( $user_id );
		$list_room_id = array ();
		foreach ( $list_room as $room ) {
			$list_room_id [] = $room->room_id;
		}

		$qb = $this->chat_user_unread->getQueryBuilder ();
		$qb->field ( "room" )->in ( $list_room_id );
		$qb->field ( "user" )->equals ( $user_id );
		$list_unread = $qb->getQuery ()->execute ();

		foreach ( $list_unread as $unread ) {
			/* @var $unread ChatUserUnread */
			foreach ( $list_room as $room ) {

				if ($room->room_id == $unread->getRoom ()) {
					$room->list_message [] = $unread->getMessage ();
					$room->num_message;
				}
			}
		}
		return $list_room;
	}
}

?>