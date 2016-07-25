<?php
namespace Sizzle\Bacon\Tests\Database;

use \Sizzle\Bacon\Database\{
    FullcontactPerson
};

/**
 * This class tests the FullcontactPerson class
 *
 * vendor/bin/phpunit --bootstrap src/tests/autoload.php src/Bacon/Tests/Database/FullcontactPersonTest
 */
class FullcontactPersonTest
extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the __construct function.
     */
    public function testConstructor()
    {
        $result = new FullcontactPerson();
        $this->assertEquals('Sizzle\Bacon\Database\FullcontactPerson', get_class($result));
    }

    /**
     * Tests the create function.
     */
    public function testCreate()
    {
        // Test function success
        $fullcontactPerson = new FullcontactPerson();
        $email = rand() . '@gmail.com';
        $json = '{
         "status": 200,
         "requestId": "139f7667-d805-4f78-a746-8f0d180dbac6",
         "likelihood": 0.92,
         "photos": [
           {
             "type": "facebook",
             "typeId": "facebook",
             "typeName": "Facebook",
             "url": "https://d2ojpxxtu63wzl.cloudfront.net/static/8db8f9d85b600bc41539e5002974f43b_e4468b5dc0ac1e8bb911bdfc2f5022c6d4f902764ea21a025674a2a3714892b1",
             "isPrimary": true
           },
           {
             "type": "yahoo",
             "typeId": "yahoo",
             "typeName": "Yahoo",
             "url": "https://d2ojpxxtu63wzl.cloudfront.net/static/391aba8b0fdfb5a70ec3371ed333f9e6_32acd5f3bf53ef67e2422755ae067055f69179292aa0b6798e3c60b159f74a4a"
           }
         ],
         "contactInfo": {
           "familyName": "Conner",
           "fullName": "Evelyn Hensley Conner",
           "givenName": "Evelyn"
         },
         "demographics": {
           "locationDeduced": {
             "normalizedLocation": "Cookeville, Tennessee",
             "deducedLocation": "Cookeville, Tennessee, United States",
             "city": {
               "name": "Cookeville"
             },
             "state": {
               "name": "Tennessee",
               "code": "TN"
             },
             "country": {
               "deduced": true,
               "name": "United States",
               "code": "US"
             },
             "continent": {
               "deduced": true,
               "name": "North America"
             },
             "county": {
               "deduced": true,
               "name": "Putnam",
               "code": "Putnam"
             },
             "likelihood": 1
           },
           "locationGeneral": "Cookeville, Tennessee",
           "age": "52",
           "ageRange": "45-54",
           "gender": "Female"
         },
         "socialProfiles": [
           {
             "type": "facebook",
             "typeId": "facebook",
             "typeName": "Facebook",
             "url": "https://www.facebook.com/evelyn.hensleyconner"
           }
         ]
        }';
        $this->assertTrue((bool) filter_var($email, FILTER_VALIDATE_EMAIL));

        $id = $fullcontactPerson->create($email, $json);
        $this->assertGreaterThan(0, $id);
    }
}
