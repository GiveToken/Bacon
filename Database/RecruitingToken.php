<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for interacting with the recruiting_token table.
 */
class RecruitingToken extends \Sizzle\Bacon\DatabaseEntity
{
    protected $long_id;
    protected $user_id;
    protected $job_title;
    protected $job_description;
    protected $skills_required;
    protected $responsibilities;
    protected $perks;
    protected $recruiting_company_id;
    protected $recruiter_profile;

    /**
     * This function constructs the class
     *
     * @param mixed  $value - optional id of the token
     * @param string $key   - optional parameter to test $value against: 'id' (default) or 'long_id'
     */
    public function __construct($value = null, string $key = null)
    {
        if ($value !== null) {
            if ($key == null || !in_array($key, array('id','long_id'))) {
                $key = 'id';
            }
            $value = $this->escape_string($value);
            $token = $this->execute_query("SELECT * FROM recruiting_token WHERE $key = '$value'")->fetch_object("Sizzle\Bacon\Database\RecruitingToken");
            if ($token) {
                foreach (get_object_vars($token) as $key => $value) {
                    if (isset($value)) {
                        $this->$key = $value;
                    }
                }
            }
        }
    }

    public function getUserTokens(int $user_id)
    {
        $user_id = (int) $user_id;
        $results =  $this->execute_query(
            "SELECT recruiting_token.*, COALESCE(recruiting_company.`name`, 'Unnamed Company') as company
            FROM recruiting_token
            LEFT JOIN recruiting_company ON recruiting_company.id = recruiting_company_id
            WHERE recruiting_token.user_id = '$user_id'
            ORDER BY company, job_title"
        );
        $user_tokens = array();
        while($token = $results->fetch_object()) {
            $user_tokens[count($user_tokens)] = $token;
        }
        $results->free();
        return $user_tokens;
    }

    public function getUser()
    {
        $user = null;
        $result = $this->execute_query(
            "SELECT * FROM user WHERE id='$this->user_id'"
        );

        if ($result->num_rows > 0) {
            $user = $result->fetch_object("Sizzle\Bacon\Database\User");
        }
        return $user;
    }

    public function getCompany()
    {
        $company = null;
        $result = $this->execute_query(
            "SELECT * FROM recruiting_company WHERE id='$this->recruiting_company_id'"
        );

        if ($result->num_rows > 0) {
            $company = $result->fetch_object("Sizzle\Bacon\Database\RecruitingCompany");
        }
        return $company;
    }

    /**
     * Gets information about recruiting companies owned by the user specified
     *
     * @param int $user_id - the user whose companies are being returned
     */
    public function getUserCompanies(int $user_id)
    {
        $user_id = (int) $user_id;
        $query = "SELECT id, `name`
                  FROM recruiting_company
                  WHERE user_id = '$user_id'";
        $results = $this->execute_query($query);
        return $results->fetch_all(MYSQLI_ASSOC);
    }

    public function init($post)
    {
        foreach (get_object_vars($post) as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    private function changeFileName(string $file_name)
    {
        $new_file_name = $this->id."_".$this->user_id."_".$file_name;
        return $new_file_name;
    }

    /**
     * Marks this token as deleted
     */
    public function delete()
    {
        $sql = "UPDATE recruiting_token SET deleted=now() WHERE id = '$this->id'";
        $this->execute_query($sql);
    }

    /**
     * This function tests a long_id for uniqueness
     *
     * @param string $long_id - optional id to test (defaults to class property)
     *
     * @return boolean - is it unique
     */
    public function uniqueLongId(string $long_id = '')
    {
        if ('' == $long_id) {
            $long_id = $this->long_id;
        }
        $long_id = $this->escape_string($long_id);
        $sql = "SELECT id FROM recruiting_token WHERE long_id = '$long_id'";
        $result = $this->execute_query($sql);
        if (0 == $result->num_rows) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function returns the screenshot file name
     *
     * @return mixed - name of screenshot file or false if there isn't one
     */
    public function screenshot()
    {
        $return = false;
        if (isset($this->id)) {
            $images = (new RecruitingTokenImage())->getByRecruitingTokenId($this->id);
            if (!empty($images)) {
                $return = $images[0]['file_name'];
            }
        }
        return $return;
    }

    /**
     * This function returns the properties in an array
     *
     * @return array - array of class properties
     */
    public function toArray()
    {
        $data = get_object_vars($this);
        unset($data['readOnly']);
        return $data;
    }

    /**
     * Gets the cities associated with a Token
     *
     * @return array - array of City objects
     */
    public function getCities()
    {
        $sql = "SELECT city_id
                FROM recruiting_token_city
                WHERE recruiting_token_id = '$this->id'
                AND deleted IS NULL";
        $results = $this->execute_query($sql)->fetch_all(MYSQLI_ASSOC);
        $return = array();
        foreach ($results as $row) {
          $return[] = new City($row['city_id']);
        }
        return $return;
    }

    /**
     * Associates a city with the token
     *
     * @param int $city_id - the city to associate
     *
     * @return boolean - success/fail
     */
    public function addCity(int $city_id) {
        if (0 < (int) $city_id && 0 < (int) $this->id) {
            $id = (new RecruitingTokenCity())->create($city_id, (int) $this->id);
            return 0 < (int) $id;
        } else {
            return false;
        }
    }

    /**
     * Unassociates a city with the token
     *
     * @param int $city_id - the city to unassociate
     *
     * @return boolean - success/fail
     */
    public function removeCity(int $city_id) {
        $sql = "SELECT id
                FROM recruiting_token_city
                WHERE recruiting_token_id = '$this->id'
                AND city_id = '$city_id'
                AND deleted IS NULL";
        $result = $this->execute_query($sql);
        $array = $result->fetch_all(MYSQLI_ASSOC);
        if (count($array) == 1) {
            $cityAssociation = new RecruitingTokenCity($array[0]['id']);
            return $cityAssociation->delete();
        } else {
            return false;
        }
    }
}
