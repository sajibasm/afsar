
# Inventory Management
This product is a customizable solution for the local market, offering features such as sales tracking, stock monitoring, customer management, payment handling, due tracking, inventory management, and more.



## Requirements
Ensure you have installed the following:

`PHP >= 7.4`

`MySQL >= 5.8`

`Redis`

`Composer >= 2.0`

## Environment
To run this project, you will need to create an .env file and update the following environment variables to your .env file.

```bash
YII_DEBUG=true
YII_ENV=dev

DB_DSN=mysql:host=127.0.0.1;dbname=database
DB_USERNAME=user
DB_PASSWORD=password

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DATABASE=0

SMTP_HOST=smtp.gmail.com
SMTP_USER_NAME=sample@gmail.com
SMTP_PASSWORD=pasword
SMTP_PORT=587
SMTP_ENCRYPTION=tls

PARAMS_SUPPORT_EMAIL=sample@email.com
PARAMS_ADMIN_EMAIL=sample@email.com
PARAMS_SECRET_KEY=GenerateAHashKey
```


## Installations
Go to the root directory and run:
```bash
composer install
```


## Permissions
Go to the root directory and run:
```bash
# Change to your project directory
# Change ownership

sudo chown www-data:www-data .env
sudo chmod 644 .env

sudo chown -R www-data:www-data runtime/
sudo chown -R www-data:www-data web/assets/

# Set directory permissions
sudo chmod -R 775 runtime/
sudo chmod -R 775 web/assets/

# Set file permissions
sudo find runtime/ -type f -exec chmod 664 {} \;
sudo find web/assets/ -type f -exec chmod 664 {} \;
```



## Support

For support, email sajib.cse03@gmail.com
