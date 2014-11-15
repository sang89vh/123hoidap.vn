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
use FAQ\Mapper\PHPJobMapper;
use Zend\Json\Json;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\Authcfg;
use Exception;
use Zend\Validator\Date;
use MongoDate;
use DateTime;
use FAQ\Mapper\TestMapper;

class PhpjobController extends FAQAbstractActionController
{

    public function initKeywordAction()
    {
        $this->setLayoutAjax();
        header("Content-Type:application/json");
        $statusAccess = 0;
        $privilege = Util::isPrivilege($this);
        if ($privilege['isAllowed']) {

            try {

                $phpJobMapper = new PHPJobMapper();
                $phpJobMapper->initKeyWord();
                $statusAccess = 1;
            } catch (Exception $e) {
                $statusAccess = 0;
                Util::writeLog($e->getTraceAsString(), \Zend\Log\Logger::ERR);
            }
        }
        $data = array(
            "status" => $statusAccess
        );
        // echo $data;
        echo Json::encode($data);
        return $this->response;
    }

    public function updateKeywordAction()
    {
        $fromDate = $this->params()->fromPost('fromDate');
        $toDate = $this->params()->fromPost('toDate');
        $type = $this->params()->fromPost('type');
        $validate = new Date('Y-m-d');
        if ($validate->isValid($fromDate) && $validate->isValid($toDate)) {
            $start = new MongoDate(strtotime($fromDate . " 00:00:00"));
            $end = new MongoDate(strtotime($toDate . " 00:00:00"));
        } else {
            $today = new DateTime();
            $start = new MongoDate(strtotime($today->format('Y-m-d') . " 00:00:00"));
            $tomorrow = $today->modify('+1 day');
            $end = new MongoDate(strtotime($tomorrow->format('Y-m-d') . " 00:00:00"));
        }

        $this->setLayoutAjax();
        header("Content-Type:application/json");
        $statusAccess = 0;
        $privilege = Util::isPrivilege($this);
        if ($privilege['isAllowed']) {
            try {

                $phpJobMapper = new PHPJobMapper();

                $phpJobMapper->updateKeyWord($start, $end,$type);
                $statusAccess = 1;
            } catch (Exception $e) {
                $statusAccess = 0;
                Util::writeLog($e->getTraceAsString(), \Zend\Log\Logger::ERR);
            }
        }
        $data = array(
            "status" => $statusAccess
        );
        // echo $data;

        echo Json::encode($data);
        return $this->response;
    }

    public function managerKeywordAction()
    {
        $privilege = Util::isPrivilege($this);
//         var_dump($privilege);
        if ($privilege['isAllowed']) {
            $this->setLayoutAdmin();
        } else {
            return $this->toNoticeWarning("Bạn chưa được cấp quyền truy nhâp trang này!");
        }

//         $t=new TestMapper();
//         $t->updateFirstImage();
    }
}
