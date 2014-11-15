<?php
namespace Web\Controller;


use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\Authcfg;
use FAQ\Mapper\NewMapper;
use FAQ\FAQCommon\FAQParaConfig;


class SupportController extends FAQAbstractActionController
{

    public function latexAction(){
    $this->setLayoutAjax();
    }
    public function helpAction(){
    $privilege=Util::isPrivilege($this);
        if($privilege['role']==Authcfg::GUEST){
        	$this->setLayoutGuest();
        }else {
        	$this->setLayoutBasic();
        }
        $newMapper = new NewMapper();
        $news = $newMapper->getNews(FAQParaConfig::NEWS_TYPE_HELP, FAQParaConfig::STATUS_ACTIVE, null, null, null);
        return array(
        		"news" => $news
        );
    }
    public function contactAction(){
        $privilege=Util::isPrivilege($this);
        if($privilege['role']==Authcfg::GUEST){
        	$this->setLayoutGuest();
        }else {
        	$this->setLayoutBasic();
        }

    }

    public function sitemapAction(){
        $privilege=Util::isPrivilege($this);
        if($privilege['role']==Authcfg::GUEST){
        	$this->setLayoutGuest();
        }else {
        	$this->setLayoutBasic();
        }
        $newMapper = new NewMapper();
        $news = $newMapper->getNews(FAQParaConfig::NEWS_TYPE_SITEMAP, FAQParaConfig::STATUS_ACTIVE, null, null, null);
        return array(
        		"news" => $news
        );
    }
    public function termAction (){
        $privilege=Util::isPrivilege($this);
        if($privilege['role']==Authcfg::GUEST){
        	$this->setLayoutGuest();
        }else {
        	$this->setLayoutBasic();
        }
        $newMapper = new NewMapper();
        $news = $newMapper->getNews(FAQParaConfig::NEWS_TYPE_TERM, FAQParaConfig::STATUS_ACTIVE, null, null, null);
        return array(
        		"news" => $news
        );
    }
    public function scoringSystemAction (){
        $privilege=Util::isPrivilege($this);
        if($privilege['role']==Authcfg::GUEST){
        	$this->setLayoutGuest();
        }else {
        	$this->setLayoutBasic();
        }
        $newMapper = new NewMapper();
        $news = $newMapper->getNews(FAQParaConfig::NEWS_TYPE_SCORING_SYSTEM, FAQParaConfig::STATUS_ACTIVE, null, null, null);
        return array(
        		"news" => $news
        );
    }
    public function communityGuidelineAction (){
        $privilege=Util::isPrivilege($this);
        if($privilege['role']==Authcfg::GUEST){
        	$this->setLayoutGuest();
        }else {
        	$this->setLayoutBasic();
        }


        $newMapper = new NewMapper();
        $news = $newMapper->getNews(FAQParaConfig::NEWS_TYPE_COMMUNITY_GUIDELINE, FAQParaConfig::STATUS_ACTIVE, null, null, null);
        return array(
        		"news" => $news
        );
    }
}