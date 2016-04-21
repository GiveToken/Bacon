<?php
namespace Sizzle\Bacon\Database;

use \Monolog\{
    Handler\SlackHandler,
    Logger
};

/**
 * This class is for interacting with the user_milestone table.
 */
class UserMilestone extends \Sizzle\Bacon\DatabaseEntity
{
    protected $id;
    protected $user_id;
    protected $milestone_id;

    /**
     * This function constructs the class from a valid user_id & milestone.
     * Fails silently for invalid input.
     *
     * @param int   $user_id   - id of the user acheiving the milestone
     * @param mixed $milestone - name or id of milestone
     */
    public function __construct(int $user_id, $milestone)
    {
        // get milestone
        $Milestone = new Milestone($milestone);

        if (isset($Milestone->id) && (int)$user_id > 0) {
            // see if this user_milestone exists already
            $query = "SELECT id, created
                      FROM user_milestone
                      WHERE user_id = '$user_id'
                      AND milestone_id = '{$Milestone->id}'";
            $result = execute_query($query);
            if ($result->num_rows > 0) {
                $user_milestone = $result->fetch_assoc();
                $this->id = $user_milestone['id'];
                $this->user_id = $user_id;
                $this->milestone_id = $Milestone->id;
                $this->created = $user_milestone['created'];
            } else {

                try {
                    // create the milestone
                    $query = "INSERT INTO user_milestone (user_id, milestone_id)
                              VALUES ('$user_id', '{$Milestone->id}')";
                    $id = insert($query);

                    // set class properties
                    $this->id = $id;
                    $this->user_id = $user_id;
                    $this->milestone_id = $Milestone->id;
                    $this->created = date('Y-m-d H:i:s');//close enough

                    // send it to Slack
                    $milestoneLogger = new Logger('milestones');
                    $User = new User($user_id);
                    $name = 'Customer Milestone Bot';
                    if (ENVIRONMENT != 'production') {
                        $slackHandler = new SlackHandler(SLACK_TOKEN, '#development', $name, false);
                        // this is turned off to avoid spamming Slack too much
                        // turn it on to test specific features
                        $slackHandler->setLevel(10000);
                    } else {
                        $slackHandler = new SlackHandler(SLACK_TOKEN, '#customers', $name, false);
                        $slackHandler->setLevel(Logger::DEBUG);
                    }
                    $milestoneLogger->pushHandler($slackHandler);
                    $milestoneLogger->log(200, "{$User->email_address} achieved the *{$Milestone->name}* milestone.");
                } catch (Exception $e) {
                    //silent fail
                }
            }
        }
    }

    /**
     * This function returns a list of customers who haven't completed the 5
     * signup milestones that take them to a true user of the product. These
     * are
     * 1) 'Signup'
     * 2) 'Add Credit Card' (currently allowing them to skip and not tracking)
     * 3) 'Confirm Email'
     * 4) 'Log In'
     * 5) 'Create Token'
     * 6) 'Token View By Non-User'
     *
     * @return array - array of user information
     */
    public static function stalledCustomers()
    {
        $query = "SELECT user.id, first_name, last_name, email_address,
                  COALESCE(MAX(web_request.created), 'Never') AS last_active,
                  (SELECT GROUP_CONCAT(milestone.`name`)
                   FROM user_milestone
                   JOIN milestone ON milestone.id = milestone_id
                   WHERE user_id = user.id
                  ) AS milestones
                  FROM user
                  LEFT JOIN web_request ON user.id = user_id
                  WHERE (user.id NOT IN (
                    SELECT user_id
                    FROM user_milestone
                    JOIN milestone ON milestone.id = milestone_id
                    WHERE `name` = 'Signup'
                  ) OR user.id NOT IN (
                    SELECT user_id
                    FROM user_milestone
                    JOIN milestone ON milestone.id = milestone_id
                    WHERE name = 'Confirm Email'
                  ) OR user.id NOT IN (
                    SELECT user_id
                    FROM user_milestone
                    JOIN milestone ON milestone.id = milestone_id
                    WHERE name = 'Log In'
                  ) OR user.id NOT IN (
                    SELECT user_id
                    FROM user_milestone
                    JOIN milestone ON milestone.id = milestone_id
                    WHERE name = 'Create Token'
                  ) OR user.id NOT IN (
                    SELECT user_id
                    FROM user_milestone
                    JOIN milestone ON milestone.id = milestone_id
                    WHERE name = 'Token View By Non-User'
                  ))
                  AND admin = 'N'
                  AND ignore_onboard_status = 'N'
                  GROUP BY user.id
                  ORDER BY last_active DESC";
        $result = execute_query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
