<?php

namespace FAQ\Mapper;

use FAQ\DB\Db;
use FAQ\FAQEntity\User;
use FAQ\FAQEntity\Key;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQEntity\Image;
use FAQ\FAQEntity\Skill;
use FAQ\FAQEntity\Location;
use FAQ\FAQCommon\Usercfg;
use FAQ\FAQEntity\UserRank;
use Exception;
use FAQ\FAQEntity\Appellation;

class UserMapper extends Db {
	protected $user;

	/**
	 *
	 * @todo get a user
	 * @return the User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 *
	 * @param \FAQ\FAQEntity\User $user
	 */
	public function setUser($user) {
		$this->user = $user;
	}

	/*
	 * (non-PHPdoc) @see \FAQ\DB\Db::__construct()
	 */
	public function __construct() {
		parent::__construct ();
		$this->user = new User ();
	}

	/**
	 *
	 * @return \FAQ\FAQEntity\User
	 */
	public function create() {
		$user = new User ();
		$user = $this->initNewUser ( $user );
		// $user->updateKeyword();
		$user->insert ();
		return $user;
	}

	/**
	 *
	 * @param User $user
	 * @return unknown
	 */
	public function update($user) {
		$user->setStatusUpdateRefere ();
		$this->commit ();
	}
	public function createKey() {
		$key = new Key ();
		$key->insert ();
		return $key;
	}

	/**
	 *
	 * @author sang
	 * @param String $userID
	 * @param String $currentUserID
	 * @return number
	 */
	public function followMember($userID, $currentUserID) {
		if ($userID == $currentUserID) {
			return 0;
		}
		try {

			/* @var $currentUser \FAQ\FAQEntity\User */
			$currentUser = $this->user->find ( $currentUserID, true );
			/* @var $user \FAQ\FAQEntity\User */
			$user = $this->user->find ( $userID, true );
			$user->setStatusUpdateRefere ();
			$currentUser->setStatusUpdateRefere ();
			// $user->setTotalUserFollow===========>thieu trong thiet ke
			$currentUser->setFollow ( $user );
			$this->commit ();
			return 1;
		} catch ( Exception $e ) {
			Util::writeLog ( $e->getTraceAsString (), \Zend\Log\Logger::EMERG );
			return 0;
		}
	}

	/**
	 *
	 * @author sang
	 * @param String $userID
	 * @param String $currentUserID
	 * @return number
	 */
	public function unFollowMember($userID, $currentUserID) {
		try {
			/* @var $currentUser \FAQ\FAQEntity\User */
			$currentUser = $this->user->find ( $currentUserID, true );
			/* @var $user \FAQ\FAQEntity\User */
			$user = $this->user->find ( $userID, true );

			$user->setStatusUpdateRefere ();
			$user->getFollowMe ()->removeElement ( $currentUser );
			$user->setTotalUserFollow ( $user->getTotalUserFollow () - 1 );

			$currentUser->setStatusUpdateRefere ();
			$currentUser->getMyFollow ()->removeElement ( $user );
			$this->commit ();

			// 2 is unfollow
			return 2;
		} catch ( Exception $e ) {
			Util::writeLog ( $e->getTraceAsString (), \Zend\Log\Logger::EMERG );
			return 0;
		}
	}

	/**
	 *
	 * @author Sang
	 * @todo get arraycollection User
	 * @param String $name
	 * @param array $orderBy
	 * @param Int $from
	 * @param Int $to
	 * @param Int $to
	 * @param Int $status
	 * @return Ambigous <\Doctrine\MongoDB\Cursor, Cursor, \Doctrine\MongoDB\EagerCursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
	 * @tutorial $from with base index is Zero "0"
	 */
	public function findUser($select, $keyWord, $orderBy, $from, $to, $status, $isGetTotal = false, $isVerified = false) {
		$qb = $this->user->getQueryBuilder ();
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}

		// set where for query

		if (! empty ( $keyWord )) {
			$keySearchs = array ();
			$keywords = explode ( ' ', $keyWord );
			foreach ( $keywords as $key => $value ) {
				$regexObj = new \MongoRegex ( "/^" . $value . "/i" );
				$keySearchs [$key] = $regexObj;
			}

			$qb = $qb->field ( 'key_word' )->in ( $keySearchs );
		}
		if (isset ( $status ) && $status != Usercfg::USER_STATUS_CURRENT_ACTIVE && $status != Usercfg::USER_STATUS_CURRENT_DEACTIVE) {
			$qb->field ( "status" )->equals ( $status );
		} elseif ($status == Usercfg::USER_STATUS_CURRENT_ACTIVE) {
			$qb->field ( "status" )->lte ( 10 );
		} elseif ($status == Usercfg::USER_STATUS_CURRENT_DEACTIVE) {
			$qb->field ( "status" )->gt ( 10 );
		}
		if ($isVerified == true) {
			$qb->field ( "is_verified" )->equals ( FAQParaConfig::STATUS_ACTIVE );
		}
		if ($isGetTotal) {
			$totalRow = $qb->getQuery ()->count ();
		}

		if (isset ( $orderBy )) {
			$qb = Util::addOrder ( $qb, $orderBy );
		}
		if (isset ( $to ) && isset ( $from )) {
			$qb = $qb->skip ( $from )->limit ( $to - $from );
			// ChromePhp::log("----->>>".$from);
			// ChromePhp::log($to - $from);
		}

		$q = $qb->getQuery ();
		$users = $q->execute ();
		if ($isGetTotal) {
			$data = array (
					"totalRow" => $totalRow,
					"users" => $users
			);
			return $data;
		} else {

			return $users;
		}
	}

	/**
	 *
	 * @author Sang
	 * @param String $userID
	 * @return \FAQ\FAQEntity\User
	 */
	public function getOneUser($userID, $select = null, $isHydrate = null) {
		// return $this->user->find($userID, true);
		$qb = $this->user->getQueryBuilder ();
		if (isset ( $isHydrate )) {
			$qb->hydrate ( $isHydrate );
		}
		if (isset ( $select )) {
			$qb = Util::selectField ( $qb, $select );
		}
		$qb->field ( "id" )->equals ( $userID );

		$q = $qb->getQuery ();
		$user = $q->getSingleResult ();
		return $user;
	}

	/**
	 *
	 * @param String $email
	 * @return Ambigous <User, NULL, unknown>
	 */
	public function getUserByEmail($email) {
		return $this->user->findOneBy ( array (
				'email' => $email
		) );
	}

	/**
	 *
	 * @todo create user rank
	 */
	public function createRankDefault() {
		$rank = new UserRank ();
		$rank->setName ( Usercfg::user_rank_new_text );
		return $rank;
	}

	/**
	 *
	 * @author izzi
	 * @todo initialize point for new user
	 * @param User $user
	 * @return User
	 */
	public function initNewUser($user) {
		$user->setTotalClosedQuestion ( 0 );

		$user->setTotalNewMessage ( 0 );

		$user->setTotalOpenQuestion ( 0 );
		$user->setTotalQuestion ( 0 );
		$user->setTotalRankPoint ( Usercfg::rank_user_registrator );
		$user->setTotalMoneyPoint ( Usercfg::money_user_registrator );

		$user->setTotalSpamQuestion ( 0 );

		$user->setTotalUserFollow ( 0 );
		$user->setTotalAnswer ( 0 );
		$user->setTotalAnswerLike ( 0 );
		$user->setTotalAnswerDislike ( 0 );
		$user->setTotalAnswerBest ( 0 );
		return $user;
	}

	/**
	 *
	 * @param User $user
	 * @return User
	 */
	public function initAvatar($user) {
		$sex = $user->getSex ();
		$link_avatar = DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "boygirl-avatar.png";
		if ($sex == FAQParaConfig::MALE) {
			$link_avatar = DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "boy-avatar.png";
		}
		if ($sex == FAQParaConfig::FEMALE) {
			$link_avatar = DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "girl-avatar.png";
		}
		$avatar_img = new Image ();
		$avatar_img->setContentType ( "image/png" );
		$avatar_img->setFile ( getcwd () . $link_avatar );
		$avatar_img->insert ();
		$user->setAvatar ( $avatar_img );
		return $user;
	}

	/**
	 *
	 * @author izzi
	 * @todo update Skill of User
	 * @param User $user
	 * @param array $skill_arr
	 */
	public function updateSkillForUser($user, $skill_text_arr) {
		// delete skill { create by user, not use more }
		$user_skills = $user->getSkill ();
		$user_skills_text_remain = array ();
		foreach ( $user_skills as $k => $v ) {
			/* @var $v Skill */
			if (! in_array ( strtolower ( $v->getName () ), array_map ( 'strtolower', $skill_text_arr ) )) {
				$user_skills->removeElement ( $v );
			} else {
				$user_skills_remain [] = $v->getName ();
			}
		}
		// add skill from skill collection if exist
		for($i = 0; $i < count ( $skill_text_arr ); $i ++) {
			if (! in_array ( strtolower ( $skill_text_arr [$i] ), array_map ( 'strtolower', $user_skills_remain ) )) {
				$skill = new Skill ();
				$qb = $qb = $skill->getQueryBuilder ();
				$qb = $qb->field ( "name" )->equals ( $skill_text_arr [$i] );
				$skills = $qb->getQuery ()->execute ();
				if (count ( $skills ) > 0) {
					// use skill existing
					$user_skills->add ( $skills->getNext () );
				} else {
					// create new skill
					$skill->setName ( $skill_text_arr [$i] );
					$skill->setCreateBy ( $user );
					$skill->insert ();
					$user_skills->add ( $skill );
				}
			}
		}
	}

	/**
	 *
	 * @author izzi
	 * @todo update Location of User
	 * @param User $user
	 * @param array $skill_arr
	 */
	public function updateLocationForUser($user, $location_text_arr, $type = null) {
		// $location_text_arr = array_unique($location_text_arr);
		if (! $type) {
			$type = 0;
		}
		// delete location { create by user, not use more }
		$user_locations = $user->getlocation ();
		foreach ( $user_locations as $user_loc ) {
			if ($user_loc->getType () == $type) {
				$user_loc->needProcess = 1;
			} else {
				$user_loc->needProcess = 0;
			}
		}
		$user_locations_remain = array ();
		$at = 0;
		foreach ( $user_locations as $k => $v ) {
			if ($v->needProcess) {
				/* @var $v location */
				if (! in_array ( strtolower ( $v->getName () ), array_map ( 'strtolower', $location_text_arr ) )) {
					$user_locations->removeElement ( $v );
				} else {
					$user_locations_remain [] = $v->getName ();
				}
			}
		}
		// add location from location collection if exist
		for($i = 0; $i < count ( $location_text_arr ); $i ++) {
			if (! in_array ( strtolower ( $location_text_arr [$i] ), array_map ( 'strtolower', $user_locations_remain ) )) {
				$location = new Location ();
				$qb = $location->getQueryBuilder ();
				$regexObj = new \MongoRegex ( "/^" . $location_text_arr [$i] . "/i" );
				$qb = $qb->field ( "name" )->equals ( $regexObj );
				$qb = $qb->field ( 'type' )->equals ( $type );
				$locations = $qb->getQuery ()->execute ();
				if (count ( $locations ) > 0) {
					// use location existing
					$user_locations->add ( $locations->getNext () );
				} else {
					// create new location
					$location->setName ( $location_text_arr [$i] );
					$location->setType ( $type );
					$location->setCreateBy ( $user );
					$location->insert ();
					$user_locations->add ( $location );
				}
			}
		}
	}

	/**
	 *
	 * @author izzi
	 * @todo : update avatar by resource
	 * @param String $imageResource
	 * @param string $image_type
	 */
	public function updateAvatarByResource($imageResource, $image_type = 'png') {
		$user = Util::getCurrentUser ();
		$tmpfname = tempnam ( "/tmp", $user->getId () );
		// sang add conttent type create url seo
		$contentType = "";

		if ($image_type == 'jpg') {
			$contentType = "image/jpeg";
		} elseif ($image_type = 'jpeg') {
			$contentType = "image/pjpeg";
		}
		// $handle = fopen($tmpfname, "w");
		if ($image_type == 'jpg' || $image_type = 'jpeg') {
			imagejpeg ( $imageResource, $tmpfname, 100 );
		}
		if ($image_type == 'png') {
			imagepng ( $imageResource, $tmpfname, 100 );
			$contentType = "image/png";
		}
		if ($image_type == 'gif') {
			imagegif ( $imageResource, $tmpfname, 100 );
			$contentType = "image/gif";
		}
		$avatar = $user->getAvatar ();
		$avatar->setContentType ( $contentType );
		$avatar->setFile ( $tmpfname );
		$this->commit ();
	}

	/**
	 *
	 * @todo plus rank point when vote best answer
	 * @param User $user
	 * @param User $owner
	 */
	public function updatePointByQuestionBeVoteBestAnswer($user, $owner = null, $resource = null, $isPlusOrMinus = "PLUS") {
		if ($isPlusOrMinus == "PLUS") {
			$owner->setTotalRankPoint ( $owner->getTotalRankPoint () + Usercfg::rank_question_vote_best_answer, Usercfg::ANSWER_VOTE_BEST, $resource );
			$user->setTotalRankPoint ( $user->getTotalRankPoint () + Usercfg::rank_question_vote_best_answer_acceptor, Usercfg::ANSWER_VOTE_BEST, $resource );
		} elseif ($isPlusOrMinus == "MINUS") {
			$owner->setTotalRankPoint ( $owner->getTotalRankPoint () - Usercfg::rank_question_vote_best_answer, Usercfg::ANSWER_UNVOTE_BEST, $resource );
			$user->setTotalRankPoint ( $user->getTotalRankPoint () - Usercfg::rank_question_vote_best_answer_acceptor, Usercfg::ANSWER_UNVOTE_BEST, $resource );
		}
		$this->updateRank ( $user );
		$this->updateRank ( $owner );
		$this->updateAppellation ( $user, $owner, $resource ,Usercfg::rank_question_vote_best_answer_acceptor,Usercfg::rank_question_vote_best_answer);
	}

	/**
	 *
	 * @todo add point when question answer be like
	 * @param User $user
	 * @param User $owner
	 */
	public function updatePointByQuestionAnswerBeLike($user, $owner = null, $resource = null, $plusOrMinus = "PLUS") {
		// $owner->setTotalMoneyPoint($owner->getTotalMoneyPoint() + Usercfg::point_question_answer_like);
		if ($plusOrMinus == "PLUS") {
			$owner->setTotalRankPoint ( $owner->getTotalRankPoint () + Usercfg::rank_question_answer_like, Usercfg::ANSWER_VOTE_UP, $resource );
		} elseif ($plusOrMinus == "MINUS") {
			$owner->setTotalRankPoint ( $owner->getTotalRankPoint () - Usercfg::rank_question_answer_dislike, Usercfg::ANSWER_UNVOTE_DOWN, $resource );
			$user->setTotalRankPoint ( $user->getTotalRankPoint () - Usercfg::rank_question_answer_dislike_uservote, Usercfg::ANSWER_UNVOTE_DOWN, $resource );
			$this->updateRank ( $user );
		}

		$this->updateRank ( $owner );
		$this->updateAppellation ( null, $owner, $resource,null,Usercfg::rank_question_answer_like );
	}
	/**
	 *
	 * @author sang
	 * @todo add point when question answer be like
	 * @param User $user
	 */
	public function updatePointByQuestionBeLike($user, $userCreateQuestion, $resource = null, $isPlusOrMinus = "PLUS") {
		if ($isPlusOrMinus == "PLUS") {
			$userCreateQuestion->setTotalRankPoint ( $userCreateQuestion->getTotalRankPoint () + Usercfg::rank_question_like, Usercfg::QUESTION_VOTE_UP, $resource );
		} elseif ($isPlusOrMinus == "MINUS") {
			$user->setTotalRankPoint ( $user->getTotalRankPoint () - Usercfg::rank_question_dislike_uservote, Usercfg::QUESTION_UNVOTE_DOWN, $resource );
			$userCreateQuestion->setTotalRankPoint ( $userCreateQuestion->getTotalRankPoint () - Usercfg::rank_question_dislike, Usercfg::QUESTION_UNVOTE_DOWN, $resource );
			$this->updateRank ( $user );
		}

		$this->updateRank ( $userCreateQuestion );
		$this->updateAppellation ( $user, $userCreateQuestion, $resource,null,Usercfg::rank_question_like );
	}
	/**
	 *
	 * @author sang
	 * @todo add point when question answer be like
	 * @param User $user
	 */
	public function updatePointByQuestionBeDislike($user, $userCreateQuestion, $resource = null, $isPlusOrMinus = "PLUS") {
		if ($isPlusOrMinus == "PLUS") {
			$user->setTotalRankPoint ( $user->getTotalRankPoint () + Usercfg::rank_question_dislike_uservote, Usercfg::QUESTION_VOTE_DOWN, $resource );
			$userCreateQuestion->setTotalRankPoint ( $userCreateQuestion->getTotalRankPoint () + Usercfg::rank_question_dislike, Usercfg::QUESTION_VOTE_DOWN, $resource );
			$this->updateRank ( $user );
		} elseif ($isPlusOrMinus == "MINUS") {

			$userCreateQuestion->setTotalRankPoint ( $userCreateQuestion->getTotalRankPoint () - Usercfg::rank_question_like, Usercfg::QUESTION_UNVOTE_UP, $resource );
		}

		$this->updateRank ( $userCreateQuestion );

		$this->updateAppellation ( $user, $userCreateQuestion, $resource,Usercfg::rank_question_dislike_uservote ,Usercfg::rank_question_dislike);
	}

	/**
	 *
	 * @todo minus point when question answer be dislike
	 * @param User $user
	 */
	public function updatePointByQuestionAnswerBeDislike($user, $owner = null, $resource = null, $isPlusOrMinus = "PLUS") {
		// $owner->setTotalMoneyPoint($owner->getTotalMoneyPoint() + Usercfg::point_question_answer_dislike);
		if ($isPlusOrMinus == "PLUS") {
			$owner->setTotalRankPoint ( $owner->getTotalRankPoint () + Usercfg::rank_question_answer_dislike, Usercfg::ANSWER_VOTE_DOWN, $resource );
			$user->setTotalRankPoint ( $user->getTotalRankPoint () + Usercfg::rank_question_answer_dislike_uservote, Usercfg::ANSWER_VOTE_DOWN, $resource );
			$this->updateRank ( $user );
		} else {
			$owner->setTotalRankPoint ( $owner->getTotalRankPoint () - Usercfg::rank_question_answer_like, Usercfg::ANSWER_UNVOTE_UP, $resource );
		}

		$this->updateRank ( $owner );
		$this->updateAppellation ( $user, $owner, $resource ,Usercfg::rank_question_answer_dislike_uservote,Usercfg::rank_question_answer_dislike);
	}

	/**
	 *
	 * @todo minus point when question be delete by admin
	 * @param User $user
	 */
	public function updatePointByQuestionBeDeletedByAdmin($owner = null, $resource = null) {
		// $user->setTotalMoneyPoint($user->getTotalMoneyPoint() + Usercfg::point_question_deleted_by_admin);
		$owner->setTotalRankPoint ( $owner->getTotalRankPoint () + Usercfg::rank_question_deleted_by_admin, Usercfg::QUESTION_CLOSE, $resource );
		// $this->updateRank ( $user );
		$this->updateRank ( $owner );
		$this->updateAppellation ( null, $owner, $resource ,null,Usercfg::rank_question_deleted_by_admin);
	}
	public function updatePointByQuestionBeUndeletedByAdmin($owner = null, $resource = null) {
		// $user->setTotalMoneyPoint($user->getTotalMoneyPoint() + Usercfg::point_question_deleted_by_admin);
		$owner->setTotalRankPoint ( $owner->getTotalRankPoint () - Usercfg::rank_question_deleted_by_admin, Usercfg::QUESTION_REOPEN, $resource );
		// $this->updateRank ( $user );
		$this->updateRank ( $owner );
		$this->updateAppellation ( null, $owner, $resource,null,Usercfg::rank_question_deleted_by_admin );
	}

	/**
	 *
	 * @todo minus point when question answer be delete by admin
	 * @param User $user
	 */
	public function updatePointByQuestionAnswerBeDeletedByAdmin($owner = null, $resource = null) {
		// $user->setTotalMoneyPoint($user->getTotalMoneyPoint() + Usercfg::point_question_answer_deleted_by_admin);
		$user->setTotalRankPoint ( $user->getTotalRankPoint () + Usercfg::rank_question_answer_deleted_by_admin, Usercfg::ANSWER_CLOSE, $resource );
		// $this->updateRank ( $user );
		$this->updateRank ( $owner );
		$this->updateAppellation ( null, $owner, $resource,null,Usercfg::rank_question_answer_deleted_by_admin );
	}

	/**
	 *
	 * @todo update rank when user point change.
	 * @param User $user
	 */
	public function updateRank($user) {
		if ($user) {
			$rankPoint = $user->getTotalRankPoint ();
			$rankName = Usercfg::user_rank_new_text;
			$hasRank = true;
			if ($rankPoint >= Usercfg::user_rank_new_min && $rankPoint < Usercfg::user_rank_new_max) {
				$rankName = Usercfg::user_rank_new_text;
			}
			if ($rankPoint >= Usercfg::user_rank_junior_min && $rankPoint < Usercfg::user_rank_junior_max) {
				$rankName = Usercfg::user_rank_junior_text;
			}
			if ($rankPoint >= Usercfg::user_rank_senior_min && $rankPoint < Usercfg::user_rank_senior_max) {
				$rankName = Usercfg::user_rank_senior_text;
			}
			if ($rankPoint >= Usercfg::user_rank_expert_min && $rankPoint < Usercfg::user_rank_expert_max) {
				$rankName = Usercfg::user_rank_expert_text;
			}
			if ($rankPoint >= Usercfg::user_rank_guru_min) {
				$rankName = Usercfg::user_rank_guru_text;
			}
			$rankMapper = new RankMapper ();
			$hasRank = $rankMapper->checkUserHasRank ( $user, $rankName );
			if (! $hasRank) {
				// add new rank
				// ChromePhp::log ( 'add new rank for ' . $user->getId () );
				$rankMapper->createNewRank ( $user, $rankName );
			}
		}
	}

	/**
	 * @update appellation for user
	 *
	 * @param \FAQ\FAQEntity\User $user
	 * @param \FAQ\FAQEntity\Subject $subject
	 */
	private function updateAppellationWithSubject($user, $subject, $rank_point) {
		$isNew = true;
		$appellation = null;
		$rankPoint = 0;
		$rankName = '';

		$appellations = $user->getAppellation ();
		if ($appellations) {
			foreach ( $appellations as $appellation ) {
				if ($appellation->getSubject ()) {

					if ($appellation->getSubject ()->getId () == $subject->getId ()) {
						$isNew = false;
						// ChromePhp::log ( 'rank point for youe:' . $rank_point );
						if ($rank_point) {

							if ($appellation->getTotalRankPoint ()) {
								$appellation->setTotalRankPoint ( $appellation->getTotalRankPoint () + $rank_point );
							} else {
								$appellation->setTotalRankPoint ( $rank_point );
							}
						}
						$rankPoint = $appellation->getTotalRankPoint ();
						
        				$rankName = "";
						if ($rankPoint >= Usercfg::user_subject_rank_new_min && $rankPoint < Usercfg::user_subject_rank_new_max) {
							$rankName = Usercfg::user_subject_rank_new_text;
						}
						if ($rankPoint >= Usercfg::user_subject_rank_junior_min && $rankPoint < Usercfg::user_subject_rank_junior_max) {
							$rankName = Usercfg::user_subject_rank_junior_text;
						}
						if ($rankPoint >= Usercfg::user_subject_rank_senior_min && $rankPoint < Usercfg::user_subject_rank_senior_max) {
							$rankName = Usercfg::user_subject_rank_senior_text;
						}
						if ($rankPoint >= Usercfg::user_subject_rank_expert_min && $rankPoint < Usercfg::user_subject_rank_expert_max) {
							$rankName = Usercfg::user_subject_rank_expert_text;
						}
						if ($rankPoint >= Usercfg::user_subject_rank_guru_min) {
							$rankName = Usercfg::user_subject_rank_guru_text;
						}
						$appellation->setRank ( $rankName );
					}
				}
			}
		}

		if ($isNew) {
			$appellation = new Appellation ();
			$appellation->setSubject ( $subject );
			$appellation->setRank ( '' );
			$appellation->setTotalRankPoint ( $rank_point );
			$user->setAppellation ( $appellation );
			// ChromePhp::log ( 'add new appellation' );
		}
	}

	/**
	 *
	 * @author sang
	 * @param array $select
	 * @param array $orderBy
	 * @param Int $from
	 * @param Int $to
	 * @return multitype:number Ambigous <\Doctrine\ODM\MongoDB\Query\mixed, \Doctrine\MongoDB\EagerCursor, \Doctrine\MongoDB\Cursor, Cursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
	 */
	public function getUserManager($select, $orderBy, $from, $to) {
		$qb = $this->user->getQueryBuilder ();
		$qb = Util::selectField ( $qb, $select );
		$totalDocument = $qb->getQuery ()->count ();
		$qb = Util::addOrder ( $qb, $orderBy );
		if (isset ( $from ) && isset ( $to )) {
			$qb->skip ( $from )->limit ( $to - $from );
		}
		$q = $qb->getQuery ();
		$listUser = $q->execute ();

		$data = array (
				'listUser' => $listUser,
				'totalDocument' => $totalDocument
		);
		return $data;
	}

	/**
	 *
	 * @todo update appellation
	 * @param
	 *        	<Article, Question> $resource
	 */
	public function updateAppellation($user = null, $owner = null, $resource = null, $user_rank_point = null, $owner_rank_point = null) {
		$type = null; // article: 1, question: 2
		$subject = null;
		if (! $resource) {
			return; // nothing to do
		}
		$resource_class = get_class ( $resource );
		if ('FAQ\\FAQEntity\\Question' == $resource_class) {
			$type = 2;
		}
		if ('FAQ\\FAQEntity\\Article' == $resource_class) {
			$type = 1;
		}
		if (! $type)
			return;
		if ($type == 2) {
			$subject = $resource->getSubject ();
		}
		if ($type == 1) {
			$subject = $resource->getSubject ();
		}
		if (! $subject)
			return;
		if ($user) {
			$this->updateAppellationWithSubject ( $user, $subject, $user_rank_point );
		}
		if ($owner) {
			$this->updateAppellationWithSubject ( $owner, $subject, $owner_rank_point );
		}
	}
}

?>