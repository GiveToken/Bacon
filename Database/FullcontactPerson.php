<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for interacting with the fullcontact_person table.
 */
class FullcontactPerson extends \Sizzle\Bacon\DatabaseEntity
{
    protected $email;
    protected $response;
    protected $recruiting_token_response_id;

    /**
     * This function creates a response in the database
     *
     * @param string $email                        - email address of respondent
     * @param string $response                     - Yes, No or Maybe
     * @param string $recruiting_token_response_id - (optional) id of the token response
     *
     * @return int $id - id of inserted row or 0 on fail
     */
    public function create(string $email, string $response, string $recruiting_token_response_id = NULL)
    {
        $this->unsetAll();

        // validate input
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $tokenResponse = new RecruitingTokenResponse($recruiting_token_response_id);
            if (isset($tokenResponse->id)) {
                $this->recruiting_token_response_id = $tokenResponse->id;
            }
            $this->email = $email;
            $this->response = $response;
            $this->save();
        }
        return (int) $this->id;
    }
}
