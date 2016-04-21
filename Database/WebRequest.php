<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for interacting with the web_request table.
 */
class WebRequest extends \Sizzle\Bacon\DatabaseEntity
{
    protected $visitor_cookie;
    protected $user_id;
    protected $host;
    protected $user_agent;
    protected $uri;
    protected $remote_ip;
    protected $script;


    /**
     * Is this a new visitor?
     *
     * @param string $visitor_cookie - the visitor cookie from the user's browser
     *
     * @return boolean - is it?
     */
    public function newVisitor(string $visitor_cookie)
    {
        $visitor_cookie = escape_string($visitor_cookie);
        $sql = "SELECT COUNT(*) requests FROM web_request
                WHERE visitor_cookie = '$visitor_cookie'";
        $result = execute_query($sql);
        if (($row = $result->fetch_assoc()) && $row['requests'] > 3) {
            return false;
        } else {
            return true;
        }
    }
}
