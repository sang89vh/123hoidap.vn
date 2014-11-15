<?php
namespace  FAQ\FAQCommon;
use Zend\Mvc\Controller\AbstractActionController;
use DoctrineModule\Options\Authentication;
use Zend\Authentication\AuthenticationService;
use Zend\Session\SaveHandler\MongoDBOptions;
use Zend\Session\SaveHandler\MongoDB;
use Zend\Session\SessionManager;
use Zend\View\Model\ViewModel;
use FAQ\FAQEntity\User;
use FAQ\DB\Db;
use FAQ\Mapper\AuthMapper;
/**
 *
 * @author izzi
 *
 */
class  FAQAbstractActionController extends AbstractActionController
{

    /**
     * @todo return email trong session, neu session time out retun null
     * @return String|NUll
     */
    protected function getEmailSession(){
        $sm = new SessionManager(); $sm->start();
        $email = $sm->getStorage()->getMetadata('email');
        if($email){
            return $email;
        }
        return null;
    }

    protected function setLayoutMedia(){
        $this->layout("layout/media");
        $this->layout()->action = $this->getEvent()->getRouteMatch()->getParam('action', 'index');
        $dirID = $this->getEvent()
        ->getRouteMatch()
        ->getParam("dirid");
        $this->forward()->dispatch("Web\Controller\Media", array(
            "action" => "nav-media",
            "controller"=>"Web\Controller\Media",
            "dirid"=>$dirID
        ));
    }
    /**
     * @todo set notice layout
     */
    protected function setLayoutNotice(){
        $this->layout('layout/notice');
    }

    /**
     * @todo use login layout
     */
    protected function setLayoutLogin(){

        $this->layout("layout/login");
    }
    /**
     * @todo use home page of subject
     */
    protected function setLayoutSubject(){
        $this->layout("layout/subject");
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'search-form',
        		'controller' => 'Web\Controller\Home'
        ));

    }
    /**
     * @todo use home page of subject
     */
    protected function setLayoutSubjectGuest(){
        $this->layout("layout/subject_guest");
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'search-form',
        		'controller' => 'Web\Controller\Home'
        ));

    }
    /**
     * @todo use in home layout
     */
    protected function setLayoutHome(){
        $this->layout("layout/home");
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'search-form',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-hashtag',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-subject',
        		'controller' => 'Web\Controller\Home'
        ));

        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-member',
        		'controller' => 'Web\Controller\Home'
        ));
    }
    /**
     * @todo use in detail question layout
     */
    protected function setLayoutQuestionDetail(){
        $this->layout("layout/question_detail");
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'search-form',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-question-hashtag',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-question-relationship',
        		'controller' => 'Web\Controller\Home'
        ));


    }

    /**
     * @todo use in home default
     */
    protected function setLayoutBasic(){
        $this->layout("layout/layout");
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'search-form',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-hashtag',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-subject',
        		'controller' => 'Web\Controller\Home'
        ));

        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-member',
        		'controller' => 'Web\Controller\Home'
        ));
    }
    /**
     * @todo use in home default
     */
    protected function setLayoutCreate(){
        $this->layout("layout/create");
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'search-form',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-hashtag',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-subject',
        		'controller' => 'Web\Controller\Home'
        ));

        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-member',
        		'controller' => 'Web\Controller\Home'
        ));
    }
    /**
     * @todo use in home guest
     */
    protected function setLayoutGuest(){
        $this->layout("layout/guest");
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'search-form',
        		'controller' => 'Web\Controller\Home'
        ));

    }
    /**
     * @todo use in detial question guest
     */
    protected function setLayoutQuestionGuest(){
        $this->layout("layout/question_detail_guest");
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'search-form',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-question-hashtag',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-question-relationship',
        		'controller' => 'Web\Controller\Home'
        ));

    }
    /**
     * @todo use in home guest
     */
    protected function setLayoutHomeGuest(){
        $this->layout("layout/home_guest");
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'search-form',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-hashtag',
        		'controller' => 'Web\Controller\Home'
        ));
        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-subject',
        		'controller' => 'Web\Controller\Home'
        ));

        $this->forward()->dispatch("Web\Controller\Home", array(
        		'action' => 'top-member',
        		'controller' => 'Web\Controller\Home'
        ));
    }
    /**
     * @todo member layout
     */
    protected function setLayoutMember(){
    	$this->layout("layout/member");
    	$this->forward()->dispatch("Web\Controller\Home", array(
    			'action' => 'search-form',
    			'controller' => 'Web\Controller\Home'
    	));
    	//set pages plugin
    	$this->forward()->dispatch("Web\Controller\Question", array(
    			'action' => 'chart-question',
    			'controller' => 'question'
    	));


    }
    /**
     * @todo member layout
     */
    protected function setLayoutMemberGuest(){
    	$this->layout("layout/member_guest");
    	$this->forward()->dispatch("Web\Controller\Home", array(
    			'action' => 'search-form',
    			'controller' => 'Web\Controller\Home'
    	));
    	//set pages plugin
    	$this->forward()->dispatch("Web\Controller\Question", array(
    			'action' => 'chart-question',
    			'controller' => 'question'
    	));


    }




    /**
     * @todo use in ajax tooltip
     */
    protected function setLayoutAjax(){
        $this->layout("layout/ajax");

    }
    /**
     * @todo use in tour in first use
     */
    protected function setLayoutTour(){
        $this->layout("layout/tour");

    }
    /**
     * @todo use in fancybox iframe
     */
    protected function setLayoutFancybox(){
        $this->layout("layout/fancybox");

    }

    /**
     * @todo use in admin
     */
    protected function setLayoutAdmin(){
    	$this->layout("layout/admin");

    }

    protected function toNoticeSuccess($message, $time=null, $url_back=null){
    	$this->setLayoutNotice();
    	$view = new ViewModel(array(
    			'message' => $message,
    			'time'=> $time,
    			'url_back'=>$url_back
    	));
    	$view->setTemplate("web/common/notice-success");
    	return $view;
    }

    protected function toNoticeError($message, $time=null, $url_back=null){
    	$this->setLayoutNotice();
    	$view = new ViewModel(array(
    			'message' => $message,
    			'time'=> $time,
    			'url_back'=>$url_back
    	));
    	$view->setTemplate("web/common/notice-error");
    	return $view;
    }
    protected function toNoticeWarning($message, $time=null, $url_back=null){
    	$this->setLayoutNotice();
    	$view = new ViewModel(array(
    			'message' => $message,
    			'time'=> $time,
    			'url_back'=>$url_back
    	));
    	$view->setTemplate("web/common/notice-warning");
    	return $view;
    }

    /**
     * @todo use in home answer
     */
    protected function setLayoutAnswer(){
    	$this->layout("layout/answer");
    	$this->forward()->dispatch("Web\Controller\Home", array(
    			'action' => 'search-form',
    			'controller' => 'Web\Controller\Home'
    	));
    	$this->forward()->dispatch("Web\Controller\Home", array(
    			'action' => 'chart-answer',
    			'controller' => 'Web\Controller\Home'
    	));

    }
    /**
     * @todo guest in home answer
     */
    protected function setLayoutAnswerGuest(){
    	$this->layout("layout/answer_guest");
    	$this->forward()->dispatch("Web\Controller\Home", array(
    			'action' => 'search-form',
    			'controller' => 'Web\Controller\Home'
    	));
    	$this->forward()->dispatch("Web\Controller\Home", array(
    			'action' => 'chart-answer',
    			'controller' => 'Web\Controller\Home'
    	));

    }
}
?>