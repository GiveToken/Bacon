<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for interacting with the web_request table.
 */
class WebRequest extends \Sizzle\Bacon\DatabaseEntity
{
    protected $visitor_cookie;
    protected $user_id;
    protected $host;
    protected $user_agent;
    protected $uri;
    protected $remote_ip;
    protected $script;
    protected $experiment_id;
    protected $experiment_version;

    /**
     * Is this a new visitor?
     *
     * @param string $visitor_cookie - the visitor cookie from the user's browser
     *
     * @return boolean - is it?
     */
    public function newVisitor(string $visitor_cookie)
    {
        $visitor_cookie = $this->escape_string($visitor_cookie);
        $sql = "SELECT COUNT(*) requests FROM web_request
                WHERE visitor_cookie = '$visitor_cookie'";
        $result = $this->execute_query($sql);
        if (($row = $result->fetch_assoc()) && $row['requests'] > 3) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Mark the web request as being part of an experiment
     *
     * @param int $id - the experiment_id of the experiement
     * @param string $version - the visitor cookie from the user's browser
     *
     * @return boolean - success marking it
     */
    public function inExperiment(int $id, string $version)
    {
        $return = false;
        // ensure this is a valid web request
        if (isset($this->id)) {
            // ensure provided experiment id is valid
            $experiment = new Experiment($id);
            if (isset($experiment->id)) {
                // create relationship
                (new ExperimentWebRequest())->create($id, $version, $this->id);
                $return = true;
            }
        }
        return $return;
    }
}
