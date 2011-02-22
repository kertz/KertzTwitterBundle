<?php
namespace Kertz\TwitterBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
    
class KertzTwitterExtension extends Extension
{
    protected $resources = array(
        'twitter' => 'twitter.xml',
        'security' => 'security.xml'
    );

    public function load(array $configs, ContainerBuilder $container)
    {
        $config = array_shift($configs);
        foreach ($configs as $tmp) {
            $config = array_replace_recursive($config, $tmp);
        }

        $loader = new XmlFileLoader($container, new FileLocator(array(__DIR__.'/../Resources/config', __DIR__.'/Resources/config')));
        foreach ($this->resources as $resource) {
            $loader->load($resource);
        }

        if (isset($config['alias'])) {
            $container->setAlias($config['alias'], 'kertz_twitter');
        }

        foreach (array('api', 'consumer_key', 'consumer_secret', 'callback_url') as $attribute) {
            if (isset($config[$attribute])) {
                $container->setParameter('kertz_twitter.' . $attribute, $config[$attribute]);
            }
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getNamespace()
    {
        return 'http://www.example.com/symfony/schema/dic/kertz_twitter';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAlias()
    {
        return 'kertz_twitter';
    }
}
