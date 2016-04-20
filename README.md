# Jaeger Email Object

[![Build Status](https://travis-ci.org/jaeger-app/email.svg?branch=master)](https://travis-ci.org/jaeger-app/email)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jaeger-app/email/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jaeger-app/email/?branch=master)
[![Author](http://img.shields.io/badge/author-@mithra62-blue.svg?style=flat-square)](https://twitter.com/mithra62)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://raw.githubusercontent.com/jaeger-app/bootstrap/master/LICENSE) 

`JaegerApp\Email` is an email abstraction that works with both SwiftMailer 3 and 5 (depending on which is already available). Note that the Email object does NOT include any version of SwiftMailer and relies on the host system to provide one. 

On top of that, `JaegerApp\Email` works with the `JaegerApp\Language` (for copy abstraction), and `JaegerApp\View` (for templating of email messages) using the Mustache templating language. 

## Installation
Add `jaeger-app/email` as a requirement to your `composer.json`:

```bash
$ composer require jaeger-app/email
```

## Send Email

At its purest, sending an email using `JaegerApp\Email` looks like the below (albeit not 100%):

```php
$vars = array('variable1' => 'Variable1');
$email->setSubject($subject)
	->setMessage($message_template)
	->setTo($emails)
	->setMailtype('html');
$email->send($vars);
```