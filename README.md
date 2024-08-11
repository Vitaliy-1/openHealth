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
npm run dev
```
### Environment variables
Before starting the application, see example of the [environment variables configuration](https://github.com/Vitaliy-1/openHealth/blob/main/.env.example).
