<?php

namespace FAQ\FAQEntity;

use FAQ\FAQCommon\Util;
use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Boolean;

/**
 * @ODM\Document
 * @author izzi
 * @todo. Collection dung chung voi Sailsjs
 */
class ChatUserUnread extends Entity {

	/**
	 * @ODM\String
	 */
	private $user;

	/**
	 * @ODM\String
	 */
	private $room;

	/**
	 * @ODM\String
	 * @todo ChatMessage.message_id
	 */
	private $message;
	/**
	 * @return the $user
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @return the $room
	 */
	public function getRoom() {
		return $this->room;
	}

	/**
	 * @return the $message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param field_type $user
	 */
	public function setUser($user) {
		$this->user = $user;
	}

	/**
	 * @param field_type $room
	 */
	public function setRoom($room) {
		$this->room = $room;
	}

	/**
	 * @param field_type $message
	 */
	public function setMessage($message) {
		$this->message = $message;
	}


}