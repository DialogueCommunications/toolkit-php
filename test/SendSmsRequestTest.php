<?php

namespace Dialogue\Toolkit\Sms\Tests;

require_once 'PHPUnit/Autoload.php';
require_once dirname(__FILE__) . '../../DialoguePartnerToolkitClientLibrary.php';

use Dialogue\Toolkit\Sms\SendSmsRequest;

class SendSmsRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SendSmsRequest
     */
    private $sendSmsRequest;

    public function setUp()
    {
        $this->sendSmsRequest = new SendSmsRequest('Test Message', '4412345678910');
    }

    /**
     * @test SendSmsRequest object should be serialised to XML
     */
    public function convertSmsRequestObjectToXml()
    {
        $xmlRequest = (string) $this->sendSmsRequest;
        $this->assertTrue(strpos($xmlRequest, '<X-E3-Message>Test Message</X-E3-Message>') !== false);
        $this->assertTrue(strpos($xmlRequest, '<X-E3-Recipients>4412345678910</X-E3-Recipients>') !== false);
    }

    /**
     * @test Additional properties should be mapped to XML
     */
    public function propertiesShouldBeMappedToXML()
    {
        $this->sendSmsRequest->concatenationLimit = 5;
        $this->sendSmsRequest->validityPeriod = new \DateInterval('P2D');
        $this->sendSmsRequest->confirmDelivery = true;
        $this->sendSmsRequest->replyPath = 'http://www.dialogue.net';
        $this->sendSmsRequest->userKey = 'user_key_val';
        $this->sendSmsRequest->userTag = 'user_tag_val';
        $this->sendSmsRequest->scheduleFor = new \DateTime('12:05PM', new \DateTimeZone('UTC'));
        $this->sendSmsRequest->sessionReplyPath = 'http://www.dialogue.co.uk';
        $this->sendSmsRequest->sessionId = '123';

        $xmlRequest = (string) $this->sendSmsRequest;

        $this->assertTrue(stripos($xmlRequest, '<X-E3-Concatenation-Limit>5</X-E3-Concatenation-Limit>') !== false);
        $this->assertTrue(stripos($xmlRequest, '<X-E3-Reply-Path>http://www.dialogue.net</X-E3-Reply-Path>') !== false);
        $this->assertTrue(stripos($xmlRequest, '<X-E3-Confirm-Delivery>on</X-E3-Confirm-Delivery>') !== false);
        $this->assertTrue(stripos($xmlRequest, '<X-E3-User-Key>user_key_val</X-E3-User-Key>') !== false);
        $this->assertTrue(stripos($xmlRequest, '<X-E3-User-Tag>user_tag_val</X-E3-User-Tag>') !== false);
        $this->assertTrue(
            stripos(
                $xmlRequest,
                '<X-E3-Schedule-For>' . $this->sendSmsRequest->scheduleFor->format("YmdHis") . '</X-E3-Schedule-For>'
            ) !== false
        );
        $this->assertTrue(
            stripos(
                $xmlRequest,
                '<X-E3-Session-Reply-Path>http://www.dialogue.co.uk</X-E3-Session-Reply-Path>'
            ) !== false
        );
        $this->assertTrue(stripos($xmlRequest, '<X-E3-Session-ID>123</X-E3-Session-ID>') !== false);
    }

    /**
     * @test Get messages property
     */
    public function getMessages()
    {
        $this->assertTrue($this->sendSmsRequest->messages == 'Test Message');
    }

    /**
     * @test Get recipients property
     */
    public function getRecipients()
    {
        $this->assertTrue($this->sendSmsRequest->recipients == '4412345678910');
    }

    /**
     * @test Set empty recipients should throw exception
     * @expectedException \InvalidArgumentException
     */
    public function setEmptyRecipientsShouldThrowException()
    {
        $this->sendSmsRequest->recipients = array();
    }

    /**
     * @test Get validity period property
     */
    public function setAndGetValidityPeriod()
    {
        $this->sendSmsRequest->validityPeriod = new \DateInterval('P2D');
        $this->assertTrue($this->sendSmsRequest->validityPeriod->d === 2);
    }

    /**
     * @test Set validity period using years should throw exception
     * @expectedException \InvalidArgumentException
     */
    public function setValidityPeriodWithYearsShouldFail()
    {
        $this->sendSmsRequest->validityPeriod = new \DateInterval('P1Y');
    }

    /**
     * @test Set validity period using months should throw exception
     * @expectedException \InvalidArgumentException
     */
    public function setValidityPeriodWithMonthsShouldFail()
    {
        $this->sendSmsRequest->validityPeriod = new \DateInterval('P1M');
    }

    /**
     * @test Set message property
     */
    public function setMessages()
    {
        $this->sendSmsRequest->messages = 'Test Message 2';
        $this->assertTrue($this->sendSmsRequest->messages === 'Test Message 2');
    }

    /**
     * @test Set recipients property
     */
    public function setRecipients()
    {
        $this->sendSmsRequest->recipients = '4412345678000';
        $this->assertTrue($this->sendSmsRequest->recipients == '4412345678000');
    }
}
