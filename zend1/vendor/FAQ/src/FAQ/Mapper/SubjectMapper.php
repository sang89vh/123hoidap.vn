<?php
namespace FAQ\Mapper;

use FAQ\DB\Db;
use FAQ\FAQEntity\Subject;
use FAQ\FAQCommon\Util;
use FAQ\FAQEntity\User;
use Exception;
use FAQ\FAQEntity\Image;
use FAQ\FAQCommon\FAQParaConfig;
/**
 *
 * @author sang
 *
 */
class SubjectMapper extends Db
{

    private $subject;

    private $user;

    public function __construct()
    {
        parent::__construct();

        $this->subject = new Subject();
        $this->user = new User();
    }

    /**
     *
     * @param Subject $subject
     */
    public function createSubject($subject)
    {
        $subject->insert();
        $this->commit();
    }

    /**
     *
     * @author izzi
     * @todo delete subject by id
     * @param String $subjectID
     * @return boolean
     * @todo Return TRUE if delete subject is successful else return FALSE
     */
    public function deleteSubject($subjectID)
    {
        $subject = $this->subject->find($subjectID, true);
        $subject->setStatus(0);
        return $subject;
    }

    /**
     * @author izzi
     * @todo update subject
     * @param String $subjectID
     * @param String $title
     * @param String $desc
     * @param String $avatarID
     */
    public function updateSubject($subjectID, $title, $desc, $avatarID=null, $keyword=null){
        $subject = $this->subject->find($subjectID, true);
        $subject->setTitle($title);
        $subject->setDesc($desc);
        $subject->setKeyWord($keyword);
        if($avatarID){
            $image = new Image();
            $image = $image->find($avatarID, true);
            $subject->setAvatar($image);
        }

        return $subject;
    }

    /**
     * @author izzi
     * @todo add new subject
     * @param string $title
     * @param string $desc
     * @param string $mediaID
     * @return \FAQ\FAQEntity\Subject
     */
    public function createNewSubject($title, $desc, $imageID=null, $keyword=null){
        $subject = new Subject();
        $subject->insert();
        $subject->setTitle($title);
        $subject->setDesc($desc);
        $subject->setKeyWord($keyword);
        if($imageID){
            $image = new Image();
            $image = $image->find($imageID, true);
            $subject->setAvatar($image);
        }
        return $subject;
    }
    /**
     *@author sang
     * @param array $select
     * @param array $keyWord
     * @param String $userIDFollow
     * @param Int $status
     * @param array $orderBy
     * @param Int $from
     * @param Int $to
     * @return Ambigous <\Doctrine\ODM\MongoDB\Query\mixed, \Doctrine\MongoDB\EagerCursor, \Doctrine\MongoDB\Cursor, Cursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
     */
    public function findSubject($select, $keyWord, $userIDFollow, $isOnlyGetFollow, $status, $orderBy, $from, $to,$isHydrate=true,$isMetaSubject=false)
    {

        // get query builder
        $qb = $this->subject->getQueryBuilder();
        // select field on collection u want to use
//         var_dump(isset($isHydrate),$isHydrate);
        if(isset($isHydrate)){
       $qb= $qb->hydrate($isHydrate);
        }
        if (!empty($select)) {
            $qb = Util::selectField($qb, $select);
        }
        // set where for query

        if ($keyWord) {
            $qb = $qb->field('key_word')->in($keyWord);
        }
        if ($userIDFollow && $isOnlyGetFollow) {
            $qb = $qb->field('user_follow.id')->equals($userIDFollow);
        }
        if ($userIDFollow && ! $isOnlyGetFollow) {
            $qb = $qb->field('user_follow.id')->notEqual($userIDFollow);
        }
        if ($status) {
//             $qb = $qb->field('status')->equals($status);
            $qb->addOr($qb->expr()->field('status')->equals($status));
        }

        if($isMetaSubject){
        	$qb->addOr($qb->expr()->field('status')->equals(FAQParaConfig::SUBJECT_META));
        }
        // set order
        if ($orderBy) {
            $qb = Util::addOrder($qb, $orderBy);
        }

        // set limit
        if (isset($from) && isset($to)) {
        	$qb = $qb->skip($from)->limit($to - $from);


        }

        // get query
        $q = $qb->getQuery();
        // execute query
        $subjects = $q->execute();
        // return result
        return $subjects;
    }

    public function followSubject($subjectID, $userID)
    {
        try {
            /* @var $sub \FAQ\FAQEntity\Subject */
            $sub = $this->subject->find($subjectID, true);
            $sub->setTotalUserFollow(1+$sub->getTotalUserFollow());
            /* @var $user \FAQ\FAQEntity\User */
            $user = $this->user->find($userID, true);
            $user->setStatusUpdateRefere();
            $user->setFollowSubject($sub);
            $sub->setStatusUpdateRefere();
            $this->commit();
            return 1;
        } catch (Exception $e) {
            Util::writeLog($e->getTraceAsString(), \Zend\Log\Logger::EMERG);
            return 0;
        }
    }

    public function unFollowSubject($subjectID, $userID)
    {
        try {

            /* @var $sub \FAQ\FAQEntity\Subject */
            $sub = $this->subject->find($subjectID, true);
            $sub->setTotalUserFollow($sub->getTotalUserFollow()-1);
            /* @var $user \FAQ\FAQEntity\User */
            $user = $this->user->find($userID, true);
            $sub->getUserFollow()->removeElement($user);
            $user->getFollowSubject()->removeElement($sub);
            $user->setStatusUpdateRefere();
            $sub->setStatusUpdateRefere();
            $this->commit();
            //2 is unfollow
            return 2;
        } catch (Exception $e) {
            Util::writeLog($e->getTraceAsString(), \Zend\Log\Logger::EMERG);
            return 0;
        }
    }

    /**
     *
     * @author izzi
     * @param String $subjectID
     * @return Subject
     */
    public function getOneStubject($subjectID)
    {
        return $this->subject->find($subjectID, true);
    }

    /**
     *
     * @author izzi
     * @todo get list subject is folowed by a user.
     * @param String $userID
     * @return Ambigous <\Doctrine\ODM\MongoDB\Query\mixed, \Doctrine\MongoDB\EagerCursor, \Doctrine\MongoDB\Cursor, Cursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
     */
    public function getSubjectsByUser($userID)
    {
        $subject = $this->subject;
        $qb = $subject->getQueryBuilder();
        $qb->select("title");
        $qb->field("user_follow.id")->equals($userID);
        $list_subject = $qb->getQuery()->execute();
        return $list_subject;
    }


    /**
     * @role: admin
     * @author izzi
     * @todo get all subject
     */
    public function getAllSubject(){
//         $qb = $this->subject->getQueryBuilder();
//         $qb->field("status")->equals(1);
//         $qb->field("id")->notEqual(FAQParaConfig::DEFAULT_SUBJECT_ID);
//         $lstSubject = $qb->getQuery()->execute();
    	$orderBy=array("total_question"=>"desc","total_user_follow"=>"desc");
    	$subjectsFollow=$this->findSubject ( null, null, Util::getIDCurrentUser (), true, FAQParaConfig::STATUS_ACTIVE, $orderBy, null, null, true, true );
    	$subjectsUnfollow = $this->findSubject ( null, null, Util::getIDCurrentUser (), false, FAQParaConfig::STATUS_ACTIVE, $orderBy, null, null, true, true );
    	$subjects = $subjectsFollow->toArray () + $subjectsUnfollow->toArray ();
        if(count($subjects)==0) return null;
        return $subjects;
    }











}

?>