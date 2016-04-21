<?php
namespace Sizzle\Bacon\Service;

use Devio\Pipedrive\Pipedrive;

/**
 * This class is for sending mail messages through Pipedrive
 */
class PipedriveClient
{

    protected $pipedrive;

    function __construct(string $api_key)
    {
        $this->pipedrive = new Pipedrive($api_key);
    }

    /**
     * Find a Person in Pipedrive by Email
     *
     * @param  mixed $newSignup - new signup's email, first and last name
     * @return mixed/null - Person data or null, if none found
     * @throws \Exception - Pipedrive errors
     */
    private function findPerson($newSignup)
    {
        $findPersonResponse = $this->pipedrive->persons()->findByName($newSignup->email_address, ["search_by_email" => true]);
        $foundPersonArray = $findPersonResponse->getData();
        if (!$findPersonResponse->isSuccess()) {
            throw new \Exception("Pipedrive request failed: ".$findPersonResponse->getContent()->error);
        }
        if (!is_null($foundPersonArray)) {
            // Person found
            return $foundPersonArray[0];
        }
        // No Person found
        return null;
    }

    /**
     * Creates a new Organization in the FreeTrial status
     *
     * @param  mixed $newSignup - new signup's email, first and last name
     * @return mixed - new Organization data
     * @throws \Exception - Pipedrive errors
     */
    private function createOrganizationWithFreeTrialStatus($newSignup)
    {
        $organizationsAddResponse = $this->pipedrive->organizations()->add(["name" => $newSignup->email_address, PIPEDRIVE_STATUS_COLUMN_KEY => "FreeTrial"]);
        if (!$organizationsAddResponse->isSuccess()) {
            throw new \Exception("Pipedrive request failed: ".$organizationsAddResponse->getContent()->error);
        }
        return $organizationsAddResponse->getData();
    }

    /**
     * Creates a new Person associated with an organization
     *
     * @param  mixed $newSignup      - new signup's email, first and last name
     * @param  mix   $organizationId - organizationId to associate with new Person
     * @return mixed - new Person data
     * @throws \Exception - Pipedrive errors
     */
    private function createPerson($newSignup, $organizationId)
    {
        // some signups don't have a first/last name, so we want to use email for name
        $name = empty($newSignup->first_name) ? $newSignup->email_address :  $newSignup->first_name." ".$newSignup->last_name;
        $personsAddResponse = $this->pipedrive->persons()
            ->add(["name" => $name, "org_id" => $organizationId, "email" => $newSignup->email_address]);
        if (!$personsAddResponse->isSuccess()) {
            throw new \Exception("Pipedrive request failed: ".$personsAddResponse->getContent()->error);
        }
        return $personsAddResponse->getData();
    }

    /**
     * Create a Deal in the Idea stage associated with
     *
     * @param  $dealName - set as the new Persons email
     * @param  $personId - new Persons Pipedrive id
     * @param  $organizationId - new Organizations Pipedrive id
     * @return mixed - new Deal data
     * @throws \Exception - Pipedrive errors
     */
    private function createDealInIdeaStage($dealName, $personId, $organizationId)
    {
        $dealsAddResponse = $this->pipedrive->deals()
            ->add(["title" => $dealName, "person_id" => $personId, "org_id" => $organizationId, "stage_id" => 18]);
        if (!$dealsAddResponse->isSuccess()) {
            throw new \Exception("Pipedrive request failed: ".$dealsAddResponse->getContent()->error);
        }
        return $dealsAddResponse->getData();
    }

    /**
     * Updates an Organization's status in Pipedrive to "FreeTrial"
     *
     * @param  string $organizationId - the Organization's id in Pipedrive
     * @throws \Exception - Pipedrive errors
     */
    private function updateOrganzationToFreeTrial($organizationId)
    {
        $organizationsUpdateResponse = $this->pipedrive->organizations()->update($organizationId, [PIPEDRIVE_STATUS_COLUMN_KEY => "FreeTrial"]);
        if (!$organizationsUpdateResponse->isSuccess()) {
            // Pipedrive failed
            throw new \Exception("Pipedrive request failed: ".$organizationsUpdateResponse->getContent()->error);
        }
        return $organizationsUpdateResponse->getData();
    }

    /**
     * Takes a new signup payload and sets up a FreeTrial status in Pipedrive.
     *
     * @param  mixed $newSignup - (email_address => ..., first_name => ..., last_name => ...)
     * @return bool - success
     */
    public function createFreeTrial($newSignup)
    {
        try {
            $person = $this->findPerson($newSignup);
            if (is_null($person)) {
                // No person found. Create an organization with status = "FreeTrial", then create a Person and
                // associate to the Organization, then create a Deal in "Idea" stage and associate
                // to the Organization Person.
                $organization = $this->createOrganizationWithFreeTrialStatus($newSignup);
                $person = $this->createPerson($newSignup, $organization->id);
                $this->createDealInIdeaStage($newSignup->email_address, $person->id, $organization->id);
            } else {
                // A Person was found. Assuming associated Organization and Deal exists as well
                // Update the organization to "FreeTrial" status
                $this->updateOrganzationToFreeTrial($person->org_id);
            }
            return true;
        } catch (\Exception $e) {
            // There was a problem with the Pipedrive API
            error_log("Error in PipedriveClient:");
            error_log($e->getMessage());
            error_log($e->getLine());
            return false;
        }
    }
}
