module.exports = {
  apps: [{
    name: 'newsroom-scheduler',
    script: 'artisan',
    args: 'schedule:work',
    interpreter: 'php',
    cwd: '/var/www/newsroom/backend',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '500M',
    env: {
      NODE_ENV: 'production'
    },
    error_file: '/var/log/pm2/newsroom-scheduler-error.log',
    out_file: '/var/log/pm2/newsroom-scheduler-out.log',
    log_date_format: 'YYYY-MM-DD HH:mm:ss Z'
  }]
};
