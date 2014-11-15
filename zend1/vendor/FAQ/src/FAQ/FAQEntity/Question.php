<?php

namespace FAQ\FAQEntity;

use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQCommon\Authcfg;

/**
 * @ODM\Document
 *
 * @todo Luu thong tin cac cau hoi cua nguoi dung
 */
class Question extends Entity {

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
	private $sys_content;
	/**
	 * @ODM\String
	 */
	private $short_content;

	/**
	 * @ODM\EmbedMany(targetDocument="HistoryContent")
	 */
	private $history_content;

	/**
	 * @ODM\Collection
	 */
	private $first_image = array ();
	/**
	 * @ODM\Int
	 */
	private $is_top;
	/**
	 * @ODM\Int
	 */
	private $is_highlight;
	/**
	 * @ODM\Collection
	 * @ODM\Index
	 */
	private $key_word = array ();
	/**
	 * @ODM\Collection
	 * @ODM\Index(unique=false, order="asc")
	 */
	private $sys_key_word = array ();
	/**
	 * @ODM\Int
	 *
	 * @todo 0 or null is'nt extraction key word<br>
	 *       1 is extraction key word
	 *
	 */
	private $is_extraction;
	/**
	 * @ODM\Collection
	 */
	private $hashtag = array ();

	/**
	 * @ODM\Int
	 */
	private $total_answer;

	/**
	 * @ODM\Int
	 */
	private $total_like;

	/**
	 * @ODM\Int
	 */
	private $total_dislike;

	/**
	 * @ODM\Int
	 */
	private $total_share;
	/**
	 * @ODM\Int
	 */
	private $total_view;

	/**
	 * @ODM\Int
	 */
	private $total_spam;

	/**
	 * @ODM\Int
	 *
	 * @todo mark admin add to spam
	 */
	private $is_admin_spam;
	/**
	 * @ODM\Date
	 *
	 * @todo mark admin add to spam
	 */
	private $dateupdated_admin_spam;

	/**
	 * @ODM\Int
	 *
	 * @todo mark approve edit wikistyle
	 */
	private $is_approve_edit_question;
	/**
	 * @ODM\Date
	 *
	 * @todo mark admin add to spam
	 */
	private $dateupdated_approve_edit_question;
	/**
	 * @ODM\Int
	 *
	 * @todo mark approve edit wikistyle
	 */
	private $is_approve_edit_answer;
	/**
	 * @ODM\Date
	 *
	 * @todo mark admin add to spam
	 */
	private $dateupdated_approve_edit_answer;

	/**
	 * @ODM\Int
	 */
	private $total_follow;

	/**
	 * @ODM\Int
	 */
	private $bonus_point;

	/**
	 * @ODM\Int
	 */
	private $old_bonus_point;

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
	 * @ODM\ReferenceMany(targetDocument="User")
	 */
	private $like;

	/**
	 * @ODM\ReferenceMany(targetDocument="User")
	 */
	private $dislike;
	/**
	 *
	 * @todo store date updated best answer
	 *       @ODM\Date
	 */
	private $date_update_best;

	/**
	 * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $create_by;
	/**
	 * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $protect_by;
	/**
	 * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $close_by;

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
	 * @ODM\ReferenceOne(targetDocument="Subject",cascade={"detach","merge","refresh","persist"})
	 */
	private $subject;

	/**
	 * @ODM\ReferenceMany(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $user_follow;

	/**
	 * @ODM\ReferenceMany(targetDocument="User", cascade={"detach","merge","refresh","persist"})
	 */
	private $spam;
	/**
	 * @ODM\EmbedMany(targetDocument="UserSpam")
	 */
	private $user_spam;

	/**
	 * @ODM\ReferenceMany(targetDocument="User", cascade={"detach","merge","refresh","persist"})
	 */
	private $share;

	/**
	 * @ODM\EmbedMany(targetDocument="Answer")
	 */
	private $answer;

	/**
	 * @ODM\ReferenceMany(targetDocument="Message",cascade={"detach","merge","refresh","persist"})
	 */
	private $chat_help;

	/**
	 *
	 * @return the ArrayCollection
	 */
	public function getHistoryContent() {
		return $this->history_content;
	}

	/**
	 *
	 * @param HistoryContent $history_content
	 */
	public function setHistoryContent($history_content) {
		$this->history_content [] = $history_content;
		return $this;
	}

	/**
	 *
	 * @return the array
	 */
	public function getFirstImage() {
		return $this->first_image;
	}

	/**
	 *
	 * @param array $first_image
	 */
	public function setFirstImage($first_image) {
		$this->first_image [] = $first_image;
		return $this;
	}

	/**
	 *
	 * @return the Int
	 */
	public function getIsTop() {
		return $this->is_top;
	}

	/**
	 *
	 * @param Int $is_top
	 */
	public function setIsTop($is_top) {
		$this->is_top = $is_top;
		return $this;
	}

	/**
	 *
	 * @return the Int
	 */
	public function getIsHighlight() {
		return $this->is_highlight;
	}

	/**
	 *
	 * @param Int $is_highlight
	 */
	public function setIsHighlight($is_highlight) {
		$this->is_highlight = $is_highlight;
		return $this;
	}
	public function __construct() {
		$this->user_follow = new ArrayCollection ();
		$this->spam = new ArrayCollection ();
		$this->user_spam = new ArrayCollection ();
		$this->share = new ArrayCollection ();
		$this->answer = new ArrayCollection ();
		$this->history_content = new ArrayCollection ();
		$this->chat_help = new ArrayCollection ();
		$this->like = new ArrayCollection ();
		$this->dislike = new ArrayCollection ();
		$this->is_admin_spam = FAQParaConfig::IS_ADMIN_SPAM_STATUS_NOTACCESS;

		$this->setTotalAnswer ( 0 );
		$this->setTotalDislike ( 0 );
		$this->setTotalFollow ( 0 );
		$this->setTotalLike ( 0 );
		$this->setTotalShare ( 0 );
		$this->setTotalSpam ( 0 );
		$this->setOldBonusPoint ( 0 );
		$this->setBonusPoint ( 0 );
		$this->setTotalView( 0 );
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
	 * @todo no containt html tag
	 * @return the String
	 */
	public function getSysContent() {
		return $this->sys_content;
	}

	/**
	 *
	 * @todo no containt html tag
	 * @param String $sys_content
	 */
	public function setSysContent($sys_content) {
		$this->sys_content = $sys_content;
		return $this;
	}

	/**
	 *
	 * @todo no containt html tag and it is substring 0 to 70
	 * @return the String
	 */
	public function getShortContent() {
		return $this->short_content;
	}

	/**
	 *
	 * @todo no containt html tag and it is substring 0 to 70
	 * @param String $short_content
	 */
	public function setShortContent($short_content) {
		$this->short_content = $short_content;
		return $this;
	}

	/**
	 *
	 * @return the array
	 */
	public function getSysKeyWord() {
		return $this->sys_key_word;
	}

	/**
	 *
	 * @param String $sys_key_word
	 */
	public function setSysKeyWord($sys_key_word) {
		$this->sys_key_word [] = $sys_key_word;
		return $this;
	}

	/**
	 *
	 * @return the Int
	 */
	public function getIsExtraction() {
		return $this->is_extraction;
	}

	/**
	 *
	 * @param Int $is_extraction
	 */
	public function setIsExtraction($is_extraction) {
		$this->is_extraction = $is_extraction;
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
	 * @todo remove all keyword
	 * @return \FAQ\FAQEntity\Question
	 */
	public function removeAllKeyWord() {
		// var_dump($key_word);
		$this->key_word = array ();
		return $this;
	}
	/**
	 *
	 * @todo replace keyword
	 * @return \FAQ\FAQEntity\Question
	 */
	public function replaceKeyWord($array_keyword) {
		// var_dump($key_word);
		$this->key_word = $array_keyword;
		return $this;
	}

	/**
	 *
	 * @param String $key_word
	 */
	public function setKeyWord($key_word) {
		// var_dump($key_word);
		$this->key_word [] = $key_word;
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	public function getHashtag() {
		return $this->hashtag;
	}

	/**
	 *
	 * @param String $hashtag
	 */
	public function setHashtag($hashtag) {
		$this->hashtag [] = $hashtag;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalAnswer() {
		return $this->total_answer;
	}

	/**
	 *
	 * @param Int $total_answer
	 */
	public function setTotalAnswer($total_answer) {
		$this->total_answer = $total_answer;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalLike() {
		return $this->total_like;
	}

	/**
	 *
	 * @param Int $total_like
	 */
	public function setTotalLike($total_like) {
		$this->total_like = $total_like;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalDislike() {
		return $this->total_dislike;
	}

	/**
	 *
	 * @param Int $total_dislike
	 */
	public function setTotalDislike($total_dislike) {
		$this->total_dislike = $total_dislike;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalShare() {
		return $this->total_share;
	}

	/**
	 *
	 * @param Int $total_share
	 */
	public function setTotalShare($total_share) {
		$this->total_share = $total_share;
		return $this;
	}

	/**
	 *
	 * @return the unknown_type
	 */
	public function getTotalView() {
		return $this->total_view;
	}

	/**
	 *
	 * @param unknown_type $total_view
	 */
	public function setTotalView($total_view) {
		$this->total_view = $total_view;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalSpam() {
		return $this->total_spam;
	}

	/**
	 *
	 * @param Int $total_spam
	 */
	public function setTotalSpam($total_spam) {
		$this->total_spam = $total_spam;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getIsAdminSpam() {
		return $this->is_admin_spam;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalFollow() {
		return $this->total_follow;
	}

	/**
	 *
	 * @return the Int
	 */
	public function getIsApproveEditQuestion() {
		return $this->is_approve_edit_question;
	}

	/**
	 *
	 * @param Int $is_approve_edit_question
	 */
	public function setIsApproveEditQuestion($is_approve_edit_question) {
		$this->is_approve_edit_question = $is_approve_edit_question;
		return $this;
	}

	/**
	 *
	 * @return the Date
	 */
	public function getDateupdatedApproveEditQuestion() {
		return $this->dateupdated_approve_edit_question;
	}

	/**
	 *
	 * @param Date $dateupdated_approve_edit_question
	 */
	public function setDateupdatedApproveEditQuestion($dateupdated_approve_edit_question) {
		$this->dateupdated_approve_edit_question = $dateupdated_approve_edit_question;
		return $this;
	}

	/**
	 *
	 * @return the Int
	 */
	public function getIsApproveEditAnswer() {
		return $this->is_approve_edit_answer;
	}

	/**
	 *
	 * @param Int $is_approve_edit_answer
	 */
	public function setIsApproveEditAnswer($is_approve_edit_answer) {
		$this->is_approve_edit_answer = $is_approve_edit_answer;
		return $this;
	}

	/**
	 *
	 * @return the Date
	 */
	public function getDateupdatedApproveEditAnswer() {
		return $this->dateupdated_approve_edit_answer;
	}

	/**
	 *
	 * @param Date $dateupdated_approve_edit_answer
	 */
	public function setDateupdatedApproveEditAnswer($dateupdated_approve_edit_answer) {
		$this->dateupdated_approve_edit_answer = $dateupdated_approve_edit_answer;
		return $this;
	}

	/**
	 *
	 * @param Int $total_follow
	 */
	public function setTotalFollow($total_follow) {
		$this->total_follow = $total_follow;
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
	 *
	 * @return Int
	 */
	public function getOldBonusPoint() {
		return $this->old_bonus_point;
	}

	/**
	 *
	 * @param Int $old_bonus_point
	 */
	public function setOldBonusPoint($old_bonus_point) {
		$this->old_bonus_point = $old_bonus_point;
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
		if ($this->isContainStatus ( FAQParaConfig::QUESTION_STATUS_DRAFT )) {
			$questionStatusLabel = $questionStatusLabel . "[Bản nháp]";
		}

		if ($this->isContainStatus ( FAQParaConfig::QUESTION_STATUS_CLOSE )) {
			$questionStatusLabel = $questionStatusLabel . "[Đã đóng]";
		}

		if ($this->isContainStatus ( FAQParaConfig::QUESTION_STATUS_PROTECT )) {
			$questionStatusLabel = $questionStatusLabel . "[Bảo vệ]";
		}
		if ($this->isContainStatus ( FAQParaConfig::QUESTION_STATUS_EXIST_BEST )) {
			$questionStatusLabel = $questionStatusLabel . "[Hoàn thành]";
		}
		if ($this->isContainStatus ( FAQParaConfig::QUESTION_STATUS_WIKI_POST )) {
			$questionStatusLabel = $questionStatusLabel . "[Wiki]";
		}

		return $questionStatusLabel;
	}
	/**
	 *
	 * @param Int $statusCheck
	 *        	FAQParaConfig::QUESTION_STATUS
	 */
	public function isContainStatus($statusCheck) {
		if ($this->status % $statusCheck == 0) {
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
	 *
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
	 * @return \MongoDate
	 */
	public function getDate_update_best() {
		return $this->date_update_best;
	}

	/**
	 *
	 * @param \MongoDate $date_update_best
	 */
	public function setDate_update_best($date_update_best) {
		$this->date_update_best = $date_update_best;
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
	 * @return the User
	 */
	public function getProtectBy() {
		return $this->protect_by;
	}

	/**
	 *
	 * @param User $protect_by
	 */
	public function setProtectBy($protect_by) {
		$this->protect_by = $protect_by;
		return $this;
	}
	/**
	 *
	 * @return the User
	 */
	public function getCloseBy() {
		return $this->close_by;
	}

	/**
	 *
	 * @param User $protect_by
	 */
	public function setCloseBy($close_by) {
		$this->close_by = $close_by;
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
	 * @return User
	 */
	public function getUpdateBy() {
		return $this->update_by;
	}

	/**
	 *
	 * @param User $update_by
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
	 * @return ArrayCollection
	 */
	public function getUserFollow() {
		return $this->user_follow;
	}

	/**
	 *
	 * @deprecated this function should call from User Entity
	 * @param User $user_follow
	 */
	public function setUserFollow($user_follow) {
		$user_follow->setFollowQuestion ( $this );
		$this->user_follow [] = $user_follow;
		return $this;
	}

	/**
	 *
	 * @todo member skip follow question
	 * @param User $user_follow
	 * @return \FAQ\FAQEntity\Question
	 */
	public function unSetUserFollow($user_follow) {
		$this->user_follow->removeElement ( $user_follow );
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getSpam() {
		return $this->spam;
	}

	/**
	 *
	 * @param User $spam
	 */
	public function setSpam($spam) {
		$spam->setSpamQuestion ( $this );
		$this->spam [] = $spam;
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
	 *
	 * @param User $spam
	 */
	public function unSetSpam($spam) {
		$this->spam->removeElement ( $spam );
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getShare() {
		return $this->share;
	}

	/**
	 *
	 * @param User $share
	 */
	public function setShare($share) {
		$share->setShareQuestion ( $this );
		$this->share [] = $share;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getAnswer() {
		return $this->answer;
	}

	/**
	 *
	 * @param Answer $answer
	 */
	public function setAnswer($answer) {
		$this->answer [] = $answer;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getChatHelp() {
		return $this->chat_help;
	}

	/**
	 *
	 * @param Message $chat_help
	 */
	public function setChatHelp($chat_help) {
		$this->chat_help [] = $chat_help;
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
	/**
	 * @odm\PrePersist
	 */
	public function autoUpdate() {
		if ($this->date_created) {
			$this->date_updated = Util::getCurrentTime ();
		}

		// Util::writeLog("this->first_image: ".$this->first_image);
		$contentTripTag = strip_tags ( $this->content, "<a><abbr><b><blockquote><br><code><dd><del><div><div><dt><em><h1><h2><h3><h4><h5><h6><i><ins><label><li><ol><p><pre><q><small><span><strong><table><tbody><td><th><tfoot><th><thead><title><tr><tr>" );
		$this->sys_content = $contentTripTag;
		$subContent = trim ( $contentTripTag );
		$subContent = mb_substr ( $subContent, 0, 3000, 'UTF-8' );
		$this->short_content = $subContent;

		$subjectQuestion = $this->subject;
		if (! empty ( $subjectQuestion )) {
			$titleSubject = $subjectQuestion->getTitle ();
			$avatarSubject = $subjectQuestion->getAvatar ();
			$subjectAvatarID = $avatarSubject->getId ();

			$contentTypeSubject = $avatarSubject->getContentType ();

			$extentionFileSubject = Util::getTypeFile ( $contentTypeSubject );
			$titleFileSeoSubject = Util::convertUrlFileName ( $titleSubject, $extentionFileSubject );

			$imgContent = Util::extractAllImage ( $this->content );
			if ($imgContent) {
				$imgContent = $imgContent [0];
			}
			if (count ( $imgContent ) > 0) {
				$this->first_image = $imgContent;
			} else {

				$imgSubject = '<img src="/media/get-image/images/' . $subjectAvatarID . '/' . $titleFileSeoSubject . '" alt="' . $titleSubject . '" title="' . $titleSubject . '">';
				$this->first_image = array (
						$imgSubject
				);
			}
		}
	}

	/**
	 * @odm\PrePersist
	 */
	public function autoSetStatus() {
		if (! $this->status) {
			$this->status = FAQParaConfig::QUESTION_STATUS_DRAFT;
		}
	}

	/**
	 *
	 * @todo increase total spam
	 * @return \FAQ\FAQEntity\Question
	 */
	public function incTotalSpam($userSpam, $isCastOpenAndReopen) {
		if (! $this->total_spam) {
			$this->total_spam = 1;
		} else {
			$this->total_spam ++;
		}
		if ($userSpam->getRoleCode () == Authcfg::ADMIN || $isCastOpenAndReopen) {
			$this->total_spam = $this->total_spam + 20;
			$this->is_admin_spam = FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_SPAM;
			$this->dateupdated_admin_spam = Util::getCurrentTime ();
		}
		return $this;
	}

	/**
	 *
	 * @todo descrease total spam
	 * @return \FAQ\FAQEntity\Question
	 */
	public function descTotalSpam($userSpam, $isCastOpenAndReopen) {
		if (! $this->total_spam) {
			$this->total_spam = 0;
		} else {
			$this->total_spam --;
		}

		if ($userSpam->getRoleCode () == Authcfg::ADMIN || $isCastOpenAndReopen) {
			$this->total_spam = - 21;
			$this->is_admin_spam = FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_NOTSPAM;
			$this->dateupdated_admin_spam = Util::getCurrentTime ();
		}

		return $this;
	}

	/**
	 *
	 * @todo increase total share
	 * @return \FAQ\FAQEntity\Question
	 */
	public function incTotalShare() {
		if (! $this->total_share)
			$this->total_share = 1;
		else
			$this->total_share ++;
		return $this;
	}

	/**
	 *
	 * @todo descrease total share
	 * @return \FAQ\FAQEntity\Question
	 */
	public function descTotalShare() {
		if (! $this->total_share)
			$this->total_share = 0;
		else
			$this->total_share --;
		return $this;
	}

	/**
	 *
	 * @todo increase total following
	 * @return \FAQ\FAQEntity\Question
	 */
	public function incTotalFollow() {
		if (! $this->total_follow)
			$this->total_follow = 1;
		else
			$this->total_follow ++;
		return $this;
	}

	/**
	 *
	 * @todo decrease total following
	 * @return \FAQ\FAQEntity\Question
	 */
	public function descTotalFollow() {
		if (! $this->total_follow)
			$this->total_follow = 0;
		else
			$this->total_follow --;
		return $this;
	}

	/**
	 *
	 * @todo increase total like
	 * @return \FAQ\FAQEntity\Question
	 */
	public function incLike() {
		if (! $this->total_like)
			$this->total_like = 1;
		else
			$this->total_like ++;
		return $this;
	}
	/**
	 *
	 * @todo increase total dislike
	 * @return \FAQ\FAQEntity\Question
	 */
	public function incDislike() {
		if (! $this->total_dislike)
			$this->total_dislike = 1;
		else
			$this->total_dislike ++;
		return $this;
	}

	/**
	 *
	 * @todo decrease total like
	 * @return \FAQ\FAQEntity\Question
	 */
	public function descLike() {
		if (! $this->total_like)
			$this->total_like = 0;
		else
			$this->total_like --;
		return $this;
	}
	/**
	 *
	 * @todo decrease total like
	 * @return \FAQ\FAQEntity\Question
	 */
	public function descDislike() {
		if (! $this->total_dislike)
			$this->total_dislike = 0;
		else
			$this->total_dislike --;
		return $this;
	}
}