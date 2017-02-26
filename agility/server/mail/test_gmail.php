<?php
/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

require __DIR__.'/PHPMailer-5.2.22/PHPMailerAutoload.php';
//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
// use $mail->Host = gethostbyname('smtp.gmail.com');
$mail->Host = 'smtp.gmail.com';
// if your network does not support SMTP over IPv6
//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;
//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = base64_decode("am9uc2l0b0BnbWFpbC5jb20=");
//Password to use for SMTP authentication
$mail->Password = base64_decode("ZnR6dHVoY25xYWJ2bGZuYw==");
//Set who the message is to be sent from
$mail->setFrom('jonsito@gmail.com', 'Juan Antonio Martínez');
//Set an alternative reply-to address
$mail->addReplyTo('jonsito@gmail.com', 'Juan Antonio Martínez');
//Set who the message is to be sent to
// $mail->addAddress('jonsito@gmail.com', 'Juan Antonio Martínez');
$mail->addAddress('juansgaviota@gmail.com', 'Juan A. Mtnez');
//Set the subject line
$mail->Subject = 'PHPMailer GMail SMTP test';
//convert HTML into a basic plain-text alternative body
$d=date("Ymd Hi");
$mail->msgHTML("<h4>Test</h4><p>Just a simple <em>HTML</em> text to test send mail in this format</p><p>Mail sent at:$d</p><hr/>");
//Replace the plain text body with one created manually
$mail->AltBody = "This is a plain-text message body for mail testing.\nMail sent at $d";
//Attach an image file
$mail->addAttachment('../../images/logos/agilitycontest.png');
//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
