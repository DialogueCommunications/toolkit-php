<?php

namespace Dialogue\Toolkit\Sms\Tests;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '../../DialoguePartnerToolkitClientLibrary.php';

use Dialogue\Toolkit\Sms\SmsReport;
use Dialogue\Toolkit\Sms\State;

class SmsReportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var smsReport
     */
    protected $smsReport;

    public function setUp()
    {
        $this->smsReport = new SmsReport();
    }

    /**
     * @test Instance can be created
     */
    public function canGetInstance()
    {
        $xml = '<callback X-E3-Delivery-Report="00" X-E3-ID="90A9893BC2B645918034F4C358A062CE" '
            . 'X-E3-Loop="1322229741.93646" X-E3-Network="Orange" X-E3-Recipients="447xxxxxxxxx" '
            . 'X-E3-Timestamp="2011-12-01 18:02:21" X-E3-User-Key="myKey1234"/>';
        $report = SmsReport::getInstance($xml);
        $this->assertNotEmpty($report);
    }

    /**
     * @test Report can be processed and properties can be accessed
     */
    public function processReport()
    {
        $xml = '<callback X-E3-Delivery-Report="00" X-E3-ID="90A9893BC2B645918034F4C358A062CE" '
            . 'X-E3-Loop="1322229741.93646" X-E3-Network="Orange" X-E3-Recipients="447xxxxxxxxx" '
            . 'X-E3-Timestamp="2011-12-01 18:02:21" X-E3-User-Key="myKey1234"/>';
        $report = SmsReport::getInstance($xml);
        $this->assertTrue($report->network == 'Orange');
    }

    /**
     * @test Successful state can be handled
     */
    public function stateSuccess()
    {
        $this->smsReport->deliveryReport = '00';
        $this->assertTrue($this->smsReport->state == State::DELIVERED);
    }

    /**
     * @test Temporary failed state can be handled
     */
    public function stateTemporaryFailed()
    {
        $this->smsReport->deliveryReport = '20';
        $this->assertTrue($this->smsReport->state == State::TEMPORARY_ERROR);
    }

    /**
     * @test Permanent failed state can be handled
     */
    public function statePermanentFailed()
    {
        $this->smsReport->deliveryReport = '40';
        $this->assertTrue($this->smsReport->state == State::PERMANENT_ERROR);
    }

    /**
     * @test No delivery report can be handled
     */
    public function stateUnknown()
    {
        $this->smsReport->deliveryReport = '';
        $this->assertTrue($this->smsReport->state == State::UNDEFINED);
    }

    /**
     * @test Unexpected delivery report value should cause exception
     * @expectedException \UnexpectedValueException
     */
    public function unknownDeliveryReportShouldCauseException()
    {
        $this->smsReport->deliveryReport = 'AA';
        $this->smsReport->state;
    }
}
