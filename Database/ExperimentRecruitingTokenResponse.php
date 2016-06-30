<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with the experiment_recruiting_token_response
 * many-to-many table.
 */
class ExperimentRecruitingTokenResponse extends \Sizzle\Bacon\DatabaseEntity
{
    protected $experiment_id;
    protected $experiment_version;
    protected $recruiting_token_response_id;

    /**
     * This function creates an entry in the experiment_web_request table
     *
     * @param int $experiment_id                - experiment id
     * @param string $experiment_version        - experiment version
     * @param int $recruiting_token_response_id - recruiting_token_response id
     *
     * @return int $id - id of inserted row or null on fail
     */
    public function create(int $experiment_id, string $experiment_version, int $recruiting_token_response_id)
    {
        $this->unsetAll();
        $this->experiment_id = $experiment_id;
        $this->experiment_version = $experiment_version;
        $this->recruiting_token_response_id = $recruiting_token_response_id;
        $this->save();
        return $this->id;
    }
}
