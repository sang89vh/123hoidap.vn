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
class Tag extends Entity {

	/**
	 * @ODM\String
	 * @ODM\Index(unique=true, order="asc")
	 */
	private $desc;
	/**
	 * @ODM\String
	 * @ODM\Index(unique=true, order="asc")
	 */
	private $tag_name;

	/**
	 * @ODM\ReferenceMany(targetDocument="Tag",cascade={"detach","merge","refresh","persist"})
	 */
	private $relationship_tag;
	/**
	 * @ODM\ReferenceOne(targetDocument="Image",cascade={"detach","merge","refresh","persist"})
	 */
	private $avatar;
	/**
	 * @ODM\String
	 */
	private $type;
	/**
	 * @ODM\Int
	 */
	private $status;
	/**
	 * @ODM\Date
	 * @ODM\Index(unique=false, order="asc")
	 */
	private $date_updated;

	/**
	 * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $create_by;
	/**
	 * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $active_by;
	/**
	 */
	public function __construct() {
		$this->relationship_tag = new ArrayCollection ();
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
	 * @return String
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 *
	 * @param String $type
	 * @tutorial -subject,question
	 *           - user
	 *           -location
	 */
	public function setType($type) {
		$this->type = $type;
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
	 * @return the ArrayCollection
	 */
	public function getRelationshipTag() {
		return $this->relationship_tag;
	}

	/**
	 *
	 * @param Tag $relationship_tag
	 */
	public function setRelationshipTag($relationship_tag) {
		$this->relationship_tag [] = $relationship_tag;
		return $this;
	}

	/**
	 *
	 * @return the String
	 */
	public function getTagName() {
		return $this->tag_name;
	}

	/**
	 *
	 * @param String $tag_name
	 */
	public function setTagName($tag_name) {
		$this->tag_name = $tag_name;
		return $this;
	}
	/**
	 *
	 * @return User
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
	 *
	 * @return the User
	 */
	public function getActiveBy() {
		return $this->active_by;
	}

	/**
	 *
	 * @param User $active_by
	 */
	public function setActiveBy($active_by) {
		$this->active_by = $active_by;
		return $this;
	}
}