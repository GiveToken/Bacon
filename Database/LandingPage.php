<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with landing pages.
 */
class LandingPage extends \Sizzle\Bacon\DatabaseEntity
{
    protected $script;

    /**
     * This function constructs the class from a random landing page
     * the specified id.
     *
     * @param int $id - optional id of the landing_page
     */
    public function __construct(int $id = null)
    {
        if ($id !== null) {
            $id = (int) $id;
            $condition = "AND id = '$id'";
        } else {
            $condition = "ORDER BY RAND() LIMIT 1";
        }
        $page = execute_query(
            "SELECT * FROM landing_page
            WHERE deleted IS NULL
            $condition"
        )->fetch_object();
        if (is_object($page)) {
            foreach (get_object_vars($page) as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Records a hit on the landing page represented by this class
     *
     * @param string $visitor_cookie - cookie identifying the visitor
     *
     * @return boolean - success of recording
     */
    public function recordHit(string $visitor_cookie)
    {
        (new LandingPageView())->create($this->id, $visitor_cookie);
    }
}
