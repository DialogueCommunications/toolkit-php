<?php

namespace Dialogue\Toolkit\Sms\Tests;

require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '../../DialoguePartnerToolkitClientLibrary.php';

use Dialogue\Toolkit\Sms\SmsReply;

class SmsReplyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SmsReply
     */
    protected $smsReply;

    /**
     * @test Reply can be parsed correctly
     * @todo Implement testStartElement().
     */
    public function replyParsesCorrectly()
    {
        $xml = "<callback X-E3-Account-Name=\"test\" X-E3-Data-Coding-Scheme=\"00\" "
            . "X-E3-Hex-Message=\"54657374204D657373616765\" X-E3-ID=\"809EF683F022441DB9C4895AED6382CF\" "
            . "X-E3-Loop=\"1322223264.20603\" X-E3-MO-Campaign=\"\" X-E3-MO-Keyword=\"\" X-E3-Network=\"Orange\" "
            . "X-E3-Originating-Address=\"447xxxxxxxxx\" X-E3-Protocol-Identifier=\"00\" "
            . "X-E3-Recipients=\"1234567890\" X-E3-Session-ID=\"1234567890\" "
            . "X-E3-Timestamp=\"2011-11-25 12:14:23.000000\" X-E3-User-Data-Header-Indicator=\"0\"/>";
        $messageReply = SmsReply::getInstance($xml);

        $this->assertTrue($messageReply->network === 'Orange');
        $this->assertTrue($messageReply->hexMessage === '54657374204D657373616765');
        $this->assertTrue($messageReply->message === 'Test Message');
    }
}