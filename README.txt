
Noor Tutoring — Bootstrap + PHP Mailer
--------------------------------------
- Uses Bootstrap 5 (CDN) and your uploaded logo (navy + gold palette).
- Contact form posts to contact.php which uses PHP mail().
- JS enhances UX by swapping the form for a success alert on success.

Deploy notes:
1) Upload all files to a PHP-capable host.
2) In contact.php, set $from to a real mailbox on your domain (e.g., no-reply@noortutoring.com).
3) Some hosts require SMTP instead of mail(). If emails don't arrive, consider PHPMailer via SMTP.

