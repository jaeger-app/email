<?php
/**
 * Jaeger
 *
 * @copyright	Copyright (c) 2015-2016, mithra62
 * @link		http://jaeger-app.com
 * @version		1.0
 * @filesource 	./Email/Swift3.php
 */
namespace mithra62\Email;

/**
 * Jaeger - Swift3 Email Abstraction
 *
 * Defines what email objects should contain
 *
 * @package Email
 * @author Eric Lamb <eric@mithra62.com>
 */
class Swift3 extends SwiftAbstract
{
    public function __construct($config = array())
    {
        $this->config = $config;
    }
    
    public function getMailer()
    {
        if(is_null($this->mailer))
        {
            if (isset($this->config['type']) && $this->config['type'] == 'smtp') {

                $transport = new \Swift_Connection_SMTP(
                    $this->config['smtp_options']['host'],
                    $this->config['smtp_options']['port']
                );
                
                $transport->setUsername($this->config['smtp_options']['connection_config']['username']);
                $transport->setPassword($this->config['smtp_options']['connection_config']['password']);
            } else {
                $transport = new \Swift_Connection_NativeMail();
            }
            
            $this->mailer = new \Swift($transport);
        }
        
        return $this->mailer;
    }
    
    public function getMessage(array $to, $from_email, $from_name, $subject, $message_body, array $attachments, $mail_type='html')
    {
        $to_list = new \Swift_RecipientList();
        foreach ($to as $key => $addr) {
            $to_list->addTo($addr);
        }
        
        $this->to_list = $to_list;
        $this->from_email = $from_email;
        $this->from_name = $from_name;
        
        $message = new \Swift_Message($subject, $message_body, "text/".$mail_type);
        return $message;
    }
    
    public function send($message, $extra = null)
    {
        return $this->getMailer()->send($message, $this->to_list, new \Swift_Address($this->from_email, $this->from_name));
    }
}