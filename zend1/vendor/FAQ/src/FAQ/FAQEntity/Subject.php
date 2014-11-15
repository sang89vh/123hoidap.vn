<?php

namespace FAQ\FAQEntity;

use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQCommon\Util;

/**
 * @ODM\Document
 *
 * @todo Danh sach cac chu de trong he thong
 */
class Subject extends Entity {

	/**
	 * @ODM\String
	 */
	private $title;

	/**
	 * @ODM\String
	 */
	private $desc;

	/**
	 * @ODM\Collection
	 * @ODM\Index
	 */
	private $key_word = array ();

	/**
	 * @ODM\ReferenceOne(targetDocument="Image",cascade={"detach","merge","refresh","persist"})
	 */
	private $avatar;

	/**
	 * @ODM\Int
	 */
	private $total_question;

	/**
	 * @ODM\Int
	 */
	private $total_user_follow;

	/**
	 * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $create_by;

	/**
	 * @ODM\Date
	 */
	private $date_created;

	/**
	 * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $update_by;

	/**
	 * @ODM\Date
	 */
	private $date_updated;

	/**
	 * @ODM\Int
	 */
	private $recomment;

	/**
	 *
	 * @todo user following subject
	 *       @ODM\ReferenceMany(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $user_follow;

	/**
	 * @ODM\ReferenceMany(targetDocument="Skill",cascade={"detach","merge","refresh","persist"})
	 */
	private $skill;

	/**
	 * @ODM\Int
	 */
	private $status;
	public function __construct() {
		$this->user_follow = new ArrayCollection ();
		$this->skill = new ArrayCollection ();
		$this->status = 1;
	}

	/**
	 *
	 * @return String
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
	 * @return String
	 */
	public function getDesc() {
		return $this->desc;
	}

	/**
	 *
	 * @param String $desc
	 */
	public function setDesc($desc) {
		$this->desc = $desc;
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
	 * @todo used when searching subject
	 * @param String $key_word
	 */
	public function setKeyWord($key_word) {
		$this->key_word = $key_word;
		return $this;
	}

	/**
	 *
	 * @return Image
	 */
	public function getAvatar() {
		return $this->avatar;
	}

	/**
	 *
	 * @param Image $avatar
	 */
	public function setAvatar($avatar) {
		$this->avatar = $avatar;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalQuestion() {
		if ($this->total_question == null) {
			return 0;
		} else {
			return $this->total_question;
		}
	}

	/**
	 *
	 * @param Int $total_question
	 */
	public function setTotalQuestion($total_question) {
		$this->total_question = $total_question;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalUserFollow() {
		return $this->total_user_follow;
	}

	/**
	 *
	 * @param Int $total_user_follow
	 */
	public function setTotalUserFollow($total_user_follow) {
		$this->total_user_follow = $total_user_follow;
		return $this;
	}

	/**
	 *
	 * @return String
	 */
	public function getCreateBy() {
		return $this->create_by;
	}

	/**
	 *
	 * @param String $create_by
	 */
	public function setCreateBy($create_by) {
		$this->create_by = $create_by;
		return $this;
	}

	/**
	 *
	 * @return Date
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
	 * @return String
	 */
	public function getUpdateBy() {
		return $this->update_by;
	}

	/**
	 *
	 * @param String $update_by
	 */
	public function setUpdateBy($update_by) {
		$this->update_by = $update_by;
		return $this;
	}

	/**
	 *
	 * @return Date
	 */
	public function getDateUpdated() {
		return $this->date_updated;
	}

	/**
	 *
	 * @param Date $date_updated
	 */
	public function setDateUpdated($date_updated) {
		$this->date_updated = $date_updated;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getRecomment() {
		return $this->recomment;
	}

	/**
	 *
	 * @param Int $recomment
	 */
	public function setRecomment($recomment) {
		$this->recomment = $recomment;
		return $this;
	}

	/**
	 *
	 * @todo user following subject
	 * @return ArrayCollection
	 */
	public function getUserFollow() {
		return $this->user_follow;
	}

	/**
	 *
	 * @deprecated this function only call from User Entity
	 * @todo user following subject
	 * @param User $user_follow
	 */
	public function setUserFollow($user_follow) {
		$this->user_follow [] = $user_follow;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getSkill() {
		return $this->skill;
	}

	/**
	 *
	 * @param Skill $skill
	 */
	public function setSkill($skill) {
		$this->skill [] = $skill;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 *
	 * @param Int $status
	 */
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}

	/**
	 * @odm\PrePersist
	 */
	public function autoSetDateChange() {
		if ($this->date_created) {
			$this->date_updated = Util::getCurrentTime ();
		}
		if (! $this->date_created) {
			$this->date_created = Util::getCurrentTime ();
		}
	}
}