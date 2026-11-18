# SAO Comercial (PHP puro + MySQL + PWA)
1. Crea la BD en InfinityFree y ejecuta `schema.sql` en phpMyAdmin.
2. Copia `config.example.php` como `config.local.php` y completa tus datos. En InfinityFree coloca ambos archivos (`config.php` y `config.local.php`) en `/htdocs`; el despliegue automático excluye ambos. No publiques credenciales reales.
3. Secrets de GitHub: FTP_HOST, FTP_USER, FTP_PASS. Cada `git push` a main despliega.
4. Abre el sitio, "Crear mi negocio" y entra.
Facturación: Ajustes -> URL y token del proveedor; envía JSON {numero,fecha,total,items[]}. Adaptar al formato del proveedor.
