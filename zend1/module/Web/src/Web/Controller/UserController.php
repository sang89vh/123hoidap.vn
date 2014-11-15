<?php

namespace Web\Controller;

use FAQ\FAQCommon\Authcfg;
use FAQ\FAQCommon\FAQAbstractActionController;
use SocialAuth\Facebook;
use SocialAuth\TwitterOAuth;
use Zend\Session\SessionManager;
use FAQ\Mapper\AuthMapper;
use FAQ\FAQEntity\User;
use Web\Forms\UpdateEmailTwiterForm;
use Zend\View\Model\ViewModel;
use FAQ\FAQCommon\Sessioncfg;
use FAQ\Mapper\UserMapper;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\Appcfg;
use Zend\Json\Json;
use FAQ\FAQEntity\Location;
use Web\Forms\UploadForm;
use FAQ\FAQCommon\Mail;
use FAQ\FAQCommon\ChromePhp;
use FAQ\FAQCommon\Usercfg;
use Editor\PhpEditor;
use FAQ\Mapper\SubjectMapper;
use Zend\Form\Annotation\Object;
use FAQ\FAQCommon\FAQParaConfig;

class UserController extends FAQAbstractActionController {
	// support communicate cross domain site
	public function whoAction() {
		$this->setLayoutAjax ();
		$currentUser = Util::getCurrentUser ();
		return array (
				'currentUser' => $currentUser
		);
	}
	/*
	 * longin face voi back link
	 */
	public function loginfaceAction() {
		// config - facebook
		$ref = $this->request->getQuery ( 'ref' );
		$facebook = new Facebook ( array (
				'appId' => Authcfg::$facebook_app_id,
				'secret' => Authcfg::$facebook_app_secret
		) );
		$url = Appcfg::$domain;
		if ($ref) {
			$url = $facebook->getLoginUrl ( array (
					'redirect_uri' => Authcfg::$facebook_redirect_url . 'ref=' . $ref,
					'scope' => Authcfg::$facebook_scope
			) );
		}
		return $this->redirect ()->toUrl ( $url );
	}
	/**
	 * @actionController
	 *
	 * @todo return login links
	 * @return array (facebook_login_url, twitter_login_url)
	 */

	// public function testAction(){
	// $this->setLayoutAjax();
	// $email = new Mail();
	// $email->sendPasswordRecover('datlong1502@yahoo.com', '1ssbk');
	// echo 'done';
	// return $this->getResponse();
	// }
	public function loginAction() {
		$this->setLayoutLogin ();
		// top subject
		$subjectMapper = new SubjectMapper ();
		$orderBy = array (
				'total_question' => 'desc'
		);

		// danh sach chu de tren he thong co status = 1.
		$subjects = $subjectMapper->findSubject ( null, null, null, null, 1, $orderBy, 0, 6 );
		// login

		$authMapper = new AuthMapper ();
		$sm = $authMapper->createSession ();
		$session_email = $authMapper->getSessionParam ( Sessioncfg::$email );
		if ($session_email) {
			$this->redirect ()->toRoute ( "qapolo" );
			return;
		}

		// config - facebook
		$facebook = new Facebook ( array (
				'appId' => Authcfg::$facebook_app_id,
				'secret' => Authcfg::$facebook_app_secret
		) );

		// config twitter
		$connection = new TwitterOAuth ( Authcfg::$twitter_consumer_key, Authcfg::$twitter_consumer_secret );
		$request_token = $connection->getRequestToken ( Authcfg::$twitter_url_callback );
		$sm = new SessionManager ();
		$sm->start ();
		$sm->getStorage ()->setMetadata ( "oauth_token", $request_token ['oauth_token'] );
		$sm->getStorage ()->setMetadata ( "oauth_token_secret", $request_token ['oauth_token_secret'] );
		$twitter_url = $connection->getAuthorizeURL ( $request_token ['oauth_token'] );
		return array (
				"facebook_login_url" => $facebook->getLoginUrl ( array (
						'redirect_uri' => Authcfg::$facebook_redirect_url,
						'scope' => Authcfg::$facebook_scope
				) ),
				"twitter_login_url" => $twitter_url,
				'subjects' => $subjects
		);
	}
	public function basicLoginAction() {
		$this->setLayoutAjax ();
		// login
		$urlBack = $this->request->getPost ( 'urlBack' );
		if (! empty ( $urlBack )) {
			Util::setSessionParam ( FAQParaConfig::URL_BACK_LOGIN, $urlBack );
		}
		$authMapper = new AuthMapper ();
		$sm = $authMapper->createSession ();
		$session_email = $authMapper->getSessionParam ( Sessioncfg::$email );
		if ($session_email) {
			$this->redirect ()->toRoute ( "qapolo" );
			return;
		}

		// config - facebook
		$facebook = new Facebook ( array (
				'appId' => Authcfg::$facebook_app_id,
				'secret' => Authcfg::$facebook_app_secret
		) );

		// config twitter
		$connection = new TwitterOAuth ( Authcfg::$twitter_consumer_key, Authcfg::$twitter_consumer_secret );
		$request_token = $connection->getRequestToken ( Authcfg::$twitter_url_callback );
		$sm = new SessionManager ();
		$sm->start ();
		$sm->getStorage ()->setMetadata ( "oauth_token", $request_token ['oauth_token'] );
		$sm->getStorage ()->setMetadata ( "oauth_token_secret", $request_token ['oauth_token_secret'] );
		$twitter_url = $connection->getAuthorizeURL ( $request_token ['oauth_token'] );
		return array (
				"facebook_login_url" => $facebook->getLoginUrl ( array (
						'redirect_uri' => Authcfg::$facebook_redirect_url,
						'scope' => Authcfg::$facebook_scope
				) ),
				"twitter_login_url" => $twitter_url
		);
	}
	public function logoutAction() {
		$sm = new SessionManager ();
		$sm->start ();
		$sm->destroy ();
		return $this->redirect ()->toRoute ( "user", array (
				"controller" => "Web\Controller\User",
				"action" => "login"
		) );
	}

	/**
	 * @actionController
	 */
	public function authAction() {
		$this->setLayoutLogin ();
		$login_type = ''; // email, facebook, twitter, google
		$login_ok = false;

		$sessionEmail = $this->getEmailSession ();
		// user loged
		if ($sessionEmail) {
			$this->toAuthSuccess ();
			return $this->getResponse ();
		}

		// login by email
		$email = $this->request->getPost ( 'email' );
		$password = $this->request->getPost ( 'password' );
		if ($email && $password)
			$login_type = 'email';

		/**
		 * check facebook login
		 * - code: agree login by facebook
		 * - error=access_denied: deny login by facebook
		 */
		$code = $this->request->getQuery ( 'code' );
		$error = $this->request->getQuery ( 'error' );
		if ($code) {

			$login_type = 'facebook';
			$login_ok = true;
		}

		if ($error) {
			$login_type = 'facebook';
			$login_ok = false;
		}

		/**
		 * Check twitter login
		 * oauth_token: agree login by twitter
		 * denied: deny login by twitter
		 */
		$denied = $this->request->getQuery ( 'denied' );
		$oauth_token = $this->request->getQuery ( 'oauth_token' );
		if ($denied) {

			$login_type = 'twitter';
			$login_ok = false;
		}
		if ($oauth_token) {
			$login_type = 'twitter';
			$login_ok = true;
		}

		// auth by email (not_user, not_password, success)
		if ($login_type == 'email') {
			$authMapper = new AuthMapper ();
			$authCode = $authMapper->isAuthByEmail ( $email, $password );
			if ($authCode == - 1)
				echo 'not_user,not_password';
			if ($authCode == - 3)
				echo 'not_user,not_password';
			if ($authCode == 1) {
				$currentUser = Util::getCurrentUser ();
				$isFirstLogin = $currentUser->getIsFirstLogin ();
				if ($isFirstLogin == 0) {
					$urlBack = "/tour/about";
					$currentUser->setIsFirstLogin ( 1 );
					$userMapper = new UserMapper ();
					$userMapper->update ( $currentUser );
					echo 'success_first';
				} else {
					echo 'success';
				}
			}
			return $this->getResponse ();
		}

		if ($login_type == 'facebook') {
			$facebook = new Facebook ( array (
					'appId' => Authcfg::$facebook_app_id,
					'secret' => Authcfg::$facebook_app_secret
			) );
			// $facebook->setAccessToken("1234");
			if (! $facebook->getUser ()) {
				return $this->toNoticeError ( "Đăng nhập với facebook không thành công, bạn vui lòng thử lại!", 200, "/user/login" );
			}

			if ($login_ok) { // agree login by facebook

				$userId = $facebook->getUser ();
				$userInfo = $facebook->api ( '/me', 'GET' );
				$authMapper = new AuthMapper ();
				$user = $authMapper->insertUserByFacebook ( $userInfo );
				if (isset ( $user->reg_code ) && "email_registered" == $user->reg_code) {
					// email da duoc dung de dang ky, can phai dang nhap
					return $this->toNoticeWarning ( "Email " . $user->getEmail () . " đã được dùng trong hệ thống. Bạn vui lòng đăng nhập với email.", 3000, "/user/login" );
				}

				$authMapper->setSessionUser ( $user );

				$this->toAuthSuccess ();
				return $this->getResponse ();
			} else { // don't agree login by facebook
				return $this->toNoticeError ( "Đăng nhập với facebook không thành công, bạn vui lòng thử lại!", 200, "/user/login" );
			}
		}

		if ($login_type == 'twitter') {
			if ($login_ok) { // agree login by twitter
				$sm = new SessionManager ();
				$sm->start ();
				$oauth_token = $sm->getStorage ()->getMetadata ( 'oauth_token' );
				$oauth_token_secret = $sm->getStorage ()->getMetadata ( 'oauth_token_secret' );
				$connection = new TwitterOAuth ( Authcfg::$twitter_consumer_key, Authcfg::$twitter_consumer_secret, $oauth_token, $oauth_token_secret );
				$oauth_verifier = $this->request->getQuery ( 'oauth_verifier' );
				$access_token = $connection->getAccessToken ( $oauth_verifier );
				if ($access_token) {
					$info = $connection->get ( 'users/show', array (
							'screen_name' => $access_token ['screen_name']
					) );
					$authMapper = new AuthMapper ();
					$user = $authMapper->insertUserByTwitter ( $info );
					$email = $user->getEmail ();
					$twitter_user_id = $user->getOpenid ()->first ()->getUserId ();

					$info->row_id = $user->getId ();
					if ($email == $twitter_user_id . '@twitter.com') {
						$this->getRequest ()->setMetadata ( "info", $info );
						$authMapper->setSessionParam ( Sessioncfg::$user_id, $user->getId () );
						return $this->forward ()->dispatch ( "Web\Controller\User", array (
								'action' => 'updateEmailTwitter'
						) );
					} else {
						$authMapper->setSessionUser ( $user );
						$this->toAuthSuccess ();
					}
				} else { // token expired
					$this->toAuthFail ();
					return $this->getResponse ();
				}
			} else { // don't agree login by facebook
				$this->toAuthFail ();
				return $this->getResponse ();
			}
		}
	}

	/**
	 *
	 * @todo redirect to success login
	 */
	private function toAuthSuccess() {
		$urlBack = Util::getSessionParam ( FAQParaConfig::URL_BACK_LOGIN );
		$currentUser = Util::getCurrentUser ();

		$isFirstLogin = $currentUser->getIsFirstLogin ();

		if ($isFirstLogin == 0) {
			$urlBack = "/tour/about";
			$currentUser->setIsFirstLogin ( 1 );
			$userMapper = new UserMapper ();
			$userMapper->update ( $currentUser );
		}
		if (empty ( $urlBack )) {
			return $this->redirect ()->toUrl ( Appcfg::$domain );
		} else {
			Util::clearSessionParam ( FAQParaConfig::URL_BACK_LOGIN );
			// var_dump(Appcfg::$domain.$urlBack);
			if (strpos ( $urlBack, Appcfg::$domain ) !== false) {
				return $this->redirect ()->toUrl ( $urlBack );
			} else {
				return $this->redirect ()->toUrl ( Appcfg::$domain . $urlBack );
			}
		}
	}

	/**
	 *
	 * @todo redirect to fail login
	 */
	private function toAuthFail() {
		return $this->redirect ()->toRoute ( "user", array (
				"controller" => "Web\Controller\User",
				"action" => "login"
		) );
	}

	/**
	 *
	 * @todo redirect to logout.
	 */
	private function toLogout() {
		return $this->redirect ()->toRoute ( "user", array (
				"controller" => "Web\Controller\User",
				"action" => "logout"
		) );
	}

	/**
	 *
	 * @todo Acount registered by twitter need to update email
	 */
	public function updateEmailTwitterAction() {
		$this->setLayoutLogin ();
		$info = $this->getRequest ()->getMetadata ( "info" );
		$updateEmailTwitterForm = new UpdateEmailTwiterForm ( "Update-Email", $info->row_id, $info->id, '', $info->name, '', $info->location, 'Update' );
		return array (
				'updateEmailTwitterForm' => $updateEmailTwitterForm
		);
	}
	/**
	 *
	 * @todo Update email twitter submit
	 */
	public function submitEmailTwitterAction() {
		$email = $this->getRequest ()->getPost ( "email" );
		$name = $this->getRequest ()->getPost ( 'name' );
		$location = $this->getRequest ()->getPost ( 'location' );
		$isEmail = filter_var ( $email, FILTER_VALIDATE_EMAIL );

		// @ bool $isUpdateEmail (if hacked or not email return false)
		$isUpdateEmail = true;
		if ($isEmail) {
			// update email
			$id = $this->getRequest ()->getPost ( "id" );
			// check id equal session(userid)
			$authMapper = new AuthMapper ();
			if ($id != $authMapper->getSessionParam ( Sessioncfg::$user_id )) {
				$isUpdateEmail = false;
				return $this->toNoticeError ( "Bạn không nên hack chúng tôi, một nơi vì cộng đồng. Thanks." ); // hacking
			}
			$user = $authMapper->getOneUser ( $id );
			$userWithEmail = $authMapper->getUserByEmail ( $email );
			if ($userWithEmail) {
				// this email is registered, if want update email goto change email
				$isUpdateEmail = false;
				return $this->toNoticeWarning ( "Email " . $email . " đã được dùng trong hệ thống. Vui lòng đăng nhập bằng email.", 4000, "/user/login" );
			} else {
				// this email is not registered, so update email and go to Web page
				$user->insert ();
				$twitterEmail = $user->getEmail ();
				$user->setEmail ( $email );
				if ($name) {
					$user->setFirstName ( $name );
				}

				// izzi: update point - registering by twitter.
				if ($user->getStatus () == Usercfg::user_status_email_missing) {
					$authMapper->updatePointByRegistering ( $user );
					$user->setStatus ( Usercfg::user_status_email_ok );
				}
				$authMapper->commit ();
				$authMapper = new AuthMapper ();
				$authMapper->createSession ();
				$authMapper->setSessionUser ( $user );
				return $this->toAuthSuccess ();
			}
		} else {
			$isUpdateEmail = false;
		}

		if (! $isUpdateEmail) {
			$authMapper = new AuthMapper ();
			$user = $authMapper->getOneUser ( $authMapper->getSessionParam ( Sessioncfg::$user_id ) );
			$authMapper->createSession ();
			$authMapper->setSessionUser ( $user );
			return $this->toNoticeWarning ( "Email của bạn chưa được xác nhận với hệ thống.", 3000, "/" );
		}
	}
	public function signupAction() {
		$user_id = Util::getIDCurrentUser ();
		if ($user_id) {
			// user loged. go to home
			return $this->redirect ()->toRoute ( "qapolo" );
		}
		$commit = $this->getRequest ()->getPost ( 'commit' );
		if ($commit != null) {
			$this->setLayoutAjax ();
			$authMapper = new AuthMapper ();
			$user = $authMapper->getUser ();
		    $email = preg_replace('/\s+/', '', $this->getRequest ()->getQuery ( 'email' ));
			$password = trim($this->getRequest ()->getQuery ( 'password' ));
			$firstname = trim($this->getRequest ()->getQuery ( 'firstname' ));
			$lastname = trim($this->getRequest ()->getQuery ( 'lastname' ));
			$birth_day = trim($this->getRequest ()->getQuery ( 'birth_day' ));
			$birth_month = trim($this->getRequest ()->getQuery ( 'birth_month' ));
			$birth_year = trim($this->getRequest ()->getQuery ( 'birth_year' ));
			$sex = trim($this->getRequest ()->getQuery ( 'sex' ));

			if(empty($email)||empty($password)||empty($firstname)||empty($lastname)||empty($birth_day)||empty($birth_month)||empty($birth_year)||empty($sex)){
			$code=	"not_valid";
			}else {
			$user->birth_day = $birth_day;
			$user->birth_month = $birth_month;
			$user->birth_year = $birth_year;
			$user->setEmail ( $email );
			$user->setPass ( $password );
			$user->setFirstName ( $firstname );
			$user->setLastName ( $lastname );
			$birthday = Util::createDate ( $birth_day, $birth_month, $birth_year );
			$user->setBirthday ( $birthday );
			$user->setSex ( $sex );
			$user->setRoleCode ( Authcfg::MEMBER );
			$code = $authMapper->signupUser ( $user );
			}
			echo $code;
			return $this->getResponse ();
		}
		$this->setLayoutLogin ();
		// config - facebook
		$facebook = new Facebook ( array (
				'appId' => Authcfg::$facebook_app_id,
				'secret' => Authcfg::$facebook_app_secret
		) );

		// config twitter
		$connection = new TwitterOAuth ( Authcfg::$twitter_consumer_key, Authcfg::$twitter_consumer_secret );
		$request_token = $connection->getRequestToken ( Authcfg::$twitter_url_callback );
		$sm = new SessionManager ();
		$sm->start ();
		$sm->getStorage ()->setMetadata ( "oauth_token", $request_token ['oauth_token'] );
		$sm->getStorage ()->setMetadata ( "oauth_token_secret", $request_token ['oauth_token_secret'] );
		$twitter_url = $connection->getAuthorizeURL ( $request_token ['oauth_token'] );
		return array (
				"facebook_login_url" => $facebook->getLoginUrl ( array (
						'redirect_uri' => Authcfg::$facebook_redirect_url,
						'scope' => Authcfg::$facebook_scope
				) ),
				"twitter_login_url" => $twitter_url,
				'subjects' => $subjects
		);
	}

	/**
	 *
	 * @author izzi
	 * @todo route for: Thong tin
	 * @return \Zend\Stdlib\ResponseInterface multitype:Ambigous \FAQ\DB\Entity, NULL, unknown>
	 */
	public function aboutAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước", 3000, "/user/login" );
		}
		$commit = $this->request->getPost ( 'commit' );

		if ($commit != null) {
			$this->setLayoutAjax ();
			$isSecurity = true;
			$isSecurity = strpos ( $this->request->getServer ( 'HTTP_REFERER' ) . '', Appcfg::$domain ) >= 0 ? true : false;
			if (! $isSecurity) {
				echo 'not_security';
				return $this->getResponse ();
			}
			$email = $this->request->getQuery ( 'email' );
			$firstName = $this->request->getQuery ( 'firstname' );
			$lastName = $this->request->getQuery ( 'lastname' );
			$birth_day = $this->request->getQuery ( 'birth_day' );
			$birth_month = $this->request->getQuery ( 'birth_month' );
			$birth_year = $this->request->getQuery ( 'birth_year' );
			$tinh = $this->request->getQuery ( 'tinh' );
			$huyen = $this->request->getQuery ( 'huyen' );
			$xa = $this->request->getQuery ( 'xa' );
			$quocTich = $this->request->getQuery ( 'quoctich' );

			$userMapper = new UserMapper ();
			$user = Util::getCurrentUser ();
			$user->setLastName ( $lastName );
			if ($quocTich)
				$user->setNationnality ( $quocTich );
			if ($birth_day && $birth_month && $birth_year) {
				$user->setBirthday ( Util::createDate ( $birth_day, $birth_month, $birth_year ) );
			}
			$skillsJson = $this->request->getQuery ( 'list_skill' );
			$locationJson = $this->request->getQuery ( 'list_location' );
			$newSkills = Json::decode ( $skillsJson );
			$newLocations = Json::decode ( $locationJson );
			// TODO phan tach cac loai location
			$loc_type = array ();
			$loc_type_items = array ();
			foreach ( $newLocations as $loc ) {
				if (! in_array ( $loc->type, $loc_type )) {
					$loc_type [] = $loc->type;
				}
			}
			foreach ( $loc_type as $type ) {
				$loc_items = array ();
				foreach ( $newLocations as $loc ) {
					if ($type == $loc->type) {
						$loc_items [] = $loc->text;
					}
				}
				$loc_type_items [$type] = $loc_items;
			}
			// init when delete all
			if (! $loc_type_items ['loc_use_noi_ct']) {
				$loc_type_items ['loc_use_noi_ct'] = array ();
			}
			if (! $loc_type_items ['loc_use_noi_ht']) {
				$loc_type_items ['loc_use_noi_ht'] = array ();
			}
			if (! $loc_type_items ['loc_use_diadanh']) {
				$loc_type_items ['loc_use_diadanh'] = array ();
			}
			$userMapper->updateSkillForUser ( $user, $newSkills );
			foreach ( $loc_type_items as $type => $loc ) {

				$userMapper->updateLocationForUser ( $user, $loc, FAQParaConfig::getLocDefault ( $type ) );
			}
			echo "saved";
			$userMapper->commit ();
			return $this->getResponse ();
		}
		$this->setLayoutHome ();
		$user = Util::getCurrentUser ();
		return array (
				"user" => $user
		);
	}

	/**
	 *
	 * @author izzi
	 * @todo route for: cai dat mat khau
	 * @return \Zend\Stdlib\ResponseInterface multitype:Ambigous \FAQ\DB\Entity, NULL, unknown>
	 */
	public function changePasswordAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}

		$email = $this->request->getPost ( 'email' );
		$isSecurity = true;
		$isSecurity = strpos ( $this->request->getServer ( 'HTTP_REFERER' ) . '', Appcfg::$domain ) >= 0 ? true : false;
		if (! $isSecurity) {
			$this->setLayoutAjax ();
			echo "not_security";
			return $this->getResponse ();
		}
		if ($email) {
			$this->setLayoutAjax ();
			// $old_password = $this->request->getPost ( 'old_password' );
			$new_password = $this->request->getPost ( 'new_password' );
			$user = Util::getCurrentUser ();
			// if ($user->getPass () != $old_password) {
			// echo "password_not_match";
			// } else {
			$authMapper = new AuthMapper ();
			// check email be used by another
			$oldEmail = $user->getEmail ();
			if ($oldEmail != $email) {
				$userExistByEmail = $authMapper->checkRegistedByEmail ( $email );
				if ($userExistByEmail) {
					echo 'email_used';
					return $this->getResponse ();
				}
			}
			// save data
			if (filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
				$user->setEmail ( $email );
				if ($user->getStatus () == Usercfg::user_status_email_missing) {
					$user->setStatus ( Usercfg::user_status_email_ok );
					// $authMapper->updatePointByRegistering ( $user );
				}
			}
			$user->setPass ( $new_password );
			$authMapper->commit ();
			echo "saved";
			// }
			return $this->getResponse ();
		}
		$this->setLayoutHome ();
		$user = Util::getCurrentUser ();
		return array (
				"user" => $user
		);
	}
	/*
	 * @todo listener action: user submit change avatar
	 */
	public function changeAvatarAction() {
		$this->setLayoutAjax ();
		$isSecurity = strpos ( $this->request->getServer ( 'HTTP_REFERER' ) . '', Appcfg::$domain ) >= 0 ? true : false;
		if (! $isSecurity)
			return $this->getResponse ();
		$option = $this->request->getPost ( 'option' );
		$link = $this->request->getPost ( 'link' );
		// resized image
		$width_avatar = $this->request->getPost ( 'width_avatar' );
		$height_avatar = $this->request->getPost ( 'height_avatar' );
		// preview image by sesized image
		$width_preview = $this->request->getPost ( 'width_preview' );
		$heigh_preview = $this->request->getPost ( 'height_preview' );
		$image_type = "jpg";
		if ($option) {
			$info = getimagesize ( $link );
			$x1 = $option ['x1'];
			$y1 = $option ['y1'];
			$x2 = $option ['x2'];
			$y2 = $option ['y2'];
		}
		// original image.
		$im_src = null;
		if (strpos ( $link, '.gif' ) > 0) {
			$image_type = 'gif';
			$im_src = imagecreatefromgif ( $link );
		}
		if (strpos ( $link, '.jpg' ) > 0 || strpos ( $link, '.jpeg' ) > 0) {
			$im_src = imagecreatefromjpeg ( $link );
		}
		if (strpos ( $link, '.png' ) > 0) {
			$image_type = 'png';
			$im_src = imagecreatefrompng ( $link );
		}
		// preview image by original image
		$im_dest = imagecreatetruecolor ( $width_preview, $heigh_preview );
		$img_src_width = imagesx ( $im_src );
		$img_src_height = imagesy ( $im_src );
		if ($x1 == null) {
			$x1 = 0;
			$y1 = 0;
			$x2 = $width_avatar;
			$y2 = $height_avatar;
		}
		// making parameters to crop image.
		$crop_x = $x1 * $img_src_width / $width_avatar;
		$crop_y = $y1 * $img_src_height / $height_avatar;
		$crop_w = ($x2 - $x1) * $img_src_width / $width_avatar;
		$crop_h = ($y2 - $y1) * $img_src_height / $height_avatar;
		/*
		 * im_dest - destinating image. im_src - original image crop_x, crop_y - top left point width_preview, height_preview - with and height of destinating image crop_w, crop_h - width, height need to crop from original image.
		 */
		$x = imagecopyresampled ( $im_dest, $im_src, 0, 0, $crop_x, $crop_y, $width_preview, $heigh_preview, $crop_w, $crop_h );
		$userMapper = new UserMapper ();
		$userMapper->updateAvatarByResource ( $im_dest, $image_type );
		echo 'saved';
		return $this->getResponse ();
	}

	/**
	 *
	 * @todo create iframe change avatar.
	 * @return multitype:string unknown |multitype:NULL |\Zend\Stdlib\ResponseInterface
	 */
	public function avatarAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		// upload file action
		$this->setLayoutAjax ();
		$file = null;
		if (isset ( $_FILES ['avatar'] )) {
			$file = $_FILES ['avatar'];
		}
		if ($file) {
			// invalid file
			$type = $file ['type'];
			if (strpos ( $type, 'gif' ) > 0 || strpos ( $type, 'png' ) > 0 || strpos ( $type, 'jpg' ) > 0 || strpos ( $type, 'jpeg' ) > 0) {
				$file_name = time () . ".jpg";
				if (strpos ( $type, 'gif' ) > 0)
					$file_name = time () . ".gif";
				if (strpos ( $type, 'png' ) > 0)
					$file_name = time () . ".png";
				if (strpos ( $type, 'jpeg' ) > 0)
					$file_name = time () . ".jpeg";
				$x = move_uploaded_file ( $_FILES ["avatar"] ["tmp_name"], "public/tmp/" . $file_name );
				return array (
						"link" => "public/tmp/" . $file_name,
						"type" => $file ['type']
				);
			} else {
				return array (
						"link" => null,
						"type" => null
				);
			}
		}

		// get file action
		$link = $this->request->getQuery ( 'link' );
		$type = $this->request->getQuery ( 'type' );
		if ($link) {
			$im;
			if ($type == 'image/gif') {
				$im = imagecreatefromgif ( $link );
				header ( 'Content-Type: image/gif' );
				imagegif ( $im );
				imagedestroy ( $im );
			}
			if ($type == 'image/jpg' || $type == 'image/jpeg') {
				$im = imagecreatefromjpeg ( $link );
				header ( 'Content-Type: image/jpg' );
				echo imagejpeg ( $im );
				imagedestroy ( $im );
			}

			if ($type == 'image/png') {
				$im = imagecreatefrompng ( $link );
				header ( 'Content-Type: image/png' );
				echo imagepng ( $im );
				imagedestroy ( $im );
			}
			return $this->getResponse ();
		}
	}
	public function forgetPasswordAction() {
		$this->setLayoutLogin ();
		$userID = Util::getIDCurrentUser ();
		if ($userID) {
			$this->redirect ()->toRoute ( "qapolo" );
			return;
		}
		$recap = $this->getServiceLocator ()->get ( "reCaptchaService" );
		if ($_POST ['recaptcha_challenge_field'] && $_POST ['recaptcha_response_field']) {
			$result = $recap->verify ( $_POST ['recaptcha_challenge_field'], $_POST ['recaptcha_response_field'] );
			$email = $_POST ['email'];
			if (! $result->getStatus () || ! $email) {
				return array (
						"recap" => $recap,
						"status" => 'not_valid',
						"message" => "Thông tin bạn cung cấp không hợp lệ"
				);
			} else {
				$authMapper = new AuthMapper ();
				$user = $authMapper->checkRegistedByEmail ( $email );
				if ($user) {
					$mail = new Mail ();
					$vs = $mail->sendPasswordRecover ( $user->getEmail (), $user->getPass () . '' );
					var_dump ( $vs );
					return array (
							"recap" => $recap,
							'status' => 'email_exist',
							"message" => "Hệ thống đã gửi tới email (" . $email . ") thông tin đăng nhập vào QApolo! Thanks."
					);
				} else {
					return array (
							"recap" => $recap,
							'status' => 'email_not_exist',
							"message" => "Email không tồn tại trong hệ thống"
					);
				}
			}
		} else {
			return array (
					"recap" => $recap,
					"status" => 'new',
					"message" => ""
			);
		}
	}
	public function rankAction() {
		$this->setLayoutBasic ();
		$privilege = Util::isPrivilege ( $this );
		if ($privilege ['role'] == Authcfg::GUEST) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập để xem nội dung này!", 3000, "/user/login" );
		}
		$user = Util::getCurrentUser ();
		return array (
				'user' => $user
		);
	}
}