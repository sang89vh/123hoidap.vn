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
use FAQ\Mapper\SubjectMapper;
use FAQ\FAQEntity\User;
use FAQ\FAQCommon\ChromePhp;
use FAQ\FAQCommon\Util;

class CatalogController extends FAQAbstractActionController
{

    /**
     * @path /admin/catalog
     * @todo redirect to subject action
     */
    public function indexAction()
    {
        $privilege = Util::isPrivilege($this);
        if ($privilege['isAllowed']) {
        	$this->setLayoutAdmin();
        } else {
        	return $this->toNoticeWarning("Bạn chưa được cấp quyền truy nhâp trang này!");
        }
    	return $this->redirect()->toRoute("catalog", array('action'=>'subject-category'));
    }





    /**
     * @path /admin/catalog/subject
     * @return multitype:
     */
    public function subjectAction(){
        $privilege = Util::isPrivilege($this);
        if ($privilege['isAllowed']) {
        	$this->setLayoutAdmin();
        } else {
        	return $this->toNoticeWarning("Bạn chưa được cấp quyền truy nhâp trang này!");
        }
        $subID = null;

    	$subjectMapper = new SubjectMapper();
    	$lstSubject = $subjectMapper->getAllSubject();
    	$subID = $this->getEvent()->getRouteMatch()->getParam("id");
    	return array(
    	    'lstSubject'=>$lstSubject,
    	    'subID'=>$subID
    	);
    }

    /**
     * @path ajax component
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function updateSubjectAction(){
        $privilege = Util::isPrivilege($this);
        if ($privilege['isAllowed']) {
        	 $this->setLayoutAjax();
        } else {
        	return $this->toNoticeWarning("Bạn chưa được cấp quyền truy nhâp trang này!");
        }
        $subjectID = null;
        $title = null;
        $desc = null;
        $act = null;
        $pKeyword = null;
		$keyword = array();
        $subjectMapper = new SubjectMapper();
        $act = $this->request->getPost('act');
        $subjectID = $this->request->getPost('id');
        $pKeyword = $this->request->getPost('keyword');
        $subjectMapper = new SubjectMapper();
        $title = $this->request->getPost('title');
        $desc = $this->request->getPost('desc');
        $mediaID = $this->request->getPost('mediaID');
        if($pKeyword){
        	$keyword = explode(",", $pKeyword);
        }
        if($act=='xoa'){
            $subjectMapper->deleteSubject($subjectID);
            $subjectMapper->commit();
            echo '#saved';
        }
        if($act == 'sua'){
            $sub = $subjectMapper->updateSubject($subjectID, $title, $desc, $mediaID,$keyword);
            $subjectMapper->commit();
            echo $sub->getId().'#saved';
        }
        if($act=='them'){
            $sub = $subjectMapper->createNewSubject($title, $desc, $mediaID, $keyword);
            $subjectMapper->commit();
            echo $sub->getId().'#saved';
        }
        return $this->getResponse();
    }
}
