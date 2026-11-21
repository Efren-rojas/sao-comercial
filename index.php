<?php
session_start();
require __DIR__ . '/config.php';
$db = new PDO(
  'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
  DB_USER,
  DB_PASS,
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC],
);
function q($s, $a = [])
{
  global $db;
  $t = $db->prepare($s);
  $t->execute($a);
  return $t;
}
function e($s)
{
  return htmlspecialchars((string) $s, ENT_QUOTES);
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
  if ($s) {
    $_SESSION['m'] = $s;
  }
  header("Location: index.php?p=$p");
  exit();
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
  if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !hash_equals(csrf(), $_POST['c'] ?? ($_SERVER['HTTP_X_CSRF'] ?? ''))
  ) {
    die('Sesión inválida, recarga la página');
  }
}
function post()
{
  return $_SERVER['REQUEST_METHOD'] === 'POST';
}
function ic($n)
{
  $d = [
    'cart' =>
      '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.7 12.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/>',
    'box' => '<path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5M12 13v8"/>',
    'users' =>
      '<circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0M16 4a4 4 0 0 1 0 8M22 21a7 7 0 0 0-4-6"/>',
    'wallet' =>
      '<path d="M3 7a2 2 0 0 1 2-2h13v4"/><path d="M3 7v11a2 2 0 0 0 2 2h15V9H5a2 2 0 0 1-2-2z"/><circle cx="16.5" cy="14.5" r="1"/>',
    'grid' =>
      '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
    'plus' => '<path d="M12 5v14M5 12h14"/>',
    'camera' => '<path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/>',
    'down' => '<path d="M12 4v11m-5-5 5 5 5-5M5 20h14"/>',
    'print' =>
      '<path d="M6 9V3h12v6M6 18H4v-7h16v7h-2"/><rect x="6" y="14" width="12" height="7" rx="1"/>',
    'db' =>
      '<ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
    'cog' =>
      '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9 7 7M17 17l2.1 2.1M4.9 19.1 7 17M17 7l2.1-2.1"/>',
    'truck' =>
      '<path d="M2 6h11v10H2zM13 9h4l4 4v3h-8"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
    'chart' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
    'bell' => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 0 0 4 0"/>',
    'hist' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
    'coin' =>
      '<circle cx="12" cy="12" r="9"/><path d="M12 7v10M15 9.5c-.5-1-1.6-1.5-3-1.5-1.7 0-3 .8-3 2s1.3 1.7 3 2 3 .8 3 2-1.3 2-3 2c-1.4 0-2.5-.5-3-1.5"/>',
    'shield' => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6z"/>',
    'msg' => '<path d="M21 12a8 8 0 0 1-12 7l-5 1.5 1.5-4.8A8 8 0 1 1 21 12z"/>',
    'out' => '<path d="M9 21H5V3h4M16 17l5-5-5-5M21 12H9"/>',
  ];
  return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' .
    ($d[$n] ?? '') .
    '</svg>';
}
function top($t)
{
  global $p;
  $s = $_SESSION['m'] ?? '';
  unset($_SESSION['m']);
  $ng = u() ? q('SELECT nombre FROM negocios WHERE id=?', [nid()])->fetchColumn() : 'SAO';
  $GLOBALS['nom'] = $ng;
  ?><!doctype html>
<html lang=es>
<head>
<meta charset=utf-8>
<meta name=viewport content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= e($t) ?> · SAO</title>
<link rel=manifest href=manifest.json>
<link rel=apple-touch-icon href=assets/i192.png>
<meta name=theme-color content="#111111">
<link rel=preconnect href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel=stylesheet>
<link rel=stylesheet href="assets/app.css?v=3">
</head>
<body>
<?php if (u()) {
  $n = [
    ['vender', 'cart', 'Vender'],
    ['productos', 'box', 'Productos'],
    ['clientes', 'users', 'Clientes'],
    ['caja', 'wallet', 'Caja'],
    ['mas', 'grid', 'Más'],
  ];
  $lg = is_file(__DIR__ . '/logos/' . nid() . '.img')
    ? '<img src="?p=logo" alt="">'
    : e(mb_strtoupper(mb_substr($ng, 0, 1))) . '<i>.</i>';
  echo "<nav><a class=lg href='?p=vender' title='" . e($ng) . "'>$lg</a><div class=it>";
  foreach ($n as $i) {
    if ($i[0] == 'productos' && u()['rol'] != 'dueno') {
      continue;
    }
    $on =
      $p == $i[0] ||
      ($i[0] == 'mas' &&
        in_array($p, ['compras', 'reportes', 'ajustes', 'historial', 'gastos', 'super']))
        ? ' class=on'
        : '';
    echo "<a href='?p=$i[0]'$on>" . ic($i[1]) . "<span>$i[2]</span></a>";
  }
  echo "</div><a class=cta href='?p=vender'>" . ic('plus') . '<span>Nueva venta</span></a></nav>';
} ?><main><?php if ($s) {
  echo '<div class=toast>' . e($s) . '</div>';
}
}
function bot()
{
  ?></main>
<div id=ticket>
</div>
<script>window.NEG=<?= json_encode(
  $GLOBALS['nom'] ?? 'SAO',
) ?>;window.CSRF="<?= csrf() ?>"<?php if (isset($GLOBALS['prods'])) {
  echo ';window.PRODUCTS=' .
    json_encode($GLOBALS['prods'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_NUMERIC_CHECK);
} ?></script>
<script src=assets/app.js>
</script>
</body>
</html><?php exit();
}
function facturar($vid, $tot, $it)
{
  $n = q('SELECT * FROM negocios WHERE id=?', [nid()])->fetch();
  if (!$n['fe_activa'] || !$n['fe_url']) {
    return null;
  }
  $b = [
    'numero' => $vid,
    'fecha' => date('c'),
    'total' => $tot,
    'items' => array_map(
      fn($x) => ['nombre' => $x[0]['nombre'], 'cantidad' => $x[1], 'precio' => $x[0]['precio']],
      $it,
    ),
  ];
  $h = curl_init($n['fe_url']);
  curl_setopt_array($h, [
    CURLOPT_POST => 1,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_TIMEOUT => 8,
    CURLOPT_HTTPHEADER => [
      'Content-Type: application/json',
      'Authorization: Bearer ' . $n['fe_token'],
    ],
    CURLOPT_POSTFIELDS => json_encode($b),
  ]);
  $r = curl_exec($h);
  $ok = $r !== false && curl_getinfo($h, CURLINFO_HTTP_CODE) < 300;
  q('UPDATE ventas SET fe_estado=?,fe_resp=? WHERE id=?', [
    $ok ? 'enviada' : 'error',
    substr((string) $r, 0, 2000),
    $vid,
  ]);
  return $ok ? 'enviada' : 'error';
}
function n($v)
{
  return (int) preg_replace('/\D/', '', (string) $v);
}
function neg()
{
  return q('SELECT * FROM negocios WHERE id=?', [nid()])->fetch();
}
function wa($t, $x)
{
  $t = preg_replace('/\D/', '', (string) $t);
  if (strlen($t) == 10) {
    $t = '57' . $t;
  }
  return $t ? "https://wa.me/$t?text=" . rawurlencode($x) : '';
}
function up($k, $sf)
{
  if (empty($_FILES[$k]['tmp_name'])) {
    return;
  }
  $gi = @getimagesize($_FILES[$k]['tmp_name']);
  if (
    $gi &&
    in_array($gi[2], [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_WEBP]) &&
    $_FILES[$k]['size'] < 1048576
  ) {
    @mkdir(__DIR__ . '/logos', 0755);
    move_uploaded_file($_FILES[$k]['tmp_name'], __DIR__ . '/logos/' . nid() . $sf . '.img');
  } else {
    go('ajustes', 'Imagen inválida (PNG, JPG o WEBP, máx. 1 MB)');
  }
}
$p = $_GET['p'] ?? 'vender';
if (!u() && !in_array($p, ['login', 'registro', 'olvide', 'reset'])) {
  go('login');
}
if (
  u() &&
  u()['rol'] != 'dueno' &&
  in_array($p, ['productos', 'compras', 'reportes', 'ajustes', 'respaldo', 'csv'])
) {
  go('vender', 'Solo el dueño puede entrar ahí');
}
chk();
if ($p == 'super' && empty(u()['sup'])) {
  go('vender');
}
if (u() && u()['rol'] != 'dueno' && in_array($p, ['gastos'])) {
  go('vender');
}
switch ($p) {
  case 'login':

    if (post()) {
      $r = q('SELECT * FROM usuarios WHERE email=?', [$_POST['email']])->fetch();
      if ($r && password_verify($_POST['clave'], $r['clave'])) {
        $_SESSION['u'] = [
          'id' => $r['id'],
          'nid' => $r['negocio_id'],
          'nombre' => $r['nombre'],
          'rol' => $r['rol'],
          'sup' => $r['super'],
        ];
        go('vender');
      }
      go('login', 'Correo o clave incorrectos');
    }
    top('Entrar');
    ?><form method=post class=card style="max-width:420px;margin:40px auto">
<h1>Entrar</h1><?= f() ?><input name=email type=email placeholder=Correo required>
<input name=clave type=password placeholder=Clave required>
<button class="btn big" style="background:var(--ac)">Entrar</button>
<p>
<a href="?p=registro">Crear mi negocio</a> · <a href="?p=olvide">Olvidé mi clave</a>
</p>
</form><?php bot();
  case 'registro':

    if (post()) {
      if (strlen($_POST['clave']) < 6) {
        go('registro', 'La clave debe tener 6 o más caracteres');
      }
      if (q('SELECT id FROM usuarios WHERE email=?', [$_POST['email']])->fetch()) {
        go('registro', 'Ese correo ya existe');
      }
      $pl = [
        'tienda' => [['Gaseosa 400ml', 2500, 24], ['Pan', 500, 40], ['Arroz 500g', 2800, 20]],
        'restaurante' => [['Almuerzo', 14000, 50], ['Jugo', 4000, 40], ['Postre', 5000, 20]],
        'joyeria' => [
          ['Anillo plata', 120000, 6],
          ['Cadena plata', 180000, 5],
          ['Aretes oro 18k', 350000, 4],
        ],
        'bisuteria' => [['Collar', 15000, 20], ['Pulsera', 8000, 30], ['Aretes', 6000, 40]],
        'ropa' => [['Camiseta', 35000, 20], ['Jean', 90000, 12], ['Buzo', 70000, 10]],
        'electronicos' => [
          ['Cargador USB-C', 25000, 15],
          ['Audífonos Bluetooth', 40000, 10],
          ['Vidrio templado', 12000, 30],
        ],
      ];
      $db->beginTransaction();
      q('INSERT INTO negocios(nombre,tipo,usa_vence)VALUES(?,?,?)', [
        $_POST['negocio'],
        $_POST['tipo'],
        $_POST['tipo'] == 'restaurante' ? 1 : 0,
      ]);
      $n = $db->lastInsertId();
      q("INSERT INTO usuarios(negocio_id,nombre,email,clave,rol)VALUES(?,?,?,?,'dueno')", [
        $n,
        $_POST['nombre'],
        $_POST['email'],
        password_hash($_POST['clave'], PASSWORD_DEFAULT),
      ]);
      foreach ($pl[$_POST['tipo']] ?? $pl['tienda'] as $x) {
        q('INSERT INTO productos(negocio_id,nombre,precio,stock)VALUES(?,?,?,?)', [
          $n,
          $x[0],
          $x[1],
          $x[2],
        ]);
      }
      $db->commit();
      go('login', 'Listo. Ya puedes entrar');
    }
    top('Crear negocio');
    ?><form method=post class=card style="max-width:420px;margin:40px auto">
<h1>Crear mi negocio</h1><?= f() ?><input name=negocio placeholder="Nombre del negocio" required>
<select name=tipo>
<option value=tienda>Tienda general<option value=restaurante>Restaurante<option value=joyeria>Joyería<option value=bisuteria>Bisutería<option value=ropa>Ropa<option value=electronicos>Electrónicos y celulares</select>
<input name=nombre placeholder="Tu nombre" required>
<input name=email type=email placeholder=Correo required>
<input name=clave type=password placeholder="Clave (mínimo 6)" required>
<button class="btn big" style="background:var(--ac)">Crear</button>
<p>
<a href="?p=login">Ya tengo cuenta</a>
</p>
</form><?php bot();
  case 'logout':
    session_destroy();
    header('Location: index.php?p=login');
    exit();
  case 'vender':

    $GLOBALS['prods'] = q(
      'SELECT id,nombre,codigo,precio,stock,minimo FROM productos WHERE negocio_id=? AND activo=1 ORDER BY nombre',
      [nid()],
    )->fetchAll();
    $cl = q('SELECT id,nombre,telefono FROM clientes WHERE negocio_id=? ORDER BY nombre', [
      nid(),
    ])->fetchAll();
    top('Vender');
    $lo = (int) q(
      'SELECT COUNT(*) FROM productos WHERE negocio_id=? AND activo=1 AND stock<=minimo',
      [nid()],
    )->fetchColumn();
    $vc = neg()['usa_vence']
      ? (int) q(
        'SELECT COUNT(*) FROM productos WHERE negocio_id=? AND activo=1 AND vence IS NOT NULL AND vence<=DATE_ADD(CURDATE(),INTERVAL 7 DAY)',
        [nid()],
      )->fetchColumn()
      : 0;
    if ($lo || $vc) {
      $tx =
        ($lo ? "$lo con stock bajo" : '') .
        ($lo && $vc ? ' · ' : '') .
        ($vc ? "$vc por vencer" : '');
      echo '<div class=alert>' .
        ic('bell') .
        '<div><b>' .
        $tx .
        '</b><small>Repón o revisa antes de que se agoten</small></div>' .
        (u()['rol'] == 'dueno' ? '<a href="?p=productos">Ver</a>' : '') .
        '</div>';
    }
    ?><div class=pos>
<section>
<div style="display:flex;gap:8px">
<input id=bq placeholder="Buscar o escribir código" autofocus>
<button id=cam class=ib type=button><?= ic(
  'camera',
) ?></button>
</div>
<div id=grid class=grid>
</div>
</section>
<aside>
<h3>Venta</h3>
<div id=cart>
</div>
<select id=cli>
<option value="">Cliente (opcional)</option><?php foreach (
  $cl
  as $c
) {
  echo "<option value=$c[id] data-t='" . e($c['telefono']) . "'>" . e($c['nombre']);
} ?></select>
<select id=met>
<option value=efectivo>Efectivo<option value=tarjeta>Tarjeta<option value=transferencia>Transferencia<option value=fiado>Fiado</select><?php if (
  is_file(__DIR__ . '/logos/' . nid() . '-qr.img')
) {
  echo '<div id=qr hidden><img src="?p=qr" alt="QR de pago"></div>';
} ?><input id=dsc class=mn inputmode=numeric placeholder="Descuento $">
<div id=tot class=tot>$0</div>
<button id=pay class="btn big">Cobrar</button>
<p id=msg>
</p>
<label class=chk>
<input type=checkbox id=ap checked> Imprimir ticket al cobrar</label>
<button id=rp class="btn sec" type=button hidden style="margin-top:8px;width:100%"><?= ic(
  'print',
) ?>Reimprimir ticket</button>
<a id=wa class="btn sec" target=_blank hidden style="margin-top:8px;width:100%"><?= ic(
  'msg',
) ?>Enviar por WhatsApp</a>
<p id=pend class=mut>
</p>
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
        $c = (int) $i['c'];
        $r = q('SELECT * FROM productos WHERE id=? AND negocio_id=? AND activo=1 FOR UPDATE', [
          $i['id'],
          nid(),
        ])->fetch();
        if (!$r || $c < 1) {
          throw new Exception('Producto inválido');
        }
        if ($c > $r['stock']) {
          throw new Exception("Sin stock suficiente de $r[nombre] (hay $r[stock])");
        }
        $tot += $c * $r['precio'];
        $it[] = [$r, $c];
      }
      if (!$it) {
        throw new Exception('Carrito vacío');
      }
      $ds = max(0, min((int) ($d['desc'] ?? 0), $tot));
      $tot -= $ds;
      $mt = in_array($d['metodo'], ['efectivo', 'tarjeta', 'transferencia', 'fiado'])
        ? $d['metodo']
        : 'efectivo';
      $cl = (int) ($d['cliente'] ?? 0) ?: null;
      if ($cl && !q('SELECT id FROM clientes WHERE id=? AND negocio_id=?', [$cl, nid()])->fetch()) {
        throw new Exception('Cliente inválido');
      }
      if ($mt == 'fiado' && !$cl) {
        throw new Exception('Elige un cliente para fiar');
      }
      q(
        'INSERT INTO ventas(negocio_id,usuario_id,cliente_id,total,descuento,metodo)VALUES(?,?,?,?,?,?)',
        [nid(), u()['id'], $cl, $tot, $ds, $mt],
      );
      $vid = $db->lastInsertId();
      foreach ($it as [$r, $c]) {
        q(
          'INSERT INTO venta_items(venta_id,producto_id,nombre,cantidad,precio,costo,iva)VALUES(?,?,?,?,?,?,?)',
          [$vid, $r['id'], $r['nombre'], $c, $r['precio'], $r['costo'], $r['iva']],
        );
        q('UPDATE productos SET stock=stock-? WHERE id=?', [$c, $r['id']]);
      }
      if ($mt == 'fiado') {
        q('UPDATE clientes SET deuda=deuda+? WHERE id=?', [$tot, $cl]);
      }
      $db->commit();
    } catch (Exception $x) {
      if ($db->inTransaction()) {
        $db->rollBack();
      }
      echo json_encode(['err' => $x->getMessage()]);
      exit();
    }
    echo json_encode(['ok' => 1, 'id' => $vid, 'total' => $tot, 'fe' => facturar($vid, $tot, $it)]);
    exit();
  case 'productos':

    $ng = neg();
    if (post()) {
      if (isset($_POST['del'])) {
        q('UPDATE productos SET activo=0 WHERE id=? AND negocio_id=?', [$_POST['del'], nid()]);
        go('productos', 'Producto quitado');
      }
      $a = [
        trim($_POST['nombre']),
        $_POST['codigo'],
        n($_POST['precio']),
        n($_POST['costo']),
        n($_POST['stock']),
        n($_POST['minimo']),
        min(100, n($_POST['iva'] ?? 0)),
        $_POST['vence'] ?? '' ?: null,
      ];
      if (!empty($_POST['id'])) {
        q(
          'UPDATE productos SET nombre=?,codigo=?,precio=?,costo=?,stock=?,minimo=?,iva=?,vence=? WHERE id=? AND negocio_id=?',
          [...$a, $_POST['id'], nid()],
        );
      } else {
        if (
          q('SELECT id FROM productos WHERE negocio_id=? AND activo=1 AND LOWER(nombre)=LOWER(?)', [
            nid(),
            $a[0],
          ])->fetch()
        ) {
          go('productos', 'Ese producto ya existe');
        }
        q(
          'INSERT INTO productos(nombre,codigo,precio,costo,stock,minimo,iva,vence,negocio_id)VALUES(?,?,?,?,?,?,?,?,?)',
          [...$a, nid()],
        );
      }
      go('productos', 'Guardado');
    }
    $ed = isset($_GET['e'])
      ? q('SELECT * FROM productos WHERE id=? AND negocio_id=?', [$_GET['e'], nid()])->fetch()
      : [];
    $ls = q(
      'SELECT * FROM productos WHERE negocio_id=? AND activo=1 ORDER BY stock<=minimo DESC,nombre',
      [nid()],
    )->fetchAll();
    top('Productos');
    ?><h1>Productos</h1>
<form method=post class=card><?= f() ?><input type=hidden name=id value="<?= e(
  $ed['id'] ?? '',
) ?>">
<input name=nombre placeholder=Nombre required value="<?= e(
  $ed['nombre'] ?? '',
) ?>">
<input name=codigo placeholder="Código de barras (opcional)" value="<?= e(
  $ed['codigo'] ?? '',
) ?>">
<input name=precio class=mn inputmode=numeric placeholder="Precio de venta (con IVA incluido)" required value="<?= e(
  $ed['precio'] ?? '',
) ?>">
<input name=costo class=mn inputmode=numeric placeholder="Costo Neto" value="<?= e(
  $ed['costo'] ?? '',
) ?>">
<input name=stock class=mn inputmode=numeric placeholder="Cantidad disponible" required value="<?= e(
  $ed['stock'] ?? '',
) ?>">
<input name=minimo class=mn inputmode=numeric placeholder="Avisar cuando queden" value="<?= e(
  $ed['minimo'] ?? 3,
) ?>">
<select name=iva><?php foreach (
  [0 => 'Sin IVA', 5 => 'IVA 5%', 19 => 'IVA 19%']
  as $k => $l
) {
  echo "<option value=$k" . (($ed['iva'] ?? 0) == $k ? ' selected' : '') . ">$l";
} ?></select><?php if ($ng['usa_vence']) {
  echo '<label class=mut>Fecha de vencimiento</label><input type=date name=vence value="' .
    e($ed['vence'] ?? '') .
    '">';
} ?><button class=btn>Guardar</button>
</form>
 <div class=sc>
<table>
<tr>
<th>Producto<th>Precio<th>Ganancia<th>Hay<?= $ng['usa_vence']
   ? '<th>Vence'
   : '' ?><th><?php foreach ($ls as $r) {
  $lo = $r['stock'] <= $r['minimo'] ? ' class=low' : '';
  echo '<tr><td>' .
    e($r['nombre']) .
    '<td>' .
    m($r['precio']) .
    '<td>' .
    m($r['precio'] - $r['costo']) .
    "<td$lo>$r[stock]" .
    ($ng['usa_vence']
      ? '<td' .
        ($r['vence'] && $r['vence'] <= date('Y-m-d', strtotime('+7 days')) ? ' class=low' : '') .
        '>' .
        e($r['vence'])
      : '') .
    "<td style='white-space:nowrap'><a class='btn sec sm' href='?p=productos&e=$r[id]'>Editar</a> <form method=post class=in>" .
    f() .
    "<input type=hidden name=del value=$r[id]><button class='btn no sm'>Quitar</button></form>";
} ?></table>
</div><?php bot();
  case 'clientes':

    if (post()) {
      if (isset($_POST['monto'])) {
        $mo = max(
          0,
          min(
            n($_POST['monto']),
            (int) q('SELECT deuda FROM clientes WHERE id=? AND negocio_id=?', [
              $_POST['cid'],
              nid(),
            ])->fetchColumn(),
          ),
        );
        q('UPDATE clientes SET deuda=deuda-? WHERE id=? AND negocio_id=?', [
          $mo,
          $_POST['cid'],
          nid(),
        ]);
        q('INSERT INTO pagos(negocio_id,cliente_id,monto)VALUES(?,?,?)', [
          nid(),
          $_POST['cid'],
          $mo,
        ]);
        go('clientes', 'Abono registrado');
      }
      if (isset($_POST['del'])) {
        if (
          (int) q('SELECT deuda FROM clientes WHERE id=? AND negocio_id=?', [
            $_POST['del'],
            nid(),
          ])->fetchColumn() > 0
        ) {
          go('clientes', 'No se puede eliminar: tiene deuda pendiente');
        }
        q('DELETE FROM clientes WHERE id=? AND negocio_id=?', [$_POST['del'], nid()]);
        go('clientes', 'Cliente eliminado');
      }
      $nm = trim($_POST['nombre']);
      $tl = trim($_POST['telefono']);
      $id = (int) ($_POST['id'] ?? 0);
      if (
        q(
          "SELECT id FROM clientes WHERE negocio_id=? AND id<>? AND (LOWER(nombre)=LOWER(?) OR (?<>'' AND telefono=?))",
          [nid(), $id, $nm, $tl, $tl],
        )->fetch()
      ) {
        go('clientes', 'Ese cliente ya está registrado');
      }
      if ($id) {
        q('UPDATE clientes SET nombre=?,telefono=? WHERE id=? AND negocio_id=?', [
          $nm,
          $tl,
          $id,
          nid(),
        ]);
      } else {
        q('INSERT INTO clientes(negocio_id,nombre,telefono)VALUES(?,?,?)', [nid(), $nm, $tl]);
      }
      go('clientes', $id ? 'Cliente actualizado' : 'Cliente agregado');
    }
    $ed = isset($_GET['e'])
      ? q('SELECT * FROM clientes WHERE id=? AND negocio_id=?', [$_GET['e'], nid()])->fetch()
      : [];
    $ls = q('SELECT * FROM clientes WHERE negocio_id=? ORDER BY deuda DESC,nombre', [
      nid(),
    ])->fetchAll();
    top('Clientes');
    ?><h1>Clientes y fiado</h1>
<form method=post class=card><?= f() ?><input type=hidden name=id value="<?= e(
  $ed['id'] ?? '',
) ?>">
<input name=nombre placeholder=Nombre required value="<?= e(
  $ed['nombre'] ?? '',
) ?>">
<input name=telefono placeholder="Teléfono (se usa para enviar el ticket por WhatsApp)" value="<?= e(
  $ed['telefono'] ?? '',
) ?>">
<button class=btn>Guardar</button>
</form>
 <div class=sc>
<table>
<tr>
<th>Cliente<th>Debe<th>Abonar<th><?php foreach ($ls as $r) {
   echo '<tr><td>' .
     e($r['nombre']) .
     '<br><small class=mut>' .
     e($r['telefono']) .
     '</small><td' .
     ($r['deuda'] ? ' class=low' : '') .
     '>' .
     m($r['deuda']) .
     '<td>' .
     ($r['deuda']
       ? '<form method=post class=in>' .
         f() .
         "<input type=hidden name=cid value=$r[id]><input name=monto class=mn inputmode=numeric placeholder=Monto style='width:120px'> <button class=sm>OK</button></form>"
       : '') .
     "<td style='white-space:nowrap'><a class='btn sec sm' href='?p=clientes&e=$r[id]'>Editar</a> <form method=post class=in onsubmit=\"return confirm('¿Eliminar este cliente?')\">" .
     f() .
     "<input type=hidden name=del value=$r[id]><button class='btn no sm'>Eliminar</button></form>";
 } ?></table>
</div><?php bot();
  case 'caja':

    $now = q('SELECT NOW() n')->fetch()['n'];
    $ul =
      q('SELECT MAX(hasta) FROM cierres WHERE negocio_id=?', [nid()])->fetchColumn() ?:
      '2000-01-01';
    $vs = q(
      'SELECT metodo,SUM(total) t,COUNT(*) n FROM ventas WHERE anulada=0 AND negocio_id=? AND creado>? AND creado<=? GROUP BY metodo',
      [nid(), $ul, $now],
    )->fetchAll();
    $ab = (int) q('SELECT SUM(monto) FROM pagos WHERE negocio_id=? AND creado>? AND creado<=?', [
      nid(),
      $ul,
      $now,
    ])->fetchColumn();
    $ef = $ab;
    foreach ($vs as $v) {
      if ($v['metodo'] == 'efectivo') {
        $ef += $v['t'];
      }
    }
    if (post()) {
      q(
        'INSERT INTO cierres(negocio_id,usuario_id,desde,hasta,esperado,contado)VALUES(?,?,?,?,?,?)',
        [nid(), u()['id'], $ul == '2000-01-01' ? $now : $ul, $now, $ef, n($_POST['contado'])],
      );
      go('caja', 'Caja cerrada');
    }
    $hs = q('SELECT * FROM cierres WHERE negocio_id=? ORDER BY id DESC LIMIT 7', [
      nid(),
    ])->fetchAll();
    top('Caja');
    ?><h1>Caja</h1>
<div class=card>
<h3>Desde el último cierre</h3><?php foreach ($vs as $v) {
  echo '<p>' .
    ucfirst($v['metodo']) .
    ': <b>' .
    m($v['t']) .
    "</b> <span class=mut>($v[n] ventas)</span></p>";
} ?><p>Abonos de fiado: <b><?= m($ab) ?></b>
</p>
<p class=tot>Efectivo esperado: <?= m(
  $ef,
) ?></p>
<form method=post><?= f() ?><input name=contado class=mn inputmode=numeric placeholder="¿Cuánto efectivo hay?" required>
<button class="btn big">Cerrar caja</button>
</form>
</div>
 <table>
<tr>
<th>Cierre<th>Esperado<th>Contado<th>Diferencia<?php foreach ($hs as $h) {
   echo "<tr><td>$h[hasta]<td>" .
     m($h['esperado']) .
     '<td>' .
     m($h['contado']) .
     '<td' .
     ($h['contado'] != $h['esperado'] ? ' class=low' : '') .
     '>' .
     m($h['contado'] - $h['esperado']);
 } ?></table><?php bot();
  case 'mas':
    top('Más'); ?><h1>Más</h1>
<div class=mas><?php if (u()['rol'] == 'dueno') {
  foreach (
    [
      ['compras', 'truck', 'Compras a proveedores'],
      ['reportes', 'chart', 'Panel y reportes'],
      ['gastos', 'coin', 'Gastos'],
      ['ajustes', 'cog', 'Ajustes y facturación'],
    ]
    as $l
  ) {
    echo "<a class=card href='?p=$l[0]'>" . ic($l[1]) . "<b>$l[2]</b></a>";
  };
} ?><a class=card href="?p=historial"><?= ic('hist') ?><b>Historial de ventas</b>
</a><?php if (
  !empty(u()['sup'])
) {
  echo '<a class=card href="?p=super">' . ic('shield') . '<b>Panel superadmin</b></a>';
} ?><a class=card href="?p=logout"><?= ic('out') ?><b>Salir (<?= e(
  u()['nombre'],
) ?>)</b>
</a>
</div><?php bot();
  case 'compras':

    $ps = q('SELECT id,nombre FROM productos WHERE negocio_id=? AND activo=1 ORDER BY nombre', [
      nid(),
    ])->fetchAll();
    if (post()) {
      $c = max(1, n($_POST['cantidad']));
      if (
        !q('SELECT id FROM productos WHERE id=? AND negocio_id=?', [$_POST['pid'], nid()])->fetch()
      ) {
        go('compras', 'Producto inválido');
      }
      q('INSERT INTO compras(negocio_id,proveedor,producto_id,cantidad,costo)VALUES(?,?,?,?,?)', [
        nid(),
        $_POST['proveedor'],
        $_POST['pid'],
        $c,
        n($_POST['costo']),
      ]);
      q('UPDATE productos SET stock=stock+?,costo=? WHERE id=?', [
        $c,
        n($_POST['costo']),
        $_POST['pid'],
      ]);
      go('compras', 'Compra registrada, stock actualizado');
    }
    $hs = q(
      'SELECT c.*,p.nombre FROM compras c JOIN productos p ON p.id=c.producto_id WHERE c.negocio_id=? ORDER BY c.id DESC LIMIT 15',
      [nid()],
    )->fetchAll();
    top('Compras');
    ?><h1>Compras</h1>
<form method=post class=card><?= f() ?><select name=pid><?php foreach (
  $ps
  as $r
) {
  echo "<option value=$r[id]>" . e($r['nombre']);
} ?></select>
<input name=proveedor placeholder=Proveedor>
<input name=cantidad class=mn inputmode=numeric placeholder="Cantidad que llegó" required>
<input name=costo class=mn inputmode=numeric placeholder="Costo por unidad" required>
<button class=btn>Registrar compra</button>
</form>
<table>
<tr>
<th>Fecha<th>Producto<th>Cant.<th>Costo<?php foreach (
  $hs
  as $h
) {
  echo '<tr><td>' .
    substr($h['creado'], 0, 10) .
    '<td>' .
    e($h['nombre']) .
    "<td>$h[cantidad]<td>" .
    m($h['costo']);
} ?></table><?php bot();
  case 'reportes':

    $n = nid();
    $hoy = q(
      'SELECT COALESCE(SUM(total),0) FROM ventas WHERE anulada=0 AND negocio_id=? AND DATE(creado)=CURDATE()',
      [$n],
    )->fetchColumn();
    $mes = q(
      "SELECT COALESCE(SUM(total),0) FROM ventas WHERE anulada=0 AND negocio_id=? AND creado>=DATE_FORMAT(NOW(),'%Y-%m-01')",
      [$n],
    )->fetchColumn();
    $dias = q(
      'SELECT DATE(creado) d,SUM(total) t,COUNT(*) n FROM ventas WHERE anulada=0 AND negocio_id=? AND creado>=CURDATE()-INTERVAL 13 DAY GROUP BY d ORDER BY d DESC',
      [$n],
    )->fetchAll();
    $mx = max(1, ...array_column($dias, 't') ?: [1]);
    $top = q(
      'SELECT i.nombre,SUM(i.cantidad) c FROM venta_items i JOIN ventas v ON v.id=i.venta_id WHERE v.negocio_id=? AND v.anulada=0 GROUP BY i.nombre ORDER BY c DESC LIMIT 5',
      [$n],
    )->fetchAll();
    $bajo = q(
      'SELECT nombre,stock FROM productos WHERE negocio_id=? AND activo=1 AND stock<=minimo',
      [$n],
    )->fetchAll();
    top('Reportes');
    ?><h1>Panel</h1><?php
$ug = (int) q(
  "SELECT COALESCE(SUM((i.precio-i.costo)*i.cantidad),0)-(SELECT COALESCE(SUM(descuento),0) FROM ventas WHERE negocio_id=? AND anulada=0 AND creado>=DATE_FORMAT(NOW(),'%Y-%m-01')) FROM venta_items i JOIN ventas v ON v.id=i.venta_id WHERE v.negocio_id=? AND v.anulada=0 AND v.creado>=DATE_FORMAT(NOW(),'%Y-%m-01')",
  [$n, $n],
)->fetchColumn();
$gm = (int) q(
  "SELECT COALESCE(SUM(monto),0) FROM gastos WHERE negocio_id=? AND creado>=DATE_FORMAT(NOW(),'%Y-%m-01')",
  [$n],
)->fetchColumn();
$mt = (int) neg()['meta'];
$ay = (int) q(
  'SELECT COALESCE(SUM(total),0) FROM ventas WHERE anulada=0 AND negocio_id=? AND DATE(creado)=CURDATE()-INTERVAL 1 DAY',
  [$n],
)->fetchColumn();
$nv = (int) q(
  'SELECT COUNT(*) FROM ventas WHERE anulada=0 AND negocio_id=? AND DATE(creado)=CURDATE()',
  [$n],
)->fetchColumn();
$pc = $ay ? round((($hoy - $ay) / $ay) * 100) : null;
?><div class=kpis>
<div class=card>
<small>Ventas de hoy</small>
<b><?= m(
  $hoy,
) ?></b>
<span class="<?= $pc !== null && $pc < 0 ? 'low' : '' ?>"><?= $pc === null
  ? 'Sin datos de ayer'
  : ($pc > 0 ? '+' : '') .
    $pc .
    '% vs ayer' ?></span>
</div>
<div class=card>
<small>Este mes</small>
<b><?= m(
  $mes,
) ?></b>
</div>
<div class=card>
<small>Utilidad del mes</small>
<b class="<?= $ug - $gm < 0
  ? 'low'
  : '' ?>"><?= m(
  $ug - $gm,
) ?></b>
<span class=mut>Ventas − costos − gastos</span>
</div>
</div><?php if ($mt) {
  $pm = round(($hoy / $mt) * 100);
  echo '<div class=card><small>Meta de hoy (' .
    m($mt) .
    ')</small><b style="font-size:clamp(44px,8vw,72px);font-weight:900;letter-spacing:-.05em;display:block">' .
    $pm .
    '%</b><div class=trk><div class=bar style="width:' .
    min(100, $pm) .
    '%"></div></div></div>';
} ?><div class=card>
<a class="btn sec noprint" href="?p=csv"><?= ic(
  'down',
) ?>Excel</a> <button class="btn sec noprint" onclick="print()"><?= ic(
  'print',
) ?>PDF / Imprimir</button> <a class="btn sec noprint" href="?p=respaldo"><?= ic(
  'db',
) ?>Respaldo</a>
</div>
 <table>
<tr>
<th>Día<th>Ventas<th>Total<th><?php foreach ($dias as $d) {
   echo "<tr><td>$d[d]<td>$d[n]<td>" .
     m($d['t']) .
     "<td style='width:30%'><div class=bar style='width:" .
     round(($d['t'] / $mx) * 100) .
     "%'></div>";
 } ?></table>
<table>
<tr>
<th>Más vendidos<th><?php foreach ($top as $t) {
  echo '<tr><td>' . e($t['nombre']) . "<td>$t[c]";
} ?></table>
<table>
<tr>
<th>Stock bajo<th><?php foreach ($bajo as $t) {
  echo '<tr><td>' . e($t['nombre']) . "<td class=low>$t[stock]";
} ?></table><?php bot();
  case 'csv':
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=ventas.csv');
    echo "\xEF\xBB\xBF";
    $o = fopen('php://output', 'w');
    fputcsv($o, ['id', 'fecha', 'total', 'descuento', 'metodo', 'factura_electronica']);
    foreach (
      q(
        'SELECT id,creado,total,descuento,metodo,fe_estado FROM ventas WHERE negocio_id=? ORDER BY id DESC',
        [nid()],
      )
      as $r
    ) {
      fputcsv($o, $r);
    }
    exit();
  case 'respaldo':
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename=respaldo-' . date('Ymd') . '.json');
    $o = [];
    foreach (['productos', 'clientes', 'ventas', 'compras', 'pagos', 'cierres', 'gastos'] as $t) {
      $o[$t] = q("SELECT * FROM $t WHERE negocio_id=?", [nid()])->fetchAll();
    }
    $o['venta_items'] = q(
      'SELECT i.* FROM venta_items i JOIN ventas v ON v.id=i.venta_id WHERE v.negocio_id=?',
      [nid()],
    )->fetchAll();
    echo json_encode($o, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
  case 'qr':
    $lf = __DIR__ . '/logos/' . nid() . '-qr.img';
    if (is_file($lf)) {
      header('Content-Type: ' . getimagesize($lf)['mime']);
      readfile($lf);
    }
    exit();
  case 'historial':

    if (post() && isset($_POST['anular']) && u()['rol'] == 'dueno') {
      try {
        $db->beginTransaction();
        $v = q('SELECT * FROM ventas WHERE id=? AND negocio_id=? AND anulada=0 FOR UPDATE', [
          $_POST['anular'],
          nid(),
        ])->fetch();
        if (!$v) {
          throw new Exception('x');
        }
        foreach (q('SELECT * FROM venta_items WHERE venta_id=?', [$v['id']])->fetchAll() as $i) {
          q('UPDATE productos SET stock=stock+? WHERE id=? AND negocio_id=?', [
            $i['cantidad'],
            $i['producto_id'],
            nid(),
          ]);
        }
        if ($v['metodo'] == 'fiado' && $v['cliente_id']) {
          q('UPDATE clientes SET deuda=GREATEST(0,deuda-?) WHERE id=?', [
            $v['total'],
            $v['cliente_id'],
          ]);
        }
        q('UPDATE ventas SET anulada=1 WHERE id=?', [$v['id']]);
        $db->commit();
      } catch (Exception $x) {
        if ($db->inTransaction()) {
          $db->rollBack();
        }
        go('historial', 'No se pudo anular la venta');
      }
      go('historial', 'Venta anulada y stock devuelto');
    }
    $ls = q(
      'SELECT v.*,c.nombre cn FROM ventas v LEFT JOIN clientes c ON c.id=v.cliente_id WHERE v.negocio_id=? ORDER BY v.id DESC LIMIT 60',
      [nid()],
    )->fetchAll();
    top('Historial');
    ?><h1>Historial</h1>
<div class=sc>
<table>
<tr>
<th>N°<th>Fecha<th>Cliente<th>Pago<th>Total<th><?php foreach (
  $ls
  as $v
) {
  $x = $v['anulada'];
  echo '<tr' .
    ($x ? " style='opacity:.5'" : '') .
    "><td>$v[id]<td>" .
    substr($v['creado'], 0, 16) .
    '<td>' .
    e($v['cn'] ?? '—') .
    "<td>$v[metodo]<td>" .
    ($x ? '<s>' . m($v['total']) . '</s> anulada' : m($v['total'])) .
    "<td style='white-space:nowrap'><a class='btn sec sm' target=_blank href='?p=ticket&id=$v[id]'>Ticket</a>" .
    (!$x && u()['rol'] == 'dueno'
      ? " <form method=post class=in onsubmit=\"return confirm('¿Anular la venta $v[id]? Se devuelve el stock.')\">" .
        f() .
        "<input type=hidden name=anular value=$v[id]><button class='btn no sm'>Anular</button></form>"
      : '');
} ?></table>
</div><?php bot();
  case 'ticket':

    $v = q(
      'SELECT v.*,c.nombre cn,c.telefono ct FROM ventas v LEFT JOIN clientes c ON c.id=v.cliente_id WHERE v.id=? AND v.negocio_id=?',
      [$_GET['id'] ?? 0, nid()],
    )->fetch();
    if (!$v) {
      go('historial');
    }
    $it = q('SELECT * FROM venta_items WHERE venta_id=?', [$v['id']])->fetchAll();
    $ng = neg();
    $iv = 0;
    foreach ($it as $i) {
      $iv += ($i['cantidad'] * $i['precio'] * $i['iva']) / (100 + $i['iva']);
    }
    $tx = "Gracias por tu compra en $ng[nombre]. Venta N° $v[id] por " . m($v['total']) . '.';
    foreach ($it as $i) {
      $tx .= "\n$i[cantidad] x $i[nombre]";
    }
    $wl = wa($v['ct'], $tx);
    ?><!doctype html>
<html lang=es>
<head>
<meta charset=utf-8>
<meta name=viewport content="width=device-width,initial-scale=1">
<title>Ticket <?= $v[
  'id'
] ?></title>
<link rel=stylesheet href="assets/app.css?v=3">
</head>
<body style="padding:20px;display:grid;justify-items:center;gap:12px">
<div id=ticket style="display:block;background:#fff;padding:18px;border-radius:16px">
<h2><?= e(
  $ng['nombre'],
) ?></h2>
<p class=c><?= e($v['creado']) ?><br>Venta N° <?= $v['id'] ?><?= $v['anulada']
  ? '<br><b>ANULADA</b>'
  : '' ?><?= $v['cn'] ? '<br>Cliente: ' . e($v['cn']) : '' ?></p>
<hr><?php foreach (
  $it
  as $i
) {
  echo '<div class=tl><span>' .
    $i['cantidad'] .
    ' x ' .
    e($i['nombre']) .
    '</span><span>' .
    m($i['cantidad'] * $i['precio']) .
    '</span></div>';
} ?><hr><?php
if ($v['descuento']) {
  echo '<div class=tl><span>Descuento</span><span>-' . m($v['descuento']) . '</span></div>';
}
if ($iv > 0) {
  echo '<div class=tl><span>IVA incluido</span><span>' . m($iv) . '</span></div>';
}
?><div class="tl tt">
<span>TOTAL</span>
<span><?= m($v['total']) ?></span>
</div>
<p>Pago: <?= e(
  $v['metodo'],
) ?></p>
<p class=c>¡Gracias por su compra!</p>
</div>
<div class=noprint style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center">
<button onclick="print()"><?= ic(
  'print',
) ?>Imprimir</button><?php if ($wl) {
  echo '<a class="btn sec" target=_blank href="' . e($wl) . '">' . ic('msg') . 'WhatsApp</a>';
} ?><a class="btn sec" href="?p=historial">Volver</a>
</div>
</body>
</html><?php exit();
  case 'gastos':

    if (post()) {
      if (isset($_POST['del'])) {
        q('DELETE FROM gastos WHERE id=? AND negocio_id=?', [$_POST['del'], nid()]);
      } else {
        if (n($_POST['monto']) < 1) {
          go('gastos', 'Escribe un valor');
        }
        q('INSERT INTO gastos(negocio_id,concepto,monto)VALUES(?,?,?)', [
          nid(),
          trim($_POST['concepto']),
          n($_POST['monto']),
        ]);
      }
      go('gastos', 'Listo');
    }
    $ls = q('SELECT * FROM gastos WHERE negocio_id=? ORDER BY id DESC LIMIT 40', [
      nid(),
    ])->fetchAll();
    $tm = (int) q(
      "SELECT COALESCE(SUM(monto),0) FROM gastos WHERE negocio_id=? AND creado>=DATE_FORMAT(NOW(),'%Y-%m-01')",
      [nid()],
    )->fetchColumn();
    top('Gastos');
    ?><h1>Gastos</h1>
<div class=kpis>
<div class=card>
<small>Gastos del mes</small>
<b><?= m(
  $tm,
) ?></b>
</div>
</div>
<form method=post class=card><?= f() ?><input name=concepto placeholder="Concepto (arriendo, servicios, nómina…)" required>
<input name=monto class=mn inputmode=numeric placeholder="Valor" required>
<button class=btn>Registrar gasto</button>
</form>
<div class=sc>
<table>
<tr>
<th>Fecha<th>Concepto<th>Valor<th><?php foreach (
  $ls
  as $g
) {
  echo '<tr><td>' .
    substr($g['creado'], 0, 10) .
    '<td>' .
    e($g['concepto']) .
    '<td>' .
    m($g['monto']) .
    '<td><form method=post class=in>' .
    f() .
    "<input type=hidden name=del value=$g[id]><button class='btn no sm'>Quitar</button></form>";
} ?></table>
</div><?php bot();
  case 'super':

    if (post() && isset($_POST['nv'])) {
      $o = q(
        "SELECT id,nombre FROM usuarios WHERE negocio_id=? AND rol='dueno' ORDER BY id LIMIT 1",
        [$_POST['nv']],
      )->fetch();
      if ($o) {
        $pw = substr(bin2hex(random_bytes(4)), 0, 8);
        q('UPDATE usuarios SET clave=? WHERE id=?', [
          password_hash($pw, PASSWORD_DEFAULT),
          $o['id'],
        ]);
        go('super', "Nueva clave de $o[nombre]: $pw");
      }
      go('super');
    }
    $ls = q(
      'SELECT n.*,(SELECT COUNT(*) FROM usuarios WHERE negocio_id=n.id) us,(SELECT COUNT(*) FROM productos WHERE negocio_id=n.id AND activo=1) np,(SELECT COUNT(*) FROM ventas WHERE negocio_id=n.id AND anulada=0) nv,(SELECT COALESCE(SUM(total),0) FROM ventas WHERE negocio_id=n.id AND anulada=0) tot,(SELECT MAX(creado) FROM ventas WHERE negocio_id=n.id) ul FROM negocios n ORDER BY n.id DESC',
    )->fetchAll();
    top('Superadmin');
    ?><h1>Superadmin</h1>
<div class=kpis>
<div class=card>
<small>Negocios</small>
<b><?= count(
  $ls,
) ?></b>
</div>
<div class=card>
<small>Ventas registradas</small>
<b><?= array_sum(
  array_column($ls, 'nv'),
) ?></b>
</div>
<div class=card>
<small>Total vendido</small>
<b><?= m(
  array_sum(array_column($ls, 'tot')),
) ?></b>
</div>
</div>
<div class=sc>
<table>
<tr>
<th>Negocio<th>Tipo<th>Usuarios<th>Productos<th>Ventas<th>Total<th>Última venta<th><?php foreach (
  $ls
  as $r
) {
  echo '<tr><td>' .
    e($r['nombre']) .
    '<td>' .
    e($r['tipo']) .
    "<td>$r[us]<td>$r[np]<td>$r[nv]<td>" .
    m($r['tot']) .
    '<td>' .
    e($r['ul'] ?? '—') .
    "<td><form method=post class=in onsubmit=\"return confirm('¿Generar nueva clave del dueño?')\">" .
    f() .
    "<input type=hidden name=nv value=$r[id]><button class='btn sec sm'>Nueva clave</button></form>";
} ?></table>
</div><?php bot();
  case 'olvide':

    if (post()) {
      if (q('SELECT id FROM usuarios WHERE email=?', [$_POST['email']])->fetch()) {
        $tk = bin2hex(random_bytes(20));
        q(
          'INSERT INTO recuperaciones(email,token,expira)VALUES(?,?,DATE_ADD(NOW(),INTERVAL 1 HOUR))',
          [$_POST['email'], hash('sha256', $tk)],
        );
        $lk =
          (isset($_SERVER['HTTPS']) ? 'https' : 'http') .
          '://' .
          $_SERVER['HTTP_HOST'] .
          $_SERVER['SCRIPT_NAME'] .
          "?p=reset&t=$tk";
        @mail(
          $_POST['email'],
          'Recuperar clave - SAO',
          "Entra aquí para crear una clave nueva (vence en 1 hora):\n$lk",
        );
      }
      go('login', 'Si el correo existe, te enviamos un enlace');
    }
    top('Recuperar');
    ?><form method=post class=card style="max-width:420px;margin:40px auto">
<h1>Recuperar clave</h1><?= f() ?><input name=email type=email placeholder=Correo required>
<button class="btn big">Enviar enlace</button>
<p>
<a href="?p=login">Volver</a>
</p>
</form><?php bot();
  case 'reset':

    $tk = $_GET['t'] ?? ($_POST['t'] ?? '');
    $r = q('SELECT * FROM recuperaciones WHERE token=? AND expira>NOW()', [
      hash('sha256', $tk),
    ])->fetch();
    if (!$r) {
      go('login', 'Enlace vencido o inválido');
    }
    if (post()) {
      if (strlen($_POST['clave']) < 6) {
        go('reset&t=' . urlencode($tk), 'Mínimo 6 caracteres');
      }
      q('UPDATE usuarios SET clave=? WHERE email=?', [
        password_hash($_POST['clave'], PASSWORD_DEFAULT),
        $r['email'],
      ]);
      q('DELETE FROM recuperaciones WHERE email=?', [$r['email']]);
      go('login', 'Clave actualizada, ya puedes entrar');
    }
    top('Nueva clave');
    ?><form method=post class=card style="max-width:420px;margin:40px auto">
<h1>Nueva clave</h1><?= f() ?><input type=hidden name=t value="<?= e(
  $tk,
) ?>">
<input name=clave type=password placeholder="Clave (mínimo 6)" required>
<button class="btn big">Guardar</button>
</form><?php bot();
  case 'logo':
    $lf = __DIR__ . '/logos/' . nid() . '.img';
    if (is_file($lf)) {
      header('Content-Type: ' . getimagesize($lf)['mime']);
      header('Cache-Control: max-age=300');
      readfile($lf);
    }
    exit();
  case 'ajustes':

    if (post()) {
      if (isset($_POST['vnombre'])) {
        if (
          strlen($_POST['vclave']) < 6 ||
          q('SELECT id FROM usuarios WHERE email=?', [$_POST['vemail']])->fetch()
        ) {
          go('ajustes', 'Correo repetido o clave muy corta');
        }
        q("INSERT INTO usuarios(negocio_id,nombre,email,clave,rol)VALUES(?,?,?,?,'vendedor')", [
          nid(),
          $_POST['vnombre'],
          $_POST['vemail'],
          password_hash($_POST['vclave'], PASSWORD_DEFAULT),
        ]);
        go('ajustes', 'Vendedor creado');
      }
      if (isset($_POST['rst'])) {
        $x = q('SELECT id,nombre FROM usuarios WHERE id=? AND negocio_id=? AND id<>?', [
          $_POST['rst'],
          nid(),
          u()['id'],
        ])->fetch();
        if ($x) {
          $pw = substr(bin2hex(random_bytes(4)), 0, 8);
          q('UPDATE usuarios SET clave=? WHERE id=?', [
            password_hash($pw, PASSWORD_DEFAULT),
            $x['id'],
          ]);
          go('ajustes', "Nueva clave de $x[nombre]: $pw");
        }
        go('ajustes');
      }
      if (isset($_POST['restaurar'])) {
        $j = json_decode(@file_get_contents($_FILES['bk']['tmp_name'] ?? ''), true);
        if (!$j || !isset($j['productos']) || empty($_POST['ok'])) {
          go('ajustes', 'Archivo inválido o falta confirmar');
        }
        $W = [
          'productos' => 'nombre,codigo,precio,costo,stock,minimo,activo,iva,vence',
          'clientes' => 'nombre,telefono,deuda',
          'ventas' => 'cliente_id,usuario_id,total,descuento,metodo,anulada,creado',
          'compras' => 'proveedor,producto_id,cantidad,costo,creado',
          'pagos' => 'cliente_id,monto,creado',
          'cierres' => 'usuario_id,desde,hasta,esperado,contado',
          'gastos' => 'concepto,monto,creado',
          'venta_items' => 'venta_id,producto_id,nombre,cantidad,precio,costo,iva',
        ];
        $N = nid();
        $M = ['productos' => [], 'clientes' => [], 'ventas' => []];
        $fx = function ($t, $r) use ($W, $N, &$M, $db) {
          $r['negocio_id'] = $N;
          $r['usuario_id'] = u()['id'];
          foreach (
            ['cliente_id' => 'clientes', 'producto_id' => 'productos', 'venta_id' => 'ventas']
            as $k => $mt
          ) {
            if (array_key_exists($k, $r)) {
              $r[$k] = $M[$mt][$r[$k]] ?? null;
            }
          }
          $c = explode(',', $W[$t] . ($t == 'venta_items' ? '' : ',negocio_id'));
          q(
            "INSERT INTO $t(" .
              implode(',', $c) .
              ')VALUES(' .
              implode(',', array_fill(0, count($c), '?')) .
              ')',
            array_map(fn($k) => $r[$k] ?? null, $c),
          );
          return $db->lastInsertId();
        };
        try {
          $db->beginTransaction();
          q('DELETE FROM venta_items WHERE venta_id IN(SELECT id FROM ventas WHERE negocio_id=?)', [
            $N,
          ]);
          foreach (
            ['productos', 'clientes', 'ventas', 'compras', 'pagos', 'cierres', 'gastos']
            as $t
          ) {
            q("DELETE FROM $t WHERE negocio_id=?", [$N]);
          }
          foreach (
            ['productos', 'clientes', 'ventas', 'compras', 'pagos', 'cierres', 'gastos']
            as $t
          ) {
            foreach ($j[$t] ?? [] as $r) {
              $id = $fx($t, $r);
              if (isset($M[$t])) {
                $M[$t][$r['id']] = $id;
              }
            };
          }
          foreach ($j['venta_items'] ?? [] as $r) {
            if (isset($M['ventas'][$r['venta_id']])) {
              $fx('venta_items', $r);
            }
          }
          $db->commit();
        } catch (Exception $x) {
          if ($db->inTransaction()) {
            $db->rollBack();
          }
          go('ajustes', 'No se pudo restaurar el respaldo');
        }
        go('ajustes', 'Respaldo restaurado');
      }
      up('logo', '');
      up('qr', '-qr');
      q(
        'UPDATE negocios SET nombre=?,usa_vence=?,meta=?,fe_activa=?,fe_url=?,fe_token=? WHERE id=?',
        [
          $_POST['nombre'],
          isset($_POST['vence']) ? 1 : 0,
          n($_POST['meta']),
          isset($_POST['fe']) ? 1 : 0,
          $_POST['url'],
          $_POST['token'],
          nid(),
        ],
      );
      go('ajustes', 'Guardado');
    }
    $n = neg();
    $us = q('SELECT id,nombre,email,rol FROM usuarios WHERE negocio_id=?', [nid()])->fetchAll();
    top('Ajustes');
    ?><h1>Ajustes</h1>
 <form method=post enctype="multipart/form-data" class=card><?= f() ?><h3>Negocio</h3>
<input name=nombre value="<?= e(
  $n['nombre'],
) ?>" required>
<label class=mut>Meta de ventas diaria</label>
<input name=meta class=mn inputmode=numeric value="<?= e(
  $n['meta'],
) ?>">
<label class=chk>
<input type=checkbox name=vence <?= $n['usa_vence']
  ? 'checked'
  : '' ?>> Manejar fechas de vencimiento</label>
<label class=mut>Logo (PNG, JPG o WEBP)</label>
<input type=file name=logo accept="image/*">
<label class=mut>QR de pago (Nequi / Daviplata)</label>
<input type=file name=qr accept="image/*">
<h3 style="margin-top:14px">Facturación electrónica (proveedor)</h3>
<label class=chk>
<input type=checkbox name=fe <?= $n[
  'fe_activa'
]
  ? 'checked'
  : '' ?>> Activar envío al proveedor</label>
<input name=url placeholder="URL de la API del proveedor" value="<?= e(
  $n['fe_url'],
) ?>">
<input name=token placeholder="Token / API key" value="<?= e(
  $n['fe_token'],
) ?>">
<button class=btn>Guardar</button>
</form>
 <form method=post class=card><?= f() ?><h3>Agregar vendedor</h3>
<input name=vnombre placeholder=Nombre required>
<input name=vemail type=email placeholder=Correo required>
<input name=vclave type=password placeholder="Clave (mínimo 6)" required>
<button class=btn>Crear</button>
</form>
 <div class=sc>
<table>
<tr>
<th>Usuario<th>Rol<th><?php foreach ($us as $x) {
   echo '<tr><td>' .
     e($x['nombre']) .
     '<br><small class=mut>' .
     e($x['email']) .
     "</small><td>$x[rol]<td>" .
     ($x['id'] != u()['id']
       ? "<form method=post class=in onsubmit=\"return confirm('¿Generar nueva clave?')\">" .
         f() .
         "<input type=hidden name=rst value=$x[id]><button class='btn sec sm'>Nueva clave</button></form>"
       : '');
 } ?></table>
</div>
 <form method=post enctype="multipart/form-data" class=card><?= f() ?><input type=hidden name=restaurar value=1>
<h3>Restaurar respaldo</h3>
<input type=file name=bk accept=".json" required>
<label class=chk>
<input type=checkbox name=ok> Entiendo que se reemplazan los datos actuales</label>
<button class="btn no">Restaurar</button>
</form>
<?php bot();
  default:
    go('vender');
}