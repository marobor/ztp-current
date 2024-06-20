<?php
/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface UserServiceInterface.
 */
interface UserServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Change password.
     *
     * @param User   $user     User
     * @param string $password Password
     */
    public function edit(User $user, string $password): void;

    /**
     * Save user.
     *
     * @param User $user User
     */
    public function save(User $user): void;
}
