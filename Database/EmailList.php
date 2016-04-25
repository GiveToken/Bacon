<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with email lists.
 */
class EmailList extends \Sizzle\Bacon\DatabaseEntity
{
    protected $user_id;
    protected $name;

    /**
     * This function constructs the class
     *
     * @param mixed  $value - optional id or name of the email list
     * @param string $key   - if $value is id or name
     */
    public function __construct($value = null, string $key = 'id')
    {
        if ($value !== null) {
            if ($key == 'id') {
                $id = (int) $value;
                $token = $this->execute_query(
                    "SELECT * FROM email_list
                    WHERE id = '$id'
                    AND deleted IS NULL"
                )->fetch_object("Sizzle\Bacon\Database\EmailList");
            } elseif ($key == 'name') {
                $value = $this->escape_string($value);
                $token = $this->execute_query(
                    "SELECT * FROM email_list
                    WHERE name = '$value'
                    AND deleted IS NULL"
                )->fetch_object("Sizzle\Bacon\Database\EmailList");
            }
            if (isset($token) && is_object($token)) {
                foreach (get_object_vars($token) as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * This function creates an entry in the email_list table
     *
     * @param int    $user_id - id of the user
     * @param string $name    - list name
     *
     * @return int $id - id of inserted row or 0 on fail
     */
    public function create(int $user_id, string $name)
    {
        $this->unsetAll();
        $this->user_id = $user_id;
        $this->name = $name;
        $this->save();
        return $this->id;
    }

    /**
     * This function updates the email_list name column
     *
     * @param string $name - list name
     *
     * @return boolean - success of update
     */
    public function update(string $name)
    {
        if (isset($this->id)) {
            $this->name = $name;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * This function marks an entry deleted in the email_list table
     *
     * @return boolean - success of deletion
     */
    public function delete()
    {
        $success = false;
        if (isset($this->id)) {
            $sql = "UPDATE email_list SET deleted = NOW() WHERE id = {$this->id}";
            execute($sql);
            $vars = get_class_vars(get_class($this));
            foreach ($vars as $key=>$value) {
                unset($this->$key);
            }
            $success = true;
        }
        return $success;
    }

    /**
     * This function gets email list information by the user id
     *
     * @param int $user_id - id of the user
     *
     * @return array - email lists associated with the user
     */
    public function getByUserId(int $user_id)
    {
        $return = array();
        $user_id = (int) $user_id;
        $query = "SELECT `name`, `id`
                  FROM email_list
                  WHERE deleted IS NULL
                  AND user_id = '$user_id'";
        $results = $this->execute_query($query);
        while ($row = $results->fetch_assoc()) {
            $return[] = $row;
        }
        return $return;
    }
}
