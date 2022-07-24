<?php

$to      = 'mgromov@cs-cart.com';
$subject = 'the subject';
$message = 'hello, this is your phpmail function. I am sorry, but the issue is caused by something else...';
$headers = 'From: mgromov@cs-cart.com' . "\r\n" .
 'X-Mailer: PHP/' . phpversion();

$result = mail($to, $subject, $message, $headers);
print $result;
