<?php

namespace Dialogue\Toolkit\Sms\Tests;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '../../DialoguePartnerToolkitClientLibrary.php';

use Dialogue\Toolkit\Sms\SendSmsResponse;

class SendSmsResponseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var sendSmsResponse
     */
    private $sendSmsResponse;

    public function setUp()
    {
        $this->sendSmsResponse = new SendSmsResponse('user', 'pass');
    }

    /**
     * @test Should get empty response if type not SMS
     */
    public function responseIsNotSms()
    {
        $parser = null;
        $parameters = array(
            'X-E3-RECIPIENTS' => 4412345678910,
            'X-E3-SUBMISSION-REPORT' => 00,
            'X-E3-ID' => 12345,
        );
        $this->sendSmsResponse->startElement($parser, 'mms', $parameters);
        $this->assertEmpty($this->sendSmsResponse->messages);
    }

    /**
     * @test Messages are processed properly by response
     */
    public function processSms()
    {
        $parser = null;
        $parameters = array(
            'X-E3-RECIPIENTS' => 4412345678910,
            'X-E3-SUBMISSION-REPORT' => 00,
            'X-E3-ID' => 12345,
        );
        $this->sendSmsResponse->startElement($parser, 'SMS', $parameters);
        $this->assertNotEmpty($this->sendSmsResponse->messages);
    }

    /**
     * @test Failed SMS should not show as successful
     */
    public function processFailedSms()
    {
        $parser = null;
        $parameters = array(
            'X-E3-RECIPIENTS' => 4412345678910,
            'X-E3-SUBMISSION-REPORT' => '4F',
            'X-E3-ID' => 12345,
            'X-E3-ERROR-DESCRIPTION' => 'Test Error',
        );
        $this->sendSmsResponse->startElement($parser, 'SMS', $parameters);
        $this->assertTrue($this->sendSmsResponse->messages[0]->successful === false);
    }
}
