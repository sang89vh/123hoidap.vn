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
use Admin\Forms\CreateNews;
use FAQ\FAQCommon\Util;
use FAQ\Mapper\NewMapper;
use FAQ\FAQEntity\News;
use FAQ\FAQCommon\FAQParaConfig;
use Zend\Json\Json;

class NewsController extends FAQAbstractActionController
{

    public function indexAction()
    {
        $privilege = Util::isPrivilege($this);
        if ($privilege['isAllowed']) {
            $this->setLayoutAdmin();
        } else {
            return $this->toNoticeWarning("Ban chưa được cấp quyền truy nhâp trang này!");
        }
    }

    public function detailAction()
    {
        $privilege = Util::isPrivilege($this);
        if ($privilege['isAllowed']) {
            $this->setLayoutBasic();
        } else {
            return $this->toNoticeWarning("Ban chưa được cấp quyền truy nhâp trang này!");
        }
        $newID = $this->getEvent()
            ->getRouteMatch()
            ->getParam("id");
        $newMapper = new NewMapper();
        $new = $newMapper->getOneNew($newID);
        return array(
            "new" => $new
        );
    }

    public function createAction()
    {
        $privilege = Util::isPrivilege($this);
        if ($privilege['isAllowed']) {
            $this->setLayoutAdmin();

            $formNews = new CreateNews();

            $request = $this->getRequest();
            if ($request->isPost()) {
                $title = $this->params()->fromPost("title");
                $content = $this->params()->fromPost("content_news");
                $type = $this->params()->fromPost("type");

                $newsMapper = new NewMapper();
                $new = new News();
                $new->setTitle($title);
                $new->setContent($content);

                $new->setCreateBy(Util::getCurrentUser());
                $new->setStatus(FAQParaConfig::STATUS_ACTIVE);
                $new->setType($type);
                $newsMapper->createNews($new);

                // Redirect to list of albums
                return $this->redirect()->toUrl("/admin/news/index");
            }
            return array(
                'formNews' => $formNews
            );
        } else {
            return $this->toNoticeWarning("Ban chưa được cấp quyền truy nhâp trang này!");
        }
    }

    public function managerAction()
    {
        $privilege = Util::isPrivilege($this);
        if ($privilege['isAllowed']) {
            $this->setLayoutAdmin();
        } else {
            return $this->toNoticeWarning("Ban chưa được cấp quyền truy nhâp trang này!");
        }

        $this->setLayoutAjax();
        /*
         * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * Easy set variables
         */

        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
        */
        $aColumns = array(
            "id",
            "id",
            "title",
            "content",
            "status",
            "type",
            "status"
        );
        $select = $aColumns;
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

            $newMapper = new NewMapper();
            $data = $newMapper->getNews(null, null, null, null,$sOrder);
            // var_dump("=====>".$data['totalDocument']);
        } catch (Exception $e) {
            Util::writeLog($e->getMessage());
        }
        $rResult = $data;

        $iFilteredTotal = count($data);

        /* Total data set length */

        $iTotal = count($data);

        /*
         * Output
         */
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        // "id",
        // "title",
        // "content",
        // "status",
        // "type"
        /* @var $new \FAQ\FAQEntity\News */
        foreach ($rResult as $key => $new) {

            $type = $new->getType();
            switch ($type) {
                case FAQParaConfig::NEWS_TYPE_ABOUT:
                    $type = "Giới thiệu";
                    break;
                case FAQParaConfig::NEWS_TYPE_COMMUNITY_GUIDELINE:
                    $type = "Nguyên tắc cộng đồng";
                    break;
                case FAQParaConfig::NEWS_TYPE_HELP:
                    $type = "Hướng dẫn sử dụng";
                    break;
                case FAQParaConfig::NEWS_TYPE_SCORING_SYSTEM:
                    $type = "Phương pháp tính điểm";
                    break;
                case FAQParaConfig::NEWS_TYPE_SITEMAP:
                    $type = "Sithe map";
                    break;
                case FAQParaConfig::NEWS_TYPE_TERM:
                    $type = "Điều khoản";
                    break;
                default:
                    $type = "không xác định??";
                    break;
            }
            $status = $new->getStatus();
            switch ($status) {
                case FAQParaConfig::STATUS_ACTIVE:
                    $status = "Đang kích hoạt";
                    break;
                case FAQParaConfig::STATUS_DEACTIVE:
                    $status = "không kích hoạt";
                    break;
                default:
                    $status = "không rõ trạng thái";
                    break;
            }
            $row = array(
                $new->getId(),
                $new->getId(),
                $new->getTitle(),
                mb_substr(strip_tags($new->getContent()), 0,300,'UTF-8')."....",
                $status,
                $type,
                $new->getStatus()
            );




            $output['aaData'][] = $row;
        }

        echo Json::encode($output);
        return $this->getResponse();
    }

    public function deleteAction()
    {
        $newID = $this->params()->fromPost("news");
        $newMapper = new NewMapper();
        $statusAccess = false;
        try {
            $newMapper->delete($newID);
            $statusAccess = true;
        } catch (\Exception $e) {
            $statusAccess = false;
        }
        $data = array(
            "status" => $statusAccess
        );

        echo Json::encode($data);
        return $this->getResponse();
    }
    public function reopenAction()
    {
        $newID = $this->params()->fromPost("news");
        $newMapper = new NewMapper();
        $statusAccess = false;
        try {
            $newMapper->reopen($newID);
            $statusAccess = true;
        } catch (\Exception $e) {
            $statusAccess = false;
        }
        $data = array(
            "status" => $statusAccess
        );

        echo Json::encode($data);
        return $this->getResponse();
    }
}
