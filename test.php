<?php
error_reporting(E_ALL);
ini_set('display_errors', 'true');

require_once('DialoguePartnerToolkitClientLibrary.php');
use Dialogue\Toolkit\Sms\SendSmsClient;
use Dialogue\Toolkit\Sms\Credentials;
use Dialogue\Toolkit\Sms\SendSmsRequest;
use Dialogue\Toolkit\Sms\SendSmsResponse;
use Dialogue\Toolkit\Sms\Sms;
use Dialogue\Toolkit\Sms\SmsReport;
use Dialogue\Toolkit\Sms\State;
use Dialogue\Toolkit\Sms\SmsReply;

date_default_timezone_set("Europe/London");

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

function my_assert_handler($file, $line, $code)
{
    echo "Assertion failed on line $line: $code";
}

assert_options(ASSERT_CALLBACK, 'my_assert_handler');

function assertException($function, $exception, $message)
{
    try {
        call_user_func($function);
        assert("false /* Exception expected */");
    } catch (Exception $e) {
        assert('get_class($e) == $exception');
        if ($message && strpos($e->getMessage(), $message) === false) {
            assert("false /* '" . $e->getMessage() . "' does not contain '$message'. */");
        }
    }
}

//
// Credentials constructors
//

// userName=null
assertException(function() {
    new Credentials(null, "pass");
}, "InvalidArgumentException", "No userName provided.");

// userName=""
assertException(function() {
    new Credentials("", "pass");
}, "InvalidArgumentException", "No userName provided.");

// password=null
assertException(function() {
    new Credentials("user", null);
}, "InvalidArgumentException", "No password provided.");

// password=""
assertException(function() {
    new Credentials("user", "");
}, "InvalidArgumentException", "No password provided.");

//
// Credentials constructor setters
//

$credentials = new Credentials("user", "pass");

// userName=null
assertException(function() {
    global $credentials;
    $credentials->userName = null;
}, "InvalidArgumentException", "No userName provided.");

// userName=""
assertException(function() {
    global $credentials;
    $credentials->userName = "";
}, "InvalidArgumentException", "No userName provided.");

// password=null
assertException(function() {
    global $credentials;
    $credentials->password = null;
}, "InvalidArgumentException", "No password provided.");

// password=""
assertException(function() {
    global $credentials;
    $credentials->password = "";
}, "InvalidArgumentException", "No password provided.");

//
// Credentials constructor getters
//

$credentials = new Credentials("user", "pass");

assert($credentials->userName == "user");
$credentials->userName = "user2";
assert($credentials->userName == "user2");

assert($credentials->password == "pass");
$credentials->password = "pass";
assert($credentials->password == "pass");

//
// SmsSendRequest constructors
//

// messages=null
assertException(function() {
    new SendSmsRequest(null, "recipient");
}, "InvalidArgumentException", "No message provided.");

// messages=""
assertException(function() {
    new SendSmsRequest("", "recipient");
}, "InvalidArgumentException", "No message provided.");

// messages=array()
assertException(function() {
    new SendSmsRequest(array(), "recipient");
}, "InvalidArgumentException", "No messages provided.");

// recipients=null
assertException(function() {
    new SendSmsRequest("message", null);
}, "InvalidArgumentException", "No recipient provided.");

// recipients=""
assertException(function() {
    new SendSmsRequest("message", "");
}, "InvalidArgumentException", "No recipient provided.");

// recipients=array()
assertException(function() {
    new SendSmsRequest("message", array());
}, "InvalidArgumentException", "No recipients provided.");

//
// SmsSendRequest constructor setters
//

$request = new SendSmsRequest("message", "recipient");

// messages=null
assertException(function() {
    global $request;
    $request->messages = null;
}, "InvalidArgumentException", "No message provided.");

// messages=""
assertException(function() {
    global $request;
    $request->messages = "";
}, "InvalidArgumentException", "No message provided.");

// messages=array()
assertException(function() {
    global $request;
    $request->messages = array();
}, "InvalidArgumentException", "No messages provided.");

// recipients=null
assertException(function() {
    global $request;
    $request->recipients = null;
}, "InvalidArgumentException", "No recipient provided.");

// recipients=""
assertException(function() {
    global $request;
    $request->recipients = "";
}, "InvalidArgumentException", "No recipient provided.");

// recipients=array()
assertException(function() {
    global $request;
    $request->recipients = array();
}, "InvalidArgumentException", "No recipients provided.");

//
// SmsSendRequest constructor getters
//

$request = new SendSmsRequest("message", "recipient");
assert($request->messages == "message");
assert($request->recipients == "recipient");
$request->messages = "message2";
$request->recipients = "recipient2";
assert($request->messages == "message2");
assert($request->recipients == "recipient2");

$request = new SendSmsRequest(array("message", "message2"), array("recipient", "recipient2"));
assert($request->messages == array("message", "message2"));
assert($request->recipients == array("recipient", "recipient2"));
$request->messages = array("message", "message2", "message3");
$request->recipients = array("recipient", "recipient2", "recipient3");
assert($request->messages == array("message", "message2", "message3"));
assert($request->recipients == array("recipient", "recipient2", "recipient3"));

//
// SmsSendRequest other getters/setters
//

$request = new SendSmsRequest("message", "recipient");

// Sender
assert($request->sender == null);
$request->sender = "sender";
assert($request->sender == "sender");
assert(strpos($request->__toString(), "<X-E3-Originating-Address>sender</X-E3-Originating-Address>") !== false);

$request->sender = null;
assert($request->sender == null);
assert(strpos($request->__toString(), "<X-E3-Originating-Address>") === false);

// ConcatenationLimit
assert($request->concatenationLimit == null);
$request->concatenationLimit = 255;
assert($request->concatenationLimit == 255);
assert(strpos($request->__toString(), "<X-E3-Concatenation-Limit>255</X-E3-Concatenation-Limit>") !== false);

$request->concatenationLimit = null;
assert($request->concatenationLimit == null);
assert(strpos($request->__toString(), "<X-E3-Concatenation-Limit>") === false);

// ScheduleFor
assert($request->scheduleFor == null);

$request->scheduleFor = new DateTime("1-Sep-12 12:30:00", new DateTimeZone("Europe/London"));
assert($request->scheduleFor == new DateTime("1-Sep-12 12:30:00", new DateTimeZone("Europe/London")));
assert(strpos($request->__toString(), "<X-E3-Schedule-For>20120901123000</X-E3-Schedule-For>") !== false);

$request->scheduleFor = new DateTime("1-Sep-12 12:30:00", new DateTimeZone("Europe/Berlin"));
assert($request->scheduleFor == new DateTime("1-Sep-12 12:30:00", new DateTimeZone("Europe/Berlin")));
assert(strpos($request->__toString(), "<X-E3-Schedule-For>20120901113000</X-E3-Schedule-For>") !== false);

$request->scheduleFor = new DateTime("1-Sep-12 12:30:00 GMT");
assert($request->scheduleFor == new DateTime("1-Sep-12 12:30:00 GMT"));
assert(strpos($request->__toString(), "<X-E3-Schedule-For>20120901133000</X-E3-Schedule-For>") !== false);

$request->scheduleFor = null;
assert($request->scheduleFor == null);
assert(strpos($request->__toString(), "<X-E3-Schedule-For>") === false);

// ConfirmDelivery
assert($request->confirmDelivery == null);
$request->confirmDelivery = true;
assert($request->confirmDelivery == true);
assert(strpos($request->__toString(), "<X-E3-Confirm-Delivery>on</X-E3-Confirm-Delivery>") !== false);
$request->confirmDelivery = false;
assert($request->confirmDelivery == false);
assert(strpos($request->__toString(), "<X-E3-Confirm-Delivery>off</X-E3-Confirm-Delivery>") !== false);

$request->confirmDelivery = null;
assert($request->confirmDelivery == null);
assert(strpos($request->__toString(), "<X-E3-Confirm-Delivery>") === false);

// ReplyPath
assert($request->replyPath == null);
$request->replyPath = "http://www.server.com/path";
assert($request->replyPath == "http://www.server.com/path");
assert(strpos($request->__toString(), "<X-E3-Reply-Path>http://www.server.com/path</X-E3-Reply-Path>") !== false);

$request->replyPath = null;
assert($request->replyPath == null);
assert(strpos($request->__toString(), "<X-E3-Reply-Path>") === false);

// UserKey
assert($request->userKey == null);
$request->userKey = "123457890";
assert($request->userKey == "123457890");
assert(strpos($request->__toString(), "<X-E3-User-Key>123457890</X-E3-User-Key>") !== false);

$request->userKey = null;
assert($request->userKey == null);
assert(strpos($request->__toString(), "<X-E3-User-Key>") === false);

// SessionReplyPath
assert($request->sessionReplyPath == null);
$request->sessionReplyPath = "http://www.server.com/path";
assert($request->sessionReplyPath == "http://www.server.com/path");
assert(strpos($request->__toString(), "<X-E3-Session-Reply-Path>http://www.server.com/path</X-E3-Session-Reply-Path>") !== false);

$request->sessionReplyPath = null;
assert($request->sessionReplyPath == null);
assert(strpos($request->__toString(), "<X-E3-Session-Reply-Path>") === false);

// SessionId
assert($request->sessionId == null);
$request->sessionId = "123457890";
assert($request->sessionId == "123457890");
assert(strpos($request->__toString(), "<X-E3-Session-ID>123457890</X-E3-Session-ID>") !== false);

$request->sessionId = null;
assert($request->sessionId == null);
assert(strpos($request->__toString(), "<X-E3-Session-ID>") === false);

// UserTag
assert($request->userTag == null);
$request->userTag = "123457890";
assert($request->userTag == "123457890");
assert(strpos($request->__toString(), "<X-E3-User-Tag>123457890</X-E3-User-Tag>") !== false);

$request->userTag = null;
assert($request->userTag == null);
assert(strpos($request->__toString(), "<X-E3-User-Tag>") === false);

// ValidityPeriod
assert($request->validityPeriod == null);

// Note DateTime is not comparable:
$request->validityPeriod = new DateInterval("P2W");
assert(var_export($request->validityPeriod, true) == var_export(new DateInterval("P2W"), true));
assert(strpos($request->__toString(), "<X-E3-Validity-Period>2w</X-E3-Validity-Period>") !== false);

$request->validityPeriod = new DateInterval("P7D");
assert(var_export($request->validityPeriod, true) == var_export(new DateInterval("P7D"), true));
assert(strpos($request->__toString(), "<X-E3-Validity-Period>1w</X-E3-Validity-Period>") !== false);

$request->validityPeriod = new DateInterval("P2D");
assert(var_export($request->validityPeriod, true) == var_export(new DateInterval("P2D"), true));
assert(strpos($request->__toString(), "<X-E3-Validity-Period>2d</X-E3-Validity-Period>") !== false);

$request->validityPeriod = new DateInterval("PT24H");
assert(var_export($request->validityPeriod, true) == var_export(new DateInterval("PT24H"), true));
// Different from Java
assert(strpos($request->__toString(), "<X-E3-Validity-Period>24h</X-E3-Validity-Period>") !== false);

$request->validityPeriod = new DateInterval("PT2H");
assert(var_export($request->validityPeriod, true) == var_export(new DateInterval("PT2H"), true));
assert(strpos($request->__toString(), "<X-E3-Validity-Period>2h</X-E3-Validity-Period>") !== false);

$request->validityPeriod = new DateInterval("PT60M");
assert(var_export($request->validityPeriod, true) == var_export(new DateInterval("PT60M"), true));
// Different from Java
assert(strpos($request->__toString(), "<X-E3-Validity-Period>60m</X-E3-Validity-Period>") !== false);

$request->validityPeriod = new DateInterval("PT2M");
assert(var_export($request->validityPeriod, true) == var_export(new DateInterval("PT2M"), true));
assert(strpos($request->__toString(), "<X-E3-Validity-Period>2m</X-E3-Validity-Period>") !== false);

$request->validityPeriod = new DateInterval("PT1S");
assert(var_export($request->validityPeriod, true) == var_export(new DateInterval("PT1S"), true));
assert(strpos($request->__toString(), "<X-E3-Validity-Period>0m</X-E3-Validity-Period>") !== false);

$request->validityPeriod = null;
assert($request->validityPeriod == null);
assert(strpos($request->__toString(), "<X-E3-Validity-Period>") === false);

// validityPeriod="P1Y"
assertException(function() {
    global $request;
    $request->validityPeriod = new DateInterval("P1Y");
}, "InvalidArgumentException", "Years not supported as unit.");

// validityPeriod="P1M"
assertException(function() {
    global $request;
    $request->validityPeriod = new DateInterval("P1M");
}, "InvalidArgumentException", "Months not supported as unit.");

// Custom properties
$request["X-E3-Custom-Property"] = "test1234";
assert($request["X-E3-Custom-Property"] == "test1234");
assert(strpos($request->__toString(), "<X-E3-Custom-Property>test1234</X-E3-Custom-Property>") !== false);
unset($request["X-E3-Custom-Property"]);
assert(strpos($request->__toString(), "<X-E3-Custom-Property>") === false);

//
// toString() (XML generation)
//

$request = new SendSmsRequest(
    array("message", "message2"),
    array("recipient", "recipient2"));
$request->sender = "sender";
$request->concatenationLimit = 255;
$request->scheduleFor = new DateTime("1-Sep-12 12:30:00", new DateTimeZone("Europe/London"));
$request->confirmDelivery = true;
$request->replyPath = "http://www.server.com/path";
$request->userKey = "123457890";
$request->sessionReplyPath = "http://www.server.com/path";
$request->sessionId = "1234567890";
$request->userTag = "123457890";
$request->validityPeriod = new DateInterval("P2W");

$xml = trim($request->__toString());
assert("<?xml version=\"1.0\"?>\n<sendSmsRequest><X-E3-Message>message</X-E3-Message><X-E3-Message>message2</X-E3-Message><X-E3-Recipients>recipient</X-E3-Recipients><X-E3-Recipients>recipient2</X-E3-Recipients><X-E3-Originating-Address>sender</X-E3-Originating-Address><X-E3-Concatenation-Limit>255</X-E3-Concatenation-Limit><X-E3-Schedule-For>20120901123000</X-E3-Schedule-For><X-E3-Confirm-Delivery>on</X-E3-Confirm-Delivery><X-E3-Reply-Path>http://www.server.com/path</X-E3-Reply-Path><X-E3-User-Key>123457890</X-E3-User-Key><X-E3-Session-Reply-Path>http://www.server.com/path</X-E3-Session-Reply-Path><X-E3-Session-ID>1234567890</X-E3-Session-ID><X-E3-User-Tag>123457890</X-E3-User-Tag><X-E3-Validity-Period>2w</X-E3-Validity-Period></sendSmsRequest>" == $xml);

//
// SendSmsClient constructor
//

$credentials = new Credentials("user", "pass");

// endpoint=null
assertException(function() {
    global $credentials;
    new SendSmsClient(null, $credentials);
}, "InvalidArgumentException", "No endpoint provided.");

// endpoint=""
assertException(function() {
    global $credentials;
    new SendSmsClient("", $credentials);
}, "InvalidArgumentException", "No endpoint provided.");

// credentials=null
assertException(function() {
    new SendSmsClient("endpoint", null);
}, "InvalidArgumentException", "No credentials provided.");

//
// SendSmsClient constructor setters
//

$credentials = new Credentials("user", "pass");
$client = new SendSmsClient("endpoint", $credentials);

// endpoint=null
assertException(function() {
    global $client;
    $client->endpoint = null;
}, "InvalidArgumentException", "No endpoint provided.");

// endpoint=""
assertException(function() {
    global $client;
    $client->endpoint = "";
}, "InvalidArgumentException", "No endpoint provided.");

// credentials=""
assertException(function() {
    global $client;
    $client->credentials = null;
}, "InvalidArgumentException", "No credentials provided.");

//
// SendSmsClient constructor getters
//

$credentials = new Credentials("user", "pass");
$client = new SendSmsClient("endpoint", $credentials);

assert($client->endpoint == "endpoint");
$client->endpoint = "endpoint2";
assert($client->endpoint == "endpoint2");

$credentials2 = new Credentials("user2", "pass2");
assert($client->credentials === $credentials);
$client->credentials = $credentials2;
assert($client->credentials === $credentials2);

//
// SendSmsClient Path
//

$credentials = new Credentials("user", "pass");
$client = new SendSmsClient("endpoint", $credentials);

// Default value
assert($client->path == "/submit_sm");

// path=null
assertException(function() {
    global $client;
    $client->path = null;
}, "InvalidArgumentException", "No path provided.");

// path=""
assertException(function() {
    global $client;
    $client->path = "";
}, "InvalidArgumentException", "No path provided.");

// path invalid
assertException(function() {
    global $client;
    $client->path = "path";
}, "InvalidArgumentException", "The path must start with '/'.");

$client->path = "/path";
assert($client->path == "/path");

//
// SendSmsClient Secure
//

$credentials = new Credentials("user", "pass");
$client = new SendSmsClient("endpoint", $credentials);

// Default is secure
assert($client->secure);
$client->secure = false;
assert(!$client->secure);

//
// SendSmsClient Submission
//

$options = getopt("l:p:");
if (count($options) != 2) {
    trigger_error("Usage: php test.php -l <login> -p <password>", E_USER_ERROR);
}
$login = $options["l"];
$password = $options["p"];

$credentials = new Credentials($login, $password);

function testClient(SendSmsClient $client)
{
    $request = new SendSmsRequest(
        "This is a test message.",
        array("447956247525", "34637975280", "999")
    );
    assertResponse($client->sendSms($request));
}

function assertResponse(SendSmsResponse $response)
{
    assert($response != null && $response->messages != null && count($response->messages) == 3);
    assertSms($response->messages[0], "447956247525", "00");
    assertSms($response->messages[1], "34637975280", "00");
    assertSms($response->messages[2], "999", "43");
}

function assertSms(Sms $sms, $recipient, $submissionReport)
{
    assert($sms->recipient == $recipient);
    assert($sms->submissionReport == $submissionReport);
    assert($sms->successful == ($submissionReport == "00"));
    assert($sms->successful ? $sms->id != "" :  $sms->id == "");
    assert($sms->successful ? $sms->errorDescription == "" : $sms->errorDescription != "");
}

$client = new SendSmsClient("sms.dialogue.net", $credentials);
$client->secure = false;
testClient($client);
$client->secure = true;
testClient($client);

$client = new SendSmsClient("sendmsg.dialogue.net", $credentials);
$client->secure = false;
testClient($client);
$client->secure = true;
testClient($client);

// Test wrong password
assertException(function() {
    testClient(new SendSmsClient("sms.dialogue.net", new Credentials("wrong", "wrong")));
}, "Exception", "X-E3-HTTP-Login/X-E3-HTTP-Password incorrect");

// Test wrong endpoint
assertException(function() {
    testClient(new SendSmsClient("wrong", new Credentials("wrong", "wrong")));
}, "Exception", "");

//
// SmsReport
//

try {
    $report = SmsReport::getInstance("<callback X-E3-Delivery-Report=\"20\" X-E3-ID=\"90A9893BC2B645918034F4C358A062CE\" X-E3-Loop=\"1322229741.93646\" X-E3-Network=\"Orange\" X-E3-Recipients=\"447xxxxxxxxx\" X-E3-Timestamp=\"2011-12-01 18:02:21\" X-E3-User-Key=\"myKey1234\"/>");

    assert($report != null);
    assert($report->id == "90A9893BC2B645918034F4C358A062CE");
    assert($report->recipient == "447xxxxxxxxx");
    assert($report->deliveryReport == "20");
    assert($report->userKey == "myKey1234");
    assert($report->timestamp != null);
    assert($report->timestamp->getTimestamp() == 1322762541);
    assert($report->network == "Orange");

    $report->deliveryReport = "00";
    assert($report->state == State::DELIVERED);
    assert($report->successful);
    $report->deliveryReport = "1F";
    assert($report->state == State::DELIVERED);
    assert($report->successful);
    $report->deliveryReport = "20";
    assert($report->state == State::TEMPORARY_ERROR);
    assert(!$report->successful);
    $report->deliveryReport = "3F";
    assert($report->state == State::TEMPORARY_ERROR);
    assert(!$report->successful);
    $report->deliveryReport = "40";
    assert($report->state == State::PERMANENT_ERROR);
    assert(!$report->successful);
    $report->deliveryReport = "7F";
    assert($report->state == State::PERMANENT_ERROR);
    assert(!$report->successful);

    $report->deliveryReport = "80";
    assertException(function() {
        global $report;
        $report->state;
    }, "UnexpectedValueException", "Unknown delivery report value:");

    $report->deliveryReport = "";
    assert($report->state == State::UNDEFINED);

} catch (Exception $e) {
    assert("false /* Exception caught: " . $e->getMessage() . " */");
}

//
// SmsReply
//

try {
    $reply = SmsReply::getInstance("<callback X-E3-Account-Name=\"test\" X-E3-Data-Coding-Scheme=\"00\" X-E3-Hex-Message=\"54657374204D657373616765\" X-E3-ID=\"809EF683F022441DB9C4895AED6382CF\" X-E3-Loop=\"1322223264.20603\" X-E3-MO-Campaign=\"\" X-E3-MO-Keyword=\"\" X-E3-Network=\"Orange\" X-E3-Originating-Address=\"447xxxxxxxxx\" X-E3-Protocol-Identifier=\"00\" X-E3-Recipients=\"1234567890\" X-E3-Session-ID=\"1234567890\" X-E3-Timestamp=\"2011-11-25 12:14:23.000000\" X-E3-User-Data-Header-Indicator=\"0\"/>");

    assert($reply != null);
    assert($reply->id == "809EF683F022441DB9C4895AED6382CF");
    assert($reply->sender == "447xxxxxxxxx");
    assert($reply->sessionId == "1234567890");
    assert($reply->hexMessage == "54657374204D657373616765");
    assert($reply->message == "Test Message");
    assert($reply->timestamp != null);
    assert($reply->timestamp->getTimestamp() == 1322223263);
    assert($reply->network == "Orange");

} catch (Exception $e) {
    assert("false /* Exception caught: " . $e->getMessage() . " */");
}