<?php

namespace FAQ\FAQCommon;

class Usercfg {
	public static $email = "email";
	public static $password = "pass";
	public static $openid_code = "openid.code";
	public static $openid_userid = "openid.user_id";

	/**
	 * ------------------------------------------------- moneying, level calculator ------------------------
	 */

	// moneying calculator
	// const money_user_following = 0; // người được follow
	// const money_user_unfollowing = 0; // người bị bỏ follow
	const money_user_registrator = 5000; // mới đăng ký vào hệ thống
// level calculator
	const rank_user_following = 1;
	const rank_user_unfollowing = - 1;
	const rank_user_registrator = 1;


	/**
	 * You gain reputation when:
	 *
	 * question is voted up: +5
	 * answer is voted up: +10
	 * answer is marked “accepted”: +15 (+2 to acceptor)
	 * suggested edit is accepted: +2 (up to +1000 total per user)
	 * bounty awarded to your answer: +full bounty amount
	 * one of your answers is awarded a bounty automatically: +1/2 of the bounty amount (see more details about how bounties work)
	 * site association bonus: +100 on each site (awarded a maximum of one time per site)
	 * If you are an experienced Stack Exchange network user with 200 or more reputation on at least one site, you will receive a starting +100 reputation bonus to get you past basic new user restrictions. This will happen automatically on all current Stack Exchange sites where you have an account, and on any other Stack Exchange sites at the time you log in.
	 *
	 * You lose reputation when:
	 *
	 * your question is voted down: −2
	 * your answer is voted down: −2
	 * you vote down an answer: −1
	 * you place a bounty on a question: −full bounty amount
	 * one of your posts receives 6 spam or offensive flags:−100
	 */
	// const rank_question_create = 0;
	// const rank_question_deleted_by_you = - 5;
	const rank_question_deleted_by_admin = - 100;
	// const rank_question_answer_deleted_by_you = - 5;
	const rank_question_answer_deleted_by_admin = - 100;

	// const rank_question_voted = 10;
// 	const rank_question_closed = - 5;

	// const rank_question_unvoted = -10;

	// const rank_question_spam = 0;

	// const rank_question_unspam = 0;

	// const rank_question_share = 0;
	// const rank_question_unshare = 0;
// 	const rank_question_follow = 4;
// 	const rank_question_unfollow = - 4;

	// Upvotes on an answer give the answerer +10 reputation.
	const rank_question_answer_like = 10;
	// Downvotes on answers remove 1 reputation from you, the voter.
	// your answer is voted down: −2
	// you vote down an answer: −1
	const rank_question_answer_dislike = - 2;
	const rank_question_answer_dislike_uservote = - 1;
	// Upvotes on a question give the asker +5 reputation.
	const rank_question_like = 5;
	// Downvotes remove 2 reputation from the post owner.
	const rank_question_dislike = - 2;
	const rank_question_dislike_uservote = - 1;

	// answer is marked “accepted”: +15 (+2 to acceptor)
	const rank_question_vote_best_answer = 15;
	const rank_question_vote_best_answer_acceptor = 2;

	/**
	 * ----------------------------- user rank ----------------------------------
	 */
	const user_rank_new_text = 'New';
	const user_rank_new_min = 0;
	const user_rank_new_max = 70;
	const user_rank_junior_text = 'Junior';
	const user_rank_junior_min = 70;
	const user_rank_junior_max = 280;
	const user_rank_senior_text = 'Senior';
	const user_rank_senior_min = 280;
	const user_rank_senior_max = 1120;
	const user_rank_expert_text = 'Expert';
	const user_rank_expert_min = 1120;
	const user_rank_expert_max = 4480;
	const user_rank_guru_text = "Guru";
	const user_rank_guru_min = 4480;

	/**
	 * --------------------------------- user subject rank ---------------------------
	 */
	const user_subject_rank_new_text = 'New';
	const user_subject_rank_new_min = 0;
	const user_subject_rank_new_max = 70;
	const user_subject_rank_junior_text = 'Junior';
	const user_subject_rank_junior_min = 70;
	const user_subject_rank_junior_max = 280;
	const user_subject_rank_senior_text = 'Senior';
	const user_subject_rank_senior_min = 280;
	const user_subject_rank_senior_max = 1120;
	const user_subject_rank_expert_text = 'Expert';
	const user_subject_rank_expert_min = 1120;
	const user_subject_rank_expert_max = 4480;
	const user_subject_rank_guru_text = "Guru";
	const user_subject_rank_guru_min = 4480;

	/**
	 * -------------------------------- user status ----------------------------
	 */
	const user_status_active = 1;
	const user_status_email_ok = 2;
	const user_status_email_missing = 3;
	const user_status_close = 11;
	// Status ACTIVE equals status less or equal 10, <=10
	const USER_STATUS_CURRENT_ACTIVE = 'ACTIVE';
	// Status DEACTIVE equals status greater 10, >10
	const USER_STATUS_CURRENT_DEACTIVE = 'DEACTIVE';

	//action bonus rank point
	const  QUESTION_VOTE_UP="vote thích câu hỏi";
	const  QUESTION_UNVOTE_UP="Hủy vote thích câu hỏi";

	const  QUESTION_VOTE_DOWN="vote không thích câu hỏi";
	const  QUESTION_UNVOTE_DOWN="Hủy vote không thích câu hỏi";
	const  QUESTION_ACTIVE_WIKI_EDIT="Chấp nhận bản sửa đổi câu hỏi";
	//VI PHẠM
	const  QUESTION_CLOSE="Đóng câu hỏi";
	const  QUESTION_REOPEN="Mở câu hỏi";

	const  ANSWER_VOTE_UP="vote thích câu trả lời";
	const  ANSWER_UNVOTE_UP="Hủy vote thích câu trả lời";

	const  ANSWER_VOTE_DOWN="vote không thích câu trả lời";
	const  ANSWER_UNVOTE_DOWN="Hủy vote không thích câu trả lời";
	const  ANSWER_ACTIVE_WIKI_EDIT="Chấp nhận bản sửa đổi câu trả lời";
	//VI PHẠM
	const  ANSWER_CLOSE="Đóng câu hỏi";
	const  ANSWER_REOPEN="Mở câu hỏi";

	const  ANSWER_VOTE_BEST="Bình chọn câu trả lời tốt nhất";
	const  ANSWER_UNVOTE_BEST="Hủy chọn câu trả lời tốt nhất";
}
?>