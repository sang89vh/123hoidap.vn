<?php

namespace Web\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\Mapper\TagMapper;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\Authcfg;
use FAQ\FAQEntity\Tag;
use Web\Forms\TagForm;
use FAQ\FAQEntity\Image;
use FAQ\FAQCommon\FAQParaConfig;
use Zend\Json\Json;

class TagController extends FAQAbstractActionController {
	/**
	 * Luu y: tag la nhung tu khoa duoc chon loc tu trong key_word
	 * tag, key_word la 2 collection khac nhau
	 * (non-PHPdoc)
	 *
	 * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
	 */
	public function indexAction() {
		$privilege = Util::isPrivilege ( $this );
		if ($privilege ['role'] == Authcfg::GUEST) {
			$this->setLayoutHomeGuest ();
		} else {
			$this->setLayoutHome ();
		}
		$query = $this->request->getQuery ( 'tag' );
		$status = $this->request->getQuery ( 'status' );
		$type = $this->request->getQuery ( 'type' );
		if (empty ( $type ) && empty ( $status )) {
			$type = "popular";
		} elseif (empty ( $type ) && ! empty ( $status )) {
			$type = null;
		}
		if ("new" == $status) {
			$status = FAQParaConfig::STATUS_TAG_APPROVE;
		} elseif ("edit" == $status) {
			$status = FAQParaConfig::STATUS_TAG_EDIT;
		} elseif ("create" == $status) {
			$status = FAQParaConfig::STATUS_TAG_CREATE;
		}
		$page = $this->getEvent ()->getRouteMatch ()->getParam ( "page" );
		if (( int ) $page < 1) {
			$page = 1;
		}
		$from = 20 * ($page - 1);
		$to = $from + 20;
		// var_dump ( $page );
		$tagMapper = new TagMapper ();
		$data = $tagMapper->getSystemTag ( $query, $type, $from, $to, array (
				"date_updated" => "desc"
		), $status );
		return array (
				"tags" => $data ["tags"],
				"totalDocument" => $data ["totalDocument"],
				"page" => $page,
				"type" => $type,
				"status" => $status,
				"isAdmin" => ($privilege ['role'] == Authcfg::ADMIN)
		);
	}
	public function editAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn không đủ quyền truy cập trang này", 3000, "/" );
		}
		$this->setLayoutHome ();
		$tagID = $this->getEvent ()->getRouteMatch ()->getParam ( "page" );
		$form = new TagForm ();
		$request = $this->getRequest ();
		$tagMapper = new TagMapper ();
		if ($request->isPost ()) {
			$post = array_merge_recursive ( $request->getPost ()->toArray (), $request->getFiles ()->toArray () );
			$form->setData ( $post );
			if ($form->isValid ()) {
				$data = $form->getData ();
			}
			if ($data ["tag_avatar"] ["error"] != 4) {
				// check type file
				$allowedExts = array (
						"gif",
						"jpeg",
						"jpg",
						"png"
				);
				$temp = explode ( ".", $data ["tag_avatar"] ["name"] );
				$extension = end ( $temp );
				if ((($data ["tag_avatar"] ["type"] == "image/gif") || ($data ["tag_avatar"] ["type"] == "image/jpeg") || ($data ["tag_avatar"] ["type"] == "image/jpg") || ($data ["tag_avatar"] ["type"] == "image/pjpeg") || ($data ["tag_avatar"] ["type"] == "image/x-png") || ($data ["tag_avatar"] ["type"] == "image/png")) && ($data ["tag_avatar"] ["size"] < 524288) && in_array ( $extension, $allowedExts )) {
					if ($data ["tag_avatar"] ["error"] > 0) {
						$error = "Error: " . $data ["tag_avatar"] ["error"] . "<br>";

						goto labelbreak;
					}
				} else {
					$error = "ảnh avatar chấp nhận định dạng: gif,jpeg,jpg,png và dung lượng tối đa là 0,5 Mb";

					goto labelbreak;
				}
			}
			try {

				$tag = $tagMapper->getOneTag ( $tagID );
				if (empty ( $tag )) {
					goto labelbreak;
				}
				// 'tag_name' => string '' (length=0)
				// 'tag_desc' => string '' (length=0)
				// 'tag_relationship' => string '' (length=0)
				// 'tag_avatar'

				$tagName = strtolower ( trim ( $data ["tag_name"] ) );
				if (strlen ( $tagName ) == 0 || strlen ( $tagName ) > 25 || preg_match ( "/a-z 0-9 + # - ./i", $tagName )) {
					$error = "Tên tag phải nhỏ hơn 25 ký tự và chỉ chấp nhận chứa các ký tự a-z 0-9 + # - .";
					goto labelbreak;
				}
				$tagRelationshipString = $data ["tag_relationship"];
				$tagRelationshipArray = explode ( ";", $tagRelationshipString );

				$tagDesc = strtolower ( trim ( $data ["tag_desc"] ) );
				;
				if (strlen ( $tagDesc ) == 0 || strlen ( $tagDesc ) < 20) {
					$error = "Mô tả tối thiểu 20 ký tự";
					goto labelbreak;
				}
				$img = new Image ();
				$img->setFile ( $data ["tag_avatar"] ["tmp_name"] );

				$tag->setCreateBy ( Util::getCurrentUser () );
				if (! empty ( $img )) {
					$tag->setAvatar ( $img );
				}
				$tag->setDateUpdated ( Util::getCurrentTime () );
				$tag->setDesc ( $tagDesc );
				$tag->setTagName ( $tagName );
				$tag->setType ( $data ["type"] );
				foreach ( $tagRelationshipArray as $key => $tagRelationshipID ) {
					$tagre = $tagMapper->getOneTag ( $tagRelationshipID );
					if (! empty ( $tagre )) {
						$tag->setRelationshipTag ( $tagre );
					}
				}
				$tag->setStatus ( FAQParaConfig::STATUS_TAG_EDIT );
				$tagMapper->updateTag ( $tag );
			} catch ( \Exception $e ) {
				$error = $e->getMessage ();
				goto labelbreak;
			}
		}

		$tag = $tagMapper->getOneTag ( $tagID );

		labelbreak:
		$error = Util::bootstrapAlert ( $error );
		return array (
				"tagID" => $tagID,
				"tag" => $tag,
				"error" => $error
		);
	}
	public function createAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn không đủ quyền truy cập trang này", 3000, "/" );
		}

		$this->setLayoutHome ();
		$request = $this->getRequest ();

		$form = new TagForm ();
		if ($request->isPost ()) {
			$post = array_merge_recursive ( $request->getPost ()->toArray (), $request->getFiles ()->toArray () );
			$form->setData ( $post );
			if ($form->isValid ()) {
				$data = $form->getData ();
				// var_dump ( $data );
			}
			// check type file
			$allowedExts = array (
					"gif",
					"jpeg",
					"jpg",
					"png"
			);
			$temp = explode ( ".", $data ["tag_avatar"] ["name"] );
			if(!empty($data ["tag_avatar"] ["type"])){
// 				var_dump($temp);
			$extension = end ( $temp );
			if ((($data ["tag_avatar"] ["type"] == "image/gif") || ($data ["tag_avatar"] ["type"] == "image/jpeg") || ($data ["tag_avatar"] ["type"] == "image/jpg") || ($data ["tag_avatar"] ["type"] == "image/pjpeg") || ($data ["tag_avatar"] ["type"] == "image/x-png") || ($data ["tag_avatar"] ["type"] == "image/png")) && ($data ["tag_avatar"] ["size"] < 524288) && in_array ( $extension, $allowedExts )) {
				if ($data ["tag_avatar"] ["error"] > 0) {
					$error = "Error: " . $data ["tag_avatar"] ["error"] . "<br>";

					goto labelbreak;
				}
			} else {
				$error = "ảnh avatar chấp nhận định dạng: gif,jpeg,jpg,png và dung lượng tối đa là 0,5 Mb";

				goto labelbreak;
			}
			}
			try {
				$tagMapper = new TagMapper ();
				$tag = new Tag ();
				// 'tag_name' => string '' (length=0)
				// 'tag_desc' => string '' (length=0)
				// 'tag_relationship' => string '' (length=0)
				// 'tag_avatar'

				$tagName = strtolower ( trim ( $data ["tag_name"] ) );
				if (strlen ( $tagName ) == 0 || strlen ( $tagName ) > 25 || preg_match ( "/a-z 0-9 + # - ./i", $tagName )) {
					$error = "Tên tag phải nhỏ hơn 25 ký tự và chỉ chấp nhận chứa các ký tự a-z 0-9 + # - .";
					goto labelbreak;
				}
				$tagName = Util::convertToTag ( $tagName );
				$tagRelationshipString = $data ["tag_relationship"];
				$tagRelationshipArray = explode ( ";", $tagRelationshipString );

				$tagDesc = strtolower ( trim ( $data ["tag_desc"] ) );
				;
				if (strlen ( $tagDesc ) == 0 || strlen ( $tagDesc ) < 20) {
					$error = "Mô tả tối thiểu 20 ký tự";
					goto labelbreak;
				}
				if(!empty($temp)){
				$img = new Image ();
				$img->setFile ( $data ["tag_avatar"] ["tmp_name"] );

				$tag->setCreateBy ( Util::getCurrentUser () );
				if (! empty ( $img )) {
					$tag->setAvatar ( $img );
				}
				}
				$tag->setDateUpdated ( Util::getCurrentTime () );
				$tag->setDesc ( $tagDesc );
				$tag->setTagName ( $tagName );
				$tag->setType ( $data ["type"] );
				foreach ( $tagRelationshipArray as $key => $tagRelationshipID ) {
					$tagre = $tagMapper->getOneTag ( $tagRelationshipID );
					if (! empty ( $tagre )) {
						$tag->setRelationshipTag ( $tagre );
					}
				}
				$tag->setStatus ( FAQParaConfig::STATUS_TAG_CREATE );
				$tagMapper->createTag ( $tag );
			} catch ( \Exception $e ) {
				$error = $e->getMessage ();
				goto labelbreak;
			}
		}
		labelbreak:
		$error = Util::bootstrapAlert ( $error );
		return array (
				"tagID" => $tagID,
				"form" => $form,
				"error" => $error
		);
	}
	public function deleteAction() {
		$this->setLayoutAjax ();
		header ( "Content-Type:application/json" );
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			$statusacess = 0;
		} else {
			$request = $this->getRequest ();
			if ($request->isPost ()) {
				$tagID = $title = $this->params ()->fromPost ( 'tag' );
				$tagMapper = new TagMapper ();
				try {
					$statusacess = $tagMapper->deleteTag ( $tagID );
				} catch ( \Exception $e ) {
					$statusacess = 0;
				}
			}
		}

		echo Json::encode ( array (
				"status" => $statusacess
		) );
		return $this->getResponse ();
	}
}