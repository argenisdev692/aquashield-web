import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    server: {
        // Activamos HTTPS ya que tenemos certificado SSL
        https: true,
        host: "0.0.0.0",
        hmr: {
            host: "localhost",
        },
    },
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/crud-main.js",
            ],
            refresh: true,
            // Añadir configuración para producción
            publicDirectory: "public",
        }),
    ],
});
