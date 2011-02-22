<?php

namespace Kertz\TwitterBundle\Tests\Services;

use Kertz\TwitterBundle\Services\Twitter;
use Kertz\TwitterBundle\Tests\MockTwitterOAuth;
use Symfony\Component\HttpFoundation\SessionStorage\SessionStorageInterface;
use Symfony\Component\HttpFoundation\Session;

class TwitterTest extends \PHPUnit_Framework_TestCase
{

    public function setUp(){
        $this->callbackURL = null;
        $this->requestToken = array(
            'oauth_token' => 'foo',
            'oauth_token_secret' => 'foo'
        );
        $this->redirectURL = "http:://twitter.com/oauth/foobar";
    }

    /**
     * @covers Kertz\TwitterBundle\Services\Twitter::connect()
     */
    public function testConnectFailureWithCallbackURL()
    {
        $this->callbackURL = "http://myapp.com/process";
        
        $connection = $this->getMockBuilder('Kertz\\TwitterBundle\\Tests\\MockTwitterOAuth')
            ->setMethods(array('getRequestToken', 'getAuthorizeURL'))
            ->getMock();

        $connection->expects($this->once())
            ->method('getRequestToken')
            ->with($this->equalTo('http://myapp.com/process'))
            ->will($this->returnValue($this->requestToken));

        $connection->http_code = 500;

        $session = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('set'))
            ->getMock();

        $session->expects($this->at(0))
            ->method('set')
            ->with('oauth_token', $this->requestToken['oauth_token'])
            ->will($this->returnValue(null));

        $session->expects($this->at(1))
            ->method('set')
            ->with('oauth_token_secret', $this->requestToken['oauth_token_secret'])
            ->will($this->returnValue(null));

        $twitter = new Twitter();

        $this->assertEquals(null ,$twitter->connect($connection, $session, $this->callbackURL));
    }

    /**
     * @covers Kertz\TwitterBundle\Services\Twitter::connect()
     */
    public function testConnectFailureWithoutCallbackURL()
    {
        $connection = $this->getMockBuilder('Kertz\\TwitterBundle\\Tests\\MockTwitterOAuth')
            ->setMethods(array('getRequestToken', 'getAuthorizeURL'))
            ->getMock();

        $connection->expects($this->once())
            ->method('getRequestToken')
            ->with($this->equalTo(null))
            ->will($this->returnValue($this->requestToken));

        $connection->http_code = 500;

        $session = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('set'))
            ->getMock();

        $session->expects($this->at(0))
            ->method('set')
            ->with('oauth_token', $this->requestToken['oauth_token'])
            ->will($this->returnValue(null));

        $session->expects($this->at(1))
            ->method('set')
            ->with('oauth_token_secret', $this->requestToken['oauth_token_secret'])
            ->will($this->returnValue(null));

        $twitter = new Twitter();

        $this->assertEquals(null ,$twitter->connect($connection, $session, $this->callbackURL));
    }

    /**
     * @covers Kertz\TwitterBundle\Services\Twitter::connect()
     */
    public function testConnectSuccessWithCallbackURL()
    {
        $this->callbackURL = "http://myapp.com/process";
        
        $connection = $this->getMockBuilder('Kertz\\TwitterBundle\\Tests\\MockTwitterOAuth')
            ->setMethods(array('getRequestToken', 'getAuthorizeURL'))
            ->getMock();

        $connection->expects($this->once())
            ->method('getRequestToken')
            ->with($this->equalTo('http://myapp.com/process'))
            ->will($this->returnValue($this->requestToken));

        $connection->expects($this->once())
            ->method('getAuthorizeURL')
            ->with($this->equalTo($this->requestToken))
            ->will($this->returnValue($this->redirectURL));

        $connection->http_code = 200;

        $session = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('set'))
            ->getMock();

        $session->expects($this->at(0))
            ->method('set')
            ->with('oauth_token', $this->requestToken['oauth_token'])
            ->will($this->returnValue(null));

        $session->expects($this->at(1))
            ->method('set')
            ->with('oauth_token_secret', $this->requestToken['oauth_token_secret'])
            ->will($this->returnValue(null));

        $twitter = new Twitter();

        $this->assertEquals($this->redirectURL ,$twitter->connect($connection, $session, $this->callbackURL));
    }

    /**
     * @covers Kertz\TwitterBundle\Services\Twitter::connect()
     */
    public function testConnectSuccessWithoutCallbackURL()
    {
        $connection = $this->getMockBuilder('Kertz\\TwitterBundle\\Tests\\MockTwitterOAuth')
            ->setMethods(array('getRequestToken', 'getAuthorizeURL'))
            ->getMock();

        $connection->expects($this->once())
            ->method('getRequestToken')
            ->with($this->equalTo(null))
            ->will($this->returnValue($this->requestToken));

        $connection->expects($this->once())
            ->method('getAuthorizeURL')
            ->with($this->equalTo($this->requestToken))
            ->will($this->returnValue($this->redirectURL));

        $connection->http_code = 200;

        $session = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('set'))
            ->getMock();

        $session->expects($this->at(0))
            ->method('set')
            ->with('oauth_token', $this->requestToken['oauth_token'])
            ->will($this->returnValue(null));

        $session->expects($this->at(1))
            ->method('set')
            ->with('oauth_token_secret', $this->requestToken['oauth_token_secret'])
            ->will($this->returnValue(null));

        $twitter = new Twitter();

        $this->assertEquals($this->redirectURL ,$twitter->connect($connection, $session, $this->callbackURL));
    }

    /**
     * @covers Kertz\TwitterBundle\Services\Twitter::process()
     */
    public function testProcessFail(){

        $accessToken = 'foo';

        $connection = $this->getMockBuilder('Kertz\\TwitterBundle\\Tests\\MockTwitterOAuth')
            ->setMethods(array('getAccessToken'))
            ->getMock();

        $connection->expects($this->once())
            ->method('getAccessToken')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue($accessToken));

        $connection->http_code = 500;

        $request = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Request')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $request->expects($this->at(0))
            ->method('get')
            ->with('oauth_token')
            ->will($this->returnValue('bar'));

        $request->expects($this->at(1))
            ->method('get')
            ->with('oauth_verifier')
            ->will($this->returnValue('foo'));        

        $session = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('set', 'get', 'remove'))
            ->getMock();

        $session->expects($this->at(0,1))
            ->method('get')
            ->with('oauth_token')
            ->will($this->returnValue('foo'));

        $session->expects($this->at(2))
            ->method('set')
            ->with('oauth_status', 'oldtoken')
            ->will($this->returnValue(null));

        $session->expects($this->at(3))
            ->method('set')
            ->with('access_token', 'foo')
            ->will($this->returnValue(null));

        $session->expects($this->at(4))
            ->method('remove')
            ->with('oauth_token', null)
            ->will($this->returnValue(null));

        $session->expects($this->at(5))
            ->method('remove')
            ->with('oauth_token_secret', null)
            ->will($this->returnValue(null));

        $twitter = new Twitter();

        $this->assertEquals(null ,$twitter->process($connection, $session, $request));

    }

    /**
     * @covers Kertz\TwitterBundle\Services\Twitter::process()
     */
    public function testProcessSuccess(){

        $accessToken = 'foo';

        $connection = $this->getMockBuilder('Kertz\\TwitterBundle\\Tests\\MockTwitterOAuth')
            ->setMethods(array('getAccessToken'))
            ->getMock();

        $connection->expects($this->once())
            ->method('getAccessToken')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue($accessToken));

        $connection->http_code = 200;

        $request = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Request')
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $request->expects($this->at(0))
            ->method('get')
            ->with('oauth_token')
            ->will($this->returnValue('bar'));

        $request->expects($this->at(1))
            ->method('get')
            ->with('oauth_verifier')
            ->will($this->returnValue('foo'));

        $session = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Session')
            ->disableOriginalConstructor()
            ->setMethods(array('set', 'get', 'remove'))
            ->getMock();

        $session->expects($this->at(0,1))
            ->method('get')
            ->with('oauth_token')
            ->will($this->returnValue('foo'));

        $session->expects($this->at(2))
            ->method('set')
            ->with('oauth_status', 'oldtoken')
            ->will($this->returnValue(null));

        $session->expects($this->at(3))
            ->method('set')
            ->with('access_token', 'foo')
            ->will($this->returnValue(null));

        $session->expects($this->at(4))
            ->method('remove')
            ->with('oauth_token', null)
            ->will($this->returnValue(null));

        $session->expects($this->at(5))
            ->method('remove')
            ->with('oauth_token_secret', null)
            ->will($this->returnValue(null));

        $twitter = new Twitter();

        $this->assertEquals($accessToken ,$twitter->process($connection, $session, $request));

    }
}