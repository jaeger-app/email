<?php
/**
 * Jaeger
 *
 * @copyright	Copyright (c) 2015-2016, mithra62
 * @link		http://jaeger-app.com
 * @version		1.0
 * @filesource 	./Email/SwiftAbstract.php
 */
namespace JaegerApp\Email;

/**
 * Jaeger - Email Abstract
 *
 * Defines what email objects should contain
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