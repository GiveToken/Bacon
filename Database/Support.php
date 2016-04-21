<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with sent support email.
 */
class Support extends \Sizzle\Bacon\DatabaseEntity
{
    protected $email_address;
    protected $message;

    /**
     * Saves a receipt of the sent support mail..
     *
     * @param $email_address
     * @param $message
     */
    public static function create(string $email_address, string $message)
    {
        $Support = new Support();
        $Support->email_address = $email_address;
        $Support->message = $message;
        $Support->save();
        return $Support;
    }
}
