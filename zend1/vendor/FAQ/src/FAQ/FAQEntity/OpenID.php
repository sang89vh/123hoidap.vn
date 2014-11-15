<?php
namespace FAQ\FAQEntity;


use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\FAQCommon\Util;
use FAQ\DB\EntityEmbed;

/**
 * @odm\EmbeddedDocument
 *
 * @todo Luu thong tin cac openid ma User dung de dang nhap,
 *       dang ky
 */
class OpenID extends EntityEmbed
{

    /**
     * @ODM\String
     */
    private $user_id;

    /**
     * @ODM\String
     */
    private $code;

    /**
     *
     * @return String
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     *
     * @param String $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     *
     * @return String
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     *
     * @param Int $code
     * @tutorial mã $openIDCode như sau<br/>
     *           1 facbook <br/>
     *           2 goole+ <br/>
     *           3 tiwtter <br/>
     *           4 zing me <br/>
     *           5 yahoo
     */
    public function setCode($code)
    {
        //@sang sua lai phan check valid $code neu can nhe
        $this->code = $code;
        return $this;
    }
}