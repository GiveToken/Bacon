<?php
namespace Sizzle\Bacon\Tests\Traits;

use Sizzle\Bacon\Database\User;

/**
 * Functions to create & tear down test users
 */
trait User
{
    protected $users = array();

    /**
     * Create a user for testing
     *
     * @return User - the new User
     */
    protected function createUser()
    {
        // create a city for testing
        $user = new User();
        $user->email_address = rand();
        $user->first_name = rand();
        $user->last_name = rand();
        $user->save();
        $this->users[] = $user->id;
        return $user;
    }

    /**
     * Deletes users created for testing
     */
    protected function deleteUsers()
    {
        foreach ($this->users as $id) {
            $sql = "DELETE FROM user WHERE id = '$id'";
            execute($sql);
        }
    }
}
