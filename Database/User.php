<?php
namespace Sizzle\Bacon\Database;

use \Sizzle\Bacon\{
    Connection
};

/**
 * This class is for interacting with the user table.
 */
class User extends \Sizzle\Bacon\DatabaseEntity
{
    protected $email_address;
    protected $first_name;
    protected $last_name;
    protected $organization_id;
    protected $password = null;
    protected $activation_key = null;
    protected $admin = "N";
    protected $stripe_id;
    protected $active_until;
    protected $position;
    protected $linkedin;
    protected $face_image;
    protected $about;
    protected $user_group;
    protected $group_admin;
    protected $reset_code;
    protected $internal;
    protected $allow_token_responses;
    protected $receive_token_notifications;

    /**
     * Sees if an email corresponds to a user
     *
     * @param string $email_address - email address of potential user
     *
     * @return boolean - does the user exist in the database??
     */
    public function exists(string $email_address)
    {
        $exists = false;
        $user = $this->fetch($email_address);
        if ($user) {
            $exists = true;
        }
        return $exists;
    }

    /**
     * Fetches a user object by email or reset code
     *
     * @param string $value - the value of the key
     * @param string $key   - email_address or reset_code
     *
     * @return User - the corresponding object
     */
    public function fetch($value, string $key = 'email_address')
    {
        $user = null;
        $value = $this->escape_string($value);
        switch ($key) {
        case 'api_key':
            $condition = "api_key = '$value'";
            break;
        case 'email_address':
            $condition = "upper(email_address) = '".strtoupper($value)."'";
            break;
        case 'reset_code':
            $condition = "reset_code = '$value'";
            break;
        default:
            return $user;
        }
        $result = $this->execute_query(
            "SELECT * FROM user
            WHERE $condition"
        );
        if ($result->num_rows > 0) {
            $user = $result->fetch_object("Sizzle\Bacon\Database\User");
        }
        return $user;
    }

    /**
     * Saves the user information from the class properties
     */
    public function save()
    {
        if (!isset($this->organization_id)) {
            $this->organization_id = false !== strpos($this->email_address, 'gosizzle.io') ? '1' : null;
        }
        parent::save();
    }

    /**
     * Activates a new user
     *
     * @param string $key - the value of the activation key
     *
     * @return boolean - was the $key for that user who is now activated
     */
    public function activate(string $key)
    {
        $key = $this->escape_string($key);
        $rows_affected = $this->execute_query(
            "UPDATE user
            SET activation_key = NULL
            WHERE id = '{$this->id}'
            AND activation_key = '$key'
            LIMIT 1"
        );
        if (Connection::$mysqli->affected_rows != 1) {
            return false;
        } else {
            $UserMilestone = new UserMilestone($this->id, 'Confirm Email');
            return true;
        }
    }

    /**
     * Gets information for the recruiter profile
     *
     * @return array - the array of information
     */
    public function getRecruiterProfile()
    {
        //
        $profile = $this->execute_query(
            "SELECT user.first_name,
            user.last_name,
            user.email_address,
            user.position,
            user.linkedin,
            organization.website,
            user.about,
            user.face_image,
            organization.`name` AS organization
            FROM user
            LEFT JOIN organization ON user.organization_id = organization.id
            WHERE user.id = '{$this->id}'"
        )->fetch_all(MYSQLI_ASSOC);
        if (1 == count($profile)) {
            return $profile[0];
        } else {
            return array();
        }
    }
}
