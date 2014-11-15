<?php

namespace Web\Controller;

use FAQ\FAQCommon\FAQAbstractActionController;
use FAQ\Mapper\MediaMapper;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;
use FFMpeg\FFMpeg;
use FAQ\FAQCommon\ChromePhp;
use FAQ\FAQCommon\Appcfg;

class MediaController extends FAQAbstractActionController {
	/**
	 *
	 * @todo return html code to show media
	 * @return \Zend\Stdlib\ResponseInterface multitype:unknown <NULL, \FAQ\FAQEntity\Media, unknown>
	 */
	public function viewImageAction() {
		$this->setLayoutAjax ();
		return $this->response;
	}
	public function getMediaAction() {
		$this->setLayoutAjax ();
		$mediaMapper = new MediaMapper ();
		$mediaID = $this->params ()->fromQuery ( 'media' );
		$typeEditor = $this->params ()->fromQuery ( 'typeEditor' );
		$width = $this->params ()->fromQuery ( 'width' );
		$height = $this->params ()->fromQuery ( 'height' );
		$media = $mediaMapper->findMediaByID ( $mediaID );
		if (! $media) {
			return $this->getResponse ();
		}
		return array (
				"media" => $media,
				'width' => $width,
				'height' => $height,
				'typeEditor' => $typeEditor
		);
	}

	/**
	 *
	 * @todo get image from media and write it to stream
	 */
	public function getMediaImageAction() {
		$this->setLayoutAjax ();
		$mediaMapper = new MediaMapper ();
		$mediaID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		if (empty ( $mediaID )) {
			$mediaID = $this->params ()->fromQuery ( 'media' );
		}
		$media = $mediaMapper->findMediaByID ( $mediaID );
		if (empty ( $media )) {
			return $this->toNoticeWarning ( "Không tồn tại file!!" );
		}
		if ($media->getType () != FAQParaConfig::MEDIA_TYPE_FILE) {
			$contentType = $media->getContentType ();
			header ( "Expires: Sat, 18 Jul 2015 05:00:00 GMT" );
			header ( "Cache-Control: max-age=6000" );
			header ( 'Content-type: ' . $contentType . ';' );
			echo $media->getFile ()->getBytes ();
		}
		return $this->getResponse ();
	}
	/**
	 *
	 * @todo get image from media and write it to stream
	 */
	public function downloadFileAction() {
		$this->setLayoutAjax ();
		$mediaMapper = new MediaMapper ();
		$mediaID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		$media = $mediaMapper->findMediaByID ( $mediaID );
		if (empty ( $media )) {
			return $this->toNoticeWarning ( "Không tồn tại file!!" );
		}
		if ($media->getType () == FAQParaConfig::MEDIA_TYPE_FILE) {
			$contentType = $media->getContentType ();
			header ( "Expires: Sat, 18 Jul 2015 05:00:00 GMT" );
			header ( "Cache-Control: max-age=6000" );
			header ( 'Content-type: ' . $contentType . ';' );
			echo $media->getFile ()->getBytes ();
		}
		return $this->getResponse ();
	}

	/**
	 *
	 * @todo get image from image and write it to stream.
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function getImageAction() {
		$mediaMapper = new MediaMapper ();
		$imageID = $this->getEvent ()->getRouteMatch ()->getParam ( "id" );
		if (empty ( $imageID )) {
			$imageID = $this->params ()->fromQuery ( 'image' );
		}
		$images = $mediaMapper->getImage ( $imageID );
		if (empty ( $images )) {
			return $this->toNoticeWarning ( "Không tồn tại file!!" );
		}
		$contentType = $images->getContentType ();
		header ( "Expires: Sat, 18 Jul 2015 05:00:00 GMT" );
		header ( "Cache-Control: max-age=6000" );
		header ( 'Content-type: ' . $contentType . ';' );
		echo $images->getFile ()->getBytes ();
		return $this->getResponse ();
	}
	public function readImageMediaAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$this->setLayoutAjax ();
		$mediaMapper = new MediaMapper ();
		$imageID = $this->getRequest ()->getQuery ( 'id' );
		$image = $mediaMapper->findMediaByID ( $imageID );
		$contentType = $image->getContentType ();
		header ( 'Content-type: ' . $contentType . ';' );
		echo $image->getFile ()->getBytes ();
		return $this->getResponse ();
	}
	public function readVideoThumbnail() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
	}

	/*
	 * @todo make media navigator
	 */
	public function navMediaAction() {
		$privilege = Util::isPrivilege ( $this );

		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước", 3000, "/user/login" );
		}
		$mediaMapper = new MediaMapper ();
		$dirID = $this->getEvent ()->getRouteMatch ()->getParam ( "dirid" );
		$nav = $mediaMapper->getNavigator ( $dirID );
		$this->layout ()->nav = $nav;
		$this->layout ()->dirID = $dirID;
	}

	/**
	 *
	 * @todo default media action
	 */
	public function indexAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$role = $privilege ['role'];
		$this->setLayoutMedia ();
		$dirID = $this->getEvent ()->getRouteMatch ()->getParam ( "dirid" );
		$mediaMapper = new MediaMapper ();
		$listSubDir = $mediaMapper->getListMediaCurrent ( Util::getIDCurrentUser (), $dirID, FAQParaConfig::MEDIA_TYPE_DIR, FAQParaConfig::MEDIA_STATUS_NORMAL, null, $role );
		// var_dump($this->getEvent()->getParams());
		return array (
				"listSubDir" => $listSubDir
		);
	}

	/**
	 *
	 * @todo media view by directory
	 * @return multitype:Ambigous <\Doctrine\Common\Collections\ArrayCollection, NULL>
	 */
	public function imageLinkAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$role = $privilege ['role'];
		$this->setLayoutMedia ();
		$dirID = $this->getEvent ()->getRouteMatch ()->getParam ( "dirid" );
		$mediaMapper = new MediaMapper ();
		$listSubDir = $mediaMapper->getListMediaCurrent ( Util::getIDCurrentUser (), $dirID, FAQParaConfig::MEDIA_TYPE_IMAGE_LINK, FAQParaConfig::MEDIA_STATUS_NORMAL, null, $role );
		return array (
				"listSubDir" => $listSubDir
		);
	}

	/**
	 *
	 * @todo media view by video link (youtube supported)
	 * @return multitype:Ambigous <\Doctrine\Common\Collections\ArrayCollection, NULL>
	 */
	public function videoLinkAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$role = $privilege ['role'];
		$this->setLayoutMedia ();
		$dirID = $this->getEvent ()->getRouteMatch ()->getParam ( "dirid" );
		$mediaMapper = new MediaMapper ();
		$listSubDir = $mediaMapper->getListMediaCurrent ( Util::getIDCurrentUser (), $dirID, FAQParaConfig::MEDIA_TYPE_VIDEO_LINK, FAQParaConfig::MEDIA_STATUS_NORMAL, null, $role );
		return array (
				"listSubDir" => $listSubDir
		);
	}

	/**
	 *
	 * @todo media view by image file
	 * @return multitype:Ambigous <\Doctrine\Common\Collections\ArrayCollection, NULL>
	 */
	public function imageFileAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$role = $privilege ['role'];
		$this->setLayoutMedia ();
		$dirID = $this->getEvent ()->getRouteMatch ()->getParam ( "dirid" );
		$mediaMapper = new MediaMapper ();
		$listSubDir = $mediaMapper->getListMediaCurrent ( Util::getIDCurrentUser (), $dirID, FAQParaConfig::MEDIA_TYPE_IMAGE, FAQParaConfig::MEDIA_STATUS_NORMAL, null, $role );
		return array (
				"listSubDir" => $listSubDir
		);
	}

	/**
	 *
	 * @todo media view by video file
	 * @return multitype:Ambigous <\Doctrine\Common\Collections\ArrayCollection, NULL>
	 */
	public function videoFileAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$role = $privilege ['role'];
		$this->setLayoutMedia ();
		$dirID = $this->getEvent ()->getRouteMatch ()->getParam ( "dirid" );
		$mediaMapper = new MediaMapper ();
		$listSubDir = $mediaMapper->getListMediaCurrent ( Util::getIDCurrentUser (), $dirID, FAQParaConfig::MEDIA_TYPE_VIDEO, FAQParaConfig::MEDIA_STATUS_NORMAL, null, $role );
		return array (
				"listSubDir" => $listSubDir
		);
	}

	/**
	 *
	 * @todo media view by file (any file)
	 * @return multitype:Ambigous <\Doctrine\Common\Collections\ArrayCollection, NULL>
	 */
	public function MediaFileAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$role = $privilege ['role'];
		$this->setLayoutMedia ();
		$dirID = $this->getEvent ()->getRouteMatch ()->getParam ( "dirid" );
		$mediaMapper = new MediaMapper ();
		$listSubDir = $mediaMapper->getListMediaCurrent ( Util::getIDCurrentUser (), $dirID, FAQParaConfig::MEDIA_TYPE_FILE, FAQParaConfig::MEDIA_STATUS_NORMAL, null, $role );
		return array (
				"listSubDir" => $listSubDir
		);
	}

	/**
	 *
	 * @todo ajax component add media
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function addMediaAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$this->setLayoutAjax ();
		$mediaParentId;

		$mediaType;
		$mediaName;
		$mediaContentType;
		$mediaType;
		$mediaFile;
		$mediaLink;
		$action = $this->getRequest ()->getPost ( "action" );
		$mediaParentId = $this->request->getPost ( "media_parent_id" );
		if (! $mediaParentId)
			$mediaParentId = null;
		$mediaName = $this->request->getPost ( 'name' );
		$mediaLink = $this->request->getPost ( 'link' );
		$mediaMapper = new MediaMapper ();
		$isExisted = $mediaMapper->isMediaNameExisted ( Util::getIDCurrentUser (), $mediaName, $mediaParentId );
		if ($isExisted) {
			echo 'existed';
			return $this->getResponse ();
		}
		if ($action == 'index') {
			$mediaType = FAQParaConfig::MEDIA_TYPE_DIR;
			$mediaMapper->createMediaDir ( $mediaName, $mediaParentId );
		}
		if ($action == 'image-link') {
			$mediaType = FAQParaConfig::MEDIA_TYPE_IMAGE_LINK;
			$contentType = null;
			if (! $mediaLink) {
				echo 'not_valid';
				return $this->getResponse ();
			} else {
				if (strpos ( $mediaLink, ".gif" ) > 0)
					$contentType = "image/gif";
				if (strpos ( $mediaLink, ".png" ) > 0)
					$contentType = "image/png";
				if (strpos ( $mediaLink, ".jpg" ) > 0 || strpos ( $mediaLink, ".jpeg" ) > 0)
					$contentType = "image/jpg";
				if (! $contentType) {
					echo 'not_valid';
					return $this->getResponse ();
				} else {
					$mediaMapper->createMediaImageLink ( $mediaName, $mediaLink, $contentType, $mediaParentId );
				}
			}
		}
		if ($action == 'video-link') {
			$mediaType = FAQParaConfig::MEDIA_TYPE_VIDEO_LINK;
			$contentType = null;
			if (! $mediaLink) {
				echo 'not_valid';
				return $this->getResponse ();
			} else {
				$contentType = "video/youtube";
				$mediaMapper->createMediaVideoLink ( $mediaName, $mediaLink, $contentType, $mediaParentId );
			}
		}
		if ($action == 'video-file') {
			$mediaType = FAQParaConfig::MEDIA_TYPE_VIDEO;
		}
		if ($action == 'image-file') {
			$mediaType = FAQParaConfig::MEDIA_TYPE_IMAGE;
		}
		if ($action == 'media-file') {
			$mediaType = FAQParaConfig::MEDIA_TYPE_FILE;
		}
		$mediaMapper->commit ();
		echo 'saved';
		return $this->getResponse ();
	}
	public function updateMediaAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$id = null;
		$act = null;
		$this->setLayoutAjax ();
		if ($this->getRequest ()->getPost ( 'act' )) {
			if ($this->getRequest ()->getPost ( 'id' )) {
				$mediaMapper = new MediaMapper ();
				$mediaMapper->deleteMedia ( $mediaMapper->findMediaByID ( $id ) );
				$mediaMapper->commit ();
				echo 'saved';
			}
		}

		return $this->getResponse ();
	}

	/**
	 *
	 * @todo upload file
	 * @return multitype:unknown |multitype:string NULL unknown
	 */
	public function uploadFileAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		// $file_type: image, video, file
		$this->setLayoutAjax ();
		$status = 'normal';
		$typeFile = $this->getRequest ()->getQuery ( "file_type" );
		$media_parent_id = $this->getRequest ()->getQuery ( 'media_parent_id' );
		$back_link = $this->request->getQuery ( 'back_link' );
		if ($typeFile) {
			return array (
					"file_type" => $typeFile,
					"media_parent_id" => $media_parent_id,
					"back_link" => $back_link
			);
		} else {

			$name = $this->getRequest ()->getPost ( 'name' );
			$mediaParentId = $this->request->getPost ( "media_parent_id" );
			$back_link = $this->getRequest ()->getPost ( "back_link" );
			$file_type = $this->getRequest ()->getPost ( "file_type" );
			if (! $mediaParentId)
				$mediaParentId = null;

			$file = $_FILES ['file'];
			$mediaMapper = new MediaMapper ();
			$isExisted = $mediaMapper->isMediaNameExisted ( Util::getIDCurrentUser (), $name, $mediaParentId );
			if (! $file)
				$status = 'not_valid';
			if (! $name)
				$status = 'not_valid';
			if ($isExisted) {
				$status = 'existed';
			} else {
				if ($file ['size'] > Appcfg::$img_media_size) {
					$status = "not_valid";
					var_dump ( "MAX-FILE-SIZE" );
				} else {
					if ($file_type == 'file') {
						$contentType = $file ['type'];
						$mediaMapper->createMediaFile ( $name, $file, $contentType, $mediaParentId );
						$mediaMapper->commit ();
						$status = 'saved';
					}
					if ($file_type == 'video') {
						$contentType = $file ['type'];
						$mediaMapper->createMediaVideo ( $name, $file, $contentType, $mediaParentId );
						$mediaMapper->commit ();
						$status = 'saved';
					}
					if ($file_type == 'image') {
						$contentType = $file ['type'];
						$mediaMapper->createMediaImage ( $name, $file, $contentType, $mediaParentId );
						$mediaMapper->commit ();
						$status = 'saved';
					}
				}
			}

			return array (
					'status' => $status,
					'back_link' => $back_link,
					'file_type' => $file_type,
					'media_parent_id' => $mediaParentId
			);
		}
	}

	/**
	 *
	 * @todo ajax create directory
	 * @return \Zend\Stdlib\ResponseInterface
	 */
	public function createDirectoryAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$this->setLayoutAjax ();
		return $this->getResponse ();
	}
	public function avatarAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$pagesize = 2;
		$page = 1;
		$end = false;
		$keyword = null;
		$from = 0;
		$to = $pagesize;
		$isSubmit = false;
		$file = null;
		$name = null;
		$mediaMapper = new MediaMapper ();
		if ($this->getRequest ()->getQuery ( 'keyword' )) {
			$keyword = $this->getRequest ()->getQuery ( 'keyword' );
		}
		if ($this->getRequest ()->getQuery ( 'page' )) {
			$page = $this->getRequest ()->getQuery ( 'page' );
			if ($page < 1) {
				$page = 1;
			}
			$from = ($page - 1) * $pagesize;
			$to = $pagesize;
		}
		if ($this->getRequest ()->getPost ( 'submit' )) {
			$isSubmit = true;
		}
		if ($isSubmit) {
			$file = $_FILES ['file'];
			$name = $this->getRequest ()->getPost ( 'name' );
			if ($file) {
				$mediaMapper->createAvatar ( $file ['tmp_name'], $name, $file ['type'] );
				$mediaMapper->commit ();
			}
			$page = 1;
			$from = 0;
			$to = $pagesize;
			$keyword = $name;
		}

		$lstAvatar = $mediaMapper->getListAvatar ( $keyword, $from, $to );
		if (($page) * $pagesize >= count ( $lstAvatar )) {

			$page = round ( count ( $lstAvatar ) / $pagesize, 0, PHP_ROUND_HALF_DOWN );
			$end = true;
		}
		$this->layout ( 'layout/media-avatar' );
		return array (
				'lstAvatar' => $lstAvatar,
				'keyword' => $keyword,
				'page' => $page,
				'end' => $end
		);
	}
	public function drapUploadAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		set_time_limit ( 0 );
		$allowed_ext = array (
				'gif',
				'jpeg',
				'jpg',
				'pjpeg',
				'png'
		);

		if ($this->getRequest ()->isPost () == false) {
			$res = array (
					'status' => 0,
					'fileName' => 'error',
					'mediaId' => '',
					'message' => ('Error! Wrong HTTP method!')
			);
			goto breadkPoint;
		}
		$file = $_FILES ['file'];

		if (array_key_exists ( 'file', $_FILES ) && $_FILES ['file'] ['error'] == 0) {

			$fileName = urlencode ( Util::convertUrlNameFile ( $file ['name'] ) );
			if (! in_array ( Util::get_extension ( $fileName ), $allowed_ext )) {
				$res = array (
						'status' => 0,
						'fileName' => 'error',
						'mediaId' => '',
						'message' => ('File chia sẻ chỉ chấp nhận định dạng sau: ' . implode ( ',', $allowed_ext ) . '!')
				);
				goto breadkPoint;
			}
			// Move the uploaded file from the temporary
			// directory to the uploads folder:


			$mediaMapper = new MediaMapper ();
			$media = $mediaMapper->createMediaImage ( $fileName, $file, FAQParaConfig::$MEDIA_MIME_TYPE [Util::get_extension ( $fileName )] );
			$titleImage = $media->getName();
			$contentType = $media->getContentType();
			$typeFile = Util::getTypeFile($contentType);
			$titleFileSeo = Util::convertUrlFileName($titleImage,$typeFile);
			$imageID=$media->getId();
			$res = array (
					'status' => 1,
					'fileName' => Util::convertUrlSeo ( $fileName, false ),
					'mediaId' => $imageID,
					'mediaUrl' => Appcfg::$domain."/media/get-media-image/images/".$imageID."/".$titleFileSeo,
					'message' => 'Upload file thành công!'
			);
			goto breadkPoint;
		}
		$res = array (
				'status' => 0,
				'fileName' => 'error',
				'mediaId' => '',
				'message' => 'Có lỗi xảy ra khi upload file!'
		);
		breadkPoint:
		echo json_encode ( $res );

		return $this->getResponse ();
	}
	public function deleteAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$this->setLayoutAjax ();
		$mediaId = $this->getRequest ()->getPost ( 'mediaId' );
		if ($mediaId != null || trim ( $mediaId ) != "") {

			$mediaMapper = new MediaMapper ();
			$status = $mediaMapper->deleteMediaById ( $mediaId, Util::getIDCurrentUser () ) ;

			$res = array (
					'status' => $status,
					'message' => 'success!'
			);
		} else {

			$res = array (
					'status' => 0,
					'message' => 'not allowed!'
			);
		}
		echo json_encode ( $res );

		return $this->getResponse ();
	}
	public function deactiveAction() {
		$privilege = Util::isPrivilege ( $this );
		if (! $privilege ['isAllowed']) {
			return $this->toNoticeWarning ( "Bạn cần đăng nhập trước!", 3000, "/user/login" );
		}
		$this->setLayoutAjax ();
		$mediaId = $this->getRequest ()->getPost ( 'mediaId' );
		if ($mediaId != null || trim ( $mediaId ) != "") {

			$mediaMapper = new MediaMapper ();
			$media=$mediaMapper->findMediaByID($mediaId);
			$status = $mediaMapper->deleteMedia ($media) ;

			$res = array (
					'status' => $status,
					'message' => 'success!'
			);
		} else {

			$res = array (
					'status' => 0,
					'message' => 'not allowed!'
			);
		}
		echo json_encode ( $res );

		return $this->getResponse ();
	}

	public function getAvatarAction()
	{
		$this->setLayoutAjax();
		$mediaMapper = new MediaMapper();
		$userID = $this->getEvent()
		->getRouteMatch()
		->getParam("id");
		$user = null;
		$mediaID = null;
		$user = Util::findUserById($userID);
		if($user){
			$mediaID =  $user->getAvatar()->getId();
		}

		if(empty($mediaID)){
			$mediaID = $this->params()->fromQuery('media');
		}
		$media = $mediaMapper->getImage($mediaID);
		if(empty($media)){
			return $this->toNoticeWarning("File không tồn tạ<i></i>!!");

		}
		if($media->getContentType()!=FAQParaConfig::MEDIA_TYPE_FILE){
			$contentType= $media->getContentType();
			header("Expires: Sat, 18 Jul 2015 05:00:00 GMT");
			header("Cache-Control: max-age=6000");
			header('Content-type: '.$contentType.';');
			echo $media->getFile()->getBytes();

		}
		return $this->getResponse();
	}
}