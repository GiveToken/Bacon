<?php
namespace Sizzle\Bacon\Service;

use \Mandrill;

/**
 * This class is for sending mail messages through Mandrill
 *
   Make sure you have set TEST_EMAIL in config/credentials.php
 */
class MandrillEmail
{
    protected $mandrill;

    /**
     * Sets up how emails will be set based on configuration
     */
    public function __construct()
    {
        $this->mandrill = new Mandrill(MANDRILL_API_KEY);
    }

    /**
     * Sends mail just like Mandrill class with the exception that outside of
     * production emails only go to TEST_EMAIL unless the flag is set otherwise.
     *
     * @param array   $params  - array of parameters to submit to mandrill
     * @param boolean $notTest - (optional) should the safety of TEST_EMAIL be ignored
     *
     * @return boolean - success of send
     */
    public function send(array $params, bool $notTest = false)
    {
        if (ENVIRONMENT != 'production' && !$notTest) {
            // this is the safety feature for testing
            $params['to'] = array(array('email'=>TEST_EMAIL));
        }
        try {
            $this->mandrill->messages->send($params);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
