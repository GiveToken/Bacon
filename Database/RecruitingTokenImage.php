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
                 WHERE recruiting_token_id = '$recruiting_token_id'";
        return execute_query($query)->fetch_all(MYSQLI_ASSOC);
    }
}
