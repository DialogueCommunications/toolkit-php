<?php

namespace Dialogue\Toolkit\Sms\Tests;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '../../DialoguePartnerToolkitClientLibrary.php';

use Dialogue\Toolkit\Sms\SendSmsClient;
use Dialogue\Toolkit\Sms\Credentials;

class SendSmsClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SendSmsClient
     */
    protected $sendSmsClient;

    public function setUp()
    {
        $this->sendSmsClient = new SendSmsClient('http://www.test.com', new Credentials('user', 'pass'));
    }

    /**
     * @test Empty endpoint should cause exception
     * @expectedException   \InvalidArgumentException
     */
    public function setEmptyEndpoint()
    {
        $this->sendSmsClient->endpoint = '';
    }

    /**
     * @test Empty credentials should cause exception
     * @expectedException   \InvalidArgumentException
     */
    public function setEmptyCredentials()
    {
        $this->sendSmsClient->credentials = '';
    }

    /**
     * @test Empty path should cause exception
     * @expectedException   \InvalidArgumentException
     */
    public function setEmptyPath()
    {
        $this->sendSmsClient->path = '';
    }

    /**
     * @test Invalid path should cause exception
     * @expectedException   \InvalidArgumentException
     */
    public function setInvalidPath()
    {
        $this->sendSmsClient->path = 'invalid_sm';
    }

    /**
     * @test Update path property
     */
    public function setPath()
    {
        $this->sendSmsClient->path = '/valid_path';
    }
}
