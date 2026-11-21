# SAO Comercial (PHP puro + MySQL + PWA)
1. Crea la BD en InfinityFree y ejecuta `schema.sql` en phpMyAdmin.
2. Copia `config.sample.php` a `config.php` en el servidor (/htdocs) con tus datos. No se sube a Git.
3. Secrets de GitHub: FTP_HOST, FTP_USER, FTP_PASS. Cada `git push` a main despliega.
4. Abre el sitio, "Crear mi negocio" y entra.
Facturación: Ajustes -> URL y token del proveedor; envía JSON {numero,fecha,total,items[]}. Adaptar al formato del proveedor.
