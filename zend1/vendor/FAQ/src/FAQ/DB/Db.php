<?php
namespace FAQ\DB;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\DocumentManager;
use Zend\Mvc\Controller\ControllerManager;
use Exception;
use FAQ\FAQCommon\DbConfig;
/**
 *
 * @author Sang89vh
 * @var $dm DocumentManager
 * @var $ctr \Zend\Mvc\Controller\AbstractActionController
 */
class Db
{
    /*@var $dm \Doctrine\ODM\MongoDB\DocumentManager */
    private  $dm;

    /*@var $ctr \Zend\ServiceManager\ServiceLocatorInterface  */
    public static $serviceLocator;


	/**
     *
     * @param AbstractActionController $ctr
     * @throws Exception
     */
    public function __construct(){
        if(Db::$serviceLocator==null){
            throw new Exception("Db__construct - Missing param(serviceLocator)");
            return;
        }
        AnnotationDriver::registerAnnotationClasses();
        $this->dm = Db::$serviceLocator->get("doctrine.documentmanager.odm_default");
        $config = $this->dm->getConfiguration();
        $config->setMetadataDriverImpl(AnnotationDriver::create(DbConfig::$DOCUMENT_DIR));
        Entity::$dm = $this->dm;

    }

   /**
    * @return \Doctrine\ODM\MongoDB\DocumentManager
    */
    protected  function getDm(){
        return $this->dm;
    }

    /**
     * @todo: perform commit all persisted document to db
     */
    public function commit(){
        $this->dm->flush();
    }



    /**
     * @todo: perform clear all document from persistence
     */
    public function rollback(){
        $this->dm->clear();
    }

    /**
     *
     * @param Entity $document
     * @return Entity
     */
    public function insert($document){
        $this->getDm()->persist($document);
        return $document;
    }
}

?>