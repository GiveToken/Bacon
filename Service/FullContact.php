<?php
namespace Sizzle\Bacon\Service;

use Sizzle\Bacon\Database\FullcontactPerson;

/**
 * This class is for API calls to the FullContact API
 *
 * Documentation: https://www.fullcontact.com/developer/docs/
 */
class FullContact
{
    /**
     * Gets person info on an email, stores it in DB & returns it
     *
     * @param string $email                        - the email to get info for
     * @param string $recruiting_token_response_id - optional id of response
     *
     * @return array - resulting info on the email
     */
    public function person(string $email, int $recruiting_token_response_id = NULL)
    {
        $url = 'https://api.fullcontact.com/v2/person.json?email='.urlencode($email);
        $header = 'X-FullContact-APIKey: '.FULLCONTACT_APIKEY;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $fullcontactResponse = curl_exec($ch);
        (new FullcontactPerson())->create($email, $fullcontactResponse, $recruiting_token_response_id);
        return json_decode($fullcontactResponse);
    }
}
