<?php
/**
 * mithra62
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2015, mithra62, Eric Lamb.
 * @link		http://mithra62.com/
 * @version		1.0
 * @filesource 	./mithra62/Email/SwiftAbstract.php
 */
 
namespace mithra62\Email;

/**
 * mithra62 - Email Object
 *
 * Wrapper to send email
 *
 * @package Email
 * @author Eric Lamb <eric@mithra62.com>
 */
abstract class SwiftAbstract
{
    /**
     * The configuration details for sending the email
     * @var array
     */
    protected $config = array();
    
    /**
     * The version of Swiftmailer we're using
     * @var string
     */
    protected $version;
    
    /**
     * The instance of Swiftmailer
     * @var \Swift_Mailer
     */
    protected $mailer;
    
    /**
     * Returns the version of Swiftmailer we're using
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
    
    /**
     * Returns the mailer object
     * @return Swift_Mailer
     */
    abstract public function getMailer();   
    
    /**
     * Abstract for creating the message object
     * @param array $to
     * @param string $from_email
     * @param string $from_name
     * @param string $subject
     * @param string $message_body
     * @param array $attachments
     * @param string $mail_type
     */
    abstract public function getMessage(array $to, $from_email, $from_name, $subject, $message_body, array $attachments, $mail_type='html');
    
    /**
     * Wrapper to send the message
     * @param object $message
     */
    abstract public function send($message, $extra = null);
}