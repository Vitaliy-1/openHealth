# Open Health
Health Information System (Медична інформаційна система)
## License & copyright
The software is distributed under [GPL version 3](https://www.gnu.org/licenses/gpl-3.0.en.html). Copyright &copy; 2023-2024 Iryna Loveiko, Vitalii Bezsheiko
## Requirements
* PHP 8.2+
* PostgreSQL 10.0+
  - Other SQL databases also supported, full list at [Laravel Documentation](https://laravel.com/docs/11.x/database)
* Access to the [eHealth API](https://uaehealthapi.docs.apiary.io/#reference/public.-medical-service-provider-integration-layer/oauth/login?console=1)
* [Composer](https://getcomposer.org), [npm](https://www.npmjs.com), [git](https://git-scm.com)
## Installation for development
### Git
```
git clone https://github.com/Vitaliy-1/openHealth mis-dev
cd mis-dev
git checkout dev
composer install
npm install
npm run build
cp .env.example .env
```
### Environment variables
Before starting the application, see example of the [environment variables configuration](https://github.com/Vitaliy-1/openHealth/blob/main/.env.example).
#### Application
```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
```
General application related variables are defined in this block. `APP_KEY` should be defined only once with `php artisan key:generate`, it's used in data encryption and changing it would lead to inability to decode encrypted data.
#### Database
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```
Database block. We recommend to use PostgreSQL `DB_CONNECTION=pgsql`. MySQL, SQLite, SQL Server, MariaDB are also supported.
#### eHEALTH
```
EHEALTH_API_URL=https://api-preprod.ehealth.gov.ua
EHEALTH_AUTH_HOST=https://auth-preprod.ehealth.gov.ua
EHEALTH_REDIRECT_URI=
EHEALTH_X_CUSTOM_PSK=
EHEALTH_API_KEY=
```
`EHEALTH_API_URL` - URL to the eHealth API environment, `https://api-preprod.ehealth.gov.ua` is URL to the preproduction instance.

`EHEALTH_AUTH_HOST` - URL to the eHealth authentication API

`EHEALTH_REDIRECT_URI` - URL to the application endpoint, which handles user authentication. It receives authentication code from eHealth, which later is exchanged to the token. Contact eHealth to provide the redirect URL of the application.

`EHEALTH_X_CUSTOM_PSK` and `EHEALTH_API_KEY` - are provided by the eHealth and are used in the authentication process. For more details see eHealth [API documentation](https://uaehealthapi.docs.apiary.io/#reference/public.-medical-service-provider-integration-layer/oauth/login).
#### Other
```
CIPHER_API_URL=
```
Application supports qualified digital signatures and uses [Cipher](https://caas.cipher.com.ua) as a service provider. See, Cipher [cryptographic API documentation](https://docs.cipher.com.ua/display/CCSUOS)
## Deployment
```
php artisan first-run
```
This command:
* generates unique application API key (see API_KEY global variable),
* creates tables
* populates tables with data, such as roles and permissions