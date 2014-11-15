<?php

namespace Web\Controller;

use FAQ\Mapper\NewMapper;
use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\FAQCommon\Util;
use FAQ\Mapper\SubjectMapper;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\Mapper\UserMapper;
use FAQ\FAQCommon\Usercfg;

class TourController extends FAQAbstractActionController {
	public function aboutAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$this->setLayoutTour ();
		$newMapper = new NewMapper ();
		$news = $newMapper->getNews ( FAQParaConfig::NEWS_TYPE_ABOUT, FAQParaConfig::STATUS_ACTIVE, null, null, null );

		$subjectMapper = new SubjectMapper ();
		$subjects = $subjectMapper->getAllSubject ();

		$userMapper = new UserMapper ();

		$users = $userMapper->findUser ( null, null, array (
				"total_rank_point" => "desc",
				"total_money_point" => "desc",
				"total_answer" => "desc"
		), null, null, Usercfg::USER_STATUS_CURRENT_ACTIVE, false,true );

		return array (
				"news" => $news,
				"subjects" => $subjects,
				"totalSubject" => count ( $subjects ),
				"list_member" => $users
		)
		;
	}
// 	public function followSubjectAction() {
// 		$privilege = Util::isPrivilege ( $this );
// 		if (! $privilege ['isAllowed']) {
// 			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
// 		}
// 		$this->setLayoutTour ();

// 		$subjectMapper = new SubjectMapper ();
// 		$subjectsFollow = $subjectMapper->findSubject ( null, null, Util::getIDCurrentUser (), true, FAQParaConfig::STATUS_ACTIVE, null, null, null, true, true );
// 		$subjectsUnfollow = $subjectMapper->findSubject ( null, null, Util::getIDCurrentUser (), false, FAQParaConfig::STATUS_ACTIVE, null, null, null, true, true );
// 		$subjects = $subjectsFollow->toArray () + $subjectsUnfollow->toArray ();
// 		return array (
// 				"subjects" => $subjects,
// 				"totalSubject" => count ( $subjects )
// 		);
// 	}
// 	public function followMemberAction() {
// 		$privilege = Util::isPrivilege ( $this );
// 		if (! $privilege ['isAllowed']) {
// 			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
// 		}
// 		$this->setLayoutTour ();

// 		$orderBy = array (
// 				"total_rank_point" => "desc",
// 				"total_money_point" => "desc",
// 				"total_answer" => "desc"
// 		);
// 		$userMapper = new UserMapper ();
// 		// $data = array(
// 		// "totalRow" => $totalRow,
// 		// "users" => users
// 		// );

// 		$data = $userMapper->findUser ( null, null, $orderBy, 0, 16, Usercfg::USER_STATUS_CURRENT_ACTIVE, true );
// 		$list_member = $data ['users'];
// 		// var_dump(count($list_member));
// 		return array (
// 				"total_member" => $data ['totalRow'],
// 				"list_member" => $list_member
// 		);
// 	}
// 	public function inviteFriendAction() {
// 		$privilege = Util::isPrivilege ( $this );
// 		if (! $privilege ['isAllowed']) {
// 			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
// 		}
// 		$this->setLayoutTour ();
// 	}
	public function communityGuidelineAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$this->setLayoutTour ();

		$newMapper = new NewMapper ();
		$news1 = $newMapper->getNews ( FAQParaConfig::NEWS_TYPE_COMMUNITY_GUIDELINE, FAQParaConfig::STATUS_ACTIVE, null, null, null );
		$news2 = $newMapper->getNews ( FAQParaConfig::NEWS_TYPE_TERM, FAQParaConfig::STATUS_ACTIVE, null, null, null );
		$news = $news2->toArray () + $news1->toArray ();
		return array (
				"news" => $news
		);
	}
}