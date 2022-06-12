<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About ChatApp

ChatApp is a API web application for realtime chatting. It has following features:

- Registration and login.
- Simple profile page.
- Creating chats with unlimited number of users.
- Chat window opens automatically for all chat participants when new message is sent in chat.
- Chat window display of all users which currentnly type in chat.
- Information of which user seen messages.
- Closing and minimizing chat windows.
- Information of unseen messages.
- History of conversations and filters for searching chat groups.
- Custom seeders to accelerate developing and testing processes.

With many others currently in development.

## Homestead Installation (not tested)

Follow [offical Laravel docs](https://laravel.com/docs/8.x/homestead) for Homestead and Vagrant installation process.

Edit Homestead.yaml file to map your project with local repo. 
*Note: do not use tabs for white space in .yaml files. Use " " white space to indent. 

Clone repo

- cd your_projects_dir
- git clone https://github.com/DimitrijeD/chatapp.git

Run Vagrant box

- cd ~/Homestead
- vagrant up

If you edit Homestead.yaml file after running 'vagrant up' you must restart box by:

- vagrant reload --provision

Open Vagrant VM:

- vagrant ssh 
- cd chatapp // or however you modified path inside .yaml to project

Copy .env.example into .env and edit it to match your config (most keys are set):

- cp .env.example .env
- php artisan key:generate

Then:

- php artisan config:cache
- php artisan migrate

Install NPM packages:

- npm install
- npm run dev

If you plan to edit frontend, run following command in separate console to compile your changes automatically:

- npm run watch-poll 

After setting up project on new machine, 

- npm install 
- npm run dev

didn't show anything on FE even tho it did compile successfully, but

- npm run watch-poll 

solved this issue.

In separate console, run following command for websockets. Mmake sure you are using correct host ip, or it might work without host parameter. Laravel Websockets require php 8+ .
- php artisan websockets:serve
- php artisan websockets:serve --host=192.168.56.56

To run all existing seeders:

- php artisan db:seed

To create users for one chat group with messages in it, run following seeder. Also you can run it as many times as you like, each time will create chat with user defined below and at least one more user. 

- php artisan db:seed --class=ChatGroupClusterSeeder

Login with user:

- http://chatapp.test/login
- email:    qwe@qwe
- password: qweqweqweQ1

For registration feature (otherwise not required) you must setup mail config in .env and run command in separate console for queueing emails:

- php artisan queue:work

