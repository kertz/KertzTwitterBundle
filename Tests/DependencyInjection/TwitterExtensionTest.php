<?php

namespace Kertz\TwitterBundle\Tests\DependencyInjection;

use Kertz\TwitterBundle\DependencyInjection\TwitterExtension;

class TwitterExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Kertz\TwitterBundle\DependencyInjection\TwitterExtension::configLoad
     */
    public function testConfigLoadLoadsDefaults()
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $extension = $this->getMockBuilder('Kertz\\TwitterBundle\\DependencyInjection\\TwitterExtension')
            ->setMethods(array('loadDefaults'))
            ->getMock();
        $extension
            ->expects($this->once())
            ->method('loadDefaults')
            ->with($container);

        $extension->configLoad(array(array()), $container);
    }

    /**
     * @covers Kertz\TwitterBundle\DependencyInjection\TwitterExtension::configLoad
     */
    public function testConfigLoadSetsAlias()
    {
        $alias = 'foo';

        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $container
            ->expects($this->once())
            ->method('setAlias')
            ->with($alias, 'kertz_twitter');

        $parameterBag = $this->getMockBuilder('Symfony\Component\DependencyInjection\ParameterBag\\ParameterBag')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag
            ->expects($this->any())
            ->method('add');

        $container
            ->expects($this->any())
            ->method('getParameterBag')
            ->will($this->returnValue($parameterBag));

        $extension = new TwitterExtension();
        $extension->configLoad(array(array('alias' => $alias)), $container);
    }

    /**
     * @covers Kertz\TwitterBundle\DependencyInjection\TwitterExtension::configLoad
     * @dataProvider parameterProvider
     */
    public function testConfigLoadSetParameters($name, $value)
    {
        $container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $container
            ->expects($this->once())
            ->method('setParameter')
            ->with('kertz_twitter.'.$name, $value);

        $parameterBag = $this->getMockBuilder('Symfony\Component\DependencyInjection\ParameterBag\\ParameterBag')
            ->disableOriginalConstructor()
            ->getMock();

        $parameterBag
            ->expects($this->any())
            ->method('add');

        $container
            ->expects($this->any())
            ->method('getParameterBag')
            ->will($this->returnValue($parameterBag));

        $extension = new TwitterExtension();
        $extension->configLoad(array(array($name => $value)), $container);
    }

    public function parameterProvider()
    {
        return array(
            array('api', 'foo'),
            array('consumer_key', 'foo'),
            array('consumer_secret', 'foo'),
            array('callback_url', 'foo'),
        );
    }
}
