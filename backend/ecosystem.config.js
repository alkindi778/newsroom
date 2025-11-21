module.exports = {
  apps: [
    {
      name: 'newsroom-queue',
      script: 'artisan',
      interpreter: 'php',
      args: 'queue:work --tries=3 --timeout=180 --sleep=3 --max-time=3600',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '512M',
      error_file: '/var/log/pm2/newsroom-queue-error.log',
      out_file: '/var/log/pm2/newsroom-queue-out.log',
      log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
      env: {
        APP_ENV: 'production',
        QUEUE_CONNECTION: 'database'
      }
    }
  ]
};
