<?php
namespace FAQ\DB;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Boolean;
use Exception;
use Documents\CustomRepository\Document;
use Zend\Validator\EmailAddress;

class Entity
{

    /**
     *
     * @var \Zend\validator\EmailAddress
     */
    protected $validatorEmail;

    /**
     * @odm\Id
     * @ODM\UniqueIndex(order="asc")
     */
    protected $id;

    /* @var $dm \Doctrine\ODM\MongoDB\DocumentManager */
    public static $dm;

    public function __construct()
    {
        $this->validatorEmail = new EmailAddress();
    }

    /**
     *
     * @return \Doctrine\ODM\MongoDB\DocumentManager $dm
     */
    public function getDm()
    {
        return $this::$dm;
    }

    /**
     *
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param String $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @todo : get Document Class Name, ex: FAQ\FAQEntity\Skill
     * @return string
     */
    public function getDocumentName()
    {
        return get_class($this);
    }

    /**
     * check connection to mongodb
     *
     * @return bool (true-connect ok, false - conect fail)
     */
    public function checkConnect()
    {
        $isok = true;
        try {
            $this::$dm->find(get_class($this), 'sdf113113113113');
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Unknown error')) {
                $isok = false;
            }
        }
        return $isok;
    }

    /**
     *
     * @todo Document to be insert, but it isn't persistent until you call commit();
     *
     */
    public function insert()
    {
        $this::$dm->persist($this);
    }

    /**
     *
     * @param String $docId
     *            - (if $docId null remove it, else remove Document with $docId)
     * @return boolean (true-remove, false - not remove)
     */
    public function remove($docId = null)
    {
        $id_remove = null;
        if ($docId) {
            $id_remove = $docId;
        } else {
            $id_remove = $this->id;
        }
        $doc_remove = $this->find($id_remove, true);
        if ($doc_remove) {
            $this::$dm->remove($doc_remove);
            if (! $docId) {
                $this::$dm->remove($this);
            }
            return true;
        }
        return false;
    }

    /**
     *
     * @param String $documentId
     * @param boolean $isHydrator
     * @return Entity (-ishydrator = true return a Document, - ishydrator = false return a array, - no document return null);
     */
    public function find($documentId, $isHydrator)
    {
        $rs = null;
        if (! $isHydrator) {
            $rs = $this::$dm->getRepository(get_class($this))
                ->findBy(array(
                '_id' => $documentId
            ))
                ->hydrate(false);
            if ($rs) {
                if (count($rs) == 0)
                    return null;
                foreach ($rs as $k => $v) {
                    return $v;
                }
            }
        } else {
            $rs = $this::$dm->find(get_class($this), $documentId);
        }
        return $rs;
    }

    /**
     *
     * @todo Find all data from database
     * @return array
     */
    public function findAll()
    {
        $rs = $this::$dm->getRepository(get_class($this))->findAll();
        return $rs;
    }

    /*
     * @todo: if you want update reference you need call me
     */
    public function setStatusUpdateRefere()
    {
        //$this->getDm()->detach($this);
        //$this->getDm()->persist($this);
    }

    /**
     *
     * @param Array $cond_assc
     * @return \Doctrine\ODM\MongoDB\Cursor (ishydrator = true return cursor-Document, ishydrator = false return cursor-array, no document return null)
     */
    public function findBy($cond_assc, $isHydrator)
    {
        $rs = $this::$dm->getRepository(get_class($this))
            ->findBy($cond_assc)
            ->hydrate($isHydrator);
        if (count($rs) == 0)
            return null;
        return $rs;
    }

    /**
     *
     * @param Array $cond_assc
     * @return Entity
     */
    public function findOneBy($cond_assc)
    {
        $rs = $this::$dm->getRepository(get_class($this))->findOneBy($cond_assc);
        if (count($rs) == 0)
            return null;
        return $rs;
    }

    /**
     *
     * @return \Doctrine\ODM\MongoDB\Query\Builder
     */
    public function getQueryBuilder()
    {

        return $this::$dm->createQueryBuilder(get_class($this));
    }
}

?>