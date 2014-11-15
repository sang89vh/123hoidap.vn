<?php

namespace FAQ\FAQEntity;

use FAQ\DB\EntityEmbed;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQCommon\Util;

/**
 * @ODM\EmbeddedDocument
 *
 * @todo luu noi dung va lich su noi dung cua cau hoi, bai viet
 */
class HistoryContent extends EntityEmbed {

	/**
	 * @ODM\String
	 */
	private $title;
	/**
	 * @ODM\String
	 */
	private $content;
	/**
	 * @ODM\String
	 */
	private $note_edit;

	/**
	 * @ODM\Int
	 */
	private $is_active;
	/**
	 * @ODM\Collection
	 * @ODM\Index
	 */
	private $key_word = array ();

	/**
	 * @ODM\Date
	 * @ODM\Index(unique=false, order="asc")
	 */
	private $date_created;

	/**
	 * @ODM\ReferenceOne(targetDocument="User",inversedBy="answer",cascade={"merge","refresh","persist"})
	 * @ODM\Index(unique=false, order="asc")
	 */
	private $create_by;

	/**
	 * @ODM\ReferenceOne(targetDocument="Subject",cascade={"detach","merge","refresh","persist"})
	 */
	private $subject;

	/**
	 * @ODM\Int
	 */
	private $bonus_point;

	/**
	 *
	 * @return the String
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 *
	 * @param String $title
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 *
	 * @return the String
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 *
	 * @param String $content
	 */
	public function setContent($content) {
		$this->content = $content;
		return $this;
	}

	/**
	 *
	 * @return the String
	 */
	public function getNoteEdit() {
		return $this->note_edit;
	}

	/**
	 *
	 * @param String $note_edit
	 */
	public function setNoteEdit($note_edit) {
		$this->note_edit = $note_edit;
		return $this;
	}

	/**
	 *
	 * @return the Int
	 */
	public function getIsActive() {
		return $this->is_active;
	}

	/**
	 *
	 * @param Int $is_active
	 */
	public function setIsActive($is_active) {
		$this->is_active = $is_active;
		return $this;
	}
	/**
	 *
	 * @return array
	 */
	public function getKeyWord() {
		return $this->key_word;
	}

	/**
	 *
	 * @param array $key_word
	 */
	public function setKeyWord($key_word) {
		// var_dump($key_word);
		$this->key_word = $key_word;
		return $this;
	}
	/**
	 *
	 * @return the Date
	 */
	public function getDateCreated() {
		return $this->date_created;
	}

	/**
	 *
	 * @param Date $date_created
	 */
	public function setDateCreated($date_created) {
		$this->date_created = $date_created;
		return $this;
	}

	/**
	 *
	 * @return the User
	 */
	public function getCreateBy() {
		return $this->create_by;
	}

	/**
	 *
	 * @param User $create_by
	 */
	public function setCreateBy($create_by) {
		$this->create_by = $create_by;
		return $this;
	}

	/**
	 *
	 * @return Subject
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 *
	 * @param Subject $subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
		return $this;
	}
	/**
	 *
	 * @return Int
	 */
	public function getBonusPoint() {
		return $this->bonus_point;
	}

	/**
	 *
	 * @param Int $bonus_point
	 */
	public function setBonusPoint($bonus_point) {
		$this->bonus_point = $bonus_point;
		return $this;
	}
	/**
	 * @odm\PrePersist
	 */
	public function autoSetDateChange() {
		if (! $this->date_created) {
			$this->date_created = Util::getCurrentTime ();
		}
	}
}