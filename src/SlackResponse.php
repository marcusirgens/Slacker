<?php
    
namespace Slacker;

class SlackResponse
{
    
    /**
     * The request we are responding to
     * 
     * @var SlackRequest
     * @access private
     */
    private $slackRequest;
    
    /**
     * The text in the response
     * 
     * (default value: '')
     * 
     * @var string
     * @access private
     */
    private $text = '';
    
    /**
     * How to respond to the user.
     * Is either 'ephemeral' or 'in_channel'
     * 
     * @var string
     * @access private
     */
    private $responseType;
    
    /**
     * Whether to send answer slash command as a delayed response.
     * 
     * @var boolean
     * @access private
     */
    private $isDelayed;
    
    /**
     * Attachments!
     * 
     * @var array
     * @access private
     */
    private $attachments;
    
    
    public function __construct(SlackRequest $slackRequest) {
        $this->slackRequest = $slackRequest;
        
        // Decide how to respond and set some defaults
        if($this->slackRequest->isCommand()) {
            $this->setEphemeral();
        } else {
            $this->setInChannel();
            $this->isDelayed(false);
        }
        
    }
    
    public function isDelayed($delayed) {
        $this->isDelayed = boolval($delayed);
    }
    
    public function setEphemeral() {
        $this->responseType = 'ephemeral';
    }
    
    public function setInChannel() {
        $this->responseType = 'in_channel';
    }
    
    public function setText($text) {
        $this->text = (string) $text;
    }
    
    private function sendDelayedResponse($response) {
	
        $ch = curl_init($this->slackRequest->responseUrl());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($response))                                                                       
        ); 
        $result = curl_exec($ch);
        curl_close($ch);

	
        return $result;
    }
    
    public function respond() {
        $response = json_encode($this->buildResponseArray(), JSON_PRETTY_PRINT);
        if(!$this->isDelayed) {
            header('Content-Type: application/json; charset=utf-8');
            echo $response;
        } else {
            //echo $response;
            $test = $this->sendDelayedResponse($response);
        }
    }
    
    public function addAttachment(SlackAttachment $att) {
        $this->attachments[] = $att;
    }
    
    private function buildResponseArray() {
        $att = [];
        foreach($this->attachments as $attachment) {
            $att[] = $attachment->toArray();
        }
        return $out = [
            'text' => (!is_null($this->text) ? $this->text : ''),
            'response_type' => $this->responseType,
            'attachments' => $att,
        ];
    }
}
    
    
?>