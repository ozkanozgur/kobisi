## Kobisi Code Challange

Kurulum adımları

- Laravel kurulumu yapıldıktan sonra migration ve seeder kullanılarak veriler yüklenebilir yada sql dosyası migration sonrası sql dosyası import edilerek verilerin yüklenmesi sağlanabilir. Sql dosyası root klasöründe "database.sql" olarak bulunmaktadır. database.sql dosyasını çalıştırmadan önce 
```
php artisan migrate
```
komutu çalıştırılmalıdır. 
- API endpointler için Postman collection dosyası da "KobisiChallange.postman_collection.json" ismi ile root klasöründe bulunmaktadır.
- Ödemelerin kontrolü için cron job'a aşağıdaki tanımlama yapılmalıdır.
<br>Url:
```
http://127.0.0.1:8000/check-package-payment
```
Cronjob:
```
0 0 * * *
```
Cronjob ödeme denemelerini queue ya ekleyecektir. Queue nin çalışması için Supervisord ayar dosyası aşağıdaki gibi olmalıdır.

```
[program:laravel-worker]
command=/laravel/yolu/artisan queue:work --sleep=3 --tries=3
process_name=%(program_name)s_%(process_num)02d
numprocs=8
priority=999
autostart=true
autorestart=true
startsecs=0
startretries=3
user=apache
redirect_stderr=true
stdout_logfile=/var/www/vhosts/queue/storage/logs/worker.log
```

Saygılarımla
