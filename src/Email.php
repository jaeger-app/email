<?php
/**
 * Jaeger
 *
 * @copyright	Copyright (c) 2015-2016, mithra62
 * @link		http://jaeger-app.com
 * @version		1.0
 * @filesource 	./Email.php
 */
namespace JaegerApp;

use JaegerApp\Exceptions\EmailException;

/**
 * Jaeger - Email Object
 *
 * Wrapper to send email
 *
 * @package Email
 * @author Eric Lamb <eric@mithra62.com>
 */
class Email
{

    /**
     * The email addresses we're sending to
     * 
     * @var array
     */
    protected $to = array();

    /**
     * The email subect language key
     * 
     * @var string
     */
    protected $subject = false;

    /**
     * The email message language key
     * 
     * @var string
     */
    protected $message = false;

    /**
     * What type of email to send (html or text)
     * 
     * @var string
     */
    protected $mailtype = 'html';

    /**
     * The mailtype values we allow
     * 
     * @var array
     */
    protected $allowed_mailtypes = array(
        'html',
        'txt'
    );

    /**
     * The View object
     * 
     * @var \JaegerApp\View
     */
    protected $view = null;

    /**
     * The Language object
     * 
     * @var \JaegerApp\Language
     */
    protected $lang = null;

    /**
     * The mailer object
     * 
     * @var Swift_mailer
     */
    protected $mailer = null;

    /**
     * The mailer logging object
     * 
     * @var Swift_Plugins_Loggers_ArrayLogger
     */
    protected $mailer_logger = null;

    /**
     * The email configuration
     * 
     * @var array
     */
    protected $config = array();

    /**
     * The view options
     * 
     * @var array
     */
    protected $view_options = array();

    /**
     * The tmeplate to use for view output
     * 
     * @var string
     */
    protected $view_template = '';

    /**
     * An array of files to add as attachments to emails
     * 
     * @var array A key => value pair of file path => new name
     */
    protected $attachemnts = array();

    /**
     * The format the configuration is expected in
     * 
     * @var array
     */
    private $config_prototype = array(
        'type' => 'smtp', // choose between `php` and `smtp`
        'smtp_options' => array( // if `smtp` chosen above, this must be completed and accurate
            'host' => '',
            'connection_config' => array(
                'username' => '',
                'password' => ''
            ),
            'port' => ''
        )
    );

    /**
     * Sets the Language object
     * 
     * @param \JaegerApp\Language $lang            
     * @return \JaegerApp\Email
     */
    public function setLang(\JaegerApp\Language $lang)
    {
        $this->lang = $lang;
        return $this;
    }

    /**
     * Returns an instance of the Language object
     * 
     * @return \JaegerApp\Language
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Sets the View object
     * 
     * @param \JaegerApp\View $view            
     * @return \JaegerApp\Email
     */
    public function setView(\JaegerApp\View $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Returns an instance of the View object
     * 
     * @return \JaegerApp\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Sets the email config
     * 
     * @param array $config            
     * @return \JaegerApp\Email
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Compiles the options to use for the view
     * 
     * @param string $template            
     * @param array $view_data            
     * @return \JaegerApp\Email
     */
    public function setViewOptions($template, array $view_data = array())
    {
        $this->view_options = $view_data;
        $this->view_template = $template;
        return $this;
    }

    /**
     * Sets the TO email address
     *
     * Note that this method resets any previously added email addresses
     * 
     * @param string $to            
     * @return \JaegerApp\Email
     */
    public function setTo($to)
    {
        $this->to = (is_array($to) ? $to : array(
            $to
        ));
        return $this;
    }

    /**
     * Sets the email addresses to send to
     * 
     * @param string $to
     *            The Email address to send to
     * @return \JaegerApp\Email
     */
    public function addTo($to)
    {
        $this->to[] = $to;
        return $this;
    }

    /**
     * Adds an attachment to an email
     * 
     * @param string $file
     *            The full path to the attachment
     * @param string $name
     *            An alternative name to use for the attachment file
     */
    public function addAttachment($file, $name = false)
    {
        if (file_exists($file)) {
            $this->attachemnts[] = array(
                $file => $name
            );
        }
        
        return $this;
    }

    /**
     * Returns an array of attachments
     * 
     * @return string
     */
    public function getAttachments()
    {
        return $this->attachemnts;
    }

    /**
     * Returns the email addresses to send to
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Sets the email subject language key
     * 
     * @param string $subject
     *            The language key for the email subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Returns the email addresses to send to
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Returns the message language key
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the email message language key
     * 
     * @param string $message
     *            Should be a language file key
     * @return \JaegerApp\Email
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Returns the mailtype
     * 
     * @return string
     */
    public function getMailtype()
    {
        return $this->mailtype;
    }

    /**
     * Sets the mailtype
     * 
     * @param string $mailtype            
     * @return \JaegerApp\Email
     */
    public function setMailtype($mailtype)
    {
        $this->mailtype = $mailtype;
        return $this;
    }

    /**
     * Returns an instance of the mail object
     * 
     * @return Email\SwiftAbstract
     */
    public function getMailer()
    {
        if (is_null($this->mailer)) {
            if(class_exists('\Swift'))
            {
                if(version_compare(\Swift::VERSION, 4, '<=') && version_compare(\Swift::VERSION, 3, '>='))
                {
                    $mailer = new Email\Swift3($this->config);
                }
                else {
                    $mailer = new Email\Swift5($this->config);
                }
            }
            else {
                $mailer = new Email\Swift5($this->config); 
            }
            
            $this->mailer = $mailer;
        }
        
        return $this->mailer;
    }

    /**
     * Resets the email object
     * 
     * @return \JaegerApp\Email
     */
    public function clear()
    {
        $this->mailer = null;
        $this->to = $this->attachemnts = array();
        $this->subject = $this->message = false;
        return $this;
    }

    /**
     * Sends the email
     * 
     * @param array $vars            
     * @throws \InvalidArgumentException
     * @throws EmailException
     */
    public function send(array $vars = array())
    {
        if (count($this->getTo()) == 0) {
            throw new \InvalidArgumentException('A "To" email address is requried');
        }
        
        if ($this->getSubject() == '') {
            throw new \InvalidArgumentException('A subject for the email must be set');
        }
        
        if ($this->getMessage() == '') {
            throw new \InvalidArgumentException('There isn\'t a message set');
        }
        
        $valid_emails = array();
        foreach ($this->getTo() as $to) {
            if (filter_var(trim($to), FILTER_VALIDATE_EMAIL)) {
                $valid_emails[] = trim($to);
            }
        }
        
        if (! $valid_emails) {
            return;
        }
        
        $mailer = $this->getMailer();
        $subject = $this->getView()->render($this->getSubject(), $vars);
        $body_message = $this->getView()->render($this->getMessage(), $vars);
        $message = $mailer->getMessage($valid_emails, $this->config['from_email'], $this->config['sender_name'], $subject, $body_message, $this->getAttachments(), $this->getMailtype());
        
        if (! $mailer->send($message)) {
            print_r($mailer->getMailer()->mailer_logger->dump());
            exit();
            throw new EmailException($this->getMailer()->ErrorInfo);
        }
        
        $this->clear();
    }
}