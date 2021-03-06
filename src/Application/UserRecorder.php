<?php declare(strict_types=1);

namespace BSP\UserAccountManagement\Application;

use BSP\UserAccountManagement\Application\Entity\User;
use BSP\UserAccountManagement\Application\Exception\EmailAlreadyUsed;
use BSP\UserAccountManagement\Application\Ports\Driven\UserId;
use BSP\UserAccountManagement\Application\Ports\Driven\UserRepository;
use BSP\UserAccountManagement\Application\Ports\Driver\UserRegistrationDriver;
use BSP\UserAccountManagement\Application\ValueObject\Email;
use BSP\UserAccountManagement\Application\ValueObject\HashedPassword;

final class UserRecorder implements UserRegistrationDriver
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @throws EmailAlreadyUsed
     */
    public function record(
        UserId $userId,
        string $email,
        string $password,
        string $name
    ): void {
        $email = new Email($email);
        $hashedPassword = HashedPassword::hash($password);
        $user = new User($userId, $email, $hashedPassword, $name);

        if ($this->userRepository->emailIsAlreadyUsed($email)) {
            throw new EmailAlreadyUsed();
        }

        $this->userRepository->add($user);
    }
}
