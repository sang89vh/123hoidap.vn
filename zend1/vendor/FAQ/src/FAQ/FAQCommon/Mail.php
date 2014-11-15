<?php
namespace FAQ\FAQCommon;
use Zend\Mail\Message;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Transport\Smtp as SmtpTransport;
/**
 *
 * @author izzi
 *
 */
class Mail {
    // config mail server
    private $host = 'smtp.live.com';
    private $name = 'smtp.live.com';
    private $port = '587';
    private $username = 'support@123hoidap.vn';
    private $password = 'Asdfqwer123';
    
    private $message;
    private $option;
    private $transport;
    public function __construct(){
        $this->message = new Message();
        $this->message->addFrom($this->username);
        $this->options   = new SmtpOptions(array(
        		'name'              => $this->name,
        		'host'              => $this->host,
        		'port'              => $this->port,
        		'connection_class'  => 'login',
        		'connection_config' => array(
        				'ssl' => 'tls',
        				'username' => $this->username,
        				'password' => $this->password,
        		),
        ));
        $this->transport = new SmtpTransport();
        $this->transport->setOptions($this->options);
    }
    
    public function sendSuccessRegistrator($to, $subject, $body){
        $this->message->addTo($to)
         ->setSubject($subject)
         ->setBody($body);
         $rs = $this->transport->send($this->message);
    }
    
    public function sendPasswordRecover($to, $pass){
        $this->message->addTo($to)
            ->setSubject("Khôi phục mật khẩu từ QApolo")
            ->setBody("Mật khẩu của bạn là:" . $pass);
        $rs = $this->transport->send($this->message);
    }
    
}
?>