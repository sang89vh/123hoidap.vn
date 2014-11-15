<?php
/**
 * memcacheSessionHandler class
 * @class           memcacheSessionHandler
 * @file            memcacheSessionHandler.class.php
 * @brief           This class is used to store session data with memcache, it store in json the session to be used more easily in Node.JS
 * @version         0.1
 * @date            2012-04-11
 * @author          Deisss
 * @licence         LGPLv3
 * This class is used to store session data with memcache, it store in json the session to be used more easily in Node.JS
 */
namespace FAQ\FAQCommon;
use Memcache;
use Zend\Session\SaveHandler\SaveHandlerInterface;
class memcacheSessionHandler implements SaveHandlerInterface{
	static $singleMemcache;
    private $host = "192.168.2.104";
    private $port = 11211;
    private $lifetime = 0;
    private $timeOut = 10800;
    private $memcache = null;

    /**
     * Constructor
     */
    public function __construct(){
        if(memcacheSessionHandler::$singleMemcache==null){
        	memcacheSessionHandler::$singleMemcache = new Memcache;
        	memcacheSessionHandler::$singleMemcache->connect($this->host, $this->port,$this->timeOut) or die("Error : Memcache is not ready");
        	session_set_save_handler(
        	array($this, "open"),
        	array($this, "close"),
        	array($this, "read"),
        	array($this, "write"),
        	array($this, "destroy"),
        	array($this, "gc")
        	);
        }
    	$this->memcache = memcacheSessionHandler::$singleMemcache;
    }

    /**
     * Destructor
     */
    public function __destruct(){
        session_write_close();
        $this->memcache->close();
    }

    /**
     * Open the session handler, set the lifetime ot session.gc_maxlifetime
     * @return boolean True if everything succeed
     */
    public function open($path, $name){
        $this->lifetime = ini_get('session.gc_maxlifetime');
        return true;
    }

    /**
     * Read the id
     * @param string $id The SESSID to search for
     * @return string The session saved previously
     */
    public function read($id){
        $tmp = $_SESSION;
        $_SESSION = json_decode($this->memcache->get("sessions/{$id}"), true);
        if(isset($_SESSION) && !empty($_SESSION) && $_SESSION != null){
            $new_data = session_encode();
            $_SESSION = $tmp;
            return $new_data;
        }else{
            return "";
        }
    }

    /**
     * Write the session data, convert to json before storing
     * @param string $id The SESSID to save
     * @param string $data The data to store, already serialized by PHP
     * @return boolean True if memcached was able to write the session data
     */
    public function write($id, $data){
        $tmp = $_SESSION;
        session_decode($data);
        $new_data = $_SESSION;
        $_SESSION = $tmp;
        return $this->memcache->set("sessions/{$id}", json_encode($new_data), 0, $this->lifetime);
    }

    /**
     * Delete object in session
     * @param string $id The SESSID to delete
     * @return boolean True if memcached was able delete session data
     */
    public function destroy($id){
        return $this->memcache->delete("sessions/{$id}");
    }

    /**
     * Close gc
     * @return boolean Always true
     */
    public function gc($maxlifetime){
        return true;
    }

    /**
     * Close session
     * @return boolean Always true
     */
    public function close(){
        return true;
    }
}
?>