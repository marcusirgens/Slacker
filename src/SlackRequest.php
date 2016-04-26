<?php
    
namespace Slacker;
    
class SlackRequest
{
    
    /**
     * Is this a valid slash command or webhook from Slack?
     * 
     * @var boolean
     * @access private
     */
    private $validRequest;
    
    /**
     * Holds all the valid tokens for this service. Array of strings.
     * 
     * @var array
     * @access private
     */
    private $validTokens;

    /**
     * What kind of request is this?
     * This string is either 'webhook' or 'command'
     * 
     * @var string
     * @access private
     */
    private $requestType;

    /**
     * The token provided by the slack request
     * 
     * @var string
     * @access private
     */
    private $token;
    
    /**
     * The team id provided by the slack request
     * 
     * @var string
     * @access private
     */
    private $teamId;
    
    /**
     * The team domain provided by the slack request
     * 
     * @var string
     * @access private
     */
    private $teamDomain;
    
    /**
     * The service id provided by the slack request
     * 
     * @var string
     * @access private
     */
    private $serviceId;
    
    /**
     * The channel id provided by the slack request
     * 
     * @var string
     * @access private
     */
    private $channelId;
    
    /**
     * The channel name provided by the slack request
     * 
     * @var string
     * @access private
     */
    private $channelName;
    
    /**
     * Is this channel private?
     * 
     * @var boolean
     * @access private
     */
    private $privateChannel;
    
    /**
     * The timestamp provided by the slack request
     * 
     * @var \DateTime
     * @access private
     */
    private $timestamp;
    
    /**
     * The user id provided by the slack request
     * 
     * @var string
     * @access private
     */
    private $userId;
    
    /**
     * The username provided by the slack request
     * 
     * @var string
     * @access private
     */
    private $userName;
    
    /**
     * The text provided by the slack request
     * 
     * @var string
     * @access private
     */
    private $text;
    
    /**
     * The command performed in the slack request, minus trailing slash
     * 
     * @var string
     * @access private
     */
    private $command;
    
    /**
     * The arguments after the command in the slack request, array of strings 
     * 
     * @var array
     * @access private
     */
    private $args = [];
    
    /**
     * The trigger word that caused the slack request
     * 
     * @var string
     * @access private
     */
    private $triggerWord;
    
    /**
     * The response URL for a delayed command response.
     * 
     * @var string
     * @access private
     */
    private $responseUrl;
    
    /**
     * Attempts to store data from a slack command or webhook.
     * 
     * @access public
     * @param array $validTokens an array of valid tokens.
     * @return void
     * @throws InvalidArgumentException if the argument provided is not an array.
     * @throws InvalidRequestException if the POST data doesn't contain a slack request
     * @throws InvalidTokenException if the request contains an unknown token
     */
    public function __construct($validTokens = []) {
        if (!is_array($validTokens)) {
            throw new \InvalidArgumentException('Invalid token type. Should be an array of strings.');
        } else {
            $this->validTokens = $validTokens;
        }
        
        if($this->validRequest = $this->isSlackRequest()) {
            $this->storeRequestData();
        } else {
            throw new InvalidRequestException('No valid slack request detected.');
        }
        $this->requestType = $this->detectRequestType();
        
        if (count($this->validTokens)) {
            if(!in_array($this->token, $this->validTokens)) {
                 throw new InvalidTokenException('Invalid token received', $this->token);
            }
        }
        
    }
    
    /**
     * Did the request contain a slash command?
     * 
     * @access public
     * @return boolean
     */
    public function isCommand() {
        return ($this->requestType == 'command');
    }
    
    /**
     * Did the request come from an outgoing webhook?
     * 
     * @access public
     * @return boolean
     */
    public function isWebhook() {
        return ($this->requestType == 'webhook');
    }
    
    /**
     * What type of request is this?
     * 
     * @access public
     * @return string "webhook" or "command"
     */
    public function requestType() {
        return $this->requestType;
    }
    
    /**
     * What trigger word triggered the outgoing webhook?
     * 
     * @access public
     * @return null|string
     */
    public function triggerWord() {
        return $this->triggerWord;
    }
    
    /**
     * Which tcommand triggered the request?
     * 
     * @access public
     * @return null|string
     */
    public function command() {
        return $this->command;
    }
    
    /**
     * Which arguments were provided with the command?
     * 
     * @access public
     * @return array
     */
    public function arguments() {
        return $this->args;
    }
    
    /**
     * What text was sent with the request?
     * 
     * @access public
     * @return string
     */
    public function text() {
        if($this->isWebhook()) {
            return $this->text;
        }
    }
    
    public function responseUrl() {
        return $this->responseUrl;
    }
    
    /**
     * Checks if this is a slack request.
     * 
     * @access private
     * @return boolean
     */
    private function isSlackRequest() {
        if (
            isset($_POST['token'])
            && isset($_POST['team_id']) 
            && (
                isset($_POST['command'])
                || isset($_POST['trigger_word'])
                || isset($_POST['text'])
            )
        ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Checks what kind of request this is
     * 
     * @access private
     * @return string
     */
    private function detectRequestType() {
        if($this->command != null) {
            return 'command';
        } elseif($this->triggerWord != null) {
            return 'webhook';
        } elseif(($this->text != null) && (!$this->command != null)) {
            return 'webhook';
        }
    }
    
    /**
     * Stores the POST data.
     * 
     * @access private
     * @return void
     */
    private function storeRequestData() {
        foreach ($_POST as $key => $value) {
            switch ($key) {
                case 'token':
                    $this->token = $value;
                    break;
                case 'timestamp':
                    $this->timestamp = new \DateTime('@' . (int) $value, new \DateTimeZone('Europe/Oslo'));
                    break;
                case 'team_id':
                    $this->teamId = $value;
                    break;
                case 'team_domain':
                    $this->teamDomain = $value;
                    break;
                case 'service_id':
                    $this->serviceId = $value;
                    break;
                case 'channel_id':
                    $this->channelId = $value;
                    break;
                case 'channel_name':
                    $this->channelName = $value;
                    if($this->channelName == 'privategroup') {
                        $this->privateChannel = true;
                    } else {
                        $this->privateChannel = false;
                    }
                    break;
                case 'user_id':
                    $this->userId = $value;
                    break;
                case 'user_name':
                    $this->userName = $value;
                    break;
                case 'command':
                    $this->command = (strpos($value, '/') === 0 ? substr($value, 1) : $value);
                    $this->args = explode(' ', preg_replace('/ +/i', ' ', $_POST['text']));
                    break;
                case 'text':
                    $this->text = $value;
                    break;
                case 'response_url':
                    $this->responseUrl = $value;
                    break;
                case 'trigger_word':
                    $this->triggerWord = $value;
                    break;
            }
        }
    }
    
}

?>