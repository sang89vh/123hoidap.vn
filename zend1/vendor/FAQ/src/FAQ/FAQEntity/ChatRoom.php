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
class ChatRoom extends Entity {

	/**
	 * @ODM\String
	 */
	private $room_id;


	/**
	 * @ODM\Collection
	 */
	private $users;

	/**
	 * @ODM\String
	 */
	private $hash;

	/**
	 * @ODM\String
	 */
	private $name;

	/**
	 * @ODM\Date
	 */
	private $create_date;

	/**
	 * @ODM\String
	 */
	private $create_by;
	/**
	 * @return the $room_id
	 */
	public function getRoom_id() {
		return $this->room_id;
	}

	/**
	 * @return the $users
	 */
	public function getUsers() {
		return $this->users;
	}

	/**
	 * @return the $hash
	 */
	public function getHash() {
		return $this->hash;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return the $create_date
	 */
	public function getCreate_date() {
		return $this->create_date;
	}

	/**
	 * @return the $create_by
	 */
	public function getCreate_by() {
		return $this->create_by;
	}

	/**
	 * @param field_type $room_id
	 */
	public function setRoom_id($room_id) {
		$this->room_id = $room_id;
	}

	/**
	 * @param field_type $users
	 */
	public function setUsers($users) {
		$this->users = $users;
	}

	/**
	 * @param field_type $hash
	 */
	public function setHash($hash) {
		$this->hash = $hash;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param field_type $create_date
	 */
	public function setCreate_date($create_date) {
		$this->create_date = $create_date;
	}

	/**
	 * @param field_type $create_by
	 */
	public function setCreate_by($create_by) {
		$this->create_by = $create_by;
	}


}