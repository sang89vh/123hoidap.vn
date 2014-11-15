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
class ChatMessage extends Entity {

	/**
	 * @ODM\String
	 */
	private $message_id;

	/**
	 * @ODM\String
	 */
	private $from;

	/**
	 * @ODM\String
	 */
	private $to;

	/**
	 * @ODM\String
	 */
	private $content;

	/**
	 * @ODM\Date
	 */
	private $create_date;
	/**
	 * @return the $message_id
	 */
	public function getMessage_id() {
		return $this->message_id;
	}

	/**
	 * @return the $from
	 */
	public function getFrom() {
		return $this->from;
	}

	/**
	 * @return the $to
	 */
	public function getTo() {
		return $this->to;
	}

	/**
	 * @return the $content
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @return the $create_date
	 */
	public function getCreate_date() {
		return $this->create_date;
	}

	/**
	 * @param field_type $message_id
	 */
	public function setMessage_id($message_id) {
		$this->message_id = $message_id;
	}

	/**
	 * @param field_type $from
	 */
	public function setFrom($from) {
		$this->from = $from;
	}

	/**
	 * @param field_type $to
	 */
	public function setTo($to) {
		$this->to = $to;
	}

	/**
	 * @param field_type $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @param field_type $create_date
	 */
	public function setCreate_date($create_date) {
		$this->create_date = $create_date;
	}


}