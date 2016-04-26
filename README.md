# Slacker
*A simple PHP library for Slack Webhooks*

## Usage
### Incoming webhooks and commands
```
$token = "MY_TOKEN';
$trigger = 'MY_TRIGGER';
$command = 'COMMAND';

try {
    $slack = new Slacker\SlackRequest([$token]); # Look for a request in POST data
} catch catch(Slacker\InvalidRequestException $e) {
    die(echo $e->getMessage());
} catch(Slacker\InvalidTokenException $e) {
    die($e->getMessage() . '(got: *' . $e->getToken() . '*)');
}

# Responding to a trigger
if ($slack->isWebhook() && ($slack->triggerWord() == $trigger)) {
    $response = new Slacker\SlackResponse($slack);
    $response->setText('I\'m responding to the *trigger word* `' . $trigger . '` in the message `' . $slack->text() . '`');
    $response->respond();
}

# Responding to commands
if ($slack->isCommand() && ($slack->command() == $command)) {
    $response = new Slacker\SlackResponse($slack);
    $response->setText('I\'m responding to the *command* `' . $command . '` with the arguments `' . implode('`, `', $slack->args() . '`');
    $response->respond();
}
```
### Adding attachments
Inject a `Slacker\SlackAttachment` to the response with `SlackResponse->addAttachment($att)`.
```
$att = new Slacker\SlackAttachment();
$att->setColor('good');
$att->setTitle('Everything is fine');
$att->setText($out);
$att->markdown(true, true, false);
$someResponse->addAttachment($att);
```
### Incoming webhooks
```
$channel = 'CHANNEL';
$username = 'USERNAME';
$webhookUrl = 'GET YOUR OWN TOKEN';

$incoming = new Slacker\SlackMessage($webhookUrl);
    $incoming->setText(':star2: Hello :star2:');
    $incoming->setChannel($channel);
    $incoming->setUsername($username);

$att = new Slacker\SlackAttachment();
    $att->setText('This is an attachment text!');
    $att->setColor('warning');
    $incoming->addAttachment($att);

$incoming->send(); # sends the request
```

*See code for more documentation*.