![JoliQuiz](https://raw.githubusercontent.com/LaurentBouquet/joliquiz/assets/JoliQuiz.png?raw=true)

[![Build Status](https://travis-ci.org/LaurentBouquet/joliquiz.svg?branch=master)](https://travis-ci.org/LaurentBouquet/joliquiz)
[![Build Status](https://semaphoreci.com/api/v1/laurentbouquet/joliquiz/branches/develop/badge.svg)](https://semaphoreci.com/laurentbouquet/joliquiz)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ffbbae0ffc224808a43894742a79df91)](https://www.codacy.com/app/LaurentBouquet/joliquiz?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=LaurentBouquet/joliquiz&amp;utm_campaign=Badge_Grade)
[![CodeFactor](https://www.codefactor.io/repository/github/laurentbouquet/joliquiz/badge)](https://www.codefactor.io/repository/github/laurentbouquet/joliquiz)

[![Deploy on Heroku](https://www.herokucdn.com/deploy/button.svg)](https://heroku.com/deploy)

## Description
Joliquiz is an online quiz software, a PHP web application developed using the Symfony framework (version 4).

Thanks to [Symfony](https://symfony.com/)


## Screenshot of a quiz in progress

![Workout page](https://raw.githubusercontent.com/LaurentBouquet/joliquiz/assets/quiz_symf3_question10.png?raw=true)


## Installation

### 1) Get all source files

```bash
git clone https://github.com/LaurentBouquet/joliquiz.git
cd joliquiz
composer install
```

### 2) Create database

In the commands below, replace **aSecurePassword** with a secure password.

Here are the steps to create the database, either with MySQL or with PostreSQL.


#### Either with MySQL

Enter this commands in a terminal prompt :
```sql
sudo mysql
CREATE USER 'joliquiz'@'localhost' IDENTIFIED BY 'aSecurePassword';
CREATE DATABASE joliquiz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON joliquiz.* TO 'joliquiz'@'localhost';
```

Update config/packages/doctrine.yaml :
```yaml
doctrine:
    dbal:
        # configure these for your MySQL database server
        driver: 'pdo_mysql'
        server_version: '5.7'
        charset: utf8mb4
        default_table_options:
            charset: utf8mb4
            collate: utf8mb4_unicode_ci

        # configure these for your PostgreSQL database server
        # driver: 'pdo_pgsql'
        # charset: utf8
```

Uncomment and update the password in this line of **.env** file :
DATABASE_URL=mysql://joliquiz:**aSecurePassword**@127.0.0.1:3306/joliquiz


Enter this commands in a terminal prompt :
```bash
# cd joliquiz
bin/console doctrine:migrations:latest
```
If an error occured "could not find driver", enter this command in a terminal prompt (and re-enter the command above) :
```bash
sudo apt install php-mysql
```


#### Or with PostgreSQL

Enter this commands in a terminal prompt :
```bash
sudo -i -u postgres
createuser --interactive
joliquiz
# -> yes
psql
ALTER USER joliquiz WITH password 'aSecurePassword';
ALTER USER joliquiz SET search_path = public;
\q
exit
```

Update config/packages/doctrine.yaml :
```yaml
doctrine:
    dbal:
        # configure these for your MySQL database server
        # driver: 'pdo_mysql'
        # server_version: '5.7'
        # charset: utf8mb4
        # default_table_options:
        #     charset: utf8mb4
        #     collate: utf8mb4_unicode_ci

        # configure these for your PostgreSQL database server
        driver: 'pdo_pgsql'
        charset: utf8
```

Uncomment and update the password in this line of **.env** file :
DATABASE_URL=pgsql://joliquiz:**aSecurePassword**@127.0.0.1:5432/joliquiz


Enter this commands in a terminal prompt :
```bash
# cd joliquiz
php bin/console doctrine:database:create
```
If an error occured "could not find driver", enter this command in a terminal prompt (and re-enter the command above) :
```bash
sudo apt install php-pgsql
```


### 3) Fill database and start built-in server

Enter this commands in a terminal prompt :
```bash
# cd joliquiz
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php bin/console server:start
```

### 4) With your web browser open url where server is listening on

For example, with your browser open this page :  http://127.0.0.1:8000 and GO !

![Workout page](https://raw.githubusercontent.com/LaurentBouquet/joliquiz/assets/quiz_symf3_question10.png?raw=true)

Here is initial credentials of the student user.
 - Username : user
 - Password : user

Here is initial credentials of the teacher user.
 - Username : teacher
 - Password : teacher

Here is initial credentials of the admin user.
 - Username : admin
 - Password : admin

Here is initial credentials of the super-admin user.
 - Username : superadmin
 - Password : superadmin



## Live Demo

[https://joliquiz.herokuapp.com/](https://joliquiz.herokuapp.com/)

Thanks to [Heroku](https://www.heroku.com/)




<!-- ## Contributing

Joliquiz is an open source project that welcomes pull requests and issues from anyone.
Before opening pull requests, please read our short Contribution Guide. -->
