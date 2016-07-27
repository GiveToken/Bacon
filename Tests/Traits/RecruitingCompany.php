<?php
namespace Sizzle\Bacon\Tests\Traits;

use Sizzle\Bacon\Database\RecruitingCompany;

/**
 * Functions to create & tear down test Recruiting Companies
 */
trait RecruitingCompany
{
    use \Sizzle\Bacon\Tests\Traits\Organization;

    protected $recruitingCompanies = array();

    /**
     * Create a RecruitingCompany for testing
     *
     * @param int $organization_id = (optional) organization id of company owner
     *
     * @return RecruitingCompany - the new RecruitingCompany
     */
    protected function createRecruitingCompany($organization_id = null)
    {
        if (!isset($organization_id)) {
            $organization = $this->createOrganization();
            $organization_id = $organization->id;
        }

        // create an RecruitingCompany for testing
        $recruitingCompany = new RecruitingCompany();
        $recruitingCompany->organization_id = $organization_id;
        $recruitingCompany->name = 'The '.rand().' Company';
        $recruitingCompany->save();
        $this->recruitingCompanies[] = $recruitingCompany->id;
        return $recruitingCompany;
    }

    /**
     * Deletes Recruiting Companies created for testing
     */
    protected function deleteRecruitingCompanies()
    {
        foreach ($this->recruitingCompanies as $id) {
            $sql = "DELETE FROM recruiting_company WHERE id = '$id'";
            (new RecruitingCompany())->execute_query($sql);
        }
    }
}
