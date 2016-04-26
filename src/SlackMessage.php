<?php

namespace Slacker;

/**
 * Uses incoming webhooks to send a message.
 *
 * @author Marcus Pettersen Irgens <marcus@comcamp.no>
 */
class SlackMessage
{
    
    /**
     * The webhook URL for your slack integration
     * 
     * @var string
     * @access private
     */
    private $webhookUrl;
    
    /**
     * The message you want to transfer.
     * 
     * @var string|null
     * @access private
     */
    private $text;
    
    /**
     * The attachments to send with the message
     * 
     * @var array
     * @access private
     */
    private $attachments = [];
    
    /**
     * URL to the icon to use as the bot's icon
     * 
     * @var string|null
     * @access private
     */
    private $iconUrl;
    
    /**
     * The username you want your bot to use
     * 
     * @var string|null
     * @access private
     */
    private $username;
    
    /**
     * Emoji code for the bot icon
     * 
     * @var string|null
     * @access private
     */
    private $iconEmoji;
    
    /**
     * The channel you want to post in, or username to send message to
     * 
     * @var string|null
     * @access private
     */
    private $channel;
    
    /**
     * Creates a message.
     * 
     * @access public
     * @param string $url The webhook URL.
     * @return void
     */
    public function __construct($url) {
        $this->webhookUrl = $url;
    }
    
    /**
     * Sets the message text.
     * 
     * @access public
     * @param string|null $text The message you want to transfer.
     * @return void
     */
    public function setText($text) {
        $this->text = $text;
    }
    
    /**
     * Adds an attachment to the message
     * 
     * @access public
     * @param SlackAttachment $att
     * @return void
     */
    public function addAttachment(SlackAttachment $att) {
        $this->attachments[] = $att;
    }
    
    /**
     * Sets the bot icon.
     * 
     * @access public
     * @param string|null $url URL to the icon to use as the bot's icon
     * @return void
     */
    public function setIconUrl($url) {
        $this->iconUrl = $url;
    }
    
    /**
     * Choose an emoji to use as the bot's icon
     * 
     * @access public
     * @param string|null $emoji Emoji code for the bot icon
     * @return void
     */
    public function setIconEmoji($emoji) {
        $this->iconEmoji = $emoji;
    }
    
    /**
     * Sets the bot's username
     * 
     * @access public
     * @param string|null $username The username you want your bot to use
     * @return void
     */
    public function setUsername($username) {
        $this->username = $username;
    }
    
    /**
     * Sets the channel name or username you want to send the message to.
     * 
     * @access public
     * @param string|null $chan The channel you want to post in, or username to send message to
     * @return void
     */
    public function setChannel($chan) {
        $this->channel = $chan;
    }
    
    /**
     * Sends the JSON data to slack.
     * 
     * @access private
     * @param string $data
     * @return void
     */
    private function performWebhook($data) {
        $data = "payload=" . $data;
	
	// You can get your webhook endpoint from your Slack settings
        $ch = curl_init($this->webhookUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
	
        return $result;
    }
    
    /**
     * Builds an array that Slack will like.
     * 
     * @access private
     * @return array JSON-encode ready array
     */
    private function buildArray() {
        $atts = [];
        foreach($this->attachments as $att) {
            $atts[] = $att->toArray();
        }
        
        return array_merge(
            (!is_null($this->text) ? ['text' => (string) $this->text ] : []),
            (!is_null($this->iconUrl) ? ['icon_url' => (string) $this->iconUrl ] : []),
            (!is_null($this->iconEmoji) ? ['icon_emoji' => (string) $this->iconEmoji ] : []),
            (!is_null($this->username) ? ['username' => (string) $this->username ] : []),
            (!is_null($this->channel) ? ['channel' => (string) $this->channel ] : []),
            (count($atts) ? ['attachments' => $atts ] : [])
        );
    }
    
    /**
     * Performs the webhook.
     * 
     * @access public
     * @return void
     */
    public function send() {
        $data = json_encode($this->buildArray(), JSON_PRETTY_PRINT);
        $this->performWebhook($data);
    }
}

?>