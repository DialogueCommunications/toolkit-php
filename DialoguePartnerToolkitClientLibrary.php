<?php namespace net\dialogue\toolkit\sms;

class Credentials
{
    public $userName, $password;

    public function __construct($userName, $password)
    {
        $this->userName = $userName;
        $this->password = $password;
    }
}

class SendSmsClient
{
    public $credentials, $endpoint, $path;

    public function __construct($credentials = null)
    {
        $this->credentials = $credentials;
        $this->endpoint = "http://sendmsg.dialogue.net";
        $this->path = "/submit_sm";
    }

    public function __get($name)
    {
        if ($name == "secure") {
            return parse_url($this->endpoint, PHP_URL_SCHEME) == "https";
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    public function __set($name, $value)
    {
        if ($name == "secure") {
            if ($value) {
                $this->endpoint = str_replace("http://", "https://", $this->endpoint);
            }
            else
            {
                $this->endpoint = str_replace("https://", "http://", $this->endpoint);
            }
        }
        else
        {
            $trace = debug_backtrace();
            trigger_error(
                'Undefined property via __set(): ' . $name .
                    ' in ' . $trace[0]['file'] .
                    ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
        }
    }

    public function sendSms($sendSmsRequest)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpoint . $this->path);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/xml; charset=UTF-8'
        ));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->credentials->userName . ":" . $this->credentials->password);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "" . $sendSmsRequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($this->__get("secure"))
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        $xml = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $status = intval($info['http_code']);
        if($status != 200)
        {
            throw new \Exception(
                $xml, $status
            );
        }

        $response = new SendSmsResponse();
        $response->messages = array();
        print_r($xml);

        $parser = xml_parser_create();
        xml_set_object($parser, $response);
        xml_set_element_handler($parser, 'startElement', null);
        xml_parse($parser, $xml);
        xml_parser_free($parser);

        return $response;
    }
}

class SendSmsRequest extends \ArrayObject
{
    public $messages, $recipients, $sender, $concatenationLimit, $scheduleFor,
        $confirmDelivery, $replyPath, $userKey, $sessionReplyPath, $sessionId,
        $userTag, $validityPeriod;

    public function __construct($messages = null, $recipients = null)
    {
        $this->messages = $messages;
        $this->recipients = $recipients;
    }

    public function __toString()
    {
        $doc = new \DOMDocument();
        $root = $doc->createElement("sendSmsRequest");
        $doc->appendChild($root);

        $xml = "<sendSmsRequest>";

        if(is_array($this->messages))
        {
            foreach ($this->messages as $message)
            {
                $el = $doc->createElement("X-E3-Message");
                $el->nodeValue = $message;
                $root->appendChild($el);
            }
        }
        else
        {
            $el = $doc->createElement("X-E3-Message");
            $el->nodeValue = $this->messages;
            $root->appendChild($el);
        }

        if(is_array($this->recipients))
        {
            foreach ($this->recipients as $recipient)
            {
                $el = $doc->createElement("X-E3-Recipients");
                $el->nodeValue = $recipient;
                $root->appendChild($el);
            }
        }
        else
        {
            $el = $doc->createElement("X-E3-Recipients");
            $el->nodeValue = $this->recipients;
            $root->appendChild($el);
        }

        if ($this->sender) {
            $el = $doc->createElement("X-E3-Originating-Address");
            $el->nodeValue = $this->sender;
            $root->appendChild($el);
        }

        if ($this->concatenationLimit) {
            $el = $doc->createElement("X-E3-Concatenation-Limit");
            $el->nodeValue = $this->concatenationLimit;
            $root->appendChild($el);
        }

        if ($this->scheduleFor) {
            $el = $doc->createElement("X-E3-Schedule-For");
            $temp = new \DateTime(null, new \DateTimeZone("UTC"));
            $temp->setTimestamp($this->scheduleFor->getTimestamp());
            $temp->setTimezone(new \DateTimeZone("Europe/London"));
            $el->nodeValue = $temp->format("YmdHis");
            $root->appendChild($el);
        }

        if (!is_null($this->confirmDelivery)) {
            $el = $doc->createElement("X-E3-Confirm-Delivery");
            $el->nodeValue = $this->confirmDelivery ? "on" : "off";
            $root->appendChild($el);
        }

        if ($this->replyPath) {
            $el = $doc->createElement("X-E3-Reply-Path");
            $el->nodeValue = $this->replyPath;
            $root->appendChild($el);
        }

        if ($this->userKey) {
            $el = $doc->createElement("X-E3-User-Key");
            $el->nodeValue = $this->userKey;
            $root->appendChild($el);
        }

        if ($this->sessionReplyPath) {
            $el = $doc->createElement("X-E3-Session-Reply-Path");
            $el->nodeValue = $this->sessionReplyPath;
            $root->appendChild($el);
        }

        if ($this->sessionId) {
            $el = $doc->createElement("X-E3-Session-ID");
            $el->nodeValue = $this->sessionId;
            $root->appendChild($el);
        }

        if ($this->userTag) {
            $el = $doc->createElement("X-E3-User-Tag");
            $el->nodeValue = $this->userTag;
            $root->appendChild($el);
        }

        if ($this->validityPeriod) {
            $el = $doc->createElement("X-E3-Validity-Period");

            if ($this->validityPeriod->y != 0) {
                trigger_error(
                    "Years not supported in DateInterval for property validityPeriod", E_USER_NOTICE
                );
            }

            if ($this->validityPeriod->m != 0) {
                trigger_error(
                    "Months not supported in DateInterval for property validityPeriod", E_USER_NOTICE
                );
            }

            if ($this->validityPeriod->i != 0) {
                $el->nodeValue = (
                    $this->validityPeriod->d * 24 * 60 +
                        $this->validityPeriod->h * 60 +
                        $this->validityPeriod->i) . "m";
            }
            else if ($this->validityPeriod->h != 0) {
                $el->nodeValue = (
                    $this->validityPeriod->d * 24 +
                        $this->validityPeriod->h) . "h";
            }
            else if ($this->validityPeriod->d % 7 == 0) {
                $el->nodeValue = ($this->validityPeriod->d / 7) . "w";
            }
            else {
                $el->nodeValue = $this->validityPeriod->d . "d";
            }

            $root->appendChild($el);
        }

        foreach ($this as $key => $value)
        {
            $el = $doc->createElement($key);
            $el->nodeValue = $value;
            $root->appendChild($el);
        }

        return $doc->saveXML();
    }
}

class SendSmsResponse
{
    public $messages;

    function startElement($parser, $name, $attrs)
    {
        if ($name == "SMS") {
            $sms = new Sms();
            $sms->recipient = $attrs["X-E3-RECIPIENTS"];
            $sms->submissionReport = $attrs["X-E3-SUBMISSION-REPORT"];
            if ($attrs["X-E3-SUBMISSION-REPORT"] == "00") {
                $sms->id = $attrs["X-E3-ID"];
                $sms->successful = true;
            }
            else {
                $sms->errorDescription = $attrs["X-E3-ERROR-DESCRIPTION"];
                $sms->successful = false;
            }
            array_push($this->messages, $sms);
        }
    }
}

class Sms
{
    public $id, $recipient, $submissionReport, $errorDescription, $successful;
}

class SmsReport
{
    public $id, $recipient, $deliveryReport, $userKey, $timestamp, $network, $state;

    public static function getInstance()
    {
        $xml = file_get_contents('php://input');
        $report = new SmsReport();
        $parser = xml_parser_create();
        xml_set_object($parser, $report);
        xml_set_element_handler($parser, 'startElement', null);
        xml_parse($parser, $xml);
        xml_parser_free($parser);
        return $report;
    }

    function startElement($parser, $name, $attrs)
    {
        function startsWith($haystack, $needle)
        {
            $length = strlen($needle);
            return (substr($haystack, 0, $length) === $needle);
        }

        if ($name == "CALLBACK") {
            $this->id = $attrs["X-E3-ID"];
            $this->recipient = $attrs["X-E3-RECIPIENTS"];
            $this->deliveryReport = $attrs["X-E3-DELIVERY-REPORT"];
            $this->userKey = $attrs["X-E3-USER-KEY"];
            $this->timestamp = \DateTime::createFromFormat("Y-m-d H:i:s",
                $attrs["X-E3-TIMESTAMP"], new \DateTimeZone("Europe/London"));
            $this->network = $attrs["X-E3-NETWORK"];
            if (!$this->deliveryReport) {
                $this->state = State::Undefined;
            }
            else if (startsWith($this->deliveryReport, "0") || startsWith($this->deliveryReport, "1")) {
                $this->state = State::Delivered;
            }
            else if (startsWith($this->deliveryReport, "1") || startsWith($this->deliveryReport, "2")) {
                $this->state = State::TemporaryError;
            }
            else if (startsWith($this->deliveryReport, "3") || startsWith($this->deliveryReport, "4")) {
                $this->state = State::PermanentError;
            }
            else
            {
                trigger_error(
                    "Unknown delivery report value: $this->deliveryReport", E_USER_NOTICE
                );
            }
        }
    }
}

class State
{
    const Undefined = 0;
    const Delivered = 1;
    const TemporaryError = 2;
    const PermanentError = 3;
}

class SmsReply
{
    public $id, $sender, $sessionId, $hexMessage, $message, $timestamp, $network;

    public static function getInstance($charset = "UTF-8")
    {
        $xml = file_get_contents('php://input');
        $reply = new SmsReply();
        $parser = xml_parser_create();
        xml_set_object($parser, $reply);
        xml_set_element_handler($parser, 'startElement', null);
        xml_parse($parser, $xml);
        xml_parser_free($parser);
        $reply->message = iconv("ISO-8859-15", $charset, pack("H*", $reply->hexMessage));
        return $reply;
    }

    function startElement($parser, $name, $attrs)
    {
        if ($name == "CALLBACK") {
            $this->id = $attrs["X-E3-ID"];
            $this->sender = $attrs["X-E3-ORIGINATING-ADDRESS"];
            $this->sessionId = $attrs["X-E3-SESSION-ID"];
            $this->hexMessage = $attrs["X-E3-HEX-MESSAGE"];
            $this->timestamp = \DateTime::createFromFormat("Y-m-d H:i:s.000000",
                $attrs["X-E3-TIMESTAMP"], new \DateTimeZone("Europe/London"));
            $this->network = $attrs["X-E3-NETWORK"];
        }
    }
}

?>