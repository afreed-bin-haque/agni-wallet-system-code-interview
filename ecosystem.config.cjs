module.exports = {
    apps: [
        {
            name: 'agni-wallet',
            script: 'artisan',
            args: 'serve --port=8082',
            interpreter: 'php',
            instances: 1,
            autorestart: true,
            watch: false,
            max_memory_restart: '1G',
            env: {
                APP_ENV: 'production',
                APP_DEBUG: 'false',
            },
        },
    ],
};
