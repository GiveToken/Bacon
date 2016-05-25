<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with the recruiting_token_image table.
 */
class RecruitingTokenImage extends \Sizzle\Bacon\DatabaseEntity
{
    protected $file_name;
    protected $recruiting_token_id;

    /**
     * Constructs the class
     *
     * @param int $id - the id of the record to pull from the database
     */
    public function __construct($id = null)
    {
        if ($id !== null && strlen($id) > 0) {
            $id = $this->escape_string($id);
            $sql = "SELECT * FROM {$this->tableName()} WHERE id = '$id' AND deleted IS NULL";
            $object = $this->execute_query($sql)->fetch_object(get_class($this));
            if (isset($object)) {
                foreach (get_object_vars($object) as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * This function creates an entry in the recruiting_token_image table
     *
     * @param string $file_name           - name of image file
     * @param int    $recruiting_token_id - recruiting token id
     *
     * @return int $id - id of inserted row or 0 on fail
     */
    public function create(string $file_name, int $recruiting_token_id)
    {
        $this->unsetAll();
        $this->file_name = $file_name;
        $this->recruiting_token_id = $recruiting_token_id;
        $this->save();
        return $this->id;
    }

    /**
     * This function gets the images for a given token
     *
     * @param int $recruiting_token_id - recruiting token id
     *
     * @return array - image(s) information
     */
    public function getByRecruitingTokenId(int $recruiting_token_id)
    {
        $recruiting_token_id = (int) $recruiting_token_id;
        $query = "SELECT * FROM recruiting_token_image
                 WHERE recruiting_token_id = '$recruiting_token_id'
                 AND deleted IS NULL";
        return $this->execute_query($query)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * "Deletes" an entry in the recruiting_token_image table
     *
     * @return boolean  - success/fail
     */
    public function delete()
    {
        $success = false;
        if (isset($this->id)) {
            $sql = "UPDATE recruiting_token_image SET deleted = NOW() WHERE id = {$this->id}";
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
