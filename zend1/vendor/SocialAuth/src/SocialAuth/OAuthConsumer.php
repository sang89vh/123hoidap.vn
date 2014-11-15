<?php
// vim: foldmethod=marker

/* Generic exception class
 */

namespace SocialAuth;
use Exception;

if (!class_exists('OAuthException')) {
  class OAuthException extends Exception {
    // pass
   
  }
}

class OAuthConsumer {
  public $key;
  public $secret;

  function __construct($key, $secret, $callback_url=NULL) {
    $this->key = $key;
    $this->secret = $secret;
    $this->callback_url = $callback_url;
  }

  function __toString() {
    return "OAuthConsumer[key=$this->key,secret=$this->secret]";
  }
}


