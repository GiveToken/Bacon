<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with recruiting token clicks.
 */
class RecruitingTokenClick extends \Sizzle\Bacon\DatabaseEntity
{
    protected $html_tag_id;
    protected $html_tag;
    protected $visitor_cookie;
    protected $recruiting_token_id;

    /**
     * Saves click information
     *
     * @param $html_tag_id
     * @param $html_tag
     * @param $visitor_cookie
     * @param $recruiting_token_id
     */
    public function create(string $html_tag_id, string $html_tag, string $visitor_cookie, int $recruiting_token_id)
    {
        $recruitingTokenClick = new RecruitingTokenClick();
        $recruitingTokenClick->html_tag_id = $html_tag_id;
        $recruitingTokenClick->html_tag = $html_tag;
        $recruitingTokenClick->visitor_cookie = $visitor_cookie;
        $recruitingTokenClick->recruiting_token_id = $recruiting_token_id;
        $recruitingTokenClick->save();
        return $recruitingTokenClick;
    }
}
