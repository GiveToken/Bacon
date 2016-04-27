<?php
namespace Sizzle\Bacon\Tests\Traits;

use Sizzle\Bacon\Database\RecruitingCompany;

/**
 * Functions to create & tear down test Recruiting Companies
 */
trait RecruitingCompany
{
    use \Sizzle\Bacon\Tests\Traits\User;

    protected $recruitingCompanies = array();

    /**
     * Create a RecruitingCompany for testing
     *
     * @param int $user_id = (optional) user id of company owner
     *
     * @return RecruitingCompany - the new RecruitingCompany
     */
    protected function createRecruitingCompany($user_id = null)
    {
        if (!isset($user_id)) {
            $user = $this->createUser();
            $user_id = $user->id;
        }

        // create an RecruitingCompany for testing
        $recruitingCompany = new RecruitingCompany();
        $recruitingCompany->user_id = $user_id;
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
