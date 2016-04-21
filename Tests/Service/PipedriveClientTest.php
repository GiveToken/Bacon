<?php
namespace Sizzle\Bacon\Tests\Service;

use \Devio\Pipedrive\Http\Response;
use \Sizzle\Bacon\Service\PipedriveClient;

/**
 * This class tests the Pipedrive class
 *
 * ./vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Service/PipedriveClientTest
 */
class PipedriveClientTest extends \PHPUnit_Framework_TestCase
{
    use \Sizzle\Bacon\Tests\Traits\User;

    protected $pipedriveClient;
    protected $mockedPipedriveAPI;
    protected $fakeUser;

    /**
     * Sets up before each test
     */
    public function setUp()
    {
        $this->mockedPipedriveAPI = \Mockery::mock('overload:\\Devio\Pipedrive\Pipedrive')->makePartial();
        $this->pipedriveClient = new PipedriveClient(PIPEDRIVE_API_TOKEN);
        $this->fakeUser = $this->createUser();
    }


    /**
     * Cleans up after the tests
     */
    public function tearDown()
    {
        \Mockery::close();
        $this->deleteUsers();
    }

    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        $this->assertEquals('Sizzle\Bacon\Service\PipedriveClient', get_class($this->pipedriveClient));
    }

    /**
     * Verify that if a newly signed-up person exists in Pipedrive and there
     * is a deal associated with them already, then the associated organization
     * is in a FreeTrial state.
     *
     * @runInSeparateProcess
     * @preserveGlobalState  disabled
     */
    public function testCreateFreeTrialWithExistingDealAndExistingPerson()
    {
        $freeTrialEmailAddress = $this->fakeUser->email_address;
        $signUpFirstName = $this->fakeUser->first_name;
        $signUpLastName = $this->fakeUser->last_name;
        $signUpPersonsName = empty($signUpFirstName) ? $freeTrialEmailAddress :  $signUpFirstName." ".$signUpLastName;

        $personId = 1;
        $organizationId = 2;

        $examplePipedrivePersonsFindByNameBodyResponse = "{
            \"success\": true,
            \"data\": [
                {
                    \"id\": ".$personId.",
                    \"email\": \"".$freeTrialEmailAddress."\",
                    \"org_id\": ".$organizationId."
                }
            ],
            \"additional_data\": {
                \"search_method\": \"search_by_email\",
                \"pagination\": {
                    \"start\": 0,
                    \"limit\": 100,
                    \"more_items_in_collection\": false
                }
            }
        }";

        $examplePipedriveUpdateOrganizationBodyResponse = "{
            \"success\": true,
            \"data\":
            {
                \"id\": ".$organizationId.",
                \"".PIPEDRIVE_STATUS_COLUMN_KEY."\": \"FreeTrial\"
            }
        }";
        // The Persons API should be called to check if a Person exists in Pipedrive
        // In this test, the Person exists and we assume so does an Organization and Deal
        $this->mockedPipedriveAPI
            ->shouldReceive('persons->findByName')
            ->ordered()->once()
            ->with($freeTrialEmailAddress, ["search_by_email" => true])
            ->andReturn(new Response(200, json_decode($examplePipedrivePersonsFindByNameBodyResponse)));

        // Finally, the Organizations API should be called to update the associated Organization's
        // status to "FreeTrial"
        $this->mockedPipedriveAPI
            ->shouldReceive('organizations->update')->ordered()->once()
            ->with($organizationId, array(PIPEDRIVE_STATUS_COLUMN_KEY => "FreeTrial"))
            ->andReturn(new Response(200, json_decode($examplePipedriveUpdateOrganizationBodyResponse)));


        $this->pipedriveClient = new PipedriveClient(PIPEDRIVE_API_TOKEN);
        $signupPayload = $this->fakeUser;

        $this->assertTrue($this->pipedriveClient->createFreeTrial($signupPayload));
    }

    /**
     * Verify that if a newly signed-up Person doesn't exist in Pipedrive,
     * we will create a new Person, Deal and Organization, and set
     * the Organization to a FreeTrial state.
     *
     * @runInSeparateProcess
     * @preserveGlobalState  disabled
     */
    public function testCreateFreeTrialWithNoExistingDealAndNoExistingPerson()
    {
        $freeTrialEmailAddress = $this->fakeUser->email_address;
        $signUpFirstName = $this->fakeUser->first_name;
        $signUpLastName = $this->fakeUser->last_name;
        $signUpPersonsName = empty($signUpFirstName) ? $freeTrialEmailAddress :  $signUpFirstName." ".$signUpLastName;

        $personId = 1;
        $organizationId = 2;
        $examplePipedrivePersonsFindByNameBodyResponse = "{
            \"success\": true,
            \"data\": null,
            \"additional_data\": {
                \"search_method\": \"search_by_email\",
                \"pagination\": {
                    \"start\": 0,
                    \"limit\": 100,
                    \"more_items_in_collection\": false
                }
            }
        }";

        $examplePipedriveOrganizationsAddBodyResponse = "{
            \"success\": true,
            \"data\": {
                \"id\": ".$organizationId.",
                \"name\": \"".$freeTrialEmailAddress."\",
                \"".PIPEDRIVE_STATUS_COLUMN_KEY."\": \"FreeTrial\"
            }
        }";

        $examplePipedrivePersonsAddBodyResponse = "{
            \"success\": true,
            \"data\": {
                \"id\": ".$personId.",
                \"org_id\": {
                    \"name\": \"".$freeTrialEmailAddress."\",
                    \"value\": \"".$organizationId."\"
                },
                \"name\": \"".$signUpPersonsName."\",
                \"email\": [
                    {
                        \"label\": \"\",
                        \"value\": \"".$freeTrialEmailAddress."\",
                        \"primary\": true
                    }
                ],
                \"org_name\": \"".$freeTrialEmailAddress."\"
            }
        }";

        $examplePipedriveDealsAddBodyResponse = "{
            \"success\": true,
            \"data\": {
                \"id\": 133,
                \"person_id\": {
                    \"name\": \"".$signUpPersonsName."\",
                    \"email\": [
                        {
                            \"label\": \"\",
                            \"value\": \"".$freeTrialEmailAddress."\",
                            \"primary\": true
                        }
                    ],
                    \"value\": \"".$personId."\"
                },
                \"org_id\": {
                    \"name\": \"".$freeTrialEmailAddress."\",
                    \"value\": \"".$organizationId."\"
                },
                \"stage_id\": 1,
                \"title\": \"".$freeTrialEmailAddress."\",
                \"person_name\": \"".$signUpPersonsName."\",
                \"org_name\": \"".$freeTrialEmailAddress."\"
            }
        }";

        // The Persons API should be called to check if a Person exists in Pipedrive
        // In this test, the Person doesn't exist
        $this->mockedPipedriveAPI->shouldReceive('persons->findByName')->ordered()->once()
            ->with($freeTrialEmailAddress, ["search_by_email" => true])
            ->andReturn(new Response(200, json_decode($examplePipedrivePersonsFindByNameBodyResponse)));

        // Then, since no Person was found, we need to create a Person,
        // Organization, and Deal. Order will be Organization->Person->Deal
        // as each subsequent endpoint needs data from a previous call.
        //
        // The Organization API should be called to create an organization
        // and set its status to FreeTrial.
        $this->mockedPipedriveAPI->shouldReceive('organizations->add')
            ->ordered()->once()
            ->with(["name" => $freeTrialEmailAddress, PIPEDRIVE_STATUS_COLUMN_KEY => "FreeTrial"])
            ->andReturn(new Response(200, json_decode($examplePipedriveOrganizationsAddBodyResponse)));

        // Then Persons API should be called to create a Person
        // associated with the just created Organization
        $this->mockedPipedriveAPI
            ->shouldReceive('persons->add')
            ->ordered()->once()
            ->with(["name" => $signUpPersonsName, "org_id" => $organizationId, "email" => $freeTrialEmailAddress])
            ->andReturn(new Response(200, json_decode($examplePipedrivePersonsAddBodyResponse)));

        // Finally the Deals API should be to create a new Deal in idea stage,
        // associated with the just created Person and Organization
        $this->mockedPipedriveAPI
            ->shouldReceive('deals->add')
            ->ordered()->once()
            ->with(["title" => $freeTrialEmailAddress, "person_id" => $personId, "org_id" => $organizationId, "stage_id" => 18])
            ->andReturn(new Response(200, json_decode($examplePipedriveDealsAddBodyResponse)));

        $this->pipedriveClient = new PipedriveClient(PIPEDRIVE_API_TOKEN);
        $signupPayload = $this->fakeUser;

        $this->assertTrue($this->pipedriveClient->createFreeTrial($signupPayload));
    }
}
