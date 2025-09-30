<?php
// contact.php — simple mailer for Noor Tutoring
// Adjust these as needed:
$to      = "info@noortutoring.com";
$from    = "no-reply@noortutoring.com"; // change to a real mailbox on your domain for best deliverability
$subject = "New Contact Form Submission — Noor Tutoring";

// Basic POST validation
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  echo "Method Not Allowed";
  exit;
}
function clean($v) { return trim(str_replace(array("\r","\n"), array(" "," "), strip_tags($v))); }

$name    = isset($_POST["name"])    ? clean($_POST["name"])    : "";
$email   = isset($_POST["email"])   ? clean($_POST["email"])   : "";
$message = isset($_POST["message"]) ? trim($_POST["message"])  : "";

if ($name === "" || $email === "" || $message === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo "Invalid input";
  exit;
}

$body  = "New message from Noor Tutoring website:\n\n";
$body .= "Name: {$name}\n";
$body .= "Email: {$email}\n\n";
$body .= "Message:\n{$message}\n";

$headers = [];
$headers[] = "From: Noor Tutoring <{$from}>";
$headers[] = "Reply-To: {$name} <{$email}>";
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";

$sent = @mail($to, $subject, $body, implode("\r\n", $headers));

if ($sent) {
  // If JS fetch is used, return JSON-friendly response
  header("Content-Type: application/json");
  echo json_encode(["ok" => true]);
} else {
  http_response_code(500);
  echo "Send failed";
}
?>
