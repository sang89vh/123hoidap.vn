<?php

namespace FAQ\FAQEntity;

use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use \Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQEntity\Message;
use FAQ\FAQCommon\Util;
use ErrorException;
use FAQ\FAQCommon\ChromePhp;
use FAQ\FAQEntity\Image;

/**
 * @ODM\Document
 *
 * @todo Luu thong tin ve nguoi dung va cac thong tin lien quan cua nguoi dung
 */
class User extends Entity {

	/**
	 * @ODM\String
	 */
	private $first_name;

	/**
	 * @ODM\String
	 */
	private $last_name;

	/**
	 * @ODM\String
	 * @ODM\Index(unique=true, order="asc")
	 */
	private $email;

	/**
	 * @ODM\Bin(type="bin_md5")
	 */
	private $pass;

	/**
	 * @ODM\ReferenceOne(targetDocument="Image",cascade={"detach","merge","refresh","persist"})
	 */
	private $avatar;

	/**
	 * @ODM\Date
	 */
	private $birthday;

	/**
	 * @ODM\String
	 */
	private $sex;

	/**
	 * @ODM\String
	 */
	private $nationnality;

	/**
	 * @ODM\Date
	 */
	private $date_created;

	/**
	 * @ODM\Date
	 */
	private $date_updated;

	/**
	 * @ODM\Int
	 */
	private $total_new_message;

	/**
	 * @ODM\Int
	 */
	private $total_new_notify;

	/**
	 * @ODM\Int
	 */
	private $total_question;

	/**
	 * @ODM\Int
	 */
	private $total_open_question;

	/**
	 * @ODM\Int
	 */
	private $total_closed_question;

	/**
	 * @ODM\Int
	 */
	private $total_spam_question;

	/**
	 * @ODM\Int
	 */
	private $total_answer;

	/**
	 * @ODM\Int
	 */
	private $total_answer_like;

	/**
	 * @ODM\Int
	 */
	private $total_answer_dislike;

	/**
	 * @ODM\Int
	 */
	private $total_answer_best;

	/**
	 * @ODM\Int
	 */
	private $total_user_follow;
	/**
	 * @ODM\Int
	 */
	private $total_vote_up;
	/**
	 * @ODM\Int
	 */
	private $total_vote_down;
	/**
	 * @ODM\Int
	 */
	private $total_vote_one_day;

	/**
	 * @ODM\Date
	 */
	private $vote_day;
	/**
	 * @ODM\Int
	 */
	private $total_flag;
	/**
	 * @ODM\Int
	 */
	private $total_flag_one_day;

	/**
	 * @ODM\Date
	 */
	private $flag_day;
	/**
	 * @ODM\Date
	 */
	private $last_date_login;
	/**
	 * @ODM\Int
	 */
	private $is_first_login;

	/**
	 * @ODM\String
	 */
	private $role_code;
	/**
	 * @ODM\Int
	 *
	 * @tutorial là các số nguyên tố
	 *           2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97
	 *
	 */
	private $special_role;

	/**
	 * @ODM\Int
	 */
	private $total_money_point;

	/**
	 * @ODM\Int
	 */
	private $total_rank_point;

	/**
	 * @ODM\Bin(type="bin_md5")
	 */
	private $security_code;

	/**
	 * @ODM\EmbedMany(targetDocument="OpenID")
	 */
	private $openid;

	/**
	 * @ODM\EmbedMany(targetDocument="UserFunctionPoint")
	 */
	private $function_point;

	/**
	 * @ODM\EmbedMany(targetDocument="UserRank")
	 */
	private $rank;

	/**
	 * @ODM\EmbedMany(targetDocument="Appellation")
	 */
	private $appellation;

	/**
	 * @ODM\EmbedMany(targetDocument="Notify")
	 *
	 * @todo Luu thong bao tu he thong gui den toi
	 */
	private $notify;

	/**
	 * @ODM\ReferenceMany(targetDocument="Key", mappedBy="user",cascade={"detach","merge","refresh","persist"})
	 */
	private $key;
	// ---------------------------------------follow--------------
	/**
	 * @ODM\ReferenceMany(targetDocument="User", inversedBy="my_follow",cascade={"detach","merge","refresh","persist"})
	 */
	private $follow_me;

	/**
	 * @ODM\ReferenceMany(targetDocument="User", inversedBy="follow_me",cascade={"detach","merge","refresh","persist"})
	 */
	private $my_follow;

	// ----------------------------------------end follow---------
	// ---------------------------------------block--------------
	/**
	 * @ODM\ReferenceMany(targetDocument="User", mappedBy="my_block",cascade={"detach","merge","refresh","persist"})
	 */
	private $block_me;

	/**
	 * @ODM\ReferenceMany(targetDocument="User", inversedBy="block_me",cascade={"detach","merge","refresh","persist"})
	 */
	private $my_block;

	// ----------------------------------------end block---------

	/**
	 * @ODM\ReferenceMany(targetDocument="Subject",cascade={"detach","merge","refresh","persist"})
	 */
	private $follow_subject;

	/**
	 * @ODM\ReferenceMany(targetDocument="Question",cascade={"detach","merge","refresh","persist"})
	 */
	private $follow_question;

	/**
	 * @ODM\ReferenceMany(targetDocument="Skill",cascade={"detach","merge","refresh","persist"})
	 */
	private $skill;

	/**
	 * @ODM\ReferenceMany(targetDocument="Message",mappedBy="from_user",cascade={"detach","merge","refresh","persist"})
	 *
	 * @todo Luu danh sach message toi gui di
	 */
	private $message;

	/**
	 * @ODM\ReferenceMany(targetDocument="Message",mappedBy="to_user",cascade={"detach","merge","refresh","persist"})
	 *
	 * @todo Luu danh sach message den toi
	 */
	private $to_message;

	/**
	 * @ODM\ReferenceMany(targetDocument="Location",cascade={"detach","merge","refresh","persist"})
	 */
	private $location;

	/**
	 * @ODM\ReferenceMany(targetDocument="Answer", mappedBy="create_by",cascade={"detach","merge","refresh","persist"})
	 */
	private $answer;

	/**
	 * @ODM\ReferenceMany(targetDocument="Question", mappedBy="create_by",cascade={"detach","merge","refresh","persist"})
	 */
	private $question;

	/**
	 * @ODM\ReferenceMany(targetDocument="Question",cascade={"detach","merge","refresh","persist"})
	 *
	 * @todo store question of answer, this be liked
	 */
	private $like_answer;

	/**
	 * @ODM\ReferenceMany(targetDocument="Question",cascade={"detach","merge","refresh","persist"})
	 *
	 * @todo store question of answer, this be disliked
	 */
	private $dislike_answer;

	/**
	 * @ODM\ReferenceMany(targetDocument="Question",cascade={"detach","merge","refresh","persist"})
	 */
	private $spam_question;

	/**
	 * @ODM\ReferenceMany(targetDocument="Question",cascade={"detach","merge","refresh","persist"})
	 */
	private $share_question;

	/**
	 * @ODM\Int
	 */
	private $status;
	/**
	 * @ODM\Int
	 */
	private $is_verified;

	/**
	 * @ODM\Collection
	 * @ODM\Index
	 *
	 * @todo set first name and last name is key word for seaching user
	 */
	private $key_word = array ();

	/**
	 *
	 * @return Int
	 */
	public function getSpecialRole() {
		return $this->special_role;
	}

	/**
	 *
	 * @param Int $special_role
	 */
	public function setSpecialRole($special_role) {
		$this->special_role = $special_role;
		return $this;
	}

	/**
	 *
	 * @todo funtion contruct for it
	 */
	public function __construct() {
		parent::__construct ();
		$this->openid = new ArrayCollection ();
		$this->key = new ArrayCollection ();
		$this->function_point = new ArrayCollection ();
		$this->rank = new ArrayCollection ();
		$this->appellation = new ArrayCollection ();
		$this->follow_me = new ArrayCollection ();
		$this->my_follow = new ArrayCollection ();
		$this->block_me = new ArrayCollection ();
		$this->block_follow = new ArrayCollection ();
		$this->follow_subject = new ArrayCollection ();

		$this->follow_question = new ArrayCollection ();
		$this->skill = new ArrayCollection ();
		$this->to_message = new ArrayCollection ();
		$this->message = new ArrayCollection ();
		$this->notify = new ArrayCollection ();
		$this->location = new ArrayCollection ();
		$this->answer = new ArrayCollection ();

		$this->question = new ArrayCollection ();
		$this->like_answer = new ArrayCollection ();
		$this->dislike_answer = new ArrayCollection ();

		$this->spam_question = new ArrayCollection ();
		$this->share_question = new ArrayCollection ();
		$this->total_vote_down = 0;
		$this->total_vote_up = 0;
		$this->total_flag = 0;
		$this->is_first_login = 0;
	}

	/**
	 *
	 * @return Sring
	 */
	public function getFirstName() {
		return $this->first_name;
	}

	/**
	 *
	 * @param String $first_name
	 */
	public function setFirstName($first_name) {
		$this->first_name = $first_name;
		return $this;
	}

	/**
	 *
	 * @return String
	 */
	public function getLastName() {
		return $this->last_name;
	}

	/**
	 *
	 * @param String $last_name
	 */
	public function setLastName($last_name) {
		$this->last_name = $last_name;
		return $this;
	}

	/**
	 *
	 * @return String
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 *
	 * @param String $email
	 */
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}

	/**
	 *
	 * @return String
	 */
	public function getPass() {
		return $this->pass;
	}

	/**
	 *
	 * @param String $pass
	 */
	public function setPass($pass) {
		$this->pass = $pass;

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
	// /**
	// *
	// * @param Image $avatar
	// */
	// public function setAvatarLinkSource($avatar_link)
	// {
	// $avatar=new Image();
	// $avatar->setFile($avatar_link);
	// //default from facebook avatar
	// $avatar->setContentType("image/jpeg");
	// $this->avatar=$avatar;
	// return $this;
	// }

	/**
	 *
	 * @return Date
	 */
	public function getBirthday() {
		return $this->birthday;
	}

	/**
	 *
	 * @param Date $birthday
	 */
	public function setBirthday($birthday) {
		$this->birthday = $birthday;
		return $this;
	}

	/**
	 *
	 * @return String
	 */
	public function getSex() {
		return $this->sex;
	}

	/**
	 *
	 * @param String $sex
	 * @tutorial Nam=FAQParaConfig::MALE
	 *           Nữ=FAQParaConfig::FEMALE
	 *           Giới tính khác=FAQParaConfig::MALEANDFEMALE
	 */
	public function setSex($sex) {
		$this->sex = $sex;
		return $this;
	}

	/**
	 *
	 * @return the Int
	 */
	public function getIsFirstLogin() {
		return $this->is_first_login;
	}

	/**
	 *
	 * @param Int $is_first_login
	 */
	public function setIsFirstLogin($is_first_login) {
		$this->is_first_login = $is_first_login;
		return $this;
	}

	/**
	 *
	 * @return String
	 */
	public function getNationnality() {
		return $this->nationnality;
	}

	/**
	 *
	 * @param String $nationnality
	 * @tutorial dua theo chuan ISO 639-1 Code<br/>
	 *           vi=>Việt Nam
	 * @link http://www.loc.gov/standards/iso639-2/php/code_list.php
	 */
	public function setNationnality($nationnality) {
		$this->nationnality = Util::getNationnality ( $nationnality );
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
	public function getTotalNewMessage() {
		return $this->total_new_message;
	}

	/**
	 *
	 * @param Int $total_new_message
	 */
	public function setTotalNewMessage($total_new_message) {
		$this->total_new_message = $total_new_message;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalNewNotify() {
		return $this->total_new_notify;
	}

	/**
	 *
	 * @param Int $total_new_notify
	 */
	public function setTotalNewNotify($total_new_notify) {
		$this->total_new_notify = $total_new_notify;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalQuestion() {
		return $this->total_question;
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
	public function getTotalOpenQuestion() {
		return $this->total_open_question;
	}

	/**
	 *
	 * @param Int $total_open_question
	 */
	public function setTotalOpenQuestion($total_open_question) {
		$this->total_open_question = $total_open_question;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalClosedQuestion() {
		return $this->total_closed_question;
	}

	/**
	 *
	 * @param Int $total_closed_question
	 */
	public function setTotalClosedQuestion($total_closed_question) {
		$this->total_closed_question = $total_closed_question;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalSpamQuestion() {
		return $this->total_spam_question;
	}

	/**
	 *
	 * @param Int $total_spam_question
	 */
	public function setTotalSpamQuestion($total_spam_question) {
		$this->total_spam_question = $total_spam_question;
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
	public function getTotalAnswerLike() {
		return $this->total_answer_like;
	}

	/**
	 *
	 * @param Int $total_answer_like
	 */
	public function setTotalAnswerLike($total_answer_like) {
		$this->total_answer_like = $total_answer_like;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalAnswerDislike() {
		return $this->total_answer_dislike;
	}

	/**
	 *
	 * @param Int $total_answer_dislike
	 */
	public function setTotalAnswerDislike($total_answer_dislike) {
		$this->total_answer_dislike = $total_answer_dislike;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalAnswerBest() {
		return $this->total_answer_best;
	}

	/**
	 *
	 * @param Int $total_answer_best
	 */
	public function setTotalAnswerBest($total_answer_best) {
		$this->total_answer_best = $total_answer_best;
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
	 * @return Date
	 */
	public function getLastDateLogin() {
		return $this->last_date_login;
	}

	/**
	 *
	 * @param Date $last_date_login
	 */
	public function setLastDateLogin($last_date_login) {
		$this->last_date_login = $last_date_login;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalVoteUp() {
		return $this->total_vote_up;
	}

	/**
	 *
	 * @param Int $total_vote_up
	 */
	public function setTotalVoteUp($total_vote_up) {
		$this->total_vote_up = $total_vote_up;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalVoteDown() {
		return $this->total_vote_down;
	}

	/**
	 *
	 * @param Int $total_vote_down
	 */
	public function setTotalVoteDown($total_vote_down) {
		$this->total_vote_down = $total_vote_down;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalVoteOneDay() {
		return $this->total_vote_one_day;
	}

	/**
	 *
	 * @param Int $total_vote_one_day
	 */
	public function setTotalVoteOneDay($total_vote_one_day) {
		$this->total_vote_one_day = $total_vote_one_day;
		return $this;
	}

	/**
	 *
	 * @return Date
	 */
	public function getVoteDay() {
		return $this->vote_day;
	}

	/**
	 *
	 * @param Date $vote_day
	 */
	public function setVoteDay($vote_day) {
		$this->vote_day = $vote_day;
		return $this;
	}

	/**
	 *
	 * @return the Int
	 */
	public function getTotalFlag() {
		return $this->total_flag;
	}

	/**
	 *
	 * @param Int $total_flag
	 */
	public function setTotalFlag($total_flag) {
		$this->total_flag = $total_flag;
		return $this;
	}

	/**
	 *
	 * @return the Int
	 */
	public function getTotalFlagOneDay() {
		return $this->total_flag_one_day;
	}

	/**
	 *
	 * @param Int $total_flag_one_day
	 */
	public function setTotalFlagOneDay($total_flag_one_day) {
		$this->total_flag_one_day = $total_flag_one_day;
		return $this;
	}

	/**
	 *
	 * @return the Date
	 */
	public function getFlagDay() {
		return $this->flag_day;
	}

	/**
	 *
	 * @param Date $flag_day
	 */
	public function setFlagDay($flag_day) {
		$this->flag_day = $flag_day;
		return $this;
	}

	/**
	 *
	 * @return String
	 */
	public function getRoleCode() {
		return $this->role_code;
	}

	/**
	 *
	 * @param Int $role_code
	 */
	public function setRoleCode($role_code) {
		$this->role_code = $role_code;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalMoneyPoint() {
		return $this->total_money_point;
	}

	/**
	 *
	 * @author ?? (sang) khong hieu doan nay lam
	 * @param Int $total_money_point
	 * @todo money point khong lien quan den xep hang
	 */
	public function setTotalMoneyPoint($total_money_point) {
		// if ($this->total_money_point != $total_money_point) {
		// $isExist = false;
		// if (! Util::$user_function_point) {
		// Util::$user_function_point = new UserFunctionPoint ();
		// }
		// $functionPoint = $this->getFunctionPoint ();
		// if ($functionPoint)
		// foreach ( $functionPoint as $p ) {
		// if ($p->getId () == Util::$user_function_point->getId ()) {
		// $isExist = true;
		// Util::$user_function_point = $p;
		// }
		// }
		// Util::$user_function_point->setMoneyPointBonus ( $total_money_point );
		// if (! $isExist) {
		// $this->setFunctionPoint ( Util::$user_function_point );
		// }
		// }
		$this->total_money_point = $total_money_point;
		return $this;
	}

	/**
	 *
	 * @return Int
	 */
	public function getTotalRankPoint() {
		return $this->total_rank_point;
	}

	/**
	 *
	 * @author sang
	 * @param Int $total_rank_point
	 */
	public function setTotalRankPoint($total_rank_point, $desc = null, $question = null) {
		if ($this->total_rank_point != $total_rank_point) {

			$user_function_point = new UserFunctionPoint ();
			$user_function_point->setRankPointBonus ( $total_rank_point - $this->total_rank_point );
			$user_function_point->setDesc ( $desc );
			$user_function_point->setQuestion ( $question );
			$this->setFunctionPoint ( $user_function_point );
		}
		$this->total_rank_point = $total_rank_point;
		return $this;
	}
	// /**
	// *
	// * @author izzi
	// * @param Int $total_rank_point
	// */
	// public function setTotalRankPoint($total_rank_point, $desc = null, $question = null) {
	// if ($this->total_rank_point != $total_rank_point) {
	// $isExist = false;
	// if (! Util::$user_function_point) {
	// Util::$user_function_point = new UserFunctionPoint ();
	// }
	// $functionPoint = $this->getFunctionPoint ();
	// if ($functionPoint)
	// foreach ( $functionPoint as $p ) {
	// if ($p->getId () == Util::$user_function_point->getId ()) {
	// $isExist = true;
	// Util::$user_function_point = $p;
	// }
	// }
	// Util::$user_function_point->setRankPointBonus ( $total_rank_point-$this->total_rank_point );
	// Util::$user_function_point->setDesc ( $desc );
	// Util::$user_function_point->setQuestion ( $question );

	// if (! $isExist) {
	// $this->setFunctionPoint ( Util::$user_function_point );
	// }
	// }
	// $this->total_rank_point = $total_rank_point;
	// return $this;
	// }

	/**
	 *
	 * @return String
	 */
	public function getSecurityCode() {
		return $this->security_code;
	}

	/**
	 *
	 * @param String $security_code
	 */
	public function setSecurityCode($security_code) {
		$this->security_code = $security_code;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getOpenid() {
		return $this->openid;
	}

	/**
	 *
	 * @param OpenID $openid
	 */
	public function setOpenid($openid) {
		$this->openid [] = $openid;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 *
	 * @param Key $key
	 */
	public function setKey($key) {
		$this->key [] = $key;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getFunctionPoint() {
		return $this->function_point;
	}

	/**
	 *
	 * @param UserFunctionPoint $function_point
	 */
	public function setFunctionPoint($function_point) {
		$this->function_point [] = $function_point;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getRank() {
		return $this->rank;
	}

	/**
	 *
	 * @param UserRank $rank
	 */
	public function setRank($rank) {
		$this->rank [] = $rank;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getAppellation() {
		return $this->appellation;
	}

	/**
	 *
	 * @param Appellation $appellation
	 */
	public function setAppellation($appellation) {
		$this->appellation [] = $appellation;
		return $this;
	}

	/**
	 *
	 * @todo add new user into my block
	 * @param User $user
	 */
	public function setBlock(User $user) {
		$user->block_me [] = $this;
		$this->my_block [] = $user;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getFollowMe() {
		return $this->follow_me;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getMyFollow() {
		return $this->my_follow;
	}

	/**
	 *
	 * @todo add new user into my follow
	 * @param User $user
	 */
	public function setFollow(User $user) {
		if (! $this->getFollowMe ()->contains ( $this )) {
			$user->follow_me [] = $this;
			$this->my_follow [] = $user;
			$user->setTotalUserFollow ( 1 + $user->getTotalUserFollow () );
		}
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getBlockMe() {
		return $this->block_me;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getMyBlock() {
		return $this->my_block;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getFollowSubject() {
		return $this->follow_subject;
	}

	/**
	 *
	 * @param Subject $follow_subject
	 */
	public function setFollowSubject($follow_subject) {
		if (! $this->getFollowSubject ()->contains ( $follow_subject )) {
			$follow_subject->setUserFollow ( $this );
			$this->follow_subject [] = $follow_subject;
		}
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getFollowQuestion() {
		return $this->follow_question;
	}

	/**
	 *
	 * @param Question $follow_question
	 */
	public function setFollowQuestion($follow_question) {
		$this->follow_question [] = $follow_question;
		return $this;
	}

	/**
	 *
	 * @todo member skip follow question
	 * @param Question $follow_question
	 */
	public function unSetFollowQuestion($follow_question) {
		$follow_question->unSetUserFollow ( $this );
		$this->follow_question->removeElement ( $follow_question );
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
	 * @return ArrayCollection
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 *
	 * @param Message $message
	 */
	public function setMessage($message) {
		$message->setFromUser ( $this );
		$this->message [] = $message;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getToMessage() {
		return $this->to_message;
	}

	/**
	 *
	 * @param Message $to_message
	 */
	public function setToMessage($to_message) {
		$this->to_message [] = $to_message;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getNotify() {
		return $this->notify;
	}

	/**
	 *
	 * @param Notify $notify
	 */
	public function setNotify($notify) {
		$this->notify [] = $notify;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getLocation() {
		return $this->location;
	}

	/**
	 *
	 * @param Location $location
	 */
	public function setLocation($location) {
		$this->location [] = $location;
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
	public function getQuestion() {
		return $this->question;
	}

	/**
	 *
	 * @param Question $question
	 */
	public function setQuestion($question) {
		$this->question [] = $question;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getLikeAnswer() {
		return $this->like_answer;
	}

	/**
	 *
	 * @param Answer $like_answer
	 */
	public function setLikeAnswer($like_answer) {
		$like_answer->setLike ( $this );
		$this->like_answer [] = $like_answer;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getDisikeAnswer() {
		return $this->dislike_answer;
	}

	/**
	 *
	 * @param Answer $dislike_answer
	 */
	public function setDislikeAnswer($dislike_answer) {
		$dislike_answer->setDislike ( $this );
		$this->dislike_answer [] = $dislike_answer;
		return $this;
	}

	/**
	 *
	 * @return ArrayCollection
	 */
	public function getSpamQuestion() {
		return $this->spam_question;
	}

	/**
	 *
	 * @param Question $spam_question
	 */
	public function setSpamQuestion($spam_question) {
		$this->spam_question [] = $spam_question;
		return $this;
	}

	/**
	 *
	 * @param Question $spam_question
	 */
	public function unSetSpamQuestion($spam_question) {
		$spam_question->unSetSpam ( $this );
		$this->spam_question->removeElement ( $spam_question );
		return $this;
	}

	/**
	 *
	 * @return \Doctrine\Common\Collections\ArrayCollection
	 */
	public function getShareQuestion() {
		return $this->share_question;
	}

	/**
	 *
	 * @param User $share_question
	 */
	public function setShareQuestion($share_question) {
		$this->share_question [] = $share_question;
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
	 * @tutorial Domain la USER <br/>
	 *           1 Chua kich hoat <br/>
	 *           2 Dang hoat dong <br/>
	 *           3 Dung hoat dong <br/>
	 */
	public function setStatus($status) {
		$this->status = $status;
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
	 * @todo used when searching user
	 * @param String $key_word
	 */
	public function setKeyWord($key_word) {
		// Util::writeLog($key_word);
		$this->key_word [] = $key_word;
		return $this;
	}

	/**
	 *
	 * @return the $is_verified
	 */
	public function getIsVerified() {
		return $this->is_verified;
	}

	/**
	 *
	 * @param field_type $is_verified
	 */
	public function setIsVerified($isVerified) {
		$this->is_verified = $isVerified;
	}

	/**
	 * @odm\PrePersist
	 */
	public function updateKeyword() {

		// reset key word
		$this->key_word = array ();
		// update email, name
		$this->setKeyWord ( Util::covertUnicode ( $this->first_name . " " . $this->last_name ) );
		$this->setKeyWord ( Util::covertUnicode ( $this->email ) );

		// update skill
		foreach ( $this->skill as $key => $skill ) {
			$this->key_word = array_unique ( array_merge ( $this->getKeyWord (), $skill->getKeyWord () ) );
		}
		// update location

		foreach ( $this->location as $key => $location ) {

			$this->key_word = array_unique ( array_merge ( $this->getKeyWord (), $location->getKeyWord () ) );
		}
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

	/**
	 * @odm\PrePersist
	 */
	public function autoSetSecurityCode() {
		if (! $this->security_code) {
			$this->security_code = $this->email . Util::getCurrentTime ();
		}
	}

	/**
	 * @odm\PrePersist
	 */
	public function autoSetDefaultAttibue() {
		if (! $this->total_money_point) {
			$this->total_money_point = 0;
		}
		if (! $this->total_rank_point) {
			$this->total_rank_point = 0;
		}

		if (! $this->total_closed_question) {
			$this->total_closed_question = 0;
		}
	}
}