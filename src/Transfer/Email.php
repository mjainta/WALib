<?php
/**
 * Holds Email.
 *
 * @package WALib
 */
namespace WALib\Transfer;

use \WALib\Application\AppConfig as AppConfig;
/**
 * An E-mail wrapper which uses PHPMailer to perform E-mail actions.
 *
 * @package WALib
 */
class Email
{
    /**
     * The PHPMailer instance used by this class to perform E-mail actions.
     *
     * @var \PHPMailer
     */
    protected $_mailer;

    /**
     * Sets the language for the translations.
     */
    public function __construct()
    {
        /*
         * Create a new PHPMailer instance.
         */
        $this->_mailer = new \PHPMailer();

        /*
         * Tell PHPMailer to use SMTP.
         */
        $this->_mailer->IsSMTP();

        /*
         * Enable SMTP debugging.
         * 0 = off (for production use)
         * 1 = client messages
         * 2 = client and server messages
         */
        if(AppConfig::get('environment') == 'development')
        {
            $this->_mailer->SMTPDebug = 2;
        }
        else
        {
            $this->_mailer->SMTPDebug = 0;
        }

        /*
         * Ask for HTML-friendly debug output.
         */
        $this->_mailer->Debugoutput = 'html';

        /*
         * Set the encryption system to use tls.
         */
        $this->_mailer->SMTPSecure = 'tls';

        /*
         * Whether to use SMTP authentication.
         */
        $this->_mailer->SMTPAuth = true;
    }

    public function sendMail($mailConfigRef, $body, $bodyAlt, $subject, $toAddr, $toName = '')
    {
        $mailAccounts = \WALib\Application\AppConfig::get('mailAccounts');
        $registerAccount = $mailAccounts[$mailConfigRef];

        /*
         * Set the hostname of the mail server.
         */
        $this->_mailer->Host = $registerAccount['host'];

        /*
         * Set the SMTP port number.
         */
        $this->_mailer->Port = $registerAccount['port'];

        /*
         * Username to use for SMTP authentication.
         */
        $this->_mailer->Username = $registerAccount['address'];

        /*
         * Password to use for SMTP authentication.
         */
        $this->_mailer->Password = $registerAccount['password'];

        /*
         * Set who the message is to be sent from.
         */
        $this->_mailer->SetFrom($registerAccount['address'], $registerAccount['replyName']);

        /*
         * Set an alternative reply-to address.
         */
        $this->_mailer->AddReplyTo($registerAccount['address'], $registerAccount['replyName']);

        /*
         * Set who the message is to be sent to.
         */
        $this->_mailer->AddAddress($toAddr, $toName);

        /*
         * Set the subject line.
         */
        $this->_mailer->Subject = $subject;

        /*
         * Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body.
         */
        $this->_mailer->Body = $body;

        /*
         * Replace the plain text body with one created manually.
         */
        $this->_mailer->AltBody = $bodyAlt;

        /*
         * Send the message and check for errors.
         */
        if(!$this->_mailer->Send())
        {
            /*
             * @todo put log message here
             */
            echo 'Mailer Error: '.$this->_mailer->ErrorInfo;
            return false;
        }
        else
        {
            return true;
        }
    }
}