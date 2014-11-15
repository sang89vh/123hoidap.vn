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
class HistoryAnswer extends EntityEmbed {

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
	 *
	 * @return String
	 * @todo Lay noi dung cua cau tra loi
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 *
	 * @param String $content
	 * @todo Dat noi dung cho cau tra loi
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
	 * @return Date
	 * @todo Lay ngay tao cau tra loi
	 */
	public function getDateCreated() {
		return $this->date_created;
	}

	/**
	 *
	 * @param Date $date_created
	 * @todo Cap nhat ngay tao cau tra loi
	 */
	public function setDateCreated($date_created) {
		$this->date_created = $date_created;
		return $this;
	}

	/**
	 *
	 * @return User
	 * @todo Lay ten User dang cau tra loi
	 */
	public function getCreateBy() {
		return $this->create_by;
	}

	/**
	 *
	 * @param User $create_by
	 * @todo Cap nhat nguoi tra loi
	 */
	public function setCreateBy($create_by) {
		$create_by->setAnswer ( $this );
		$this->create_by = $create_by;
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