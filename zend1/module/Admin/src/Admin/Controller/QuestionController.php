<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Admin for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\Mapper\QuestionMapper;
use FAQ\FAQCommon\Util;
use Exception;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;
use FAQ\FAQCommon\FAQParaConfig;
class QuestionController extends FAQAbstractActionController
{
    public function indexAction()
    {
        $this->setLayoutAdmin();
        return array();
    }
    public function detailAction()
    {
         $this->setLayoutBasic();
         $questionID = $this->getEvent()
         ->getRouteMatch()
         ->getParam("id");
         $this->getRequest()->setMetadata(array(
         		"questionID" => $questionID
         ));
         $privilege = Util::isPrivilege($this);
         if(!$privilege['isAllowed']){
         return  $this->toNoticeError("Câu hỏi hiện không có!",3000,"/") ;
         }
         $questionMapper = new QuestionMapper();
         $question = $questionMapper->getOneQuestion($questionID);

         $type_editor = "MARKDOWN";


         $view = new ViewModel(array(
         		"question" => $question,
         		"type_editor" => $type_editor
         ));
         $view->setTemplate('web/question/detail.phtml'); // path to phtml file under view folder
         return $view;

    }

public function listQuestionAction()
    {
        $this->setLayoutAjax();
        /*
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * Easy set variables
         */

        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
        */
        $aColumns = array(
            "status",
            "is_admin_spam",
            "id",
            "title",
            "content",
            "status",
            "subject",
            "date_created",
            "total_spam",
            "total_share","total_view",
            "total_like",
            "total_dislike",
            "total_answer",
            "create_by",
            'is_admin_spam'
        );
        $select =$aColumns;
        /* Indexed column (used for fast and accurate table cardinality) */
        $sIndexColumn = "id";

        /* DB table to use */
        $sTable = "ajax";

        /*
         * Paging
         */
        $start = 0;
        $skip = 10;
        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $start = intval($_GET['iDisplayStart']);
            $skip = intval($_GET['iDisplayLength']);
        }

        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = array();
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i ++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $col = $aColumns[intval($_GET['iSortCol_' . $i])];
                    $order = $_GET['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc';
                    $sOrder[$col] = $order;
                }
            }
        }

        /*
         * Filtering NOTE this does not match the built-in DataTables filtering which does it word by word on any field. It's possible to do here, but concerned about efficiency on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere = array();
        if (isset($_GET['sSearch']) && $_GET['sSearch'] != "") {

            for ($i = 0; $i < count($aColumns); $i ++) {
                $sWhere[$aColumns[$i]] = $_GET['sSearch'];
            }
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i ++) {
            if (isset($_GET['bSearchable_' . $i]) && $_GET['bSearchable_' . $i] == "true" && $_GET['sSearch_' . $i] != '') {
                $sWhere[$aColumns[$i]] = $_GET['sSearch_' . $i];
            }
        }

        /*
         * SQL queries Get data to display
         */
        try {



        $questionMapper = new QuestionMapper();
        $data = $questionMapper->getQuestionManager($select,$sOrder, $start,$start+ $skip);
//         var_dump("=====>".$data['totalDocument']);
        } catch (Exception $e) {
            Util::writeLog($e->getMessage());
        }
        $rResult = $data['listQuestion'];

        $iFilteredTotal = $data['totalDocument'];

        /* Total data set length */

        $iTotal = $data['totalDocument'];

        /*
         * Output
         */
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        /* @var $question \FAQ\FAQEntity\Question */
        foreach ($rResult as $key => $question) {
            $row = array(
                $question->getId(),
                $question->getId(),
                $question->getId(),
                $question->getTitle(),
                mb_substr(strip_tags($question->getContent()), 0,70,'UTF-8'),
                $question->getStatus(),
                $question->getSubject()->getTitle(),
               date('Y-m-d h:m:s',$question->getDateCreated()->getTimestamp()),
                $question->getTotalSpam(),
                $question->getTotalShare(),
                $question->getTotalLike(),
                $question->getTotalDislike(),
                $question->getTotalAnswer(),
                $question->getCreateBy()->getFirstName(),
                $question->getIsAdminSpam()==FAQParaConfig::IS_ADMIN_SPAM_STATUS_ACCESS_SPAM?'true':'false'
            );
//             var_dump($question->getIsAdminSpam());
            $output['aaData'][] = $row;

        }

        echo Json::encode($output);
        return $this->getResponse();
    }
}
