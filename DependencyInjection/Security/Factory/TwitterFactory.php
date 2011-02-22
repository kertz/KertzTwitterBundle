<?php

namespace Kertz\TwitterBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class TwitterFactory extends AbstractFactory
{
    public function __construct()
    {
        //Nothing to do here
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'kertz_twitter';
    }

    protected function getListenerId()
    {
        return 'kertz_twitter.security.authentication.listener';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        // with user provider
        if (isset($config['provider'])) {
            $authProviderId = 'kertz_twitter.auth.'.$id;

            $container
                ->setDefinition($authProviderId, new DefinitionDecorator('kertz_twitter.auth'))
                ->addArgument(new Reference($userProviderId))
                ->addArgument(new Reference('security.account_checker'))
            ;

            return $authProviderId;
        }

        // without user provider
        return 'kertz_twitter.auth';
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPointId)
    {
        $entryPointId = 'kertz_twitter.security.authentication.entry_point.'.$id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('kertz_twitter.security.authentication.entry_point'))
        ;

        return $entryPointId;
    }
}
