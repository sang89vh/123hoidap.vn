<?php
namespace FAQ\Mapper;

use FAQ\DB\Db;
use FAQ\FAQEntity\Question;
use FAQ\FAQEntity\User;
use FAQ\FAQEntity\Key;
use FAQ\FAQCommon\Util;
use FAQ\FAQEntity\Rank;
use FAQ\FAQEntity\UserCategory;
use FAQ\FAQCommon\Mail;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQEntity\Image;
use FAQ\FAQEntity\Skill;
use FAQ\FAQEntity\Location;
use FAQ\FAQCommon\Usercfg;
use FAQ\FAQEntity\Zsinhvien;
use FAQ\FAQEntity\Zlophoc;

class Zmapper extends Db
{

    private $sv;
    private $lh;
    public function __construct(){
        parent::__construct();
        $this->sv = new Zsinhvien();
        $this->lh = new Zlophoc();
    }

    /**
     *
     * @param String $name
     * @param String $locate
     * @return \FAQ\FAQEntity\Zlophoc
     */
    public function createLopHoc($name, $locate){
        $lh = new Zlophoc();
        $lh->insert();
        $lh->setName($name);
        $lh->setLocate($locate);
        return $lh;
    }

    /**
     *
     * @param String $name
     * @param int $age
     * @return \FAQ\FAQEntity\Zsinhvien
     */
    public function createSinhVien($name, $age){
        $sv = new Zsinhvien();
        $sv->insert();
        $sv->setName($name);
        $sv->setAge($age);
        return $sv;
    }


    /**
     *
     * @param Zlophoc $lh
     * @param Zsinhvien $sv
     */
    public function addSinhVien($lh, $sv){
      $lh->addSinhVien($sv);
    }

    /**
     *
     * @param Zsinhvien $sv
     * @param Zlophoc $lh
     */
    public function addLopHoc($sv, $lh){
        $sv->addLopHoc($lh);
    }

    /**
     *
     * @param String $svID
     * @return Zsinhvien
     */
    public function findSinhVien($svID){
        return $this->sv->find($svID, true);
    }

    /**
     *
     * @param String $lhID
     * @return Zlophoc
     */
    public function findLopHoc($lhID){
        $lh = $this->lh;
        return $lh->find($lhID, true);

    }

    public function removeLopHoc($lhID){
        $this->lh->remove($lhID);
    }

    public function removeSinhVien($svID){
        $this->sv->remove($svID);
    }

    public function testRef(){

    }
}

?>