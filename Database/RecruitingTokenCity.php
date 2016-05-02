<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with the recruiting_token_city
 * many-to-many table.
 */
class RecruitingTokenCity extends \Sizzle\Bacon\DatabaseEntity
{
    protected $city_id;
    protected $recruiting_token_id;

    /**
     * This function constructs the class from a many-to-many relationship
     *
     * @param int $id - optional id of the recruiting_token_city
     */
    public function __construct(int $id = null)
    {
        $page = $this->execute_query(
            "SELECT * FROM recruiting_token_city
            WHERE deleted IS NULL
            AND id = '$id'"
        )->fetch_object();
        if (is_object($page)) {
            foreach (get_object_vars($page) as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    /**
     * This function creates an entry in the recruiting_token_city table
     *
     * @param int $city_id             - city id
     * @param int $recruiting_token_id - recruiting token id
     *
     * @return int $id - id of inserted row or 0 on fail
     */
    public function create(int $city_id, int $recruiting_token_id)
    {
        $this->unsetAll();
        $this->city_id = $city_id;
        $this->recruiting_token_id = $recruiting_token_id;
        $this->save();
        return $this->id;
    }

    /**
     * "Deletes" an entry in the recruiting_token_city table
     *
     * @return boolean  - success/fail
     */
    public function delete()
    {
        $success = false;
        if (isset($this->id)) {
            $sql = "UPDATE recruiting_token_city SET deleted = NOW() WHERE id = {$this->id}";
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
}
