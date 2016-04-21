<?php
namespace Sizzle\Bacon\Tests\Traits;

use Sizzle\Bacon\Database\Organization;

/**
 * Functions to create & tear down test Organizations
 */
trait Organization
{
    protected $organizations = array();

    /**
     * Create a organization for testing
     *
     * @return Organization - the new organization
     */
    protected function createOrganization()
    {
        // create an organization for testing
        $name = 'The '.rand().' Corporation';
        $website = 'http://www.'.rand().'.org';
        $organization = new Organization((new Organization())->create($name, $website));
        $this->organizations[] = $organization->id;
        return $organization;
    }

    /**
     * Deletes Organizations created for testing
     */
    protected function deleteOrganizations()
    {
        foreach ($this->organizations as $id) {
            $sql = "DELETE FROM organization WHERE id = '$id'";
            execute($sql);
        }
    }
}
