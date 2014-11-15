<?php

namespace FAQ\FAQEntity;

use FAQ\DB\EntityEmbed;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;

/**
 * @ODM\EmbeddedDocument
 *
 * @todo Cau tra loi cua cau hoi
 */
class Answer extends EntityEmbed {

	/**
	 * @ODM\String
	 */
	private $content;
	/**
	 * @ODM\EmbedMany(targetDocument="HistoryAnswer")
	 */
	private $history_content;
	/**
	 * @ODM\Boolean
	 */
	private $is_best;

	/**
	 * @ODM\Int
	 */
	private $total_like;

	/**
	 * @ODM\Int
	 */
	private $total_dislike;

	/**
	 * @ODM\Date
	 * @ODM\Index(unique=false, order="asc")
	 */
	private $date_created;

	/**
	 * @ODM\Date
	 * @ODM\Index(unique=false, order="asc")
	 */
	private $date_updated;
	/**
	 *
	 * @todo lưu trạng thái của question
	 * @tutorial Domain là QUESTION, giá trị là các số nguyên tố như sau:
	 *           <br> 2-Đã bị xóa-FAQParaConfig::QUESTION_STATUS_TEMP_DELETE
	 *           <br> 3-Bản nháp-FAQParaConfig::QUESTION_STATUS_DRAFT
	 *           <br> 5-Đang hỏi-FAQParaConfig::QUESTION_STATUS_OPEN
	 *           <br> 7-Đã đóng-FAQParaConfig::QUESTION_STATUS_CLOSE
	 *           <br> 11-Đã có câu trả lời tốt nhất-FAQParaConfig::QUESTION_STATUS_EXIST_BEST
	 *           <br> 13-Bảo vệ-FAQParaConfig::QUESTION_STATUS_PROTECT
	 *           <br> 17-WIKI-FAQParaConfig::QUESTION_STATUS_WIKI_POST
	 *           <br> 19
	 *           <br> 23
	 *           <br> 29
	 *           <br> 31
	 *           <br> 37
	 *           <br> 41
	 *           <br> 43
	 *           <br> 47
	 *           <br> 53
	 *           <br> 59
	 *           <br> 61
	 *           <br> 67
	 *           <br> 71
	 *           <br> 73
	 *           <br> 79
	 *           <br> 83
	 *           <br> 89
	 *           <br> 97
	 *           @ODM\Int
	 */
	private $status;
	/**
	 * @ODM\ReferenceOne(targetDocument="User",inversedBy="answer",cascade={"merge","refresh","persist"})
	 * @ODM\Index(unique=false, order="asc")
	 */
	private $create_by;

	/**
	 * @ODM\ReferenceMany(targetDocument="User")
	 */
	private $like;

	/**
	 * @ODM\ReferenceMany(targetDocument="User")
	 */
	private $dislike;

	/**
	 * @ODM\EmbedMany(targetDocument="Reply")
	 * @ODM\Index(unique=false, order="asc")
	 */
	private $reply;
	/**
	 * @ODM\EmbedMany(targetDocument="UserSpam")
	 */
	private $user_spam;
	public function __construct() {
		$this->like = new ArrayCollection ();
		$this->dislike = new ArrayCollection ();
		$this->reply = new ArrayCollection ();
		$this->status=FAQParaConfig::QUESTION_STATUS_OPEN;
		$this->user_spam = new ArrayCollection ();
	}

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
	 * @return the ArrayCollection
	 */
	public function getHistoryContent() {
		return $this->history_content;
	}

	/**
	 *
	 * @param HistoryAnswer $history_content
	 */
	public function setHistoryContent($history_content) {
		$this->history_content [] = $history_content;
		return $this;
	}
	/**
	 *
	 * @return Boolean
	 * @todo Dat co danh dau cau tra loi hay nhat
	 * @tutorial TRUE:cau tra loi hay nhat
	 *           Null hoac FALSE : Khong phai la cau tra loi hay nhat
	 */
	public function getIsBest() {
		return $this->is_best;
	}

	/**
	 *
	 * @param Boolean $is_best
	 * @todo Danh dau cau tra loi la tot nhat
	 */
	public function setIsBest($is_best) {
		$this->is_best = $is_best;
		return $this;
	}

	/**
	 *
	 * @return Int
	 * @todo Lay tong so nguoi like cau tra loi
	 */
	public function getTotalLike() {
		return $this->total_like;
	}

	/**
	 *
	 * @param Int $total_like
	 * @todo Cap nhat tong so nguoi like cau tra loi
	 */
	public function setTotalLike($total_like) {
		$this->total_like = $total_like;
		return $this;
	}

	/**
	 *
	 * @return Int
	 * @todo Lay tong so nguoi dislike cau tra loi
	 */
	public function getTotalDislike() {
		return $this->total_dislike;
	}

	/**
	 *
	 * @param Int $total_dislike
	 * @todo Cap nhat so nguoi disklike cau tra loi
	 */
	public function setTotalDislike($total_dislike) {
		$this->total_dislike = $total_dislike;
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
	 * @return Date
	 * @todo Lay ngay cap nhat cau tra loi
	 */
	public function getDateUpdated() {
		return $this->date_updated;
	}

	/**
	 *
	 * @param Date $date_updated
	 * @todo Cap nhat ngay sua doi cau tra loi
	 */
	public function setDateUpdated($date_updated) {
		$this->date_updated = $date_updated;
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
	 *
	 * @return Int
	 */
	private function getStatus() {
		return $this->status;
	}
	/**
	 *
	 * @return String <br> 2-Đã bị xóa-FAQParaConfig::QUESTION_STATUS_TEMP_DELETE
	 *         <br> 3-Bản nháp-FAQParaConfig::QUESTION_STATUS_DRAFT
	 *         <br> 5-Đang hỏi-FAQParaConfig::QUESTION_STATUS_OPEN
	 *         <br> 7-Đã đóng-FAQParaConfig::QUESTION_STATUS_CLOSE
	 *         <br> 11-Đã có câu trả lời tốt nhất-FAQParaConfig::QUESTION_STATUS_EXIST_BEST
	 *         <br> 13-Bảo về-FAQParaConfig::QUESTION_STATUS_PROTECT
	 *         <br> 17-WIKI-FAQParaConfig::QUESTION_STATUS_WIKI_POST
	 *
	 */
	public function getStatusLabel() {
		$questionStatusLabel = "";
// var_dump($this->status);
		if ($this->isContainStatus ( FAQParaConfig::QUESTION_STATUS_WIKI_POST )) {
			$questionStatusLabel = $questionStatusLabel . "#Cộng đồng Wiki";
		}

		return $questionStatusLabel;
	}
	/**
	 *
	 * @param Int $statusCheck
	 *        	FAQParaConfig::QUESTION_STATUS
	 */
	public function isContainStatus($statusCheck) {
// 		var_dump($this->status);
// 		var_dump($statusCheck);
		if (($this->status % $statusCheck) == 0) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 *
	 * @param Int $status
	 * @tutorial
	 *
	 *
	 *
	 */
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}
	/**
	 *
	 * @param Int $status
	 * @tutorial FAQParaConfig::QUESTION_STATUS..
	 *
	 */
	public function addStatus($status) {
		if (! $this->isContainStatus ( $status )) {
			$this->status = $this->status * $status;
		}
	}
	/**
	 *
	 * @param Int $status
	 * @tutorial FAQParaConfig::QUESTION_STATUS..
	 *
	 */
	public function removeStatus($status) {
		if ($this->isContainStatus ( $status )) {
			$this->status = $this->status / $status;
		}
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getLike() {
		return $this->like;
	}

	/**
	 *
	 * @param User $like
	 */
	public function setLike($like) {
		$this->like [] = $like;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getDislike() {
		return $this->dislike;
	}

	/**
	 *
	 * @param User $dislike
	 */
	public function setDislike($dislike) {
		$this->dislike [] = $dislike;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 * @todo Lay thong tin Reply cau tra loi
	 */
	public function getReply() {
		return $this->reply;
	}

	/**
	 *
	 * @param Reply $reply
	 * @todo Cap nhat them thong tin Reply cho cau tra loi
	 */
	public function setReply($reply) {
		$this->reply [] = $reply;
		return $this;
	}
	/**
	 *
	 * @return the UserSpam
	 */
	public function getUserSpam() {
		return $this->user_spam;
	}

	/**
	 *
	 * @param UserSpam $user_spam
	 */
	public function setUserSpam($user_spam) {
		$this->user_spam [] = $user_spam;
		return $this;
	}
	/**
	 * @odm\PrePersist
	 */
	public function autoSetDateChange() {
		if ($this->date_created && ! $this->date_updated) {
			$this->date_updated = Util::getCurrentTime ();
		}
		if (! $this->date_created) {
			$this->date_created = Util::getCurrentTime ();
		}
	}
}