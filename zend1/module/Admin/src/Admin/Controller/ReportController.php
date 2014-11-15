<?php

namespace Admin\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\FAQCommon\Util;
use FAQ\Mapper\QuestionMapper;
use FAQ\FAQCommon\FAQParaConfig;

class ReportController extends FAQAbstractActionController {
	public function questionAction() {
		$privilege = Util::isPrivilege ( $this );
		// var_dump($privilege);
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeError ( "Không được cấp quyền truy cập!", 3000, "/" );
		}

		$questionMapper = new QuestionMapper ();
		$reportQuestionPerDay = $questionMapper->reportChartPerDay ();
		$chartPerDayQuestion = array ();
		foreach ( $reportQuestionPerDay as $key => $report ) {

			array_push ( $chartPerDayQuestion, array (
					'dateUpdated' => $report ['_id'],
					'totalQuestion' => $report ['value']
			) );
		}
		$reportQuestionAll = $questionMapper->reportChartAll ();
		// var_dump($reportQuestionAll);
		// // var_dump($reportQuestion);
		// foreach ($reportQuestion as $user) {
		// var_dump($user);
		// }
		$this->setLayoutAdmin ();
		$chartAllQuestion = array ();

		foreach ( $reportQuestionAll as $key => $report ) {
			switch ($report ['_id']) {
				case FAQParaConfig::QUESTION_STATUS_CLOSE :
					array_push ( $chartAllQuestion, array (
							'status:Đang đóng' => $report ['value']
					) );
					break;
				case FAQParaConfig::QUESTION_STATUS_DRAFT :
					array_push ( $chartAllQuestion, array (
							'status' => 'Bản nháp',
							'total' => $report ['value']
					) );
					break;
				case FAQParaConfig::QUESTION_STATUS_OPEN :
					array_push ( $chartAllQuestion, array (
							'status' => 'Đang hỏi',
							'total' => $report ['value']
					) );
					break;
				case FAQParaConfig::QUESTION_STATUS_TEMP_DELETE :
					array_push ( $chartAllQuestion, array (
							'status' => 'Đã xóa',
							'total' => $report ['value']
					) );
					break;
				default :
					array_push ( $chartAllQuestion, array (
							'status' => 'Trạng thái khác',
							'total' => $report ['value']
					) );
					break;
			}
		}

		return array (

				"chartAllQuestion" => $chartAllQuestion,
				"chartPerDayQuestion" => $chartPerDayQuestion
		);
	}
}
