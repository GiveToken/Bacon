<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with email credentials.
 */
class EmailCredential extends \Sizzle\Bacon\DatabaseEntity
{
    protected $user_id;
    protected $username;
    protected $password;
    protected $smtp_host;
    protected $smtp_port;

    /**
     * This function constructs the class
     *
     * @param int $id - optional id of the email credentials
     */
    public function __construct(int $id = null)
    {
        if ($id !== null) {
            $id = (int) $id;
            $token = $this->execute_query(
                "SELECT * FROM email_credential
                WHERE id = '$id'
                AND deleted IS NULL"
            )->fetch_object("Sizzle\Bacon\Database\EmailCredential");
            if (is_object($token)) {
                foreach (get_object_vars($token) as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * This function creates an entry in the email_credential table
     *
     * @param int    $user_id   - id of the user
     * @param string $username  - credential username
     * @param string $password  - credential password
     * @param string $smtp_host - the SMTP host to connect to
     * @param string $smtp_port - the SMTP port to connect to on that host
     *
     * @return int $id - id of inserted row or 0 on fail
     */
    public function create(int $user_id, string $username, string $password, string $smtp_host, string $smtp_port)
    {
        $this->unsetAll();
        $this->user_id = $user_id;
        $this->username = $username;
        $this->password = $password;
        $this->smtp_host = $smtp_host;
        $this->smtp_port = $smtp_port;
        $this->save();
        return $this->id;
    }

    /**
     * This function updates the email_credential table by marking the old
     * credentials deleted and creating new ones. This is so we still have any
     * old credentials used to send emails for a robust audit trail since
     * we're sending emails with their credentials.
     *
     * @param string $username  - credential username
     * @param string $password  - credential password
     * @param string $smtp_host - the SMTP host to connect to
     * @param string $smtp_port - the SMTP port to connect to on that host
     *
     * @return boolean - success of update
     */
    public function update(string $username, string $password, string $smtp_host, string $smtp_port)
    {
        $id = 0;
        if (isset($this->id)) {
            $user_id = $this->user_id;
            $this->delete();
            $id = $this->create($user_id, $username, $password, $smtp_host, $smtp_port);
        }
        return ($id > 0);
    }

    /**
     * This function marks an entry deleted in the email_credential table
     *
     * @return boolean - success of deletion
     */
    public function delete()
    {
        $success = false;
        if (isset($this->id)) {
            $sql = "UPDATE email_credential SET deleted = NOW() WHERE id = {$this->id}";
            $this->execute_query($sql);
            $vars = get_class_vars(get_class($this));
            foreach ($vars as $key=>$value) {
                if ($key != 'readOnly') {
                    unset($this->$key);
                }
            }
            $success = true;
        }
        return $success;
    }

    /**
     * This function gets email credential information by the user id
     *
     * @param int $user_id - id of the user
     *
     * @return array - email credentials associated with the user
     */
    public function getByUserId(int $user_id)
    {
        $return = array();
        $user_id = (int) $user_id;
        $query = "SELECT CONCAT(`username`, '@', smtp_host, ':', smtp_port) AS credential, `id`
                  FROM email_credential
                  WHERE deleted IS NULL
                  AND user_id = '$user_id'";
        $results = $this->execute_query($query);
        while ($row = $results->fetch_assoc()) {
            $return[] = $row;
        }
        return $return;
    }
}
