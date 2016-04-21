<?php
namespace Sizzle\Bacon\Service;

use google\appengine\api\mail\Message;

/**
 * This class is for sending mail messages through Google App Engine
 */
class GoogleMail
{
    /**
     * Sends mail assuming you're inside Google App Engine
     *
     * @param string $to      - who to send to
     * @param string $subject - the email subject
     * @param string $message - the email message
     * @param string $from    - who email is from
     *
     * @return boolean - success of send
     */
    public function sendMail(string $to, string $subject, string $message, string $from)
    {
        try {
            $mail_options = [
                "sender" => $from,
                "to" => $to,
                "subject" => $subject,
                "htmlBody" => $message
            ];
            $message = new Message($mail_options);
            $message->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
