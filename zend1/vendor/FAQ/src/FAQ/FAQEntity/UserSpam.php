<?php

namespace FAQ\FAQEntity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use FAQ\DB\EntityEmbed;
use FAQ\FAQCommon\Util;
use FAQ\FAQCommon\FAQParaConfig;

/**
 * @ODM\EmbeddedDocument
 *
 * @todo Luu thong thong tin bao vi pham cho cau hoi, bai viet
 */
class UserSpam extends EntityEmbed {

	/**
	 * @ODM\Int
	 *
	 * @todo FAQParaconfig::TYPE_VOTE_SPAM
	 *       FAQParaconfig::TYPE_VOTE_UNSPAM
	 */
	private $spam_or_unspam;
	/**
	 * @ODM\String
	 */
	private $type;

	/**
	 * @ODM\Date
	 */
	private $date_updated;

	/**
	 * @ODM\ReferenceOne(targetDocument="User",cascade={"detach","merge","refresh","persist"})
	 */
	private $create_by;
	public function __construct() {
		parent::__construct ();
		$this->spam_or_unspam = FAQParaConfig::TYPE_VOTE_SPAM;

	}
	/**
	 *
	 * @return String
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 *
	 * @param String $type
	 */
	public function setType($type) {
		$this->type = $type;
		return $this;
	}

	/**
	 *
	 * @return Date
	 */
	public function getDateUpdated() {
		return $this->date_updated;
	}

	/**
	 *
	 * @param Date $date_updated
	 */
	public function setDateUpdated($date_updated) {
		$this->date_updated = $date_updated;
		return $this;
	}

	/**
	 *
	 * @todo is person spam it
	 *
	 * @return User
	 */
	public function getCreateBy() {
		return $this->create_by;
	}

	/**
	 *
	 * @todo is persion spam it
	 *
	 * @param
	 *        	User
	 */
	public function setCreateBy($create_by) {
		$this->create_by = $create_by;
		return $this;
	}

	/**
	 *
	 * @return the Int
	 */
	public function getSpamOrUnspam() {
		return $this->spam_or_unspam;
	}

	/**
	 *
	 * @param Int $spam_or_unspam
	 */
	public function setSpamOrUnspam($spam_or_unspam) {
		$this->spam_or_unspam = $spam_or_unspam;
		return $this;
	}

	/**
	 * @odm\PrePersist
	 */
	public function autoSetDateChange() {
		if (! $this->date_updated) {
			$this->date_updated = Util::getCurrentTime ();
		}
	}
}