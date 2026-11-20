#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ -n "$DB_HOST" ]; then
  php -r '
    $env = file_get_contents(".env");
    $map = [
      "APP_NAME" => "\"Sepa Indonesia\"",
      "APP_URL" => getenv("APP_URL") ?: "http://localhost:8080",
      "APP_TIMEZONE" => "Asia/Jakarta",
      "APP_LOCALE" => "id",
      "DB_CONNECTION" => getenv("DB_CONNECTION") ?: "mysql",
      "DB_HOST" => getenv("DB_HOST") ?: "mysql",
      "DB_PORT" => getenv("DB_PORT") ?: "3306",
      "DB_DATABASE" => getenv("DB_DATABASE") ?: "sepa_absensi",
      "DB_USERNAME" => getenv("DB_USERNAME") ?: "sepa",
      "DB_PASSWORD" => getenv("DB_PASSWORD") ?: "sepa_secret",
      "SESSION_DRIVER" => getenv("SESSION_DRIVER") ?: "database",
      "SESSION_LIFETIME" => "30",
      "CACHE_STORE" => getenv("CACHE_STORE") ?: "database",
      "QUEUE_CONNECTION" => getenv("QUEUE_CONNECTION") ?: "database",
      "AUTH_MODEL" => "App\\Models\\Karyawan",
    ];
    foreach ($map as $key => $value) {
      if (preg_match("/^{$key}=.*/m", $env)) {
        $env = preg_replace("/^{$key}=.*/m", $key."=".$value, $env);
      } else {
        $env .= PHP_EOL.$key."=".$value;
      }
    }
    file_put_contents(".env", $env);
  '
fi

if [ ! -d vendor ]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if ! grep -q '^APP_KEY=base64:' .env; then
  php artisan key:generate --force --no-interaction
fi

php -r '
  $host = getenv("DB_HOST") ?: "mysql";
  $port = (int) (getenv("DB_PORT") ?: 3306);
  $user = getenv("DB_USERNAME") ?: "sepa";
  $pass = getenv("DB_PASSWORD") ?: "sepa_secret";
  $db = getenv("DB_DATABASE") ?: "sepa_absensi";
  for ($i = 0; $i < 30; $i++) {
    try {
      new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass);
      echo "DB ready\n";
      exit(0);
    } catch (Throwable $e) {
      sleep(2);
    }
  }
  fwrite(STDERR, "DB not ready\n");
  exit(1);
'

php artisan migrate --force --no-interaction
php artisan db:seed --force --no-interaction

chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

exec "$@"
