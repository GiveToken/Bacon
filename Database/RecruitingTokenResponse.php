<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for interacting with the recruiting_token_response table.
 */
class RecruitingTokenResponse extends \Sizzle\Bacon\DatabaseEntity
{
    protected $recruiting_token_id;
    protected $email;
    protected $name;
    protected $visitor_cookie;

    /**
     * This function creates a resonse in the database
     *
     * @param string $recruiting_token_id - long id of the token
     * @param string $email               - email address of respondent
     * @param string $response            - Yes, No or Maybe
     * @param string $name                - (optional) name of responder
     * @param string $cookie              - unique cookie for this visitor
     *
     * @return int $id - id of inserted row or 0 on fail
     */
    public function create(string $recruiting_token_id, string $email, string $response, string $name, string $cookie = '')
    {
        $this->unsetAll();

        // validate input
        if (filter_var($email, FILTER_VALIDATE_EMAIL)
            && in_array($response, array('Yes', 'No', 'Maybe'))
        ) {
            $recruiting_token_id = $this->escape_string($recruiting_token_id);
            $result = $this->execute_query(
                "SELECT id from recruiting_token
                WHERE long_id = '$recruiting_token_id'"
            );
            if ($row = $result->fetch_assoc()) {
                $this->recruiting_token_id = $row['id'];
                $this->email = $email;
                $this->response = $response;
                $this->name = $name;
                $this->visitor_cookie = $cookie;
                $this->save();
            }
        }
        return (int) $this->id;
    }

    /**
     * This function gets the responses to a token or all user tokens
     *
     * @param int $user_id - id of the user
     * @param int $long_id - optional long id of the token
     *
     * @return array - responses
     */
    public function get(int $user_id, string $long_id = '')
    {
        $responses = array();
        if (isset($user_id)) {
            $user_id = (int) $user_id;
            $long_id = $this->escape_string($long_id);
            if ('' != $long_id) {
                $result = $this->execute_query(
                    "SELECT id from recruiting_token
                    WHERE long_id = '$long_id'"
                );
                if ($row = $result->fetch_assoc()) {
                    $recruiting_token_id = $row['id'];
                } else {
                    return $responses;
                }
            }
            $query = "SELECT recruiting_token_response.id,
                      recruiting_token_response.`email`,
                      recruiting_token_response.`response`,
                      recruiting_token_response.`created`,
                      recruiting_token.job_title,
                      recruiting_token.long_id
                      FROM recruiting_token_response, recruiting_token
                      WHERE recruiting_token_response.recruiting_token_id = recruiting_token.id
                      AND recruiting_token.user_id = '$user_id' ";
            $query .= isset($recruiting_token_id) ? "AND recruiting_token.id = '$recruiting_token_id'" : '';
            $result = $this->execute_query($query);
            while ($row = $result->fetch_assoc()) {
                $responses[] = $row;
            }
        }
        return $responses;
    }

}
