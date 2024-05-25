
# Inventory Management
This product is a customizable solution for the local market, offering features such as sales tracking, stock monitoring, customer management, payment handling, due tracking, inventory management, and more.



## Requirements
Ensure you have installed the following:

```bash
1. PHP 7.4
2. MySQL 5.8 to 8.0
3. Redis
4. Composer
```
## Environment
To run this project, you will need to create an .env file and update the following environment variables to your .env file.

`YII_DEBUG`

`YII_ENV`

`DB_DSN`

`DB_USERNAME`

`DB_PASSWORD`

`REDIS_HOST`

`REDIS_PORT`

`REDIS_DATABASE`

`SMTP_HOST`

`SMTP_USER_NAME`

`SMTP_PASSWORD`

`SMTP_PORT`

`SMTP_ENCRYPTION`

`PARAMS_SUPPORT_EMAIL`

`PARAMS_ADMIN_EMAIL`

`PARAMS_SECRET_KEY`

## Installations
Go to the root directory and run:
```bash
composer install
```


## Permissions
Go to the root directory and run:
```bash
# Change to your project directory
cd project path

# Change ownership
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
