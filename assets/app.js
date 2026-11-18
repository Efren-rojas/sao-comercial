const $=s=>document.querySelector(s),fm=n=>'$'+Number(n).toLocaleString('es-CO'),esc=s=>String(s).replace(/[&<>"']/g,c=>'&#'+c.charCodeAt(0)+';');
if('serviceWorker'in navigator)navigator.serviceWorker.register('sw.js');
if($('#grid')){const P=window.PRODUCTS||[],cart={},g=$('#grid');
const msg=(t,e)=>{$('#msg').textContent=t;$('#msg').style.color=e?'var(--er)':'var(--ok)'};
const draw=()=>{const q=$('#bq').value.toLowerCase();g.innerHTML=P.filter(p=>!q||p.nombre.toLowerCase().includes(q)||p.codigo==q).map(p=>`<button class=pr data-i="${p.id}"><b>${esc(p.nombre)}</b><span>${fm(p.precio)}</span><small class="${p.stock<=p.minimo?'low':''}">${p.stock} disp.</small></button>`).join('')||'<p class=mut>Sin resultados</p>'};
const rc=()=>{let t=0;$('#cart').innerHTML=Object.entries(cart).map(([id,c])=>{const p=P.find(x=>x.id==id);t+=p.precio*c;return`<div class=ln><span>${esc(p.nombre)}</span><div><button data-m="${id}">−</button> ${c} <button data-a="${id}">+</button></div><b>${fm(p.precio*c)}</b></div>`}).join('')||'<p class=mut>Toca un producto para empezar</p>';$('#tot').textContent=fm(Math.max(0,t-(+$('#dsc').value||0)))};
const add=id=>{const p=P.find(x=>x.id==id);if(!p)return;if((cart[id]||0)>=p.stock)return msg('No hay más de '+p.nombre,1);cart[id]=(cart[id]||0)+1;rc()};
const post=b=>fetch('index.php?p=venta',{method:'POST',headers:{'X-CSRF':CSRF},body:JSON.stringify(b)}).then(r=>r.json());
const sync=async()=>{const q=JSON.parse(localStorage.q||'[]'),rest=[];for(const b of q){try{const r=await post(b);if(r.err)msg('Venta pendiente rechazada: '+r.err,1)}catch(_){rest.push(b)}}localStorage.q=JSON.stringify(rest);$('#pend').textContent=rest.length?rest.length+' venta(s) sin enviar (sin internet)':''};
g.onclick=e=>{const b=e.target.closest('.pr');if(b)add(b.dataset.i)};
$('#cart').onclick=e=>{const a=e.target.dataset.a,m=e.target.dataset.m;if(a)add(a);if(m){if(--cart[m]<1)delete cart[m];rc()}};
$('#bq').oninput=draw;$('#dsc').oninput=rc;
$('#bq').onkeydown=e=>{if(e.key=='Enter'){const p=P.find(x=>x.codigo&&x.codigo==e.target.value);if(p){add(p.id);e.target.value='';draw()}}};
$('#pay').onclick=async()=>{const items=Object.entries(cart).map(([id,c])=>({id:+id,c}));if(!items.length)return msg('Agrega productos',1);const b={items,cliente:$('#cli').value,metodo:$('#met').value,desc:+$('#dsc').value||0};
try{const r=await post(b);if(r.err)return msg(r.err,1);msg('Venta registrada: '+fm(r.total))}catch(_){const q=JSON.parse(localStorage.q||'[]');q.push(b);localStorage.q=JSON.stringify(q);msg('Sin internet: venta guardada, se enviará sola')}
items.forEach(i=>P.find(p=>p.id==i.id).stock-=i.c);for(const k in cart)delete cart[k];$('#dsc').value='';draw();rc();sync()};
$('#cam').onclick=async()=>{if(!('BarcodeDetector'in window))return alert('Tu navegador no soporta escáner. Escribe el código.');const v=document.createElement('video');v.className='vid';document.body.append(v);const s=await navigator.mediaDevices.getUserMedia({video:{facingMode:'environment'}});v.srcObject=s;v.play();const d=new BarcodeDetector(),st=()=>{clearInterval(t);s.getTracks().forEach(x=>x.stop());v.remove()},t=setInterval(async()=>{const r=await d.detect(v);if(r[0]){const p=P.find(x=>x.codigo==r[0].rawValue);if(p){add(p.id);st()}}},500);v.onclick=st};
addEventListener('online',sync);draw();rc();sync()}
