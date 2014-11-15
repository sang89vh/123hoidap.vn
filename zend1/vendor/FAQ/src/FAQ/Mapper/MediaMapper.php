<?php

namespace FAQ\Mapper;

use FAQ\DB\Db;
use FAQ\FAQEntity\Question;
use FAQ\FAQEntity\User;
use FAQ\FAQEntity\Key;
use FAQ\FAQCommon\Util;
use FAQ\FAQEntity\Image;
use FAQ\FAQEntity\Media;
use FAQ\FAQCommon\FAQParaConfig;
use Doctrine\Common\Collections\ArrayCollection;
use FAQ\FAQCommon\Authcfg;

class MediaMapper extends Db {
	private $image;
	private $media;
	public function __construct() {
		parent::__construct ();
		$this->image = new Image ();
		$this->media = new Media ();
	}
	/**
	 *
	 * @param String $imageID
	 * @return \FAQ\FAQEntity\Image
	 */
	public function getImage($imageID) {
		return $this->image->find ( $imageID, true );
	}

	/**
	 *
	 * @param String $mediaID
	 * @return NULL Media
	 */
	public function findMediaByID($mediaID) {
		if (! $mediaID)
			return null;
		$media = $this->media;
		$media = $media->find ( $mediaID, true );
		return $media;
	}

	/**
	 *
	 * @author izzi
	 * @todo get list media is child of parentID
	 * @param String $userID
	 *        	(12341234fas341234)
	 * @param String $mediaType
	 *        	(image, video)
	 * @param String $mediaStatus
	 *        	(deleled, normal)
	 * @param String $ParentID
	 *        	(null, 13434123412412414dsf)
	 * @return
	 *
	 */
	public function getListMediaCurrent($userID, $parentID = null, $mediaType = null, $mediaStatus = null, $order = null, $role = null) {
		if(empty($order)){
			$order=array("uploadDate"=>desc);
		}
		$qb = $this->media->getQueryBuilder ();
		if ($parentID == null) {
			$qb = $qb->field ( "parent" )->equals ( null );
		} else {
			$qb = $qb->field ( "parent.id" )->equals ( $parentID );
		}
		if ($mediaStatus == null) {
			$qb = $qb->field ( "status" )->equals ( FAQParaConfig::MEDIA_STATUS_NORMAL );
		} else {
			$qb = $qb->field ( "status" )->equals ( $mediaStatus );
		}
		if ($role != Authcfg::ADMIN) {
			$qb = $qb->field ( "create_by.id" )->equals ( $userID );
		}
		if ($mediaType != null) {
			$qb = $qb->field ( "type" )->equals ( $mediaType );
		}
		$qb=Util::addOrder($qb, $order);
		$listMedia = $qb->getQuery ()->execute ();
		$arr = new ArrayCollection ();
		foreach ( $listMedia as $media ) {
			/* @var $media Media */
			$m = null;
			$m->id = $media->getId ();
			$m->name = $media->getName ();
			$m->type = $media->getType ();
			$m->contentType = $media->getContentType ();
			$m->link = $media->getLink ();
			$arr [] = $m;
		}
		return $arr;
	}
	public function getListMediaAll($userID, $parentID = null, $mediaType = null, $mediaStatus = null, $order = null) {
		$media = $this->media;
		$qb = $media->getQueryBuilder ();
		if ($parentID == null) {
			$qb->field ( "parent" )->equals ( null );
		} else {
			$qb->field ( "parent.id" )->equals ( $parentID );
		}
		if ($mediaStatus == null) {
			$qb = $qb->field ( "status" )->equals ( FAQParaConfig::MEDIA_STATUS_NORMAL );
		} else {
			$qb = $qb->field ( "status" )->equals ( $mediaStatus );
		}
		if ($mediaType != null) {
			$qb = $qb->field ( "type" )->equals ( $mediaType );
		}
		$listMedia = $qb->getQuery ()->execute ();
		$arr = new ArrayCollection ();
		foreach ( $listMedia as $k => $m ) {
			/* @var $m Media */
			$this->getChildMedia ( $m, $mediaType, $mediaStatus, $arr );
		}
		return $arr;
	}

	/**
	 *
	 * @param Media $media
	 * @param String $mediaType
	 * @param int $mediaStatus
	 * @param ArrayCollection $arr
	 */
	private function getChildMedia($media, $mediaType, $mediaStatus, $arr) {
		if ($media == null)
			return;
		else {
			$m = null;
			$m->id = $media->getId ();
			$m->name = $media->getName ();
			$m->type = $media->getType ();
			$m->contentType = $media->getContentType ();
			// type condition
			$isAddByType = false;
			if ($mediaType == null) {
				$isAddByType = true;
			} else {
				if ($mediaType == $media->getType ())
					$isAddByType = true;
			}
			// status condition
			$isAddByStatus = false;
			if ($mediaStatus == null) {
				if ($media->getStatus () == FAQParaConfig::MEDIA_STATUS_NORMAL)
					$isAddByStatus = true;
			} else {
				if ($media->getStatus () == $mediaStatus) {
					$isAddByStatus = true;
				}
			}
			// pass condition
			if ($isAddByStatus && $isAddByType) {
				$arr [] = $m;
			}
			foreach ( $media->getChildren () as $k => $child ) {
				$this->getChildMedia ( $child, $mediaType, $mediaStatus, $arr );
			}
		}
	}
	/**
	 *
	 * @author izzi
	 * @todo create navigator array from a media
	 * @param string $mediaID
	 * @return Array:
	 */
	public function getNavigator($mediaID) {
		$media = $this->media;
		$media = $media->find ( $mediaID, true );
		$arr = new ArrayCollection ();
		$this->getMediaParent ( $media, $arr );
		return array_reverse ( $arr->toArray () );
	}

	/**
	 * @izzi
	 *
	 * @todo de quy tao navigator.
	 * @param Media $media
	 */
	private function getMediaParent($media, $arrMedia) {
		if ($media == null)
			return;
		else {
			$m = null;
			$m->id = $media->getId ();
			$m->name = $media->getName ();
			$arrMedia [] = $m;
			$this->getMediaParent ( $media->getParent (), $arrMedia );
		}
	}
	public function createMediaVideoLink($mediaName, $mediaLink, $contentType, $parentID = null) {
		$media = new Media ();
		$media->setType ( FAQParaConfig::MEDIA_TYPE_VIDEO_LINK );
		$media->setName ( $mediaName );
		$media->setLink ( $mediaLink );
		$media->setContentType ( $contentType );
		if ($parentID) {
			$parent = $media->find ( $parentID, true );
			$media->setParent ( $parent );
			$parent->setChildren ( $media );
			$parent->setStatusUpdateRefere ();
		} else {
			$media->setParent ( null );
		}
		$media->insert ();
		return $media;
	}
	public function createMediaImageLink($mediaName, $mediaLink, $contentType, $parentID = null) {
		$media = new Media ();
		$media->setType ( FAQParaConfig::MEDIA_TYPE_IMAGE_LINK );
		$media->setName ( $mediaName );
		$media->setLink ( $mediaLink );
		$media->setContentType ( $contentType );
		$parent = $media->find ( $parentID, true );
		if (!empty($parent)) {

			$media->setParent ( $parent );
			$parent->setChildren ( $media );
			$parent->setStatusUpdateRefere ();
		} else {
			$media->setParent ( null );
		}
		$media->insert ();
		return $media;
	}
	public function createMediaFile($mediaName, $mediaFile, $contentType, $parentID = null) {
		$media = new Media ();
		$media->setType ( FAQParaConfig::MEDIA_TYPE_FILE );
		$media->setName ( $mediaName );
		// create file at here
		$media->setFile ( $mediaFile ['tmp_name'] );
		$media->setContentType ( $contentType );
		if ($parentID) {
			$parent = $media->find ( $parentID, true );
			$media->setParent ( $parent );
			$parent->setChildren ( $media );
			$parent->setStatusUpdateRefere ();
		} else {
			$media->setParent ( null );
		}
		$media->insert ();
		return $media;
	}
	public function createMediaVideo($mediaName, $mediaFile, $contentType, $parentID = null) {
		$media = new Media ();
		$media->setType ( FAQParaConfig::MEDIA_TYPE_VIDEO );
		$media->setName ( $mediaName );
		// create file at here
		$media->setFile ( $mediaFile ['tmp_name'] );
		$media->setContentType ( $contentType );
		if ($parentID) {
			$parent = $media->find ( $parentID, true );
			$media->setParent ( $parent );
			$parent->setChildren ( $media );
			$parent->setStatusUpdateRefere ();
		} else {
			$media->setParent ( null );
		}
		$media->insert ();
		return $media;
	}
	public function createMediaImage($mediaName, $mediaFile, $contentType, $parentID = null) {
		$media = new Media ();
		$media->setType ( FAQParaConfig::MEDIA_TYPE_IMAGE );
		$media->setName ( $mediaName );
		// create file at here
		$media->setFile ( $mediaFile ['tmp_name'] );
		$media->setContentType ( $contentType );
		$parent = $media->find ( $parentID, true );
		if (!empty($parent)) {

			$media->setParent ( $parent );
			$parent->setChildren ( $media );
			$parent->setStatusUpdateRefere ();
		} else {
			$media->setParent ( null );
		}
		$media->insert ();
		$this->commit ();
		return $media;
	}
	public function createMediaDir($mediaName, $parentID = null) {
		$media = new Media ();
		$media->setType ( FAQParaConfig::MEDIA_TYPE_DIR );
		$media->setName ( $mediaName );
		if ($parentID) {
			$parent = $media->find ( $parentID, true );
			$media->setParent ( $parent );
			$parent->setChildren ( $media );
			$parent->setStatusUpdateRefere ();
		} else {
			$media->setParent ( null );
		}
		$media->insert ();
		return $media;
	}

	/**
	 *
	 * @todo delete a media by set status = deleted
	 * @param Media $media
	 */
	public function deleteMedia($media) {
		if ($media == null){
			return 0;
		}
		if(Util::getIDCurrentUser()!=$media->getCreate_by()->getId()){
			return -1;
		}
		$media->setStatus ( FAQParaConfig::MEDIA_STATUS_DELETED );
		$children = $media->getChildren ();
		foreach ( $children as $k => $child ) {
			$this->deleteMedia ( $child );
		}
		return 1;
	}
	/**
	 *
	 * @author sang
	 * @todo delete a media by set status = deleted
	 * @param String $mediaId, String $IdUserDelete
	 */
	public function deleteMediaById($mediaId, $IdUserDelete) {
		$media = $this->media->find ( $mediaId, true );
		if ($media==null||$media->getCreate_by ()->getId () != $IdUserDelete) {
			return 0;
		} else {
			if ($this->media->remove ( $mediaId )) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	/**
	 *
	 * @todo check media name is existing in a set
	 * @param String $userID
	 * @param String $mediaName
	 * @param String $parentID
	 * @return boolean
	 */
	public function isMediaNameExisted($userID, $mediaName, $parentID = null) {
		if ($mediaName == "" || $mediaName == null)
			return false;
		$media = $this->media;
		$qb = $media->getQueryBuilder ();
		$qb = $qb->field ( "create_by.id" )->equals ( $userID );
		if ($parentID == null) {
			$qb->field ( "parent" )->equals ( null );
		} else {
			$qb->field ( "parent.id" )->equals ( $parentID );
		}
		$qb->field ( "name" )->equals ( $mediaName );
		$rs = $qb->getQuery ()->execute ();
		if (count ( $rs ) > 0)
			return true;
		else
			return false;
	}

	/**
	 *
	 * @todo get list image from Image collection
	 * @param String $keyword
	 * @param int $from
	 * @param int $to
	 * @return NULL Ambigous \Doctrine\MongoDB\EagerCursor, \Doctrine\MongoDB\Cursor, Cursor, boolean, multitype:, \Doctrine\MongoDB\ArrayIterator, NULL, unknown, number, object>
	 */
	public function getListAvatar($keyword, $from, $to) {
		$avatar = $this->image;
		$qb = $avatar->getQueryBuilder ();
		if ($keyword) {
			$regexObj = new \MongoRegex ( "/^" . $keyword . "/i" );
			$qb->field ( 'name' )->equals ( $regexObj );
		}
		if ($from) {
			$qb->skip ( $from );
		}
		if ($to) {
			$qb->limit ( $to );
		}
		$lst_avatar = $qb->getQuery ()->execute ();
		if (count ( $lst_avatar ) == 0) {
			return null;
		}
		return $lst_avatar;
	}
	public function createAvatar($file, $name, $contentType) {
		$image = new Image ();
		$image->setContentType ( $contentType );
		$image->setFile ( $file );
		$image->setName ( $name );
		$image->insert ();
	}
}

?>