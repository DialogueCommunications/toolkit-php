<?php

namespace Dialogue\Toolkit\Sms\Tests;

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '../../DialoguePartnerToolkitClientLibrary.php';

use Dialogue\Toolkit\Sms\Credentials;

class CredentialsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Credentials
     */
    private $credentials;

    public function setUp()
    {
        $this->credentials = new Credentials('user', 'pass');
    }

    /**
     * @test Username can be accessed
     */
    public function getUsername()
    {
        $this->assertTrue($this->credentials->userName == 'user');
    }

    /**
     * @test Password can be accessed
     */
    public function testGetPassword()
    {
        $this->assertTrue($this->credentials->password == 'pass');
    }

    /**
     * @test Username can be updated
     */
    public function testSetUsername()
    {
        $this->credentials->userName = 'newuser';
        $this->assertTrue($this->credentials->userName == 'newuser');
    }

    /**
     * @test Password can be updated
     */
    public function testSetPassword()
    {
        $this->credentials->password = 'newpass';
        $this->assertTrue($this->credentials->password == 'newpass');
    }

    /**
     * @test Username can not be set blank
     * @expectedException \InvalidArgumentException
     */
    public function testSetEmptyUsername()
    {
        $this->credentials->userName = '';
    }

    /**
     * @test Password can not be set blank
     * @expectedException \InvalidArgumentException
     */
    public function testSetEmptyPassword()
    {
        $this->credentials->password = '';
    }
}
