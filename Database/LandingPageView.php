<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with landing page views.
 */
class LandingPageView extends \Sizzle\Bacon\DatabaseEntity
{
    protected $landing_page_id;
    protected $visitor_cookie;

    /**
     * This function constructs the class from a landing page view
     *
     * @param int $id - optional id of the landing_page_view
     */
    public function __construct(int $id = null)
    {
        if ($id !== null) {
            $id = (int) $id;
            $page = execute_query(
                "SELECT * FROM landing_page
                 WHERE deleted IS NULL
                 AND id = '$id'"
            )->fetch_object();
            if (is_object($page)) {
                foreach (get_object_vars($page) as $key => $value) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * This function creates an entry in the landing_page_view table
     *
     * @param int    $landing_page_id - id of the landing page
     * @param string $visitor_cookie  - credential username
     *
     * @return int $id - id of inserted row or 0 on fail
     */
    public function create(int $landing_page_id, string $visitor_cookie)
    {
        $this->unsetAll();
        $this->landing_page_id = $landing_page_id;
        $this->visitor_cookie = $visitor_cookie;
        $this->save();
        return $this->id;
    }
}
