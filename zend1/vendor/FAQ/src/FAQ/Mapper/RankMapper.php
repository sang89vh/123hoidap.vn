<?php
namespace FAQ\Mapper;
use FAQ\DB\Db;
use FAQ\FAQEntity\Rank;
use FAQ\FAQEntity\UserRank;
use FAQ\FAQCommon\ChromePhp;

/**
 *
 * @author sang
 *
 */
class RankMapper extends  Db
{
    private $rank;
    public function __construct()
    {
    	parent::__construct();
    	$this->rank = new Rank();
        
    }

    /**
     * @todo check user has rank
     * @param \FAQ\FAQEntity\User $user
     * @param String $rankName
     */
    public function checkUserHasRank($user, $rankName){
        $hasRank = false;
        if($user==null) return true;
        $ranks = $user->getRank();
        foreach($ranks as $rank){
            /*@var $rank \FAQ\FAQEntity\UserRank  */
            if($rank->getName()== $rankName){
                $hasRank = true;
            }
        }
        return $hasRank;
    }
    
    public function createNewRank($user, $rankName){
        $rank = new UserRank();
        $rank->setName($rankName);
        $user->setRank($rank);
    }

}

?>