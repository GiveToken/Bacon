<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with the github_issue table.
 */
class GithubIssue extends \Sizzle\Bacon\DatabaseEntity
{
    protected $repository;
    protected $issue;
    protected $closed;

    /**
     * Constructs the class from an optional id
     *
     * @param int $id - optional id of the email credentials
     */
    public function __construct(int $id = null)
    {
        if ($id !== null) {
            $id = (int) $id;
            $token = $this->execute_query(
                "SELECT * FROM github_issue
                WHERE id = '$id'"
            )->fetch_object("Sizzle\Bacon\Database\GithubIssue");
            if (is_object($token)) {
                foreach (get_object_vars($token) as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Creates a new row
     *
     * @param string $repository - name of repo
     * @param int $issue         - id of issue
     *
     * @return int - id of newly created issue
     */
    public function create(string $repository, int $issue)
    {
        $this->unsetAll();
        $this->repository = $repository;
        $this->issue = $issue;
        $this->save();
        return $this->id;
    }

    /**
     * Marks the issue closed. This might be a dumb idea.
     *
     * @return boolean - success of close
     */
    public function close()
    {
        if (isset($this->id)) {
            $this->closed = 'Yes';
            $this->save();
            return true;
        }
        return false;
    }
}
