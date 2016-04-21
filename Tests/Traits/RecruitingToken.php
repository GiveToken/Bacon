<?php
namespace Sizzle\Bacon\Tests\Traits;

use Sizzle\Bacon\Database\RecruitingToken;

/**
 * Functions to create & tear down test RecruitingTokens
 */
trait RecruitingToken
{
    use RecruitingCompany, User {
        User::createUser insteadof RecruitingCompany;
        User::deleteUsers insteadof RecruitingCompany;
    }

    protected $recruitingTokens = array();

    /**
     * Create a RecruitingToken for testing
     *
     * @param int   $user_id    - (optional) user id of token owner
     * @param mixed $company_id - (optional) company id of for the token or 'none'
     *
     * @return RecruitingToken - the new RecruitingToken
     */
    protected function createRecruitingToken($user_id = null, $company_id = null)
    {
        if (!isset($user_id)) {
            $user = $this->createUser();
            $user_id = $user->id;
        }
        if (!isset($company_id)) {
            $company = $this->createRecruitingCompany($user_id);
            $company_id = $company->id;
        }

        // create an RecruitingCompany for testing
        $recruitingToken = new RecruitingToken();
        $recruitingToken->user_id = $user_id;
        $recruitingToken->long_id = substr(md5(microtime()), rand(0, 26), 20);
        if (0 < (int) $company_id) {
            $recruitingToken->recruiting_company_id = $company_id;
        }
        $recruitingToken->save();
        $this->recruitingTokens[] = $recruitingToken->id;
        return $recruitingToken;
    }

    /**
     * Deletes RecruitingTokens created for testing
     */
    protected function deleteRecruitingTokens()
    {
        foreach ($this->recruitingTokens as $id) {
            $sql = "DELETE FROM recruiting_token WHERE id = '$id'";
            execute($sql);
        }
        $this->deleteRecruitingCompanies();
        $this->deleteUsers();
    }
}
