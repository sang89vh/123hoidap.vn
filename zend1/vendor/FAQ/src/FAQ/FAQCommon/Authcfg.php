<?php

namespace FAQ\FAQCommon;

/**
 *
 * @author izzi
 *
 */
class Authcfg {

	// openid code
	public static $facebook = 'FACEBOOK';
	public static $twitter = 'TWITTER';
	public static $google_plus = 'GOOGLEPLUS';
	public static $zing_me = "ZINGME";
	public static $yahoo = "YAHOO";

	// facebook app info
	public static $facebook_app_id = "599267310165150";
	public static $facebook_app_secret = "dde07630a2ea6beb4336ceb0390179bd";
	public static $facebook_redirect_url = "http://123hoidap.vn/user/auth?";
	public static $facebook_scope = "email";

	// twitter app info
	public static $twitter_consumer_key = "E4sHwB0Pm5U4qHD31xA9jQ";
	public static $twitter_consumer_secret = "dCCHKgLlFJTuxB7DGk2hHs5lse3u8tTeLWQ3oruo";
	public static $twitter_access_token_key = "348948693-43AfdDKt2gK7aw3s8Lin7ncmj2MebNO3ZelDvsy6";
	public static $twitter_access_token_secret = "jkKh8BH9bw2ME8zIf1x9cBMUuzM2h0gcpyUekYxIiE";
	public static $twitter_url_callback = "http://123hoidap.vn/user/auth?";

	// role
	const GUEST = 'GUEST';
	const MEMBER = 'MEMBER';
	const MODERATOR = 'MODERATOR';
	const SUPPORT = 'SUPPORT';
	const ADMIN = 'ADMIN';
	const SUPPERADMIN = 'SUPPER_ADMIN';


	/**
	 *
	 * @var Expanded editing, deletion and undeletion privileges
	 */
	const TRUSTED_USER = 20000;
	/**
	 *
	 * @var set highlight question
	 */
	const SET_HIGHLIGHT = 18000;
	/**
	 *
	 * @var Mark questions as protected
	 */
	const PROTECT_QUESTIONS = 15000;
	/**
	 *
	 * @var Handle flags, access reports, delete questions
	 */
	const ACCESS_TO_MODERATOR_TOOLS = 10000;
	/**
	 *
	 * @var Approve edits to tag wikis made by regular users
	 */
	const APPROVE_TAG_WIKI_EDITS = 5000;
	/**
	 *
	 * @var Help decide whether posts are off-topic or duplicates
	 */
	const CAST_CLOSE_AND_REOPEN_VOTES = 3000;
	/**
	 *
	 * @var Decide which tags have the same meaning as others
	 */
	const CREATE_TAG_SYNONYMS = 2500;
	/**
	 *
	 * @var Edits to any question or answer are applied immediately
	 */
	const EDIT_QUESTIONS_AND_ANSWERS = 2000;
	/**
	 *
	 * @var Add new tags to the site
	 */
	const CREATE_TAGS = 1500;
	/**
	 *
	 * @var Create chat rooms where only specific users may talk
	 */
	const CREATE_GALLERY_CHAT_ROOMS = 1000;
	/**
	 *
	 * @var You've been around for a while; see vote counts
	 */
	const ESTABLISHED_USER = 1000;
	/**
	 *
	 * @var Access first posts and late answers review queues
	 */
	const ACCESS_REVIEW_QUEUES = 500;
	/**
	 *
	 * @var View and cast close/reopen votes on your own questions
	 */
	const VIEW_CLOSE_VOTES = 250;
	/**
	 *
	 * @var Some ads are now automatically disabled
	 */
	const REDUCE_ADS = 200;
	/**
	 *
	 * @var Indicate when questions and answers are not useful
	 */
	const VOTE_DOWN = 125;
	/**
	 *
	 * @var Create new chat rooms
	 */
	const CREATE_CHAT_ROOMS = 100;
	/**
	 *
	 * @var Collaborate on the editing and improvement of wiki posts
	 */
	const EDIT_COMMUNITY_WIKI = 100;
	/**
	 *
	 * @var Offer some of your reputation as bounty on a question
	 */
	const SET_BOUNTIES = 75;
	/**
	 *
	 * @var Leave comments on other people's posts
	 */
	const COMMENT_EVERYWHERE = 50;
	/**
	 *
	 * @var Participate in this site's chat rooms
	 */
	const TALK_IN_CHAT = 20;
	/**
	 *
	 * @var Bring content to the attention of the community via flags
	 */
	const FLAG_POSTS = 15;
	/**
	 *
	 * @var Indicate when questions and answers are useful
	 */
	const VOTE_UP = 15;
	/**
	 *
	 * @var Create answers that can be easily edited by most users
	 */
	const CREATE_WIKI_POSTS = 10;
	/**
	 *
	 * @var Post more links, answer protected questions
	 */
	const REMOVE_NEW_USER_RESTRICTIONS = 10;
	/**
	 *
	 * @var Discuss the site itself: bugs, feedback, and governance
	 */
	const PARTICIPATE_IN_META = 5;

	/**
	 *
	 * @var Ask a question or contribute an answer
	 */
	const CREATE_POSTS = 1;
}
?>