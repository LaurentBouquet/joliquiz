![JoliQuiz](https://raw.githubusercontent.com/LaurentBouquet/joliquiz/assets/JoliQuiz.png?raw=true)

[![Build Status](https://travis-ci.com/LaurentBouquet/joliquiz.svg?branch=master)](https://travis-ci.com/LaurentBouquet/joliquiz)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ffbbae0ffc224808a43894742a79df91)](https://www.codacy.com/app/LaurentBouquet/joliquiz?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=LaurentBouquet/joliquiz&amp;utm_campaign=Badge_Grade)
[![CodeFactor](https://www.codefactor.io/repository/github/laurentbouquet/joliquiz/badge)](https://www.codefactor.io/repository/github/laurentbouquet/joliquiz)

## Description
Joliquiz is an online quiz software, a PHP web application developed using the [Symfony framework (version 4)](https://symfony.com/).


## Screenshots

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

Here are the steps to create the database, either with MySQL or with PostgreSQL.


#### MySQL

Check that you have the PHP MySQL driver installed with :
```bash
sudo apt install php-mysql
```

Enter these commands in a terminal prompt :
```sql
sudo mysql
CREATE USER 'joliquiz'@'localhost' IDENTIFIED BY 'aSecurePassword';
CREATE DATABASE joliquiz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON joliquiz.* TO 'joliquiz'@'localhost';
```

Update `config/packages/doctrine.yaml` :
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

Copy `.env` file to `.env.local`

Uncomment and update the password in this line of **.env.local** file :
`DATABASE_URL=mysql://joliquiz:**aSecurePassword**@127.0.0.1:3306/joliquiz`


Enter this commands in a terminal prompt :
```bash
bin/console doctrine:migrations:latest
```


#### PostgreSQL

First, start by checking that you have the PHP PostgreSQL driver installed with :
```bash
sudo apt install -y php-pgsql
```

Enter these commands in a terminal prompt :
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

Update the following config file located at `config/packages/doctrine.yaml` :
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

Uncomment and update the password in this line of the `.env` file :
`DATABASE_URL=pgsql://joliquiz:**aSecurePassword**@127.0.0.1:5432/joliquiz?charset=UTF-8`

Copy then the `.env` file to `.env.local`
```bash
cp .env .env.local
```

Then, enter the following commands :
```bash
php bin/console doctrine:database:create
```


### 3) Init database and start the built-in server

Migrate and start the server with the following commands :
```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php bin/console server:start
```

### 4) With your browser, open the URL where the server is listening

Open the following page : http://127.0.0.1:8000 and GO !

![Workout page](https://raw.githubusercontent.com/LaurentBouquet/joliquiz/assets/home_page.png?raw=true)

Here are the defaults credentials for the following roles :

User
 - Username : user
 - Password : user

Teacher
 - Username : teacher
 - Password : teacher

Administrator
 - Username : admin
 - Password : admin

Superadministrator
 - Username : superadmin
 - Password : superadmin



## Live Demo

[https://joliquiz.herokuapp.com/](https://joliquiz.herokuapp.com/)

Thanks to [Heroku](https://www.heroku.com/)

<!-- French version of JoliQuiz : [https://joliquiz.joliciel.fr/](https://joliquiz.joliciel.fr/) -->




## Set time zone

You must set your time zone in the php.ini file: 

```ini
date.timezone = Europe/Paris 
```



## Contributing

Joliquiz is an open source project that welcomes pull requests and issues from anyone.
Before opening pull requests, please read our short Contribution Guide.
