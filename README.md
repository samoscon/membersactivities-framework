# membersactivities-framework
Framework managing the ticketing for members taking part to an activity

Steps to deploy the basic framework in Apache2 web server with Oracle (or MariaDB) database running in a Linux environment:

1. Create a root folder for your project with the name [Your Name of the root folder]
2. Execute in your project root folder "composer init". Add following dependencies to your composer.json:

    "require": {
        "setasign/fpdf": "^1.8",
        "blueimp/jquery-file-upload": "9.22.*",
        "tinymce/tinymce": "4.*",
        "chillerlan/php-qrcode": "*",
        "samoscon/membersactivities-framework": "1.*",
        "google/auth": "^1.18",
        "guzzlehttp/guzzle": "*",
        "google/apiclient": "^2.15",
        "google/apiclient-services": "~0.300"
    },
    "scripts": {
        "pre-autoload-dump": "Google\\Task\\Composer::cleanup"
    },
    "extra": {
        "google/apiclient-services": [
        "Walletobjects"
        ]
    }

3. Execute in your project root folder "composer install".
4. Copy the files and folders under ./vendor/samoscon/membersactivities-framework/example/ to your root folder
5. Update the ./config/app_options.ini file with your passwords and settings and 
        (if applicable) copy your Google Wallet Ticket key.json file in the config folder
6. Set-up a datebase with the DatabaseSetup.sql
7. Insert manually a first member with a [name], [email], role = "A", active = "1", subscriptionuntil = "2099-12-31" 
        (no password required, as you will set-up a password during your first login)
