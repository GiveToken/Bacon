<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with the experiment table.
 */
class Experiment extends \Sizzle\Bacon\DatabaseEntity
{
    protected $id;
    protected $user_id;
    protected $title;
    protected $completed;
    protected $created;

    /**
     * Gets all the experiments
     *
     * @return array - an array of names & ids of experiments
     */
    public function getAll()
    {
        return  $this->execute_query("SELECT `id`, `title`, `completed`, `created`
            FROM experiment
            ORDER BY `id`"
        )->fetch_all(MYSQLI_ASSOC);
    }
}
