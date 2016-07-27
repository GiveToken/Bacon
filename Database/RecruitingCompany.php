<?php
namespace Sizzle\Bacon\Database;

/**
 * This class is for interacting with the recruiting_company table.
 */
class RecruitingCompany extends \Sizzle\Bacon\DatabaseEntity
{
    protected $organization_id;
    protected $name;
    protected $description;
    protected $logo;
    protected $website;
    protected $values;
    protected $facebook;
    protected $linkedin;
    protected $youtube;
    protected $twitter;
    protected $google_plus;
    protected $pinterest;

    /**
     * Gets all the companies
     *
     * @return array - an array of names & ids of companies
     */
    public function getAll()
    {
        return  $this->execute_query(
            "SELECT recruiting_company.id,
            CONCAT(COALESCE(recruiting_company.`name`,''), ' (',  COALESCE(organization.`name`, 'No organization'), ')') AS `name`
            FROM recruiting_company
            LEFT JOIN organization ON recruiting_company.organization_id = organization.id
            ORDER BY `name`"
        )->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets tokens associated with this company
     *
     * @return array - a RecruitingToken array
     */
    public function getTokens()
    {
        $return = array();
        if (isset($this->id)) {
            $result = $this->execute_query(
                "SELECT recruiting_token.id
                FROM recruiting_token
                WHERE recruiting_token.recruiting_company_id = {$this->id}"
            )->fetch_all(MYSQLI_ASSOC);
            foreach ($result as $row) {
                $return[$row['id']] = new RecruitingToken($row['id']);
            }
        }
        return $return;
    }
}
