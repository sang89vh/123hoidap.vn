<?php
namespace Web\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\Authcfg;
use FAQ\Mapper\NewMapper;
use FAQ\FAQCommon\FAQParaConfig;

class AboutController extends FAQAbstractActionController
{

    public function indexAction()
    {
        $privilege = Util::isPrivilege($this);
        if ($privilege['role'] == Authcfg::GUEST) {
            $this->setLayoutGuest();
        } else {
            $this->setLayoutHome();
        }

        $newMapper = new NewMapper();
        $news = $newMapper->getNews(FAQParaConfig::NEWS_TYPE_ABOUT, FAQParaConfig::STATUS_ACTIVE, null, null, null);
        return array(
            "news" => $news
        );
    }
}

