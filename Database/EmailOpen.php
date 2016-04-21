<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with email_open.
 */
class EmailOpen extends \Sizzle\Bacon\DatabaseEntity
{
    protected $email_template_id;
    protected $email_address;
    protected $recruiting_token_id;

    /**
     * This function creates an entry in the email_open table
     *
     * @param int    $template - id of the email template
     * @param string $email    - email recipient that opened the email
     * @param int    $token    - (optional) recruiting token id
     *
     * @return int $id - id of inserted row or 0 on fail
     */
    public function create(int $template, string $email, int $token = null)
    {
        $this->unsetAll();
        $this->email_template_id = $template;
        $this->email_address = $email;
        $this->recruiting_token_id = $token;
        $this->save();
        return $this->id;
    }

}
