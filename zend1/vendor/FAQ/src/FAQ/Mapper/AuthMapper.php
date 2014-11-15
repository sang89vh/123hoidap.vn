<?php
namespace FAQ\Mapper;

use FAQ\FAQEntity\User;
use FAQ\FAQCommon\Usercfg;
use FAQ\FAQEntity\OpenID;
use DoctrineModule\Options\Authentication;
use Zend\Authentication\AuthenticationService;
use FAQ\FAQEntity\Location;
use FAQ\FAQCommon\Authcfg;
use FAQ\FAQCommon\Sessioncfg;
use FAQ\FAQCommon\Util;
use FAQ\FAQEntity\Image;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQEntity\UserRank;
use FAQ\Mapper\UserMapper;
/**
 *
 * @author izzi
 *
 */
class AuthMapper extends UserMapper
{

    private $location;

    public function __construct()
    {
        parent::__construct();
        $this->location = new Location();
    }

    /**
     * test: yes
     *
     * @todo check user be registed by openid, if not add new openid; also check email be used, if it be used so either insert new user.
     * @param String $openId_code
     * @param array $user_info
     * @return User
     */
    private function insertUserByOpenId($openId_code, $userOpenId, $user)
    {
        $userExist = $this->checkRegistedByOpenid($openId_code, $userOpenId);
        if ($userExist) {
            return $userExist; // user be registed by openid
        }

        // registered yet, insert new openid but check email be used first.
        $email = $user->getEmail();
        $userRegistered = $this->checkRegistedByEmail($email);
        if ($userRegistered) {
            $this->getDm()->detach($user);
            $user = $userRegistered;
            $user->setStatusUpdateRefere();
            $user->reg_code = 'email_registered';
        } else {
            // send mail notice registrator ok.
            /**
             * temporary cancel send mail, using it later
             * $mail = new Mail();
             * $mail->sendSuccessRegistrator($email, "Welcome to QApolo", "hi ".$user->getFirstName()."<br/"."Email: ".$email.
             * ", password: ".$user->getPass()."<br/>Thanks you");
             */

            // set avatar content for new user
            if ($user->getAvatar()) {
                $tmpfname = tempnam("/tmp", $user->getId());
                $handle = fopen($tmpfname, "w");
                fwrite($handle, file_get_contents($user->getAvatar()));
                fclose($handle);

                $avatar_img = new Image();
                $avatar_img->setFile($tmpfname);
                $avatar_img->insert();
                $user->setAvatar($avatar_img);
            }
            $rank_default = $this->createRankDefault();
            $user->setRank($rank_default);
            $this->initNewUser($user);
            $openid = new OpenID();
            $openid->setCode($openId_code);
            $openid->setUserId($userOpenId);
            $user->setOpenid($openid);
            $user->reg_code = 'ok';
            $this->commit();
        }
        return $user;
    }

    /**
     *
     * @todo insert user to User, check existing before insert.
     * @param array $info
     */
    public function insertUserByFacebook($info)
    {
        $id = $info['id'];
        $name = $info['name'];
        $firstName = $info['first_name'];
        $lastName = $info['last_name'];
        $home = $info['link'];
        $username = $info['username'];
        if(!empty($info['location'])){
        $locationId = $info['location']['id'];
        }
        if(!empty($info['location'])){
        $locationName = $info['location']['name'];
        }
        $sex = $info['gender'];
        $email = $info['email'];
        $password = Util::getRandomPassword();
        $timezone = $info['timezone'];
        $locale = $info['locale'];
        $avatar_link = 'https://graph.facebook.com/' . $id . '/picture?type=large';
        $birthDay = Util::createDate("1", "1", "2000");
        $user = $this->create();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        // $location = new Location();
        // $location->setName($locationName);
        // $user->setLocation($location);
        // sang : check exists loation from db
        if (! empty($locationName)) {

            $location = $this->location->findOneBy(array(
                "name" => trim($locationName)
            ));
            if (empty($location)) {
                $location = new Location();
                $location->setName(trim($locationName));
                $location->setKeyWord(Util::covertUnicode($locationName));
                $location->setCreateBy($user);
                $location->insert();
            }
            // var_dump($location);
            // if(!empty($location->getName())){
            $user->setLocation($location);
            // }
        }
        $user->setSex($sex);
        $user->setBirthday($birthDay);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user->setStatus(Usercfg::user_status_email_ok);
            $user->setEmail($email);
        } else {
            $user->setStatus(Usercfg::user_status_email_missing);
        }
        $user->setPass($password);
        $user->setRoleCode(Authcfg::MEMBER);

        $user->setAvatar($avatar_link);

        $user = $this->insertUserByOpenId(Authcfg::$facebook, $id, $user);
        return $user;
    }

    /**
     *
     * @todo insert user to User, check existing before insert.
     * @param array $info
     */
    public function insertUserByTwitter($info)
    {
        $id = $info->id;
        $name = $info->name;
        // $firstName, $lastName
        $home = 'https://twitter.com/' . $info->screen_name;
        // $username, $locationId
        $locationName = $info->location;
        // $sex
        $avatar_link = $info->profile_image_url;
        $email = $id . '@twitter.com';
        $timezone = $info->time_zone;
        $password = Util::getRandomPassword();
        $user = $this->create();
        $user->setFirstName($name);
        $user->setLastName("");
        $user->setBirthday(Util::createDate(1, 1, 1988));
        if ($locationName) {
            $location = new Location();
            $qb = $location->getQueryBuilder();
            $qb = $qb->field("name")->equals($locationName);
            $lstLoc = $qb->getQuery()->execute();
            if ($lstLoc->getNext()) {
                $user->setLocation($lstLoc->getNext());
            } else {
                $location->setName($locationName);
                $location->insert();
                $location->setCreateBy($user);
                $user->setLocation($location);
            }
        }
        $user->setStatus(Usercfg::user_status_email_missing);
        $user->setPass($password);
        $user->setEmail($email);
        $user->setAvatar($avatar_link);
        $user->setRoleCode(Authcfg::MEMBER);
        $user = $this->insertUserByOpenId(Authcfg::$twitter, $id . '', $user);
        return $user;
    }

    /**
     * test: yes
     *
     * @todo check email is used to be registering. if email is used return user, else return null
     * @param String $email
     * @return User
     */
    public function checkRegistedByEmail($email)
    {
        $user = $this->user;
        $userCursor = $user->findBy(array(
            Usercfg::$email => $email
        ), true);
        if (! $userCursor)
            return null;
        return $userCursor->getNext();
    }

    /**
     * test: yes
     *
     * @todo check user be registed by a openid. if user registed return user, else return null;
     * @param String $openId_code
     * @param String $userOpenId
     * @return User
     */
    public function checkRegistedByOpenid($openId_code, $userOpenId)
    {
        $user = $this->user;
        $user = $user->getQueryBuilder()
            ->field(Usercfg::$openid_code)
            ->equals($openId_code)
            ->field(Usercfg::$openid_userid)
            ->equals($userOpenId)
            ->getQuery()
            ->execute();
        return $user->getNext();
    }

    /**
     * test: yes
     * @toto insert User with email, if user exised return it and don't insert
     *
     * @param User $user
     * @return User
     */
    public function insertUserByEmail($user)
    {
        $userExist = $this->checkRegistedByEmail($user->getEmail());
        if ($userExist)
            return $userExist;
        $user->insert();
        $this->commit();
        return $user;
    }

    /**
     * test: yes
     *
     * @todo authenticate user by email and password
     * @param String $email
     * @param String $pass
     * @return boolean
     */
    public function checkAuthByEmail($email, $pass)
    {
        $user = $this->user;
        $user = $user->findBy(array(
            Usercfg::$email => $email,
            Usercfg::$password => $pass
        ), true);
        if ($user)
            return true;
        return false;
    }

    /**
     *
     * @param String $email
     * @param String $pass
     * @return number (-1: email_not_found, -3: password_not_found, 1: success)
     */
    public function isAuthByEmail($email, $pass)
    {
        if (! $email || ! $pass)
            return - 1;
        $user = $this->user;
        $authOdmAdapter = $this::$serviceLocator->get('doctrine.authenticationadapter.odm_default');
        $authOdmStorage = $this::$serviceLocator->get("doctrine.authenticationstorage.odmdefault");

        $authOptions = new Authentication();
        $authOptions->setIdentityProperty(Usercfg::$email);
        $authOptions->setCredentialProperty(Usercfg::$password);
        $authOptions->setObjectManager($user->getDm());
        $authOptions->setIdentityClass($user->getDocumentName());
        $sm = $this->createSession();
        $authOptions->setSessionManager($sm);
        $authOdmAdapter->setOptions($authOptions);
        $authOdmStorage->setOptions($authOptions);
        $authOdmAdapter->setIdentityValue($email);
        $authOdmAdapter->setCredentialValue($pass);

        /* @var $check \Zend\Authentication\Result */
        $auth = new AuthenticationService($authOdmStorage, $authOdmAdapter);
        $check = $auth->authenticate();

        if ($check->getCode() == '1') {
            $this->setSessionParam(Sessioncfg::$email, $email);
            $user = $user->findBy(array(
                "email" => $email
            ), true)->getNext();
            $this->setSessionUser($user);
//             ChromePhp::log('email:' . $this->createSession()
//                 ->getStorage()
//                 ->getMetadata("email"));
        }
        return $check->getCode();
    }

    /**
     *
     * @param String $key
     * @param String $value
     */
    public function setSessionParam($key, $value)
    {
        $sm = Util::$sm;
        $sm->getStorage()->setMetadata($key, $value);
    }

    /**
     *
     * @todo save user to session
     * @param User $user
     */
    public function setSessionUser($user)
    {
        $this->setSessionParam(Sessioncfg::$email, $user->getEmail());
        $this->setSessionParam(Sessioncfg::$user_id, $user->getId());
    }

    /**
     *
     * @param String $key
     */
    public function getSessionParam($key)
    {
        $sm = Util::$sm;
        return $sm->getStorage()->getMetadata($key);
    }

    /**
     *
     * @todo create session with mongodb storage
     * @return \Zend\Session\SessionManager
     */
    public function createSession()
    {
        return Util::$sm;
    }

    /**
     *
     * @todo call when user signup. function check user exist and some contraint
     *       - return ('not_valid', 'email_used', 'valid'
     * @param User $user
     * @return String
     */
    public function signupUser($user)
    {
        // check email valid
        $isEmailValid = filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL);
        if (! $isEmailValid)
            return "not_valid";
            // check email is used in system
        $existUser = $this->checkRegistedByEmail($user->getEmail());
        if ($existUser)
            return "email_used";
            // check date
        $birth_day = $user->birth_day;
        $birth_month = $user->birth_month;
        $birth_year = $user->birth_year;
        if ($birth_day <= 0 or $birth_day > 31)
            return "not_valid";
        if ($birth_month <= 0 or $birth_month > 12)
            return "not_valid";
        if ($birth_year <= 1900 or $birth_year > 2013)
            return "not_valid";
        if ($user->getSex() != FAQParaConfig::FEMALE && $user->getSex() != FAQParaConfig::MALE && $user->getSex() != FAQParaConfig::MALEANDFEMALE)
            return "not_valid";
        if (! $user->getFirstName() || ! $user->getLastName())
            return "not_valid";

            // init a new user
        $this->initNewUser($user);
        $user->setStatus(Usercfg::user_status_email_ok);
        // init avatar default
        $this->initAvatar($user);
        // init rank
        $rank = new UserRank();
        $rank->setName(Usercfg::user_rank_new_text);
        $user->setRank($rank);
        $this->getDm()->persist($user);
        // izzi: update point - registering by email
        //$this->updatePointByRegistering($user);
        $this->commit();
        $this->setSessionUser($user);
        return "valid";
    }
}

?>