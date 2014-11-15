<?php
namespace FAQ\FAQEntity;


use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Float;
use FAQ\DB\EntityEmbed;

/**
 * @ODM\EmbeddedDocument
 * @todo Vi tri dia ly theo toa do x, y
 */
class Coordinates extends EntityEmbed
{

    /**
     * @ODM\Float
     */
    public $x;

    /**
     * @ODM\Float
     */
    public $y;

    /**
     *
     * @return Float
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     *
     * @param Float $x
     */
    public function setX($x)
    {
        $this->x = $x;
        return $this;
    }

    /**
     *
     * @return Float
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     *
     * @param Float $y
     */
    public function setY($y)
    {
        $this->y = $y;
        return $this;
    }
}