<?php
namespace FAQ\FAQEntity;

use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use \Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQEntity\Message;
use FAQ\FAQCommon\Util;
use ErrorException;

/**
 * @ODM\Document
 *
 * @todo Luu thong tin ve nguoi dung va cac thong tin lien quan cua nguoi dung
 */
class Zsinhvien extends Entity
{

    /**
     * @ODM\String
     */
    private $name;
    
    /**
     * @ODM\String
     */
    private $age;
    
    /**
     * @ODM\ReferenceMany  (cascade={"persist"})
     */
    private $friend; 
    
    /**
     * @ODM\ReferenceMany (cascade={"persist"})
     */
    private $list_lh;
    
    /**
	 * @return the $friend
	 */
	public function getFriend() {
		return $this->friend;
	}

	/**
	 * @param field_type $friend
	 */
	public function setFriend($friend) {
		$this->friend = $friend;
	}

	public function __construct(){
        $this->list_lh = new ArrayCollection();
        $this->friend = new ArrayCollection();
    }
    
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	
	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}


    /**
	 * @return the $age
	 */
	public function getAge() {
		return $this->age;
	}

	/**
	 * @return ArrayCollection $list_lh
	 */
	public function getList_lh() {
		return $this->list_lh;
	}

	/**
	 * @param field_type $age
	 */
	public function setAge($age) {
		$this->age = $age;
	}

	/**
	 * @param Ambigous <unknown, \Doctrine\Common\Collections\ArrayCollection> $list_lh
	 */
	public function setList_lh($list_lh) {
		$this->list_lh = $list_lh;
	}

	public function addLopHoc($lh){
        $this->list_lh[] = $lh;
    }
    
    public function removeLopHoc($lh){
        $this->list_lh->removeElement($lh);
    }
    
    public function addFriend($sv){
        $this->friend[] = $sv;
    }
    
    public function removeFriend($sv){
        $this->friend->removeElement($sv);
    }
}