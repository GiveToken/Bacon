<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for database interaction with the bot_user_agent table.
 */
class BotUserAgent extends \Sizzle\Bacon\DatabaseEntity
{
    protected $id;
    protected $user_agent;
    protected $created;
    protected $deleted;

    /**
     * Gets all the bot user agents
     *
     * @return array - an array of bots with hit counts
     */
    public function getAllHits()
    {
        return  $this->execute_query("SELECT bot_user_agent.user_agent, count(*) hits
                                      FROM bot_user_agent, web_request
                                      WHERE web_request.user_agent = bot_user_agent.user_agent
                                      GROUP BY bot_user_agent.user_agent
                                      ORDER BY hits DESC;"
        )->fetch_all(MYSQLI_ASSOC);
    }
}
