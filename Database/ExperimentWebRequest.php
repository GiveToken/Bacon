<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with the experiment_web_request
 * many-to-many table.
 */
class ExperimentWebRequest extends \Sizzle\Bacon\DatabaseEntity
{
    protected $experiment_id;
    protected $experiment_version;
    protected $web_request_id;

    /**
     * This function creates an entry in the experiment_web_request table
     *
     * @param int $experiment_id          - experiment id
     * @param string $experiment_version  - experiment version
     * @param int $web_request_id         - web_request id
     *
     * @return int $id - id of inserted row or 0 on fail
     */
    public function create(int $experiment_id, string $experiment_version, int $web_request_id)
    {
        $this->unsetAll();
        $this->experiment_id = $experiment_id;
        $this->experiment_version = $experiment_version;
        $this->web_request_id = $web_request_id;
        $this->save();
        return $this->id;
    }
}
