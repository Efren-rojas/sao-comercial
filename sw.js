const C='sao1';
self.addEventListener('install',e=>{e.waitUntil(caches.open(C).then(c=>c.addAll(['assets/app.css','assets/app.js','manifest.json'])));self.skipWaiting()});
self.addEventListener('fetch',e=>{const r=e.request,u=new URL(r.url);if(r.method!='GET'||u.origin!=location.origin)return;const ok=r.mode!='navigate'||(!u.searchParams.get('p')||u.searchParams.get('p')=='vender');
e.respondWith(fetch(r).then(x=>{if(ok){const k=x.clone();caches.open(C).then(c=>c.put(r,k))}return x}).catch(()=>caches.match(r)))});
