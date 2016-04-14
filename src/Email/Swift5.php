<?php
namespace mithra62\Email;

use Swift_SmtpTransport;
use Swift_MailTransport;
use Swift_Message;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Plugins_Loggers_ArrayLogger;
use Swift_Plugins_LoggerPlugin;

class Swift5 extends SwiftAbstract
{
    /**
     * Set it up
     * @param unknown $config
     */
    public function __construct($config = array())
    {
        $this->config = $config;
        if( !class_exists('\Swift_Mailer') ) {
            require_once dirname(__FILE__).'../../../vendor/swiftmailer/swiftmailer/lib/swift_required.php';
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \mithra62\Email\SwiftAbstract::getMailer()
     */
    public function getMailer()
    {
        if(is_null($this->mailer))
        {
            if (isset($this->config['type']) && $this->config['type'] == 'smtp') {
                $transport = \Swift_SmtpTransport::newInstance($this->config['smtp_options']['host'], $this->config['smtp_options']['port']);
                $transport->setUsername($this->config['smtp_options']['connection_config']['username']);
                $transport->setPassword($this->config['smtp_options']['connection_config']['password']);
            } else {
                $transport = \Swift_MailTransport::newInstance();
            }
            
            $this->mailer = \Swift_Mailer::newInstance($transport);
            $this->mailer_logger = new \Swift_Plugins_Loggers_ArrayLogger();
            $this->mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($this->mailer_logger));
        }
        
        return $this->mailer;
    }
    
    /**
     * (non-PHPdoc)
     * @see \mithra62\Email\SwiftAbstract::getMessage()
     */
    public function getMessage(array $to, $from_email, $from_name, $subject, $message_body, array $attachments, $mail_type='html')
    {
        $message = \Swift_Message::newInstance();
        $message->setTo($to);
        if ($attachments) {
            foreach ($attachments as $attachment) {
                foreach ($attachment as $file => $alt_name) {
                    if ($alt_name == '') {
                        $message->attach(\Swift_Attachment::fromPath($file));
                    } else {
                        $message->attach(\Swift_Attachment::fromPath($file)->setFilename($alt_name));
                    }
                }
            }
        }
        
        $message->setFrom($from_email, $from_name);
        $message->setSubject($subject);
        if ($mail_type == 'html') {
            $message->setBody($message_body, 'text/html');
        } else {
            $message->setBody($message_body);
        }
        
        return $message;
    }
    
    public function send($message, $extra = null)
    {
        return $this->getMailer()->send($message);
    }
}