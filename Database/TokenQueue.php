<?php
namespace Sizzle\Bacon\Database;

use Sizzle\Bacon\Connection;

/**
 * This class is for interacting with the token_queue table.
 */
class TokenQueue extends \Sizzle\Bacon\DatabaseEntity
{
    protected $email_address;
    protected $subject;
    protected $body;
    protected $source; // WEBSITE_UPLOAD, EMAIL, MANUAL
    protected $worked;

    /**
     * Marks the queue item as worked
     *
     * @return boolean - success of update
     */
    public function markWorked()
    {
        if (isset($this->id)) {
            $sql = "UPDATE token_queue SET worked = now() WHERE worked IS NULL AND id = '{$this->id}'";
            $this->execute_query($sql);
            return 1 == Connection::$mysqli->affected_rows;
        }
        return false;
    }

    /**
     * Gets the unworked items in the queue
     *
     * @param int $count - how many items to return (defaults to 10)
     *
     * @return array - the unworked queue elements
     */
    public function getUnworked(int $count = 10)
    {
        $return = array();
        if (0 < (int) $count) {
            $sql = "SELECT `email_address`, `subject`, `body`, `source`
                    FROM token_queue
                    WHERE worked IS NULL
                    ORDER BY created ASC
                    LIMIT {$count}";
            $return = ($this->execute_query($sql))->fetch_all(MYSQLI_ASSOC);
        }
        return $return;
    }
}
