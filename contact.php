<?php
// contact.php — PHPMailer over Zoho SMTP, .env via parse_ini_file (no getenv)
// composer require phpmailer/phpmailer

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

/* ------------------ helpers ------------------ */
function clean(?string $v): string {
  return trim(str_replace(["\r","\n"], ' ', strip_tags($v ?? '')));
}
function wants_json(): bool {
  $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
  $xhr    = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
  return (stripos($accept, 'application/json') !== false) || (strtolower($xhr) === 'xmlhttprequest');
}
function respond(int $status, string $msg, array $extra = []): void {
  if (wants_json()) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code($status);
    echo json_encode(['ok' => $status < 400, 'message' => $msg] + $extra);
  } else {
    $thankYou = $extra['thankYou'] ?? '';
    if ($status < 400 && $thankYou) {
      header('Location: ' . $thankYou);
    } else {
      http_response_code($status);
      header('Content-Type: text/plain; charset=UTF-8');
      echo $msg;
    }
  }
  exit;
}

/* ------------------ load .env ------------------ */
/* Adjust these paths if needed (symlinks, etc.) */
$paths = [
  __DIR__,
  __DIR__ . '/..',
];
$env = null;
foreach ($paths as $p) {
  $f = rtrim($p, '/'). '/.env';
  if (is_readable($f)) { $env = parse_ini_file($f, false, INI_SCANNER_RAW); break; }
}
if (!$env) {
  respond(500, 'Mailer config not found');
}

/* Trim & normalize */
$SMTP_HOST   = clean($env['SMTP_HOST']   ?? 'smtp.zoho.com');
$SMTP_PORT   = (int)clean($env['SMTP_PORT'] ?? '587');
$SMTP_SECURE = strtolower(clean($env['SMTP_SECURE'] ?? 'tls')); // tls|ssl
$SMTP_USER   = clean($env['SMTP_USER']   ?? '');
$SMTP_PASS   = clean($env['SMTP_PASS']   ?? '');
$SMTP_TO     = clean($env['SMTP_TO']     ?? 'info@noortutoring.com');
$FROM_NAME   = clean($env['FROM_NAME']   ?? 'Noor Tutoring');
$SUBJECT     = clean($env['MAIL_SUBJECT'] ?? 'New Contact Form Submission — Noor Tutoring');
$THANK_URL   = clean($env['THANK_YOU_URL'] ?? '/thank-you.html');

if ($SMTP_USER === '' || $SMTP_PASS === '') {
  respond(500, 'Mailer not configured (missing SMTP credentials)');
}

/* ------------------ request guards ------------------ */
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  respond(405, 'Method Not Allowed');
}
if (!empty($_POST['bot-field'] ?? '')) {
  respond(200, 'Thanks!', ['thankYou' => $THANK_URL]); // honeypot trip
}

/* ------------------ collect input ------------------ */
$name    = clean($_POST['name']    ?? '');
$email   = clean($_POST['email']   ?? '');
$message = trim($_POST['message']  ?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
  respond(400, 'Please provide a valid name, email, and message.');
}

/* ------------------ build message ------------------ */
$htmlBody = '<h2>New message from Noor Tutoring website</h2>'
  . '<p><strong>Name:</strong> ' . htmlentities($name) . '<br>'
  . '<strong>Email:</strong> ' . htmlentities($email) . '</p>'
  . '<p><strong>Message:</strong><br>' . nl2br(htmlentities($message)) . '</p>';

$textBody = "New message from Noor Tutoring website:\n\n"
  . "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}\n";

/* ------------------ send via PHPMailer ------------------ */
$mail = new PHPMailer(true);

try {
  $mail->isSMTP();
  $mail->Host       = $SMTP_HOST;
  $mail->SMTPAuth   = true;
  $mail->Username   = $SMTP_USER;            // Zoho mailbox (e.g., info@noortutoring.com)
  $mail->Password   = $SMTP_PASS;            // Zoho App Password
  if ($SMTP_SECURE === 'ssl') {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $SMTP_PORT ?: 465;
  } else {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $SMTP_PORT ?: 587;
  }

  // OPTIONAL: if your server lacks CA bundle and TLS fails, temporarily uncomment:
  // $mail->SMTPOptions = [
  //   'ssl' => [
  //     'verify_peer' => false,
  //     'verify_peer_name' => false,
  //     'allow_self_signed' => true,
  //   ],
  // ];

  // From must match Zoho mailbox for SPF/DKIM/DMARC alignment
  $mail->setFrom($SMTP_USER, $FROM_NAME);
  $mail->addAddress($SMTP_TO);
  $mail->addReplyTo($email, $name); // allows direct reply to visitor

  $mail->isHTML(true);
  $mail->CharSet  = 'UTF-8';
  $mail->Subject  = $SUBJECT;
  $mail->Body     = $htmlBody;
  $mail->AltBody  = $textBody;

  // Optional trace headers
  $mail->addCustomHeader('X-Form-Origin', 'noortutoring.com');
  $mail->addCustomHeader('X-Project', 'Noor Tutoring Contact');

  $mail->send();

  respond(200, 'Message sent', ['delivered' => true, 'thankYou' => $THANK_URL]);

} catch (Exception $e) {
  // Quiet response to user, no stack details leaked
  // For one-time troubleshooting, uncomment the logging lines below
  // @file_put_contents('/tmp/noor_mail_error.log',
  //   '['.date('c').'] PHPMailer: '.$e->getMessage().' | ErrorInfo: '.$mail->ErrorInfo.PHP_EOL,
  //   FILE_APPEND
  // );
  respond(500, 'Send failed');
}
