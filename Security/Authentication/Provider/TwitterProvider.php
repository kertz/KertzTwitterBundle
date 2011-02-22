<?php

namespace Kertz\TwitterBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\User\AccountInterface;
use Symfony\Component\Security\Core\User\AccountCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedAccountException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;

use Kertz\TwitterBundle\Security\Authentication\Token\TwitterUserToken;
use Kertz\TwitterBundle\Services\Twitter;

class TwitterProvider implements AuthenticationProviderInterface
{
    protected $twitter;
    protected $accessToken;
    protected $userProvider;
    protected $accountChecker;

    public function __construct(Twitter $twitter, UserProviderInterface $userProvider = null, AccountCheckerInterface $accountChecker = null)
    {
        if (null !== $userProvider && null === $accountChecker) {
            throw new \InvalidArgumentException('$accountChecker cannot be null, if $userProvider is not null.');
        }
        $this->twitter = $twitter;
        $this->userProvider = $userProvider;
        $this->accountChecker = $accountChecker;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        try {
            if ($this->accessToken = $this->twitter->getAccessToken()) {
                return $this->createAuthenticatedToken($this->accessToken['user_id']);
            }
        } catch (AuthenticationException $failed) {
            throw $failed;
        } catch (\Exception $failed) {
            throw new AuthenticationException('Unknown error', $failed->getMessage(), $failed->getCode(), $failed);
        }

        throw new AuthenticationException('The Twitter user could not be retrieved from the session.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof TwitterUserToken;
    }

    protected function createAuthenticatedToken($uid)
    {
        if (null === $this->userProvider) {
            return new TwitterUserToken($uid);
        }

        $user = $this->userProvider->loadUserByUsername($uid);
        if (!$user instanceof AccountInterface) {
            throw new \RuntimeException('User provider did not return an implementation of account interface.');
        }

        $this->accountChecker->checkPreAuth($user);
        $this->accountChecker->checkPostAuth($user);

        return new TwitterUserToken($user, $user->getRoles());
    }

    /**
     * Finds a user by account
     *
     * @param AccountInterface $user
     */
    public function loadUserByAccount(AccountInterface $user)
    {
        throw new UnsupportedAccountException('Account is not supported.');
    }
}
