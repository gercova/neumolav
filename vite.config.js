import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Entry points por módulo (permite cargar Vue solo donde se usa)
                'resources/js/modules/home.js',
                'resources/js/modules/quickview.js',
                'resources/js/modules/dashboard.js',
                'resources/js/modules/histories.js',
                'resources/js/modules/history-form.js',
                'resources/js/modules/exams-see.js',
                'resources/js/modules/exam-form.js',
                'resources/js/modules/exams.js',
                'resources/js/modules/appointments.js',
                'resources/js/modules/appointments-see.js',
                'resources/js/modules/appointment-form.js',
                'resources/js/modules/reports.js',
                'resources/js/modules/reports-see.js',
                'resources/js/modules/report-form.js',
                'resources/js/modules/risks.js',
                'resources/js/modules/risks-see.js',
                'resources/js/modules/risk-form.js',
                'resources/js/modules/categories.js',
                'resources/js/modules/presentations.js',
                'resources/js/modules/drugs.js',
                'resources/js/modules/auth.js',
                'resources/js/modules/diagnostics.js',
                'resources/js/modules/occupations.js',
                'resources/js/modules/permissions.js',
                'resources/js/modules/roles.js',
                'resources/js/modules/users.js',
                'resources/js/modules/specialties.js',
                'resources/js/modules/enterprise.js',
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
            '@': '/resources/js',
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },
})