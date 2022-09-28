import {defineConfig, loadEnv} from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import fs from 'fs'
import {resolve} from 'path'

export default ({ mode }) => {
    // Load the local .env and merge with process env
    process.env = Object.assign(process.env, loadEnv(mode, process.cwd(), ''));

    // Return the Vite config
    return defineConfig({
        server: mode === 'development' ? detectServerConfig(
            process.env.VITE_APP_URL || '',
            process.env.VITE_KEY_PATH || '',
            process.env.VITE_CERT_PATH || '',
        ) : {},
        plugins: [
            laravel({
                input: [
                    // 'resources/css/app.css', // import in JS to avoid FLOUC
                    'resources/js/app.js',
                ],
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                '@': path.resolve('resources/js'),
                '@medialibrary': path.resolve(__dirname + "/vendor/spatie/laravel-medialibrary-pro/resources/js")
            },
            modules: [
                "node_modules"
            ]
        }
    })
}

function detectServerConfig (host, key, cert) {
    let keyPath = resolve(key)
    let certificatePath = resolve(cert)

    if (!fs.existsSync(keyPath)) {
        return {}
    }

    if (!fs.existsSync(certificatePath)) {
        return {}
    }

    return {
        hmr: { host },
        host,
        https: {
            key: fs.readFileSync(keyPath),
            cert: fs.readFileSync(certificatePath),
        },
    }
}
