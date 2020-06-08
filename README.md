
# IPPanel Laravel SDK

This repository contains open source Laravel client for `ippanel` API. Documentation can be found at: <http://docs.ippanel.com>.



## Installation

use with composer:

```bash
composer require smart-raya/ippanel-laravel
```

then publish config file:
```bash
php artisan vendor:publish --provider="SmartRaya\IPPanelLaravel\IPPanelServiceProvider"
```

and add `IPPANEL_API` in `.env` file
```bash
APP_NAME=Laravel
APP_ENV=local
...

IPPANEL_API=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```
## Examples

For using sdk, you only need to call `IPPanel::Command()`


### Credit check

```php
# return float64 type credit amount
$credit = IPPanel::getCredit();

```

### Send one to many

For sending sms, obviously you need `originator` number, `recipients` and `message`.

```php
$bulkID =IPPanel::send(
    "+9810001",          // originator
    ["98912xxxxxxx"],    // recipients
    "ippanel is awesome" // message
);

```

If send is successful, a unique tracking code returned and you can track your message status with that.

### Get message summery

```php
$bulkID = "message-tracking-code";

$message = IPPanel::get_message($bulkID);

echo $message->status;   // get message status
echo $message->cost;     // get message cost
echo $message->payback;  // get message payback
```

### Get message delivery statuses

```php
$bulkID = "message-tracking-code"

list($statuses, $paginationInfo) = IPPanel::fetchStatuses($bulkID, 0, 10)

// you can loop in messages statuses list
foreach($statuses as status) {
    echo sprintf("Recipient: %s, Status: %s", $status->recipient, $status->status);
}

echo sprintf("Total: ", $paginationInfo->total);
```

### Inbox fetch

fetch inbox messages

```php
list($messages, $paginationInfo) = IPPanel::fetchInbox(0, 10);

foreach($messages as $message) {
    echo sprintf("Received message %s from number %s in line %s", $message->message, $message->sender, $message->number);
}
```

### Pattern create

For sending messages with predefined pattern(e.g. verification codes, ...), you hav to create a pattern. a pattern at least have a parameter. parameters defined with `%param_name%`.

```php
$pattern = IPPanel::createPattern("%name% is awesome", False);

echo $pattern->code;
```

### Send with pattern

```php
$patternValues = [
    "name" => "IPPANEL",
];

$bulkID = IPPanel::sendPattern(
    "t2cfmnyo0c",    // pattern code
    "+9810001",      // originator
    "98912xxxxxxx",  // recipient
    $patternValues,  // pattern values
);
```

### Error checking

```php
use SmartRaya\IPPanelLaravel\Errors\Error;
use  SmartRaya\IPPanelLaravel\Errors\HttpException;

try{
    $bulkID = IPPanel::send("9810001", ["98912xxxxx"], "ippanel is awesome");
} catch (Error $e) { // ippanel error
    var_dump($e->unwrap()); // get real content of error
    echo $e->getCode();

    // error codes checking
    if ($e->code() == ResponseCodes::ErrUnprocessableEntity) {
        echo "Unprocessable entity";
    }
} catch (HttpException $e) { // http error
    var_dump($e->getMessage()); // get stringified error
    echo $e->getCode();
}
```