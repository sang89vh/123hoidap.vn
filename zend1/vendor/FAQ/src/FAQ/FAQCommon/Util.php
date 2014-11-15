<?php

namespace FAQ\FAQCommon;

use MongoDate;
use FAQ\FAQEntity\User;
use FAQ\Mapper\UserMapper;
use MongoBinData;
use FAQ\Mapper\AuthMapper;
use Zend\Session\SessionManager;
use SocialAuth\Facebook;
use Exception;

class Util {
	public static $sm;
	private static $logger;
	public static $user_function_point;

	/**
	 *
	 * @param \Doctrine\ODM\MongoDB\Query\Builder $qb
	 * @param array $select
	 * @return \Doctrine\ODM\MongoDB\Query\Builder
	 */
	public static function selectField($qb, $select) {
		foreach ( $select as $key => $value ) {
			$qb = $qb->select ( $value );
		}

		return $qb;
	}

	/**
	 *
	 * @param \Doctrine\ODM\MongoDB\Query\Builder $qb
	 * @param array $orderBy
	 * @return \Doctrine\ODM\MongoDB\Query\Builder
	 */
	public static function addOrder($qb, $orderBy) {
		foreach ( $orderBy as $key => $value ) {
			$qb = $qb->sort ( $key, $value );
		}
		return $qb;
	}

	/**
	 *
	 * @todo not finished yet
	 * @param String $domain
	 * @param String $code
	 * @return String
	 * @tutorial get domain, code from FAQ\FAQCommon\FAQParaConfig
	 */
	public static function getStatus($domain, $code) {
		$status = "";

		return $status;
	}

	/**
	 *
	 * @return \Doctrine\ODM\MongoDB\Mapping\Annotations\Date
	 */
	public static function getCurrentTime() {
		return new MongoDate ();
	}

	/**
	 *
	 * @return \MongoDate
	 * @tutorial $today=getdate();
	 *           $userLike->setVoteDay(Util::createDate($today["mday"], $today["mon"], $today["year"]));
	 */
	public static function createDate($day, $month, $year, $hour = "00", $minute = "00", $second = "00") {
		return new MongoDate ( strtotime ( "$year-$month-$day $hour:$minute:$second" ) );
	}

	/**
	 *
	 * @param string $codeLocation
	 * @return string
	 * @tutorial dua theo chuan ISO 639-1 Code
	 * @link http://www.loc.gov/standards/iso639-2/php/code_list.php
	 */
	public static function getNationnality($codeLocation = "vi") {
		$nationnality = "Other";
		switch ($codeLocation) {
			case "vi" :
				$nationnality = "Viá»‡t Nam";
				break;
			case "us" :
				$nationnality = "United States of America";
				break;
		}
		return $nationnality;
	}

	/**
	 *
	 * @param $select array
	 * @todo get User from session
	 * @return User
	 */
	public static function getCurrentUser($select = null, $isHydrate = null) {

		// temporaly get user from db
		$userMapper = new UserMapper ();
		$user = $userMapper->getOneUser ( Util::getIDCurrentUser (), $select, $isHydrate );
		return $user;
	}

	/**
	 *
	 * @todo get User from session
	 * @return String
	 */
	public static function getIDCurrentUser() {
		$au = new AuthMapper ();
		$userID = $au->getSessionParam ( Sessioncfg::$user_id );
		return $userID;
	}

	/**
	 *
	 * @todo , generate password for user registratived by social network
	 * @return number
	 */
	public static function getRandomPassword() {
		$rand = rand ( 1234, 9999 );
		return $rand;
	}

	/**
	 *
	 * @todo convert to bin md5 data apply for password
	 * @param String $value
	 * @return \MongoBinData
	 */
	public static function toBinMd5($value) {
		$md5 = new MongoBinData ( $value, MongoBinData::MD5 );
		return $md5;
	}
	public static function getDialog($view, $title, $message) {
		$dialog = $view->partial ( '/util/dialog.phtml' );

		$dialog = str_replace ( "@faq_title@", $title, $dialog );
		$dialog = str_replace ( "@message@", $message, $dialog );
		return $dialog;
	}

	/**
	 *
	 * @param unknown $message
	 * @param Int $prioritie
	 * @tutorial \Zend\Log\Logger::EMERG = 0; // Emergency: system is unusable <br/>
	 *           ALERT = 1; // Alert: action must be taken immediately <br/>
	 *           CRIT = 2; // Critical: critical conditions <br/>
	 *           ERR = 3; // Error: error conditions <br/>
	 *           WARN = 4; // Warning: warning conditions <br/>
	 *           NOTICE = 5; // Notice: normal but significant condition <br/>
	 *           INFO = 6; // Informational: informational messages <br/>
	 *           DEBUG = 7; // Debug: debug messages <br/>
	 */
	public static function writeLog($message, $prioritie = 3) {
		if (! Util::$logger) {
			$writer = new \Zend\Log\Writer\Stream ( FAQParaConfig::dirLogFile . date ( 'Ymd' ) . '_app_log.log' );
			Util::$logger = new \Zend\Log\Logger ();
			Util::$logger->addWriter ( $writer );
		}
		Util::$logger->log ( \Zend\Log\Logger::EMERG, $message );
	}
	public static function bootboxAlert($message) {
		echo "<script type='text/javascript'>
                bootbox.alert('" . $message . "');
             </script>";
	}
	public static function bootstrapAlert($message) {
		if (! empty ( $message )) {
			return "<div class='alert alert-danger'>'.$message.'</div>";
		} else {
			return "";
		}
	}

	/**
	 *
	 * @param String $key
	 * @param String $value
	 */
	public static function setSessionParam($key, $value) {
		$sm = Util::$sm;
		$sm->getStorage ()->setMetadata ( $key, $value );
	}

	/**
	 *
	 * @todo get value stored in session by key
	 * @param String $key
	 */
	public static function getSessionParam($key) {
		$sm = Util::$sm;
		return $sm->getStorage ()->getMetadata ( $key );
	}

	/**
	 *
	 * @todo delete session by key
	 * @param String $key
	 */
	public static function clearSessionParam($key) {
		$sm = new SessionManager ();
		$sm->start ();
		return $sm->getStorage ()->setMetadata ( $key, null );
		// return $sm->getStorage()->clear($key);
	}

	/**
	 *
	 * @param String $document
	 * @param String $type
	 * @return String
	 * @tutorial if type equals html and script then <br/>
	 *           $type=FAQParaConfig::TYPE_TRIP_HTML*FAQParaConfig::TYPE_TRIP_SCRIPT
	 */
	public static function html2txt($document, $type) {
		$search = array ();
		if ($type % FAQParaConfig::TYPE_TRIP_SCRIPT == 0) {
			array_push ( $search, '@<script[^>]*?>.*?</script>@si' );
		}
		if ($type % FAQParaConfig::TYPE_TRIP_HTML == 0) {
			array_push ( $search, '@<[\/\!]*?[^<>]*?>@si' );
		}
		if ($type % FAQParaConfig::TYPE_TRIP_STYLE == 0) {
			array_push ( $search, '@<style[^>]*?>.*?</style>@siU' );
		}
		if ($type % FAQParaConfig::TYPE_TRIP_LINE == 0) {
			array_push ( $search, '@<![\s\S]*?--[ \t\n\r]*>@' );
		}

		$text = preg_replace ( $search, '', $document );
		return $text;
	}
	public static function checkOwnerData($document) {
		return Util::getIDCurrentUser () == $document->getCreateBy ()->getId ();
	}

	/**
	 *
	 * @param String $str
	 * @return String
	 * @todo conver unicode to cp11
	 */
	public static function covertUnicode($str) {
		$str = strtolower ( $str );
		$unicode = array (
				'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
				'd' => 'đ',
				'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
				'i' => 'í|ì|ỉ|ĩ|ị',
				'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
				'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
				'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
				'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
				'D' => 'Đ',
				'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
				'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
				'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
				'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
				'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
		);
		foreach ( $unicode as $nonUnicode => $uni ) {
			$str = preg_replace ( "/($uni)/i", $nonUnicode, $str );
		}
		return $str;
	}

	/**
	 *
	 * @param User $user
	 */
	public static function getUserName($user) {
		$name = $user->getFirstName () . " " . $user->getLastName ();
		return $name;
	}

	/**
	 *
	 * @param User $user
	 */
	public static function getUserNameSeo($user) {
		if (! empty ( $user )) {
			$name = $user->getFirstName () . " " . $user->getLastName ();
			$name = Util::convertUrlSeo ( $name );
		}
		return $name;
	}

	/**
	 *
	 * @param User $user
	 */
	public static function getUserRankText($user) {
		$rankText = "";
		if ($user->getRank ()->first ()) {
			$rankText = $user->getRank ()->first ()->getName ();
		}
		return $rankText;
	}

	/**
	 *
	 * @param User $user
	 */
	public static function getUserRank($user) {
		return $user->getRank ();
	}

	/**
	 *
	 * @param User $user
	 */
	public static function getUserAppellationText($user) {
		$appellation = "";
		$isFirst = true;
		if ($user->getAppellation ()) {
			foreach ( $user->getAppellation () as $k => $v ) {
				/* @var $v \FAQ\FAQEntity\Appellation */
				if ($isFirst) {
					$appellation = $v->getSubject ()->getTitle () . ': ' . $v->getRank ();
				} else {
					$appellation = $appellation . ', ' . $v->getSubject ()->getTitle ();
				}
				$isFirst = false;
			}
		}
		return $appellation;
	}

	/**
	 *
	 * @param \FAQ\FAQCommon\FAQAbstractActionController $controller
	 * @param Int $point
	 * @tutorial ex: Authcfg::ACCESS_REVIEW_QUEUES
	 *           <br> isAllowed
	 *           <br> role
	 *           <br> privilegeByPoint
	 *           <br> totalRankPoint
	 * @return boolean
	 */
	public static function isPrivilege($controller, $point = 0) {
		$acl = $controller->getServiceLocator ()->get ( "FAQAcl" );
		$currentUserID = Util::getIDCurrentUser ();
		$totalRankPoint = - 1;
		$isPrivilegeByPoint = false;
		if (! empty ( $currentUserID )) {
			$currentUser = Util::getCurrentUser ();
			$totalRankPoint = $currentUser->getTotalRankPoint ();
			// var_dump(!empty($point),$point>=$totalRankPoint,"p:".$point,$totalRankPoint);
			if (! empty ( $point ) && $totalRankPoint >= $point) {
				$isPrivilegeByPoint = true;
			}
			$role = $currentUser->getRoleCode ();
		} else {

			$role = Authcfg::GUEST;
		}
		$isAllowed = $acl->isAllowed ( $role, $controller->params ( "controller" ), $controller->params ( "action" ) );
		// $isAllowed = true; // temp;
		$data = array (
				"isAllowed" => $isAllowed,
				"role" => $role,
				"totalRankPoint" => $totalRankPoint,
				"privilegeByPoint" => $isPrivilegeByPoint
		);
		return $data;
	}

	/**
	 *
	 * @todo alert fancybox login page
	 */
	public static function fancyboxLogin() {
		echo '<script type="text/javascript">
redirectLogin();
</script>';
	}

	/**
	 *
	 * @todo alert fancybox login page
	 */
	public static function redirectLogin() {
		echo '<script type="text/javascript">
redirectLogin();
</script>';
	}

	/**
	 *
	 * @author sang
	 * @todo extract img from html
	 * @param String $html
	 * @return array imahe
	 */
	public static function extractImage($html) {
		preg_match ( '/<img[^>]+>/i', $html, $matches );
		return $matches;
	}

	/**
	 *
	 * @author sang
	 * @todo extract img from question, get first image
	 * @param Question $question
	 * @return String
	 */
	public static function getFirstImageQuestion($question) {
		$img = "";

		$subjectQuestion = $question->getSubject ();
		if (! empty ( $subjectQuestion )) {
			$titleSubject = $subjectQuestion->getTitle ();
			$avatarSubject = $subjectQuestion->getAvatar ();
			$subjectAvatarID = $avatarSubject->getId ();

			$contentTypeSubject = $avatarSubject->getContentType ();

			$extentionFileSubject = Util::getTypeFile ( $contentTypeSubject );
			$titleFileSeoSubject = Util::convertUrlFileName ( $titleSubject, $extentionFileSubject );

			$content = $question->getContent ();

			$img = "";
			$imgs = Util::extractImage ( $content );
			if (count ( $imgs ) > 0) {
				foreach ( $imgs as $im ) {
					$img = $img . $im;
				}
			} else {
				$img = '<img src="/media/get-image/images/' . $subjectAvatarID . '/' . $titleFileSeoSubject . '" alt="' . $titleSubject . '" title="' . $titleSubject . '">';
			}
		}
		return $img;
	}

	/**
	 *
	 * @author izzi
	 * @todo extract img from html
	 * @param String $html
	 * @return array imahe
	 */
	public static function extractAllImage($html) {
		preg_match_all ( '/<img[^>]+>/i', $html, $matches );
		return $matches;
	}

	/**
	 *
	 * @author sang
	 * @todo covert title question, to url to seo
	 * @param String $title
	 * @return String
	 */
	public static function convertUrlSeo($title) {
		$url = trim ( Util::covertUnicode ( $title ) );
		$url = str_replace ( "-", " ", $url );
		$url = preg_replace ( '!\s+!', ' ', $url );
		$url = str_replace ( " ", "-", $url );
		$url = preg_replace ( '/[^0-9a-zA-Z-]/', '', $url );
		return strtolower ( urlencode ( $url ) . ".html" );
	}

	/**
	 *
	 * @author sang
	 * @todo conver title file to url to seo
	 * @param String $title
	 * @return String
	 */
	public static function convertUrlFileName($fileName, $extention) {
		$url = trim ( Util::covertUnicode ( $fileName ) );
		$url = str_replace ( "-", " ", $url );
		$url = preg_replace ( '!\s+!', ' ', $url );
		$url = str_replace ( " ", "-", $url );
		$url = preg_replace ( '/[^0-9a-zA-Z-]/', '', $url );
		if (! empty ( $extention )) {
			$url = urlencode ( $url ) . "." . $extention;
		} else {
			$url = urlencode ( $url );
		}
		return strtolower ( $url );
	}

	/**
	 *
	 * @link http://www.pontikis.net/blog/auto_post_on_facebook_with_php
	 * @param array $params
	 *        	<br>
	 *        	= array( <br>
	 *        	// this is the main access token (facebook profile) <br>
	 *        	"access_token" => "CAACYHYyWcnIBAJ1LwRLTERQNXJ4qRCqoTf2pgs1V2AiZCOreWAH0bY2UKaoyD3elfcEZAZAs6fZAEYPAZC5OlU6ZCg8Org32D13LiencxZA0PsNzkQPPuZCiZAukgMnqLXM0F2ZBlYZAnZB08qVwOo6DgiCJSlkoZCb9VVs0ARsleZBcduzTQKZBvIcvjLZBXXr5ye8R4FcZD", <br>
	 *        	"message" => "Here is a blog post about auto posting on Facebook using PHP #php #facebook", <br>
	 *        	"link" => "http://www.pontikis.net/blog/auto_post_on_facebook_with_php", <br>
	 *        	"picture" => "http://i.imgur.com/lHkOsiH.png", <br>
	 *        	"name" => "How to Auto Post on Facebook with PHP", <br>
	 *        	"caption" => "www.pontikis.net", <br>
	 *        	"description" => "Automatically post on Facebook with PHP using Facebook PHP SDK. How to create a Facebook app. Obtain and extend Facebook access tokens. Cron automation." <br>
	 *        	); <br>
	 */
	public static function shareLink($params) {
		$config = array ();
		$config ['appId'] = Authcfg::$facebook_app_id;
		$config ['secret'] = Authcfg::$facebook_app_secret;
		$config ['fileUpload'] = false; // optional
		$fb = new Facebook ( $config );
		$params = array (
				// this is the main access token (facebook profile)
				"access_token" => "CAACYHYyWcnIBAJ1LwRLTERQNXJ4qRCqoTf2pgs1V2AiZCOreWAH0bY2UKaoyD3elfcEZAZAs6fZAEYPAZC5OlU6ZCg8Org32D13LiencxZA0PsNzkQPPuZCiZAukgMnqLXM0F2ZBlYZAnZB08qVwOo6DgiCJSlkoZCb9VVs0ARsleZBcduzTQKZBvIcvjLZBXXr5ye8R4FcZD",
				"message" => "Here is a blog post about auto posting on Facebook using PHP #php #facebook",
				"link" => "http://www.pontikis.net/blog/auto_post_on_facebook_with_php",
				"picture" => "http://i.imgur.com/lHkOsiH.png",
				"name" => "How to Auto Post on Facebook with PHP",
				"caption" => "www.pontikis.net",
				"description" => "Automatically post on Facebook with PHP using Facebook PHP SDK. How to create a Facebook app. Obtain and extend Facebook access tokens. Cron automation."
		);

		try {
			$ret = $fb->api ( '/me/feed', 'POST', $params );
			echo 'Successfully posted to Facebook Personal Profile';
		} catch ( \Exception $e ) {
			echo $e->getMessage ();
		}
	}

	/**
	 *
	 * @link http://www.pontikis.net/blog/auto_post_on_facebook_with_php
	 * @param array $params
	 *        	<br>
	 *        	$params = array(<br>
	 *        	// this is the access token for Fan Page<br>
	 *        	"access_token" => "CAACYHYyWcnIBAMK1y2tqiRKx8bBXGFFzjdUamOlMZCBJrTSL8ic1z5sZBarBi3DbTh9mMUz3aiZCAQRNHvOmcMxLZC53FNtkrVCq8rZCLsyjbQVZAt8o7S6Rd1UT0LK7AkgyZAlu11MC9rWND8eZBiKjjiYjwmBMLWko7k6GGPZCREehKRNFCsyM1Ll7sFb1hXycZD",<br>
	 *        	"message" => "Here is a blog post about auto posting on Facebook using PHP #php #facebook",<br>
	 *        	"link" => "http://www.pontikis.net/blog/auto_post_on_facebook_with_php",<br>
	 *        	"picture" => "http://i.imgur.com/lHkOsiH.png",<br>
	 *        	"name" => "How to Auto Post on Facebook with PHP",<br>
	 *        	"caption" => "www.pontikis.net",<br>
	 *        	"description" => "Automatically post on Facebook with PHP using Facebook PHP SDK. How to create a Facebook app. Obtain and extend Facebook access tokens. Cron automation."<br>
	 *        	);
	 */
	public static function postToFanpage($params) {
		$config = array ();
		$config ['appId'] = Authcfg::$facebook_app_id;
		$config ['secret'] = Authcfg::$facebook_app_secret;
		$config ['fileUpload'] = false; // optional
		$fb = new Facebook ( $config );

		$params = array (
				// this is the access token for Fan Page
				"access_token" => "CAACYHYyWcnIBAMK1y2tqiRKx8bBXGFFzjdUamOlMZCBJrTSL8ic1z5sZBarBi3DbTh9mMUz3aiZCAQRNHvOmcMxLZC53FNtkrVCq8rZCLsyjbQVZAt8o7S6Rd1UT0LK7AkgyZAlu11MC9rWND8eZBiKjjiYjwmBMLWko7k6GGPZCREehKRNFCsyM1Ll7sFb1hXycZD",
				"message" => "Here is a blog post about auto posting on Facebook using PHP #php #facebook",
				"link" => "http://www.pontikis.net/blog/auto_post_on_facebook_with_php",
				"picture" => "http://i.imgur.com/lHkOsiH.png",
				"name" => "How to Auto Post on Facebook with PHP",
				"caption" => "www.pontikis.net",
				"description" => "Automatically post on Facebook with PHP using Facebook PHP SDK. How to create a Facebook app. Obtain and extend Facebook access tokens. Cron automation."
		);

		try {
			// Write a long description for your Page

			// http://123hoidap.vn
			// Facebook Page ID 494078664023150
			$ret = $fb->api ( '/494078664023150/feed', 'POST', $params );
			echo 'Successfully posted to Facebook Fan Page';
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}

	/**
	 *
	 * @param String $contentType
	 * @return mixed the key is type of file for needle if it is found in the array, false otherwise.
	 */
	public static function getTypeFile($contentType) {
		$type = array_search ( $contentType, FAQParaConfig::$MEDIA_MIME_TYPE );
		return strtolower ( $type );
	}

	/**
	 * Truncates text.
	 *
	 * Cuts a string to the length of $length and replaces the last characters
	 * with the ending if the text is longer than length.
	 *
	 * @param string $text
	 *        	to truncate.
	 * @param integer $length
	 *        	Length of returned string, including ellipsis.
	 * @param string $ending
	 *        	Ending to be appended to the trimmed string.
	 * @param boolean $exact
	 *        	If false, $text will not be cut mid-word
	 * @param boolean $considerHtml
	 *        	If true, HTML tags would be handled correctly
	 * @return string Trimmed string.
	 */
	public static function truncate($text, $length = 100, $ending = '…', $exact = true, $considerHtml = false) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen ( preg_replace ( '/<.*?>/', '', $text ) ) <= $length) {
				return $text;
			}

			// splits all html-tags to scanable lines
			preg_match_all ( '/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER );

			$total_length = strlen ( $ending );
			$open_tags = array ();
			$truncate = '';

			foreach ( $lines as $line_matchings ) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (! empty ( $line_matchings [1] )) {
					// if it's an “empty element'' with or without xhtml-conform closing slash (f.e.)

					if (preg_match ( '/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings [1] )) {
						// do nothing
						// if tag is a closing tag (f.e. )
					} else if (preg_match ( '/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings [1], $tag_matchings )) {
						// delete tag from $open_tags list
						$pos = array_search ( $tag_matchings [1], $open_tags );
						if ($pos !== false) {
							unset ( $open_tags [$pos] );
						}
						// if tag is an opening tag (f.e. )
					} else if (preg_match ( '/^<\s*([^\s>!]+).*?>$/s', $line_matchings [1], $tag_matchings )) {
						// add tag to the beginning of $open_tags list
						array_unshift ( $open_tags, strtolower ( $tag_matchings [1] ) );
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings [1];
				}

				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen ( preg_replace ( '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings [2] ) );
				if ($total_length + $content_length > $length) {
					// the number of characters which are left
					// $left = $length – $total_length;
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all ( '/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings [2], $entities, PREG_OFFSET_CAPTURE )) {
						// calculate the real length of all entities in the legal range
						foreach ( $entities [0] as $entity ) {
							if ($entity [1] + 1 - $entities_length <= $left) {
								$left --;
								$entities_length += strlen ( $entity [0] );
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= mb_substr ( $line_matchings [2], 0, $left + $entities_length, 'UTF-8' );
					// maximum lenght is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings [2];
					$total_length += $content_length;
				}

				// if the maximum length is reached, get off the loop
				if ($total_length >= $length) {
					break;
				}
			}
		} else {
			if (strlen ( $text ) <= $length) {
				return $text;
			} else {
				$truncate = mb_substr ( $text, 0, $length - strlen ( $ending ), 'UTF-8' );
			}
		}

		// if the words shouldn't be cut in the middle...
		if (! $exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos ( $truncate, ' ' );
			if (isset ( $spacepos )) {
				// ...and cut the text in this position
				$truncate = mb_substr ( $truncate, 0, $spacepos, 'UTF-8' );
			}
		}

		// add the defined ending to the text
		$truncate .= $ending;

		if ($considerHtml) {
			// close all unclosed html-tags
			foreach ( $open_tags as $tag ) {
				$truncate .= '';
			}
		}

		return $truncate;
	}

	/**
	 *
	 * @todo out error message when run with debug mode
	 * @param Exception $e
	 */
	public static function debug($e) {
		if (Appcfg::$debug == 2 || Appcfg::$debug == 1) {
			ChromePhp::log ( $e->getFile () . ',' . $e->getLine () . '--->' . $e->getMessage () );
		}
		if (Appcfg::$debug == 3) {
			var_dump ( $e->getFile () . ',' . $e->getLine () . '--->' . $e->getMessage () );
			return;
		}
		if (Appcfg::$debug == 4) {
			var_dump ( $e->getFile () . ',' . $e->getLine () );
			var_dump ( $e->getMessage () );
			var_dump ( $e->getTrace () );
			return;
		}
	}

	/**
	 *
	 * @param \Zend\Mvc\MvcEvent $evt
	 */
	public static function infoController($evt) {
		if (Appcfg::$debug == 2 || Appcfg::$debug == 3 || Appcfg::$debug == 4) {
			var_dump ( $evt->getRouteMatch ()->getParam ( 'controller', null ) . '-->' . $evt->getRouteMatch ()->getParam ( 'action', null ) );
		}
		if (Appcfg::$debug == 1) {
			ChromePhp::log ( $evt->getRouteMatch ()->getParam ( 'controller', null ) . '-->' . $evt->getRouteMatch ()->getParam ( 'action', null ) );
		}
	}
	public static function diff($old, $new) {
		foreach ( $old as $oindex => $ovalue ) {
			$nkeys = array_keys ( $new, $ovalue );
			foreach ( $nkeys as $nindex ) {
				$matrix [$oindex] [$nindex] = isset ( $matrix [$oindex - 1] [$nindex - 1] ) ? $matrix [$oindex - 1] [$nindex - 1] + 1 : 1;
				if ($matrix [$oindex] [$nindex] > $maxlen) {
					$maxlen = $matrix [$oindex] [$nindex];
					$omax = $oindex + 1 - $maxlen;
					$nmax = $nindex + 1 - $maxlen;
				}
			}
		}
		if ($maxlen == 0)
			return array (
					array (
							'd' => $old,
							'i' => $new
					)
			);
		return array_merge ( Util::diff ( array_slice ( $old, 0, $omax ), array_slice ( $new, 0, $nmax ) ), array_slice ( $new, $nmax, $maxlen ), Util::diff ( array_slice ( $old, $omax + $maxlen ), array_slice ( $new, $nmax + $maxlen ) ) );
	}
	/**
	 *
	 * @author sang
	 * @param
	 *        	String html $old
	 * @param
	 *        	String html $new
	 * @return string
	 */
	public static function htmlDiff($old, $new) {
		$diff = Util::diff ( explode ( ' ', $old ), explode ( ' ', $new ) );
		foreach ( $diff as $k ) {
			if (is_array ( $k ))
				$ret .= (! empty ( $k ['d'] ) ? "<del>" . implode ( ' ', $k ['d'] ) . "</del> " : '') . (! empty ( $k ['i'] ) ? "<ins>" . implode ( ' ', $k ['i'] ) . "</ins> " : '');
			else
				$ret .= $k . ' ';
		}
		return $ret;
	}
	/**
	 *
	 * @author sang
	 * @param unknown $file_name
	 * @return string
	 */
	public static function get_extension($file_name) {
		$ext = explode ( '.', $file_name );
		$ext = array_pop ( $ext );
		return strtolower ( $ext );
	}

	/**
	 *
	 * @author sang
	 * @param String $title
	 * @return String
	 */
	public static function convertUrlNameFile($title) {
		$url = Util::covertUnicode ( $title );
		$url = preg_replace ( '/\s+/', '-', $url );
		$url = preg_replace ( '/[^0-9a-zA-Z-.]/', '', $url );

		return strtolower ( urlencode ( $url ) );
	}
	/**
	 *
	 * @author sang
	 * @param String $inputTag
	 * @return String
	 */
	public static function convertToTag($inputTag) {
		$url = Util::covertUnicode ( $inputTag );
		$url = preg_replace ( '/\s+/', '-', $url );
		$url = preg_replace ( '/[^0-9a-zA-Z-.]/', '', $url );
		$url = substr ( $url, 0, 50 );
		return strtolower ( urlencode ( $url ) );
	}

	/**
	 *
	 * @param
	 *        	$id
	 * @todo Find user by id
	 * @return User
	 */
	public static function findUserById($id) {
		$userMapper = new UserMapper ();
		$user = $userMapper->getOneUser ( $id );
		return $user;
	}
}

?>