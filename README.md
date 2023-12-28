# Insighthub

## What is Insighthub?
InsightHub acts as an integrated solution to streamline log monitoring, making it easier for ServerAvatar users to access and analyze crucial information. By leveraging the ServerAvatar API, this addon ensures efficient communication, allowing users to effortlessly retrieve and display logs and summary data directly within the UI.

###  Key Features
-  **ServerAvatar API Integration:** InsightHub seamlessly connects with the ServerAvatar API, ensuring smooth communication between the two platforms.
-  **Log Fetching:** The addon fetches logs from ServerAvatar, providing users with real-time access to essential server data.
-  **Data Summaries:** InsightHub summarizes log data, presenting users with a clear and concise overview of server activities.
-  **User-Friendly UI:** The user interface is designed for ease of use, making log monitoring a straightforward and intuitive process.

#### Installation Steps

1. Install Project Dependency
```sh
composer install --no-dev --optimize-autoloader
```

2. Copy .env.example
```sh
cp .env.example .env
```

3. Generate Application Key
```sh
php artisan key:generate
```

4. Set Database Details
```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=insighthub
DB_USERNAME=
DB_PASSWORD=
```

5. Set Redis as a Queue Driver & Cache Driver
```
QUEUE_CONNECTION=redis
CACHE_DRIVER=redis
```

**Note:** If you set password to redis server then add redis password.

```
REDIS_PASSWORD="password here"
```

6. Run Migration
```sh
php artisan migrate
```

7. Set Cronjob
```
* * * * * php /var/www/serveravatar/v7/insighthub/artisan schedule:run >> /dev/null 2>&1
```
**Note:** Replace Project Directory according to your project.

8. Setup Horizon
Setup this configuration into supervisor.
```
[program:insighthub]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/serveravatar/v7/insighthub/artisan horizon
autostart=true
autorestart=true
user={system user}
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/serveravatar/v7/insighthub/storage/logs/horizon.log
stopwaitsecs=3600
```
**Note:** Replace Project Directory according to your project.

9. Restart Queue & Terminate Horizon
```
php artisan queue:restart
php artisan horizon:terminate
```