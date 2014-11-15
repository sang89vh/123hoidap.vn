<?php

namespace Web\Service;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use FAQ\FAQCommon\Authcfg;

class FAQAcl {
	public static $acl;
	public function __construct() {
		if (! isset ( FAQAcl::$acl )) {
			// var_dump("fasdf");
			FAQAcl::$acl = new Acl ();
			// list role
			$guest = new Role ( Authcfg::GUEST );
			FAQAcl::$acl->addRole ( $guest );
			$member = new Role ( Authcfg::MEMBER );
			FAQAcl::$acl->addRole ( $member, $guest );

			$moderator = new Role ( Authcfg::MODERATOR );
			FAQAcl::$acl->addRole ( $moderator, $member );

			$support = new Role ( Authcfg::SUPPORT );
			FAQAcl::$acl->addRole ( $support, $moderator );

			$admin = new Role ( Authcfg::ADMIN );
			FAQAcl::$acl->addRole ( $admin, $support );

			$supperAdmin = new Role ( Authcfg::SUPPERADMIN );
			FAQAcl::$acl->addRole ( $supperAdmin );

			// list resource
			$guestResource = array (
					'Web\Controller\Home\index',
					'Web\Controller\Home\question',
					'Web\Controller\Home\search',
					'Web\Controller\Home\top-hashtag',
					'Web\Controller\Home\top-member',
					'Web\Controller\Home\top-subject',
					'Web\Controller\Home\search-form',

					'Web\Controller\About\index',

					'Web\Controller\Answer\member-dislike',
					'Web\Controller\Answer\member-like',
					'Web\Controller\Answer\premember-dislike',
					'Web\Controller\Answer\premember-like',

					'Web\Controller\Comment\member-dislike',
					'Web\Controller\Comment\member-like',
					'Web\Controller\Comment\premember-dislike',
					'Web\Controller\Comment\premember-like',

					'Web\Controller\Member\home',
					'Web\Controller\Member\index',
					'Web\Controller\Member\overview',

					'Web\Controller\Question\detail',

					'Web\Controller\Question\member-follow',
					'Web\Controller\Question\member-share',
					'Web\Controller\Question\member-spam',
					'Web\Controller\Question\revision',

					'Web\Controller\Search\find',
					'Web\Controller\Search\index',
					'Web\Controller\Search\location-json',
					'Web\Controller\Search\skill-json',

					'Web\Controller\Subject\detail',
					'Web\Controller\Subject\index',
					'Web\Controller\Subject\list-subject',
					'Web\Controller\Subject\overview',
					'Web\Controller\Subject\question',

					'Web\Controller\Support\community-guideline',
					'Web\Controller\Support\contact',
					'Web\Controller\Support\help',
					'Web\Controller\Support\index',
					'Web\Controller\Support\scoring-system',
					'Web\Controller\Support\sitemap',
					'Web\Controller\Support\term',

					'Web\Controller\User\auth',
					'Web\Controller\User\avatar',

					'Web\Controller\User\forget-password',
					'Web\Controller\User\info',
					'Web\Controller\User\login',
					'Web\Controller\User\signup',
					'Web\Controller\User\submit-email-twitter',
					'Web\Controller\User\update-email-twitter'
			);
			$memberResource = array (
					'Web\Controller\Answer\answer',
					'Web\Controller\Answer\edit-wikistyle',
					'Web\Controller\Answer\best',
					'Web\Controller\Answer\dislike',
					'Web\Controller\Answer\index',
					'Web\Controller\Answer\like',
					'Web\Controller\Answer\like-list',
					'Web\Controller\Answer\dislike-list',
					'Web\Controller\Answer\best-list',
					'Web\Controller\Answer\save-wikistyle',
					'Web\Controller\Answer\active-version',
					'Web\Controller\Answer\form-spam',

					'Web\Controller\Comment\comment',
					'Web\Controller\Comment\dislike',
					'Web\Controller\Comment\like',

					'Web\Controller\Media\add-media',
					'Web\Controller\Media\create-directory',
					'Web\Controller\Media\get-image',
					'Web\Controller\Media\image-file',
					'Web\Controller\Media\image-link',
					'Web\Controller\Media\index',
					'Web\Controller\Media\media-file',
					'Web\Controller\Media\nav-media',
					'Web\Controller\Media\read-image-media',
					'Web\Controller\Media\read-image-media',
					'Web\Controller\Media\upload-file',
					'Web\Controller\Media\video-file',
					'Web\Controller\Media\video-link',

					'Web\Controller\Member\action-member',

					'Web\Controller\Message\detail',
					'Web\Controller\Message\inbox',
					'Web\Controller\Message\index',
					'Web\Controller\Message\more-message',
					'Web\Controller\Message\chat-box',
					'Web\Controller\Message\notify',
					'Web\Controller\Message\send',

					'Web\Controller\Question\add-share',
					'Web\Controller\Question\askme-list',
					'Web\Controller\Question\close',
					'Web\Controller\Question\closed-list',
					'Web\Controller\Question\content-question',
					'Web\Controller\Question\create',
					'Web\Controller\Question\edit-wikistyle',
					'Web\Controller\Question\delete',
					'Web\Controller\Question\draft',
					'Web\Controller\Question\finish-question',
					'Web\Controller\Question\follow',
					'Web\Controller\Question\follow-list',
					'Web\Controller\Question\index',
					'Web\Controller\Question\open-list',
					'Web\Controller\Question\overview',
					'Web\Controller\Question\save-question',
					'Web\Controller\Question\save-wikistyle',
					'Web\Controller\Question\select-subject',
					'Web\Controller\Question\share',
					'Web\Controller\Question\spam',
					'Web\Controller\Question\spam-list',
					'Web\Controller\Question\unfollow',
					'Web\Controller\Question\unspam',
					'Web\Controller\Question\like',
					'Web\Controller\Question\dislike',
					'Web\Controller\Question\active-version',
					'Web\Controller\Question\protect-question',
					'Web\Controller\Question\unprotect-question',
					'Web\Controller\Question\form-spam',
					'Web\Controller\Question\close-question',
					'Web\Controller\Question\reopen-question',
					'Web\Controller\Question\highlight-question',
					'Web\Controller\Question\unhighlight-question',
					'Web\Controller\Question\top-question',
					'Web\Controller\Question\untop-question',

					'Web\Controller\Subject\action-subject',
					'Web\Controller\Subject\list-subject',

					'Web\Controller\User\change-avatar',
					'Web\Controller\User\change-password',
					'Web\Controller\User\logout',
					'Web\Controller\User\rank',
					'Web\Controller\User\about',

					'Web\Controller\Tour\about',
					'Web\Controller\Tour\community-guideline',
					'Web\Controller\Tour\follow-member',
					'Web\Controller\Tour\follow-subject',
					'Web\Controller\Tour\invite-friend',

					'Web\Controller\Review\index',
					'Web\Controller\Review\spam-question',
					'Web\Controller\Review\unspam-question',
					'Web\Controller\Review\edit-question',
					'Web\Controller\Review\edit-answer'
			);
			$supportResource = array ();
			$moderatorResource = array ();
			$adminResource = array (
					'Admin\Controller\Phpjob\manager-keyword',
					'Admin\Controller\Phpjob\init-keyword',
					'Admin\Controller\Phpjob\update-keyword',

					'Admin\Controller\Catalog\index',
					'Admin\Controller\Catalog\usubject',
					'Admin\Controller\Catalog\update-subject',

					'Admin\Controller\Report\question',

					'Admin\Controller\News\index',
					'Admin\Controller\News\manager',
					'Admin\Controller\News\create',

					'Admin\Controller\Member\index',
					'Admin\Controller\Member\list-user',
					'Admin\Controller\Member\deactive',
					'Admin\Controller\Member\active',

					'Web\Controller\Tag\create',
					'Web\Controller\Tag\edit'
			)
			;
			// $supperAdmin allow all privigle
			FAQAcl::$acl->allow ( $supperAdmin );
			// guest
			FAQAcl::$acl->allow ( $guest, null, $guestResource );
			// member
			FAQAcl::$acl->allow ( $member, null, $memberResource );
			// moderator
			FAQAcl::$acl->allow ( $moderator, null, $moderatorResource );
			// support
			FAQAcl::$acl->allow ( $support, null, $supportResource );
			// admin
			FAQAcl::$acl->allow ( $admin, null, $adminResource );
		}
	}

	/**
	 *
	 * @param String $role
	 * @param String $privilege
	 * @return true if is allow, false if not allow
	 * @todo check privilege user's role
	 *
	 */
	public function isAllowed($role, $controller, $action) {
		// var_dump(FAQAcl::$acl);
		$privilege = $controller . "\\" . $action;
		$isAllow = FAQAcl::$acl->isAllowed ( $role, null, $privilege );
		return $isAllow;
	}
}
