<?php

namespace FAQ\FAQEntity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\Document
 *
 * @todo Danh sach cac tu khoa co trong he thong
 *       Dung trong tim kiem bai viet, cau hoi, nguoi dung, chu de
 *       co trong he thong
 */
class KeyWord extends Entity {

	/**
	 * @ODM\String
	 * @ODM\Index(unique=true, order="asc")
	 */
	private $desc;

	/**
	 * @ODM\Collection
	 * @ODM\Index
	 */
	private $key = array ();
	/**
	 * @ODM\ReferenceOne(targetDocument="Image",cascade={"detach","merge","refresh","persist"})
	 */
	private $avatar;
	/**
	 * @ODM\Int
	 */
	private $type;



	/**
	 * @ODM\ReferenceOne(targetDocument="Question",cascade={"detach","merge","refresh","persist"})
	 */
	private $question;

	/**
	 * @ODM\ReferenceOne(targetDocument="Subject",cascade={"detach","merge","refresh","persist"})
	 */
	private $subject;

	/**
	 * @ODM\ReferenceOne(targetDocument="location",cascade={"detach","merge","refresh","persist"})
	 */
	private $location;

	/**
	 * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $user;
	/**
	 * @ODM\ReferenceOne(targetDocument="Skill",cascade={"detach","merge","refresh","persist"})
	 */
	private $skill;

	/**
	 * @ODM\Date
	 * @ODM\Index(unique=false, order="asc")
	 */
	private $date_updated;
	/**
	 *
	 * @return the Skill $skill
	 */
	public function getSkill() {
		return $this->skill;
	}

	/**
	 *
	 * @param Skill $skill
	 */
	public function setSkill($skill) {
		$this->skill = $skill;
	}

	/**
	 *
	 * @return array
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 *
	 * @param String $key
	 */
	public function setKey($key) {
		$this->key [] = strtolower ( $key );
		return $this;
	}

	/**
	 *
	 * @param ArrayCollection $key
	 */
	public function concatKey($collectKey) {
		$this->key = $this->key + $collectKey;
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
	 * @return array
	 */
	public function getDesc() {
		return $this->desc;
	}

	/**
	 *
	 * @param String $desc
	 */
	public function setDesc($desc) {
		$this->desc = strtolower ( $desc );
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 *
	 * @param Int $type
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}




	/**
	 *
	 * @return Question
	 */
	public function getQuestion() {
		return $this->question;
	}

	/**
	 *
	 * @param Question $question
	 */
	public function setQuestion($question) {
		$this->question = $question;
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
	 * @return the Location
	 */
	public function getLocation() {
		return $this->location;
	}

	/**
	 *
	 * @param Location $location
	 */
	public function setLocation($location) {
		$this->location = $location;
		return $this;
	}

	/**
	 *
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 *
	 * @param User $user
	 */
	public function setUser($user) {
		$this->user = $user;
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
}