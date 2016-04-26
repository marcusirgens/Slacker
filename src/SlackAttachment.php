<?php

namespace Slacker;

class SlackAttachment
{
    
    /**
     * A plain-text summary of the attachment. This text will be used in clients that don't show formatted text (eg. IRC, mobile notifications) and should not contain any markup.
     * 
     * @var string
     * @access private
     */
    private $fallback;
    
    /**
     * An optional value that can either be one of good, warning, danger, or any hex color code (eg. #439FE0). This value is used to color the border along the left side of the message attachment.
     * 
     * @var string
     * @access private
     */
    private $color;
    
    /**
     * This is optional text that appears above the message attachment block.
     * 
     * @var mixed
     * @access private
     */
    private $pretext;
    
    /**
     * A valid URL that displays a small 16x16px image to the left of the authorName text. Will only work if authorName is not null.
     * 
     * @var string
     * @access private
     */
    private $authorIcon;
    
    /**
     * A valid URL that will hyperlink the authorName text. Will only work if authorName is not null.
     * 
     * @var string
     * @access private
     */
    private $authorLink;
    
    /**
     * Small text used to display the author's name.
     * 
     * @var string
     * @access private
     */
    private $authorName;
    
    /**
     * The title is displayed as larger, bold text near the top of a message attachment.
     * 
     * @var string
     * @access private
     */
    private $title;
        
    /**
     * By passing a valid URL, the title text will be hyperlinked.
     * 
     * @var string
     * @access private
     */
    private $titleLink;
    
    /**
     * This is the main text in a message attachment, and can contain standard message markup. The content will automatically collapse if it contains 700+ characters or 5+ linebreaks, and will display a "Show more..." link to expand the content.
     * 
     * @var string
     * @access private
     */
    private $text;
    
    /**
     * Array of arrays, inner arrays must be of the following format: ['title' => string, 'value' => string, 'short' => boolean]
     * 
     * Will be displayed in a table inside the message attachment.
     * Title is shown as a bold heading above the value text. It cannot contain markup and will be escaped for you.
     * Value is the text value of the field. It may contain standard message markup and must be escaped as normal. May be multi-line.
     * Short is an optional flag indicating whether the value is short enough to be displayed side-by-side with other values.
     *
     * @var array
     * @access private
     */
    private $fields = [];
    
    /**
     * A valid URL to an image file that will be displayed inside a message attachment. We currently support the following formats: GIF, JPEG, PNG, and BMP. Large images will be resized to a maximum width of 400px or a maximum height of 500px, while still maintaining the original aspect ratio.
     * 
     * @var string
     * @access private
     */
    private $imageUrl;
    
    /**
     * A valid URL to an image file that will be displayed as a thumbnail on the right side of a message attachment. We currently support the following formats: GIF, JPEG, PNG, and BMP. The thumbnail's longest dimension will be scaled down to 75px while maintaining the aspect ratio of the image. The filesize of the image must also be less than 500 KB.
     * 
     * @var string
     * @access private
     */
    private $thumbUrl;
    
    /**
     * Slack messages may be formatted using a simple markup language similar to Markdown.
     * 
     * @var array
     * @access private
     */
    private $markdown = [
        'pretext' => false,
        'text' => true,
        'fields' => false
    ];

    /**
     * Sets the fallback text.
     *
     * @access public
     * @param string $string This is a plain-text summary of the attachment. This text will be used in clients that don't show formatted text (eg. IRC, mobile notifications) and should not contain any markup.
     * @return void
     */
    public function setFallback($string) {
        $this->fallback = $string;
    } 
    
    /**
     * Sets the color.
     * 
     * @access public
     * @param null|string $color An optional value that can either be one of good, warning, danger, or any hex color code (eg. #439FE0). This value is used to color the border along the left side of the message attachment.
     * @return void
     */
    public function setColor($color) {
        if(in_array($color, ['good', 'warning', 'danger'])) {
            $this->color = $color;
        } elseif(preg_match('/#([a-fA-F0-9]{3}){1,2}\b/', $color)) {
            $this->color = $color;
        } elseif(is_null($color)) {
            $this->color = null;
        }
    }
    
    /**
     * Sets the text.
     * 
     * @access public
     * @param string $text This is the main text in a message attachment, and can contain standard message markup. The content will automatically collapse if it contains 700+ characters or 5+ linebreaks, and will display a "Show more..." link to expand the content.
     * @return void
     */
    public function setText($text) {
        $this->text = $text;
    }
    
    /**
     * Decides which fields should be parsed as markdown.
     * 
     * @access public
     * @param bool $text (default: true)
     * @param bool $pretext (default: false)
     * @param bool $fields (default: false)
     * @return void
     */
    public function markdown($text = true, $pretext = false, $fields = false) {
        $this->markdown['text'] = boolval($text);
        $this->markdown['pretext'] = boolval($pretext);
        $this->markdown['fields'] = boolval($fields);
    }
    
    /**
     * Sets the pretext.
     * 
     * @access public
     * @param string $pretext This is optional text that appears above the message attachment block.
     * @return void
     */
    public function setPretext($pretext) {
        $this->pretext = $pretext; 
    }
    
    /**
     * Sets author data.
     * 
     * @access public
     * @param mixed $name Small text used to display the author's name. (default: null)
     * @param mixed $link A valid URL that will hyperlink the authorName text. Will only work if authorName is not null. (default: null)
     * @param string $img A valid URL that displays a small 16x16px image to the left of the authorName text. Will only work if authorName is not null. (default: null)
     * @return void
     */
    public function setAuthor($name = null, $link = null, $img = null) {
        $this->authorName = $name;
        $this->authorLink = $link;
        $this->authorIcon = $img;
    }
    
    /**
     * Sets the title and it's link.
     * 
     * @access public
     * @param string $title The title is displayed as larger, bold text near the top of a message attachment.
     * @param mixed $url By passing a valid URL, the title text will be hyperlinked. (default: null)
     * @return void
     */
    public function setTitle($title, $url = null) {
        $this->title = $title;
        $this->titleLink = $url;
    }
    
    /**
     * Adds a field.
     * 
     * @access public
     * @param string $title
     * @param string $value
     * @param bool $short (default: false)
     * @return void
     */
    public function addField($title, $value, $short = false) {
        $this->fields[] = [
            'title' => (string) $title,
            'value' => (string) $value,
            'short' => boolval($short)
        ];
    }
    
    /**
     * Sets the image.
     *
     * @access public
     * @param string $url A valid URL to an image file that will be displayed inside a message attachment. We currently support the following formats: GIF, JPEG, PNG, and BMP. Large images will be resized to a maximum width of 400px or a maximum height of 500px, while still maintaining the original aspect ratio.
     * @return void
     */
    public function setImage($url) {
        $this->imageUrl = $url;
    }
    
    /**
     * Sets the thumbnail.
     * 
     * @access public
     * @param string $url A valid URL to an image file that will be displayed as a thumbnail on the right side of a message attachment. We currently support the following formats: GIF, JPEG, PNG, and BMP. The thumbnail's longest dimension will be scaled down to 75px while maintaining the aspect ratio of the image. The filesize of the image must also be less than 500 KB.
     * @return void
     */
    public function setThumb($url) {
        $this->thumbUrl = $url;
    }
    
    /**
     * Returns the attachment in a format that should result in perfect JSON for Slack.
     * 
     * @access public
     * @return array
     */
    public function toArray() {
        return array_merge(
            (!is_null($this->text) ? ['text' => $this->text] : []),
            (!is_null($this->pretext) ? ['pretext' => $this->pretext] : []),
            (!is_null($this->fallback) ? ['fallback' => $this->fallback] : []),
            (!is_null($this->color) ? ['color' => $this->color] : []),
            (!is_null($this->title) ? ['title' => $this->title] : []),
            (!is_null($this->imageUrl) ? ['image_url' => $this->imageUrl] : []),
            (!is_null($this->thumbUrl) ? ['thumb_url' => $this->thumbUrl] : []),
            (!is_null($this->titleLink) && !is_null($this->title) ? ['title_link' => $this->titleLink] : []),
            (!is_null($this->authorName) ? ['author_name' => $this->authorName] : []),
            (!is_null($this->authorName) && !is_null($this->authorLink) ? ['author_link' => $this->authorLink] : []),
            (!is_null($this->authorName) && !is_null($this->authorIcon) ? ['author_icon' => $this->authorIcon] : []),
            ['mrkdwn_in' => array_merge(
                ($this->markdown['text'] ? ['text'] : []),
                ($this->markdown['fields'] ? ['fields'] : []),
                ($this->markdown['pretext'] ? ['pretext'] : [])
            )],
            (count($this->fields) ? $this->fields : [])
        );
    }
    
}

?>