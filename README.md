# Payeezy API Client Library for PHP
[![Build Status](https://travis-ci.org/payeezy/payeezy-php.svg?branch=master)](https://travis-ci.org/payeezy/payeezy-php)
## Description
Payeezy PHP SDK is built to make developers life easy to integrate with the Payeezy API (https://developer.payeezy.com) for processing payements with various payment methods. Download the SDK, follow instructions to start testing against the sandbox environment with developer credentials.
## Installation ##

You can use **Composer** or simply **Download the Release**

### Composer

The preferred method is via [composer](https://getcomposer.org). Follow the
[installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have
composer installed.

Once composer is installed, execute the following command in your project root to install this library:

```sh
composer require payeezy/payeezy-php:dev-master
```

Finally, be sure to include the autoloader:

```php
require_once '/path/to/your-project/vendor/autoload.php';
```

### Download the Release

If you abhor using composer, you can download the package in its entirety. The [Releases](https://github.com/payeezy/payeezy-php/releases) page lists all stable versions. Download any file
with the name `payeezy-php-[RELEASE_NAME].zip` for a package including this library and its dependencies.

Uncompress the zip file you download, and include the autoloader in your project:

```php
require_once '/path/to/payeezy-php/vendor/autoload.php';
```
## Examples ##
See the [`examples/`](examples) directory for examples of the key client features. You can
view them in your browser by running the php built-in web server.

```
$ php -S localhost:8000 -t examples/
```

And then browsing to the host and port you specified
(in the above example, `http://localhost:8000`).

### Basic Example ###

```php
require_once 'vendor/autoload.php';

$client = new Payeezy_Client();
$client->setApiKey("YOUR_API_KEY");
$client->setApiSecret("YOUR_API_SECRET");
$client->setMerchantToken("YOUR_MERCHANT_TOKEN");
$client->setUrl("https://api-cert.payeezy.com/v1/transactions");

$card_transaction = new Payeezy_CreditCard($client);

$response = $card_transaction->purchase([
  "merchant_ref" => "Astonishing-Sale",
  "amount" => "1299",
  "currency_code" => "USD",
  "credit_card" => array(
    "type" => "visa",
    "cardholder_name" => "John Smith",
    "card_number" => "4788250000028291",
    "exp_date" => "1020",
    "cvv" => "123"
  )
]);
echo "<pre>";
var_dump($response);
echo "</pre>";
```

## Testing ##
Run the PHPUnit tests with PHPUnit. You can configure API keys and token in BaseTest.php
```
phpunit tests/
```

## Contributing ##
1. Fork the PHP client library on GitHub
2. Decide which code you want to submit. A submission should be a set of changes that addresses one issue in the issue tracker. Please file one change per issue, and address one issue per change. If you want to make a change that doesn't have a corresponding issue in the issue tracker, please file a new ticket!
3. Ensure that your code adheres to standard PHP conventions, as used in the rest of the library.
4. Ensure that there are unit tests for your code.
5. Submit a pull request with your patch on Github.

## Coding Style ##
To check for coding style violations, run
```
vendor/bin/phpcs src --standard=coder_ruleset.xml -np
```
To automatically fix (fixable) coding style violations, run
```
vendor/bin/phpcbf src --standard=coder_ruleset.xml
```

## Feedback ##
We appreciate the time you take to try out our sample code and welcome your feedback. Here are a few ways to get in touch:
* For generally applicable issues and feedback, create an issue in this repository.
* support@payeezy.com - for personal support at any phase of integration
* [1.855.799.0790](tel:+18557990790)  - for personal support in real time 

## Terms of Use ##
Terms and conditions for using Payeezy API SDK: Please see [Payeezy Terms & conditions](https://developer.payeezy.com/terms-use)
 
### License ###
The Payeezy PHP SDK is open source and available under the MIT license. See the LICENSE file for more info.

## Project Maintenance ##
### Github pages ###
```
git subtree push --prefix docs origin gh-pages
```
https://gist.github.com/cobyism/4730490
