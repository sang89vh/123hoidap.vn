<?php
namespace FAQ\DB;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Boolean;
use Exception;

class EntityEmbed
{

    /**
     * @odm\Id
     * @ODM\UniqueIndex(order="asc")
     */
    protected $id;

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
    public function __construct(){

    }
}

?>