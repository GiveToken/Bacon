<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with the experiment_note table.
 */
class ExperimentNote extends \Sizzle\Bacon\DatabaseEntity
{
    protected $id;
    protected $user_id;
    protected $note;
    protected $experiment_id;
    protected $created;

    /**
     * Gets all the notes for an experiment
     *
     * @param int $experiment_id - id of the experiment
     *
     * @return array - an array of names & ids of experiments
     */
    public function getAll(int $experiment_id)
    {
        return  $this->execute_query(
            "SELECT experiment_note.`id`,
            experiment_note.`note`,
            experiment_note.`created`,
            experiment_note.`user_id`,
            COALESCE(first_name, email_address) AS `user`
            FROM experiment_note, user
            WHERE experiment_id = '$experiment_id'
            AND experiment_note.user_id = user.id
            ORDER BY `id` DESC"
        )->fetch_all(MYSQLI_ASSOC);
    }
}
