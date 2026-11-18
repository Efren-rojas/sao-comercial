# SAO Comercial (PHP puro + MySQL + PWA)
1. Crea la BD en InfinityFree y ejecuta `schema.sql` en phpMyAdmin.
2. Usa `config.example.php` como referencia y crea `/htdocs/config.php` en el hosting con los datos de tu base de datos. No subas `config.php` a GitHub.
3. Secrets de GitHub: FTP_HOST, FTP_USER, FTP_PASS. Cada `git push` a main despliega.
4. Abre el sitio, "Crear mi negocio" y entra.
Facturación: Ajustes -> URL y token del proveedor; envía JSON {numero,fecha,total,items[]}. Adaptar al formato del proveedor.
