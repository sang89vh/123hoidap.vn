<?php

namespace Web\Controller;

use FAQ\FAQCommon\Authcfg;
use FAQ\FAQCommon\FAQAbstractActionController;
use SocialAuth\Facebook;
use SocialAuth\TwitterOAuth;
use Zend\Session\SessionManager;
use FAQ\Mapper\AuthMapper;
use FAQ\FAQEntity\User;
use Web\Forms\UpdateEmailTwiterForm;
use Zend\View\Model\ViewModel;
use FAQ\FAQCommon\Sessioncfg;
use FAQ\Mapper\UserMapper;
use FAQ\FAQCommon\Util;
use FAQ\Mapper\SearchMapper;
use Zend\Json\Json;
use FAQ\FAQCommon\FAQParaConfig;
use FAQ\FAQCommon\Usercfg;
use Doctrine\DBAL\Types\VarDateTimeType;

class SearchController extends FAQAbstractActionController {
	// izzi
	// TODO Tim kiem Cong ty
	public function locationCongtyJsonAction() {
		$this->setLayoutAjax ();
		$keyword = $this->request->getQuery ( 'q' );
		if (! $keyword) {
			$keyword = 'a';
		}
		$searchMapper = new SearchMapper ();
		$list_location = $searchMapper->searchLocationByKeyword ( $keyword, null, 7, FAQParaConfig::LOC_TYPE_CONGTY, FAQParaConfig::LOC_TYPE_CONGTY + 10 );
		$arrLocation = array ();
		foreach ( $list_location as $k => $v ) {
			$loc = null;
			$loc->value = $v;
			$arrLocation [] = $loc;
		}
		echo Json::encode ( $arrLocation );
		return $this->getResponse ();
	}
	/**
	 * @izzi.
	 * Tim kiem user theo ten
	 * params: query='long'
	 */
	public function memberByKeywordAction() {
		$this->setLayoutAjax ();
		$keyword = $this->request->getQuery ( 'q' );
		if (! $keyword)
			$keyword = 'a';
		$userMapper = new UserMapper ();
		$listUser = $userMapper->findUser ( array (
				"first_name",
				"last_name",
				"_id"
		), $keyword, "first_name asc", 0, 15, Usercfg::USER_STATUS_CURRENT_ACTIVE );
		$arrUser = array ();
		foreach ( $listUser as $user ) {
			/* @var $user User */
			$item = null;
			$item->value = $user->getFirstName () . ' ' . $user->getLastName ();
			$item->data = $user->getId ();
			$arrSkill [] = $item;
		}
		echo Json::encode ( $arrSkill );
		return $this->getResponse ();
	}

	// izzi
	// TODO tim kiem noi hoc tap
	public function locationHoctapJsonAction() {
		$this->setLayoutAjax ();
		$keyword = $this->request->getQuery ( 'q' );
		if (! $keyword) {
			$keyword = 'a';
		}
		$searchMapper = new SearchMapper ();
		$list_location = $searchMapper->searchLocationByKeyword ( $keyword, null, 7, FAQParaConfig::LOC_TYPE_TRUONGHOC, FAQParaConfig::LOC_TYPE_TRUONGHOC + 10 );
		$arrLocation = array ();
		foreach ( $list_location as $k => $v ) {
			$loc = null;
			$loc->value = $v;
			$arrLocation [] = $loc;
		}
		echo Json::encode ( $arrLocation );
		return $this->getResponse ();
	}

	// izzi
	// TODO Tim kiem Dia Danh
	public function locationDiadanhJsonAction() {
		$this->setLayoutAjax ();
		$keyword = $this->request->getQuery ( 'q' );
		if (! $keyword) {
			$keyword = 'a';
		}
		$searchMapper = new SearchMapper ();
		$list_location = $searchMapper->searchLocationByKeyword ( $keyword, null, 7, FAQParaConfig::LOC_TYPE_DIADANH, FAQParaConfig::LOC_TYPE_DIADANH + 10 );
		$arrLocation = array ();
		foreach ( $list_location as $k => $v ) {
			$loc = null;
			$loc->value = $v;
			$arrLocation [] = $loc;
		}
		echo Json::encode ( $arrLocation );
		return $this->getResponse ();
	}

	// izzi
	// TODO Tim kiem skill
	public function skillJsonAction() {
		$this->setLayoutAjax ();
		$keyword = $this->request->getQuery ( 'q' );
		if (! $keyword)
			$keyword = 'a';
		$searchMapper = new SearchMapper ();
		$list_skill = $searchMapper->searchSkillByKeyword ( $keyword, null, 7 );
		$arrSkill = array ();
		foreach ( $list_skill as $k => $v ) {
			$skill = null;
			$skill->value = $v;
			$arrSkill [] = $skill;
		}
		echo Json::encode ( $arrSkill );
		return $this->getResponse ();
	}
	/**
	 *
	 * @author sang
	 * @todo find subject with key word
	 */
	public function subjectAction() {
		$this->setLayoutAjax ();

		$query = $this->request->getQuery ( 'query' );

		$searchMapper = new SearchMapper ();
		$result = $searchMapper->searchKeyWordSubject ( $query, null, 0, 15 );

		$suggestions = array ();
		foreach ( $result as $key => $value ) {
			array_push ( $suggestions, array (
					'value' => $value,
					'data' => $value
			) );
		}

		$data = array (
				'query' => $query,
				'suggestions' => $suggestions
		);
		echo Json::encode ( $data );
		return $this->getResponse ();
	}
	/**
	 *
	 * @author sang
	 * @todo find member with key word
	 */
	public function memberAction() {
		$this->setLayoutAjax ();

		$query = $this->request->getQuery ( 'query' );

		$searchMapper = new SearchMapper ();
		$result = $searchMapper->searchKeyWordMember ( $query, null, 0, 15 );

		$suggestions = array ();
		foreach ( $result as $key => $value ) {
			array_push ( $suggestions, array (
					'value' => $value,
					'data' => $value
			) );
		}

		$data = array (
				'query' => $query,
				'suggestions' => $suggestions
		);
		echo Json::encode ( $data );
		return $this->getResponse ();
	}
	/**
	 *
	 * @author sang
	 * @todo autosugest from search tool bar
	 */
	public function findAction() {
		$this->setLayoutAjax ();

		$query = $this->request->getQuery ( 'query' );
		$isOnlyQuestion = $this->request->getQuery ( 'question' );
		$isOnlySubject = $this->request->getQuery ( 'subject' );
		$isOnlyUser = $this->request->getQuery ( 'user' );
		$isOnlySkill = $this->request->getQuery ( 'skill' );
		$isOnlyLocation = $this->request->getQuery ( 'location' );
		$isTitle = $this->request->getQuery ( 'title' );
		$searchMapper = new SearchMapper ();
		$keyWords = $searchMapper->searchKeyWord ( $isOnlyQuestion, $isOnlySubject, $isOnlyUser, $isOnlyLocation, $isOnlySkill, array (
				'id',
				'key',
				'desc',
				'type',
				'question'
		), $query, null, 0, 15 );

		$suggestions = array ();
		foreach ( $keyWords as $k => $keyWord ) {
			$keys = $keyWord ['key'];
			foreach ( $keys as $ke => $key ) {
				if (($isTitle == "false") && strlen ( $key ) > 20 && (! empty ( $keyWord ['question'] ))) {
					// remove title question
					continue;
				}
				array_push ( $suggestions, array (
						'value' => $key,
						'data' => $key
				) );
			}
		}

		$data = array (
				'query' => $query,
				'suggestions' => $suggestions
		);
		echo Json::encode ( $data );
		return $this->getResponse ();
	}
	/**
	 *
	 * @author sang
	 * @todo autosugest from create question
	 */
	public function questionAction() {
		$this->setLayoutAjax ();
		$query = $this->request->getQuery ( 'query' );
		$searchMapper = new SearchMapper ();
		$questions = $searchMapper->searchQuestion ( $query, 0, 15 );

		$suggestions = array ();
		foreach ( $questions as $k => $question ) {
			$value = Util::covertUnicode ( $question->getTitle () );
			$data = $question->getId ();
			$data = "/question/detail/" . $data . "/" . Util::convertUrlSeo ( $value );
			array_push ( $suggestions, array (
					'value' => $value,
					'data' => $data
			) );
		}

		$data = array (
				'query' => $query,
				'suggestions' => $suggestions
		);
		echo Json::encode ( $data );
		return $this->getResponse ();
	}

	/**
	 *
	 * @author sang
	 * @todo autosugest tag
	 */
	public function findTagAction() {
		$this->setLayoutAjax ();

		$query = $this->request->getQuery ( 'query' );

		$searchMapper = new SearchMapper ();
		$tags = $searchMapper->searchTag ( $query, null, 0, 15 );
		// var_dump(count($tags));
		$suggestions = array ();
		/* @var $tag /FAQ/FAQEntity/Tag */
		foreach ( $tags as $k => $tag ) {

			array_push ( $suggestions, array (
					'value' => $tag->getTagName () . " :: " . $tag->getDesc (),
					'data' => $tag->getTagName ()
			// 'data' => $tag->getId ()
						) );
		}

		$data = array (
				'query' => $query,
				'suggestions' => $suggestions
		);
		echo Json::encode ( $data );
		return $this->getResponse ();
	}
	public function indexAction() {
		$this->setLayoutAjax ();
		$userMapper = new UserMapper ();
		$user = new User ();
		$user = $user->find ( "524312538bf0d1840700503c", true );
		$userMapper->deleteOneSkill ( $user );
		return $this->getResponse ();
	}
}