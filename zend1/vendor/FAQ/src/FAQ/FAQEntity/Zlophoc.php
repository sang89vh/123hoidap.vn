<?php
namespace FAQ\FAQEntity;
use FAQ\DB\Entity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use \Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQEntity\Message;
use FAQ\FAQCommon\Util;
use ErrorException;

//(cascade={"persist"})
/**
 * @ODM\Document
 *
 * @todo Luu thong tin ve nguoi dung va cac thong tin lien quan cua nguoi dung
 */
class Zlophoc extends  Entity
{
    /**
     *  @odm\String
     */
    private $name;
    
    
    /**
     *  @odm\String
     */
    private $locate;
    
    /**
     * @ODM\ReferenceMany
     */
    private $friend;
    
    /**
     * @ODM\ReferenceMany (cascade={"persist"})
     */
    private $list_sv;
    
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

	/**
	 * @return the $locate
	 */
	public function getLocate() {
		return $this->locate;
	}

	/**
	 * @param field_type $locate
	 */
	public function setLocate($locate) {
		$this->locate = $locate;
	}

	public function __construct(){
        $this->list_sv = new ArrayCollection();
    }
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @return ArrayCollection $list_sv
	 */
	public function getList_sv() {
		return $this->list_sv;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @param \Doctrine\Common\Collections\ArrayCollection $list_sv
	 */
	public function setList_sv($list_sv) {
		$this->list_sv = $list_sv;
	}
	
	public function addSinhVien($sv){
	    $this->list_sv[] = $sv;
	}

	public function removeSinhVien($sv){
	    $this->list_sv->removeElement($sv);
	}
	
	
}

?>