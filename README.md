
# Noor Tutoring — Bootstrap + PHP Mailer + Jekyll
--------------------------------------
- Uses Bootstrap 5 (CDN) and your uploaded logo (navy + gold palette).
- Contact form posts to contact.php which uses PHP mail().
- JS enhances UX by swapping the form for a success alert on success.

## Install Ruby and Bundler
```
# macOS example (Homebrew)
brew install ruby
gem install bundler
# Windows/Linux: install Ruby first, then:
gem install bundler
```

## Run Locally

```
bundle install
bundle exec jekyll serve
# open http://127.0.0.1:4000
```


## Build for production

```
JEKYLL_ENV=production bundle exec jekyll build
# output will be in ./_site/
```



Deploy notes:
1) Upload all files to a PHP-capable host.
2) In contact.php, set $from to a real mailbox on your domain (e.g., no-reply@noortutoring.com).
3) Some hosts require SMTP instead of mail(). If emails don't arrive, consider PHPMailer via SMTP.

