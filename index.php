<?php
session_start();
require __DIR__ . '/config.php';
$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
function q($s, $a = [])
{
    global $db;
    $t = $db->prepare($s);
    $t->execute($a);
    return $t;
}
function e($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES);
}
function u()
{
    return $_SESSION['u'] ?? null;
}
function nid()
{
    return u()['nid'];
}
function m($n)
{
    return '$' . number_format($n, 0, ',', '.');
}
function go($p, $s = '')
{
    if ($s) $_SESSION['m'] = $s;
    header("Location: index.php?p=$p");
    exit;
}
function csrf()
{
    return $_SESSION['c'] ?? ($_SESSION['c'] = bin2hex(random_bytes(16)));
}
function f()
{
    return '<input type=hidden name=c value="' . csrf() . '">';
}
function chk()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals(csrf(), $_POST['c'] ?? $_SERVER['HTTP_X_CSRF'] ?? '')) die('Sesión inválida, recarga la página');
}
function post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
function top($t)
{
    global $p;
    $s = $_SESSION['m'] ?? '';
    unset($_SESSION['m']); ?>
    <!doctype html>
    <html lang=es>

    <head>
        <meta charset=utf-8>
        <meta name=viewport content="width=device-width,initial-scale=1,viewport-fit=cover">
        <title><?= e($t) ?> · SAO</title>
        <link rel=manifest href=manifest.json>
        <link rel=apple-touch-icon href=assets/i192.png>
        <meta name=theme-color content="#0071e3">
        <link rel=stylesheet href=assets/app.css>
    </head>

    <body>
        <?php if (u()) {
            $n = [['vender', '🛒', 'Vender'], ['productos', '📦', 'Productos'], ['clientes', '👥', 'Clientes'], ['caja', '💵', 'Caja'], ['mas', '⋯', 'Más']];
            echo '<nav>';
            foreach ($n as $i) {
                if ($i[0] == 'productos' && u()['rol'] != 'dueno') continue;
                $on = $p == $i[0] || ($i[0] == 'mas' && in_array($p, ['compras', 'reportes', 'ajustes'])) ? ' class=on' : '';
                echo "<a href='?p=$i[0]'$on><b>$i[1]</b>$i[2]</a>";
            }
            echo '</nav>';
        } ?><main><?php if ($s) echo '<div class=toast>' . e($s) . '</div>';
                }
                function bot()
                { ?></main>
        <script>
            window.CSRF = "<?= csrf() ?>"
            <?php if (isset($GLOBALS['prods'])) echo ';window.PRODUCTS=' . json_encode($GLOBALS['prods'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_NUMERIC_CHECK) ?>
        </script>
        <script src=assets/app.js></script>
    </body>

    </html><?php exit;
                }
                function facturar($vid, $tot, $it)
                {
                    $n = q("SELECT * FROM negocios WHERE id=?", [nid()])->fetch();
                    if (!$n['fe_activa'] || !$n['fe_url']) return null;
                    $b = ['numero' => $vid, 'fecha' => date('c'), 'total' => $tot, 'items' => array_map(fn($x) => ['nombre' => $x[0]['nombre'], 'cantidad' => $x[1], 'precio' => $x[0]['precio']], $it)];
                    $h = curl_init($n['fe_url']);
                    curl_setopt_array($h, [CURLOPT_POST => 1, CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => 8, CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $n['fe_token']], CURLOPT_POSTFIELDS => json_encode($b)]);
                    $r = curl_exec($h);
                    $ok = $r !== false && curl_getinfo($h, CURLINFO_HTTP_CODE) < 300;
                    q("UPDATE ventas SET fe_estado=?,fe_resp=? WHERE id=?", [$ok ? 'enviada' : 'error', substr((string)$r, 0, 2000), $vid]);
                    return $ok ? 'enviada' : 'error';
                }
                $p = $_GET['p'] ?? 'vender';
                if (!u() && !in_array($p, ['login', 'registro'])) go('login');
                if (u() && u()['rol'] != 'dueno' && in_array($p, ['productos', 'compras', 'reportes', 'ajustes', 'respaldo', 'csv'])) go('vender', 'Solo el dueño puede entrar ahí');
                chk();
                switch ($p) {
                    case 'login':
                        if (post()) {
                            $r = q("SELECT * FROM usuarios WHERE email=?", [$_POST['email']])->fetch();
                            if ($r && password_verify($_POST['clave'], $r['clave'])) {
                                $_SESSION['u'] = ['id' => $r['id'], 'nid' => $r['negocio_id'], 'nombre' => $r['nombre'], 'rol' => $r['rol']];
                                go('vender');
                            }
                            go('login', 'Correo o clave incorrectos');
                        }
                        top('Entrar'); ?><form method=post class=card style="max-width:420px;margin:40px auto">
            <h1>Entrar</h1><?= f() ?><input name=email type=email placeholder=Correo required><input name=clave type=password placeholder=Clave required><button class="btn big" style="background:var(--ac)">Entrar</button>
            <p><a href="?p=registro">Crear mi negocio</a></p>
        </form><?php bot();
                    case 'registro':
                        if (post()) {
                            if (strlen($_POST['clave']) < 6) go('registro', 'La clave debe tener 6 o más caracteres');
                            if (q("SELECT id FROM usuarios WHERE email=?", [$_POST['email']])->fetch()) go('registro', 'Ese correo ya existe');
                            $pl = ['tienda' => [['Gaseosa 400ml', 2500, 24], ['Pan', 500, 40], ['Arroz 500g', 2800, 20]], 'papeleria' => [['Cuaderno', 4500, 30], ['Lapicero', 1200, 60], ['Resma carta', 16000, 10]], 'ferreteria' => [['Martillo', 25000, 8], ['Clavos 1lb', 5000, 15], ['Cinta métrica', 12000, 10]], 'restaurante' => [['Almuerzo', 14000, 50], ['Jugo', 4000, 40], ['Postre', 5000, 20]]];
                            $db->beginTransaction();
                            q("INSERT INTO negocios(nombre,tipo)VALUES(?,?)", [$_POST['negocio'], $_POST['tipo']]);
                            $n = $db->lastInsertId();
                            q("INSERT INTO usuarios(negocio_id,nombre,email,clave,rol)VALUES(?,?,?,?,'dueno')", [$n, $_POST['nombre'], $_POST['email'], password_hash($_POST['clave'], PASSWORD_DEFAULT)]);
                            foreach ($pl[$_POST['tipo']] ?? $pl['tienda'] as $x) q("INSERT INTO productos(negocio_id,nombre,precio,stock)VALUES(?,?,?,?)", [$n, $x[0], $x[1], $x[2]]);
                            $db->commit();
                            go('login', 'Listo. Ya puedes entrar');
                        }
                        top('Crear negocio'); ?><form method=post class=card style="max-width:420px;margin:40px auto">
            <h1>Crear mi negocio</h1><?= f() ?><input name=negocio placeholder="Nombre del negocio" required><select name=tipo>
                <option value=tienda>Tienda
                <option value=papeleria>Papelería
                <option value=ferreteria>Ferretería
                <option value=restaurante>Restaurante
            </select><input name=nombre placeholder="Tu nombre" required><input name=email type=email placeholder=Correo required><input name=clave type=password placeholder="Clave (mínimo 6)" required><button class="btn big" style="background:var(--ac)">Crear</button>
            <p><a href="?p=login">Ya tengo cuenta</a></p>
        </form><?php bot();
                    case 'logout':
                        session_destroy();
                        header('Location: index.php?p=login');
                        exit;
                    case 'vender':
                        $GLOBALS['prods'] = q("SELECT id,nombre,codigo,precio,stock,minimo FROM productos WHERE negocio_id=? AND activo=1 ORDER BY nombre", [nid()])->fetchAll();
                        $cl = q("SELECT id,nombre FROM clientes WHERE negocio_id=? ORDER BY nombre", [nid()])->fetchAll();
                        top('Vender'); ?><div class=pos>
            <section>
                <div style="display:flex;gap:8px"><input id=bq placeholder="Buscar o escribir código" autofocus><button id=cam style="margin:6px 0">📷</button></div>
                <div id=grid class=grid></div>
            </section>
            <aside>
                <h3>Venta</h3>
                <div id=cart></div><select id=cli>
                    <option value="">Cliente (opcional)</option><?php foreach ($cl as $c) echo "<option value=$c[id]>" . e($c['nombre']); ?>
                </select><select id=met>
                    <option value=efectivo>Efectivo
                    <option value=tarjeta>Tarjeta
                    <option value=transferencia>Transferencia
                    <option value=fiado>Fiado
                </select><input id=dsc type=number min=0 placeholder="Descuento $">
                <div id=tot class=tot>$0</div><button id=pay class="btn big">Cobrar</button>
                <p id=msg></p>
                <p id=pend class=mut></p>
            </aside>
        </div><?php bot();
                    case 'venta':
                        header('Content-Type: application/json');
                        $d = json_decode(file_get_contents('php://input'), true);
                        try {
                            $db->beginTransaction();
                            $tot = 0;
                            $it = [];
                            foreach ($d['items'] as $i) {
                                $c = (int)$i['c'];
                                $r = q("SELECT * FROM productos WHERE id=? AND negocio_id=? AND activo=1 FOR UPDATE", [$i['id'], nid()])->fetch();
                                if (!$r || $c < 1) throw new Exception('Producto inválido');
                                if ($c > $r['stock']) throw new Exception("Sin stock suficiente de $r[nombre] (hay $r[stock])");
                                $tot += $c * $r['precio'];
                                $it[] = [$r, $c];
                            }
                            if (!$it) throw new Exception('Carrito vacío');
                            $ds = max(0, min((int)($d['desc'] ?? 0), $tot));
                            $tot -= $ds;
                            $mt = in_array($d['metodo'], ['efectivo', 'tarjeta', 'transferencia', 'fiado']) ? $d['metodo'] : 'efectivo';
                            $cl = (int)($d['cliente'] ?? 0) ?: null;
                            if ($cl && !q("SELECT id FROM clientes WHERE id=? AND negocio_id=?", [$cl, nid()])->fetch()) throw new Exception('Cliente inválido');
                            if ($mt == 'fiado' && !$cl) throw new Exception('Elige un cliente para fiar');
                            q("INSERT INTO ventas(negocio_id,usuario_id,cliente_id,total,descuento,metodo)VALUES(?,?,?,?,?,?)", [nid(), u()['id'], $cl, $tot, $ds, $mt]);
                            $vid = $db->lastInsertId();
                            foreach ($it as [$r, $c]) {
                                q("INSERT INTO venta_items(venta_id,producto_id,nombre,cantidad,precio)VALUES(?,?,?,?,?)", [$vid, $r['id'], $r['nombre'], $c, $r['precio']]);
                                q("UPDATE productos SET stock=stock-? WHERE id=?", [$c, $r['id']]);
                            }
                            if ($mt == 'fiado') q("UPDATE clientes SET deuda=deuda+? WHERE id=?", [$tot, $cl]);
                            $db->commit();
                        } catch (Exception $x) {
                            if ($db->inTransaction()) $db->rollBack();
                            echo json_encode(['err' => $x->getMessage()]);
                            exit;
                        }
                        echo json_encode(['ok' => 1, 'id' => $vid, 'total' => $tot, 'fe' => facturar($vid, $tot, $it)]);
                        exit;
                    case 'productos':
                        if (post()) {
                            if (isset($_POST['del'])) {
                                q("UPDATE productos SET activo=0 WHERE id=? AND negocio_id=?", [$_POST['del'], nid()]);
                                go('productos', 'Producto quitado');
                            }
                            $a = [$_POST['nombre'], $_POST['codigo'], max(0, (int)$_POST['precio']), max(0, (int)$_POST['costo']), max(0, (int)$_POST['stock']), max(0, (int)$_POST['minimo'])];
                            if (!empty($_POST['id'])) q("UPDATE productos SET nombre=?,codigo=?,precio=?,costo=?,stock=?,minimo=? WHERE id=? AND negocio_id=?", [...$a, $_POST['id'], nid()]);
                            else q("INSERT INTO productos(nombre,codigo,precio,costo,stock,minimo,negocio_id)VALUES(?,?,?,?,?,?,?)", [...$a, nid()]);
                            go('productos', 'Guardado');
                        }
                        $ed = isset($_GET['e']) ? q("SELECT * FROM productos WHERE id=? AND negocio_id=?", [$_GET['e'], nid()])->fetch() : [];
                        $ls = q("SELECT * FROM productos WHERE negocio_id=? AND activo=1 ORDER BY stock<=minimo DESC,nombre", [nid()])->fetchAll();
                        top('Productos'); ?><h1>Productos</h1>
        <form method=post class=card><?= f() ?><input type=hidden name=id value="<?= e($ed['id'] ?? '') ?>"><input name=nombre placeholder=Nombre required value="<?= e($ed['nombre'] ?? '') ?>"><input name=codigo placeholder="Código de barras (opcional)" value="<?= e($ed['codigo'] ?? '') ?>"><input name=precio type=number placeholder="Precio de venta" required value="<?= e($ed['precio'] ?? '') ?>"><input name=costo type=number placeholder="Costo" value="<?= e($ed['costo'] ?? '') ?>"><input name=stock type=number placeholder="Cantidad disponible" required value="<?= e($ed['stock'] ?? '') ?>"><input name=minimo type=number placeholder="Avisar cuando queden (ej. 3)" value="<?= e($ed['minimo'] ?? 3) ?>"><button class=btn>Guardar</button></form>
        <table>
            <tr>
                <th>Producto
                <th>Precio
                <th>Hay
                <th><?php foreach ($ls as $r) {
                            $lo = $r['stock'] <= $r['minimo'] ? ' class=low' : '';
                            echo "<tr><td>" . e($r['nombre']) . "<td>" . m($r['precio']) . "<td$lo>$r[stock]<td><a href='?p=productos&e=$r[id]'>Editar</a> <form method=post class=in>" . f() . "<input type=hidden name=del value=$r[id]><button class='btn no' style='min-height:32px;padding:0 10px'>Quitar</button></form>";
                        } ?>
        </table><?php bot();
                    case 'clientes':
                        if (post()) {
                            if (isset($_POST['monto'])) {
                                $mo = max(0, min((int)$_POST['monto'], (int)q("SELECT deuda FROM clientes WHERE id=? AND negocio_id=?", [$_POST['cid'], nid()])->fetchColumn()));
                                q("UPDATE clientes SET deuda=deuda-? WHERE id=? AND negocio_id=?", [$mo, $_POST['cid'], nid()]);
                                q("INSERT INTO pagos(negocio_id,cliente_id,monto)VALUES(?,?,?)", [nid(), $_POST['cid'], $mo]);
                                go('clientes', 'Abono registrado');
                            }
                            q("INSERT INTO clientes(negocio_id,nombre,telefono)VALUES(?,?,?)", [nid(), $_POST['nombre'], $_POST['telefono']]);
                            go('clientes', 'Cliente agregado');
                        }
                        $ls = q("SELECT * FROM clientes WHERE negocio_id=? ORDER BY deuda DESC,nombre", [nid()])->fetchAll();
                        top('Clientes'); ?><h1>Clientes y fiado</h1>
        <form method=post class=card><?= f() ?><input name=nombre placeholder=Nombre required><input name=telefono placeholder=Teléfono><button class=btn>Agregar</button></form>
        <table>
            <tr>
                <th>Cliente
                <th>Debe
                <th>Abonar<?php foreach ($ls as $r) echo "<tr><td>" . e($r['nombre']) . "<br><small class=mut>" . e($r['telefono']) . "</small><td" . ($r['deuda'] ? ' class=low' : '') . ">" . m($r['deuda']) . "<td>" . ($r['deuda'] ? "<form method=post class=in>" . f() . "<input type=hidden name=cid value=$r[id]><input name=monto type=number min=1 max=$r[deuda] placeholder=Monto style='width:110px'> <button>OK</button></form>" : ''); ?>
        </table><?php bot();
                    case 'caja':
                        $now = q("SELECT NOW() n")->fetch()['n'];
                        $ul = q("SELECT MAX(hasta) FROM cierres WHERE negocio_id=?", [nid()])->fetchColumn() ?: '2000-01-01';
                        $vs = q("SELECT metodo,SUM(total) t,COUNT(*) n FROM ventas WHERE negocio_id=? AND creado>? AND creado<=? GROUP BY metodo", [nid(), $ul, $now])->fetchAll();
                        $ab = (int)q("SELECT SUM(monto) FROM pagos WHERE negocio_id=? AND creado>? AND creado<=?", [nid(), $ul, $now])->fetchColumn();
                        $ef = $ab;
                        foreach ($vs as $v) if ($v['metodo'] == 'efectivo') $ef += $v['t'];
                        if (post()) {
                            q("INSERT INTO cierres(negocio_id,usuario_id,desde,hasta,esperado,contado)VALUES(?,?,?,?,?,?)", [nid(), u()['id'], $ul == '2000-01-01' ? $now : $ul, $now, $ef, (int)$_POST['contado']]);
                            go('caja', 'Caja cerrada');
                        }
                        $hs = q("SELECT * FROM cierres WHERE negocio_id=? ORDER BY id DESC LIMIT 7", [nid()])->fetchAll();
                        top('Caja'); ?><h1>Caja</h1>
        <div class=card>
            <h3>Desde el último cierre</h3><?php foreach ($vs as $v) echo "<p>" . ucfirst($v['metodo']) . ": <b>" . m($v['t']) . "</b> <span class=mut>($v[n] ventas)</span></p>"; ?><p>Abonos de fiado: <b><?= m($ab) ?></b></p>
            <p class=tot>Efectivo esperado: <?= m($ef) ?></p>
            <form method=post><?= f() ?><input name=contado type=number placeholder="¿Cuánto efectivo hay?" required><button class="btn big">Cerrar caja</button></form>
        </div>
        <table>
            <tr>
                <th>Cierre
                <th>Esperado
                <th>Contado
                <th>Diferencia<?php foreach ($hs as $h) echo "<tr><td>$h[hasta]<td>" . m($h['esperado']) . "<td>" . m($h['contado']) . "<td" . ($h['contado'] <> $h['esperado'] ? ' class=low' : '') . ">" . m($h['contado'] - $h['esperado']); ?>
        </table><?php bot();
                    case 'mas':
                        top('Más'); ?><h1>Más</h1>
        <div class="card mas"><?php if (u()['rol'] == 'dueno') echo '<a href="?p=compras">📥 Compras a proveedores</a><hr><a href="?p=reportes">📊 Reportes</a><hr><a href="?p=ajustes">⚙️ Ajustes y facturación</a><hr>'; ?><a href="?p=logout">Salir (<?= e(u()['nombre']) ?>)</a></div><?php bot();
                                                                                                                                                                                                                                                                                        case 'compras':
                                                                                                                                                                                                                                                                                            $ps = q("SELECT id,nombre FROM productos WHERE negocio_id=? AND activo=1 ORDER BY nombre", [nid()])->fetchAll();
                                                                                                                                                                                                                                                                                            if (post()) {
                                                                                                                                                                                                                                                                                                $c = max(1, (int)$_POST['cantidad']);
                                                                                                                                                                                                                                                                                                if (!q("SELECT id FROM productos WHERE id=? AND negocio_id=?", [$_POST['pid'], nid()])->fetch()) go('compras', 'Producto inválido');
                                                                                                                                                                                                                                                                                                q("INSERT INTO compras(negocio_id,proveedor,producto_id,cantidad,costo)VALUES(?,?,?,?,?)", [nid(), $_POST['proveedor'], $_POST['pid'], $c, (int)$_POST['costo']]);
                                                                                                                                                                                                                                                                                                q("UPDATE productos SET stock=stock+?,costo=? WHERE id=?", [$c, (int)$_POST['costo'], $_POST['pid']]);
                                                                                                                                                                                                                                                                                                go('compras', 'Compra registrada, stock actualizado');
                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                            $hs = q("SELECT c.*,p.nombre FROM compras c JOIN productos p ON p.id=c.producto_id WHERE c.negocio_id=? ORDER BY c.id DESC LIMIT 15", [nid()])->fetchAll();
                                                                                                                                                                                                                                                                                            top('Compras'); ?><h1>Compras</h1>
        <form method=post class=card><?= f() ?><select name=pid><?php foreach ($ps as $r) echo "<option value=$r[id]>" . e($r['nombre']); ?></select><input name=proveedor placeholder=Proveedor><input name=cantidad type=number min=1 placeholder="Cantidad que llegó" required><input name=costo type=number placeholder="Costo por unidad" required><button class=btn>Registrar compra</button></form>
        <table>
            <tr>
                <th>Fecha
                <th>Producto
                <th>Cant.
                <th>Costo<?php foreach ($hs as $h) echo "<tr><td>" . substr($h['creado'], 0, 10) . "<td>" . e($h['nombre']) . "<td>$h[cantidad]<td>" . m($h['costo']); ?>
        </table><?php bot();
                                                                                                                                                                                                                                                                                        case 'reportes':
                                                                                                                                                                                                                                                                                            $n = nid();
                                                                                                                                                                                                                                                                                            $hoy = q("SELECT COALESCE(SUM(total),0) FROM ventas WHERE negocio_id=? AND DATE(creado)=CURDATE()", [$n])->fetchColumn();
                                                                                                                                                                                                                                                                                            $mes = q("SELECT COALESCE(SUM(total),0) FROM ventas WHERE negocio_id=? AND creado>=DATE_FORMAT(NOW(),'%Y-%m-01')", [$n])->fetchColumn();
                                                                                                                                                                                                                                                                                            $dias = q("SELECT DATE(creado) d,SUM(total) t,COUNT(*) n FROM ventas WHERE negocio_id=? AND creado>=CURDATE()-INTERVAL 13 DAY GROUP BY d ORDER BY d DESC", [$n])->fetchAll();
                                                                                                                                                                                                                                                                                            $mx = max(1, ...array_column($dias, 't') ?: [1]);
                                                                                                                                                                                                                                                                                            $top = q("SELECT i.nombre,SUM(i.cantidad) c FROM venta_items i JOIN ventas v ON v.id=i.venta_id WHERE v.negocio_id=? GROUP BY i.nombre ORDER BY c DESC LIMIT 5", [$n])->fetchAll();
                                                                                                                                                                                                                                                                                            $bajo = q("SELECT nombre,stock FROM productos WHERE negocio_id=? AND activo=1 AND stock<=minimo", [$n])->fetchAll();
                                                                                                                                                                                                                                                                                            top('Reportes'); ?><h1>Reportes</h1>
        <div class=card>
            <p>Hoy: <b><?= m($hoy) ?></b></p>
            <p>Este mes: <b><?= m($mes) ?></b></p><a class="btn sec noprint" href="?p=csv">⬇ Excel (CSV)</a> <button class="sec noprint" style="background:#e8e8ed;color:#1d1d1f" onclick="print()">🖨 PDF / Imprimir</button> <a class="btn sec noprint" href="?p=respaldo">💾 Respaldo</a>
        </div>
        <table>
            <tr>
                <th>Día
                <th>Ventas
                <th>Total
                <th><?php foreach ($dias as $d) echo "<tr><td>$d[d]<td>$d[n]<td>" . m($d['t']) . "<td style='width:30%'><div class=bar style='width:" . round($d['t'] / $mx * 100) . "%'></div>"; ?>
        </table>
        <table>
            <tr>
                <th>Más vendidos
                <th><?php foreach ($top as $t) echo "<tr><td>" . e($t['nombre']) . "<td>$t[c]"; ?>
        </table>
        <table>
            <tr>
                <th>Stock bajo
                <th><?php foreach ($bajo as $t) echo "<tr><td>" . e($t['nombre']) . "<td class=low>$t[stock]"; ?>
        </table><?php bot();
                                                                                                                                                                                                                                                                                        case 'csv':
                                                                                                                                                                                                                                                                                            header('Content-Type: text/csv; charset=utf-8');
                                                                                                                                                                                                                                                                                            header('Content-Disposition: attachment; filename=ventas.csv');
                                                                                                                                                                                                                                                                                            echo "\xEF\xBB\xBF";
                                                                                                                                                                                                                                                                                            $o = fopen('php://output', 'w');
                                                                                                                                                                                                                                                                                            fputcsv($o, ['id', 'fecha', 'total', 'descuento', 'metodo', 'factura_electronica']);
                                                                                                                                                                                                                                                                                            foreach (q("SELECT id,creado,total,descuento,metodo,fe_estado FROM ventas WHERE negocio_id=? ORDER BY id DESC", [nid()]) as $r) fputcsv($o, $r);
                                                                                                                                                                                                                                                                                            exit;
                                                                                                                                                                                                                                                                                        case 'respaldo':
                                                                                                                                                                                                                                                                                            header('Content-Type: application/json');
                                                                                                                                                                                                                                                                                            header('Content-Disposition: attachment; filename=respaldo-' . date('Ymd') . '.json');
                                                                                                                                                                                                                                                                                            $o = [];
                                                                                                                                                                                                                                                                                            foreach (['productos', 'clientes', 'ventas', 'compras', 'pagos', 'cierres'] as $t) $o[$t] = q("SELECT * FROM $t WHERE negocio_id=?", [nid()])->fetchAll();
                                                                                                                                                                                                                                                                                            $o['venta_items'] = q("SELECT i.* FROM venta_items i JOIN ventas v ON v.id=i.venta_id WHERE v.negocio_id=?", [nid()])->fetchAll();
                                                                                                                                                                                                                                                                                            echo json_encode($o, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                                                                                                                                                                                                                                                                                            exit;
                                                                                                                                                                                                                                                                                        case 'ajustes':
                                                                                                                                                                                                                                                                                            if (post()) {
                                                                                                                                                                                                                                                                                                if (isset($_POST['vnombre'])) {
                                                                                                                                                                                                                                                                                                    if (strlen($_POST['vclave']) < 6 || q("SELECT id FROM usuarios WHERE email=?", [$_POST['vemail']])->fetch()) go('ajustes', 'Correo repetido o clave muy corta');
                                                                                                                                                                                                                                                                                                    q("INSERT INTO usuarios(negocio_id,nombre,email,clave,rol)VALUES(?,?,?,?,'vendedor')", [nid(), $_POST['vnombre'], $_POST['vemail'], password_hash($_POST['vclave'], PASSWORD_DEFAULT)]);
                                                                                                                                                                                                                                                                                                    go('ajustes', 'Vendedor creado');
                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                q("UPDATE negocios SET nombre=?,fe_activa=?,fe_url=?,fe_token=? WHERE id=?", [$_POST['nombre'], isset($_POST['fe']) ? 1 : 0, $_POST['url'], $_POST['token'], nid()]);
                                                                                                                                                                                                                                                                                                go('ajustes', 'Guardado');
                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                            $n = q("SELECT * FROM negocios WHERE id=?", [nid()])->fetch();
                                                                                                                                                                                                                                                                                            top('Ajustes'); ?><h1>Ajustes</h1>
        <form method=post class=card><?= f() ?><h3>Negocio</h3><input name=nombre value="<?= e($n['nombre']) ?>" required>
            <h3>Facturación electrónica (proveedor)</h3><label><input type=checkbox name=fe style="width:auto;min-height:0" <?= $n['fe_activa'] ? 'checked' : '' ?>> Activar envío al proveedor</label><input name=url placeholder="URL de la API del proveedor" value="<?= e($n['fe_url']) ?>"><input name=token placeholder="Token / API key" value="<?= e($n['fe_token']) ?>"><button class=btn>Guardar</button>
        </form>
        <form method=post class=card><?= f() ?><h3>Agregar vendedor</h3><input name=vnombre placeholder=Nombre required><input name=vemail type=email placeholder=Correo required><input name=vclave type=password placeholder="Clave (mínimo 6)" required><button class=btn>Crear</button></form><?php bot();
                                                                                                                                                                                                                                                                                                default:
                                                                                                                                                                                                                                                                                                    go('vender');
                                                                                                                                                                                                                                                                                            }
