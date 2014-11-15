<?php

namespace FAQ\FAQCommon;

/**
 *
 * @author izzi
 *
 */
class FAQParaConfig {

	// linux
	// const dirLogFile = "/log/";
	// windown
	const dirLogFile = "E:\\";

	// status follow subject used in java script
	const statusFollow = "Đang theo dõi";
	const statusUnfollow = "Chưa theo dõi";
	const actionFollow = "Theo dõi";
	const actionUnfollow = "Hủy theo dõi";

	// sex
	const MALE = "Nam";
	const FEMALE = "Nữ";
	const MALEANDFEMALE = "Giới tính khác";
	// Entity Question
	/**
	 *
	 * @var Int
	 * @todo Status of the question when this has not finished yet
	 */
	const QUESTION_STATUS_DRAFT = 3;

	/**
	 *
	 * @var Int
	 * @todo Status of the question when this is opening
	 */
	const QUESTION_STATUS_OPEN = 5;

	/**
	 *
	 * @var Int
	 * @todo Status of the question when this has closed
	 */
	const QUESTION_STATUS_CLOSE = 7;
	/**
	 *
	 * @var Int
	 * @todo Status of the question when this has EXIST ANSWER IS BESTs
	 */
	const QUESTION_STATUS_EXIST_BEST = 11;

	/**
	 *
	 * @var Int
	 * @todo Status of the question when this has TEMP DELETE
	 */
	const QUESTION_STATUS_TEMP_DELETE = 2;
	/**
	 *
	 * @var Int
	 * @todo Status of the question when this has protect
	 */
	const QUESTION_STATUS_PROTECT = 13;
	/**
	 *
	 * @var Int
	 * @todo Status of the question when it is Privileges > Create Wiki Posts
	 */
	const QUESTION_STATUS_WIKI_POST = 17;

	/**
	 *
	 * @var Int
	 * @todo max total spam question
	 */
	const QUESTION_MAX_SPAM = 10;

	/**
	 *
	 * @var Int
	 * @todo QUESTION IS HOT
	 */
	const QUESTION_HOT = 1;
	/**
	 *
	 * @var Int
	 * @todo QUESTION IS NOT HOT
	 */
	const QUESTION_NOT_HOT = 0;
	/**
	 *
	 * @var Int
	 * @todo QUESTION IS highlight
	 */
	const QUESTION_HIGHLIGHT = 1;
	/**
	 *
	 * @var Int
	 * @todo QUESTION IS NOT highlight
	 */
	const QUESTION_NOT_HIGHLIGHT = 0;






	/**
	 *
	 * @var Int
	 * @todo Status of the all collection when this has active
	 */
	const STATUS_ACTIVE = 1;
	/**
	 *
	 * @var Int
	 * @todo Status of the all collection when this has active
	 */
	const STATUS_DEACTIVE = 0;
	/**
	 *
	 * @var Int
	 * @todo Status FOR SUBJECT IS META
	 */
	const SUBJECT_META = 2;


	const STATUS_TAG_APPROVE = 1;
	const STATUS_TAG_CREATE = 2;
	const STATUS_TAG_EDIT = 3;



	/**
	 *
	 * @var Int
	 * @todo type of function html2text is script
	 */
	const TYPE_TRIP_SCRIPT = 1;

	/**
	 *
	 * @var Int
	 * @todo type of function html2text is html tag
	 */
	const TYPE_TRIP_HTML = 3;

	/**
	 *
	 * @var Int
	 * @todo type of function html2text is html multiple STYLE
	 */
	const TYPE_TRIP_STYLE = 5;

	/**
	 *
	 * @var Int
	 * @todo type of function html2text is html multiple lines
	 */
	const TYPE_TRIP_LINE = 7;

	/**
	 *
	 * @tutorial : media is defined by following code
	 *
	 */
	const MEDIA_TYPE_FILE = 'file';
	const MEDIA_TYPE_IMAGE = 'image';
	const MEDIA_TYPE_VIDEO = 'video';
	const MEDIA_TYPE_VIDEO_LINK = 'video_link';
	const MEDIA_TYPE_IMAGE_LINK = 'image_link';
	const MEDIA_TYPE_DIR = 'dir';
	// mime type file allow
	public static $MEDIA_MIME_TYPE = array (
			"pdf" => "application/pdf",
			"latex" => "application/x-latex",
			"txt" => "application/vnd.oasis.opendocument.text", // : OpenDocument Text
			"txt" => "text/plain", // : OpenDocument Text
			"ods" => "application/vnd.oasis.opendocument.spreadsheet", // : OpenDocument Spreadsheet"
			"odp" => "application/vnd.oasis.opendocument.presentation", // : OpenDocument Presentation"
			"odg" => "application/vnd.oasis.opendocument.graphics", // : OpenDocument Graphics"/
			"xls" => "application/vnd.ms-excel", // : Microsoft Excel files
			"xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // : Microsoft Excel 2007 files
			"ppt" => "application/vnd.ms-powerpoint", // : Microsoft Powerpoint files
			"pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation", // : Microsoft Powerpoint 2007 files
			"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // : Microsoft Word 2007 files
			"doc" => "application/msword", // : Microsoft Word 2007 files
			"json" => "application/json",
			"js" => "application/javascript",
			"zip" => 'application/zip',
			"gzip" => 'application/gzip',
			"rar" => 'application/x-rar-compressed',
			"7z" => 'application/x-7z-compressed',
			"tar" => 'application/x-tar',
			"mp4" => "audio/mp4",
			"mpeg" => "audio/mpeg",
			"ogg" => "audio/ogg",
			"webm" => "audio/webm",
			"mp3" => "audio/mp3",
			"gif" => "image/gif",
			"png" => "image/jpeg",
			"jpeg" => "image/pjpeg",
			"PNG" => "image/png",
			"mpg" => "video/mpeg",
			"mp4" => "video/mp4",
			"ogg" => "video/ogg",
			"webm" => "video/webm",
			"wmv" => "video/x-ms-wmv",
			"flv" => "video/x-flv",
			"epub" => "application/epub+zip",
			"prc" => "application/x-mobipocket-ebook",
			"prc" => "application/x-mobipocket"
	);

	/**
	 *
	 * @tutorial : media status is defined by following code: deleted or normal
	 */
	const MEDIA_STATUS_DELETED = 0;
	const MEDIA_STATUS_NORMAL = 1;

	/**
	 *
	 * @var Int
	 * @todo Status the notify is unread
	 */
	const TYPE_NOTIFY_STATUS = 1;



	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is answer question, if this >100
	 *       action for artilce
	 */
	const TYPE_NOTIFY_ANSWER_QUESTION = 101;



	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is report question, if this >100
	 *       action for artilce
	 */
	const TYPE_NOTIFY_REPORT_QUESTION = 102;
	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is report ANSWER question, if this >100
	 *       action for artilce
	 */
	const TYPE_NOTIFY_REPORT_ANSWER = 103;



	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is unreport question, if this >100
	 *       action for artilce
	 */
	const TYPE_NOTIFY_UNREPORT_QUESTION = 103;



	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is add new question, if this >100
	 *       action for QUESTION
	 */
	const TYPE_NOTIFY_NEW_QUESTION = 104;

	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is add LIKE ANSWER, if this >100
	 *       action for QUESTION
	 */
	const TYPE_NOTIFY_LIKE_ANSWER_QUESTION = 105;

	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is add DISLIKE ANSWER, if this >100
	 *       action for QUESTION
	 */
	const TYPE_NOTIFY_DISLIKE_ANSWER_QUESTION = 106;

	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is add BEST ANSWER, if this >100
	 *       action for QUESTION
	 */
	const TYPE_NOTIFY_BEST_ANSWER_QUESTION = 107;

	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is add LIKE QUESTION, if this >100
	 *       action for QUESTION
	 */
	const TYPE_NOTIFY_LIKE_QUESTION = 108;

	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is add DISLIKE QUESTION, if this >100
	 *       action for QUESTION
	 */
	const TYPE_NOTIFY_DISLIKE_QUESTION = 109;
	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is add edit wiki style QUESTION, if this >100
	 *       action for QUESTION
	 */
	const TYPE_NOTIFY_EDIT_WIKISTYLE_QUESTION = 110;
	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is ACVTIVE CONTENT edit wiki style QUESTION, if this >100
	 *       action for QUESTION
	 */
	const TYPE_NOTIFY_ACTIVE_WIKISTYLE_QUESTION = 111;
	/**
	 *
	 * @var Int
	 * @todo type of action generate nofify is ACVTIVE CONTENT edit wiki style ANSWER, if this >100
	 *       action for QUESTION
	 */
	const TYPE_NOTIFY_ACTIVE_WIKISTYLE_ANSWER = 112;

	/**
	 *
	 * @var Int
	 * @todo type of collection news, it is about
	 */
	const NEWS_TYPE_ABOUT = 1;
	/**
	 *
	 * @var Int
	 * @todo type of collection news, it is help
	 */
	const NEWS_TYPE_HELP = 2;
	/**
	 *
	 * @var Int
	 * @todo type of collection news, it is term
	 */
	const NEWS_TYPE_TERM = 3;
	/**
	 *
	 * @var Int
	 * @todo type of collection news, it is community-guideline
	 */
	const NEWS_TYPE_COMMUNITY_GUIDELINE = 4;
	/**
	 *
	 * @var Int
	 * @todo type of collection news, it is scoring-system
	 */
	const NEWS_TYPE_SCORING_SYSTEM = 5;
	/**
	 *
	 * @var Int
	 * @todo type of collection news, it is sitemap
	 */
	const NEWS_TYPE_SITEMAP = 6;

	/**
	 * Location type
	 */
	const LOC_TYPE_DIADANH = 0;
	const LOC_TYPE_TINH = 1;
	const LOC_TYPE_HUYEN = 2;
	const LOC_TYPE_XA = 3;
	const LOC_TYPE_CONGTY = 20;
	const LOC_TYPE_BANNGANH = 30;
	const LOC_TYPE_BO = 31;
	const LOC_TYPE_SO = 32;
	const LOC_TYPE_TRUONGHOC = 40;
	const LOC_TYPE_CAP1 = 41;
	const LOC_TYPE_CAP2 = 42;
	const LOC_TYPE_CAP3 = 43;
	const LOC_TYPE_CAODANG = 44;
	const LOC_TYPE_DAIHOC = 45;
	const LOC_TYPE_QUOCGIA = 50;

	/**
	 * Location use
	 */
	public static $loc_use_noi_ht = array (
			40
	);
	public static $loc_use_noi_ct = array (
			20
	);
	public static $loc_use_diadanh = array (
			0,
			1,
			2,
			3,
			4,
			5,
			6,
			7
	);
	public static $loc_use_quocgia = array (
			50
	);
	public static function getLocDefault($loc_use) {
		if ($loc_use == 'loc_use_noi_ht') {
			return 40;
		}
		if ($loc_use == 'loc_use_noi_ct') {
			return 20;
		}
		if ($loc_use == 'loc_use_quocgia') {
			return 50;
		}
		if ($loc_use == 'loc_use_diadanh') {
			return 0;
		}
	}
	const DEFAULT_SUBJECT_ID = "52b1bd2e7eebacbf6b0000a5";
	const URL_BACK_LOGIN = "URL_BACK_LOGIN";
	const VOTE_MAX_TOTAL_PER_ONE_DAY = 30;
	const FLAG_MAX_TOTAL_PER_ONE_DAY = 30;
	const TYPE_VOTE_SPAM = 1;
	const TYPE_VOTE_UNSPAM = - 1;
	const IS_ADMIN_SPAM_STATUS_NOTACCESS = 0;
	const IS_ADMIN_SPAM_STATUS_ACCESS_SPAM = 1;
	const IS_ADMIN_SPAM_STATUS_ACCESS_NOTSPAM = 2;
	const IS_APPROVE_EDIT_QUESTION_NOTACCESS = 0;
	const IS_APPROVE_EDIT_QUESTION_ACCESS = 1;
	const IS_APPROVE_EDIT_ANSWER_NOTACCESS = 0;
	const IS_APPROVE_EDIT_ANSWER_ACCESS = 1;
	const VERIFIED = 1;
}
?>