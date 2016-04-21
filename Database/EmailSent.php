<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with emails sent by customers.
 */
class EmailSent extends \Sizzle\Bacon\DatabaseEntity
{
    protected $email_credential_id;
    protected $body;
    protected $recruiting_token_id;
    protected $from;
    protected $to;
    protected $cc;
    protected $bcc;
    protected $reply_to;
    protected $subject;
    protected $success;
    protected $error_message;

    /**
     * This function creates an entry in the email_sent table
     *
     * @param array $details - information to insert
     *
     * @return int $id - id of inserted row or 0 on fail
     */
    public function create(array $details)
    {
        $id = 0;
        if (is_array($details) && isset($details['to'], $details['from'], $details['subject'],$details['body'], $details['email_credential_id'], $details['success'])
        ) {
            // details contains minimal subset

            $keys = get_class_vars(get_class($this));
            $this->unsetAll();
            foreach ($details as $key=>$value) {
                if (array_key_exists($key, $keys)) {
                    $this->$key = $value;
                }
            }
            $this->save();
            $id = $this->id;
        }
        return $id;
    }
}
