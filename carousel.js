const slides=window.CAROUSEL_SLIDES;let index=0,stream,busy=false,active=false;
const photo=document.getElementById('photo'),statusEl=document.getElementById('status'),start=document.getElementById('start'),video=document.getElementById('camera');
const dots=document.getElementById('dots');
slides.forEach((slide,i)=>{const b=document.createElement('button');b.setAttribute('aria-label','Show image '+(i+1)+': '+slide.title);b.onclick=()=>show(i);dots.append(b);const preload=new Image();preload.src=slide.src;});
function show(n){index=(n+slides.length)%slides.length;const item=slides[index];document.getElementById('missing').hidden=true;photo.src=item.src;photo.alt=item.alt;document.getElementById('title').textContent=item.title;document.getElementById('count').textContent=(index+1)+' / '+slides.length;[...dots.children].forEach((b,i)=>b.setAttribute('aria-current',String(i===index)));}
photo.onerror=()=>document.getElementById('missing').hidden=false;photo.onload=()=>document.getElementById('missing').hidden=true;
document.getElementById('prev').onclick=()=>show(index-1);document.getElementById('next').onclick=()=>show(index+1);
const carousel=document.getElementById('carousel');let gesture=null;
carousel.addEventListener('pointerdown',e=>{if(e.pointerType==='mouse'&&e.button!==0)return;gesture={x:e.clientX,y:e.clientY};carousel.setPointerCapture(e.pointerId);});
carousel.addEventListener('pointerup',e=>{if(!gesture)return;const dx=e.clientX-gesture.x,dy=e.clientY-gesture.y;gesture=null;if(Math.abs(dx)>35&&Math.abs(dx)>Math.abs(dy)*1.2)show(index+(dx<0?1:-1));});
carousel.addEventListener('pointercancel',()=>gesture=null);
carousel.addEventListener('keydown',e=>{if(e.key==='ArrowLeft'||e.key==='ArrowRight'){e.preventDefault();show(index+(e.key==='ArrowRight'?1:-1));}});
let cover=false;document.getElementById('fit').onclick=e=>{cover=!cover;photo.style.objectFit=cover?'cover':'contain';e.target.textContent=cover?'Image: fill':'Image: fit';};
document.getElementById('full').onclick=async()=>{try{if(document.fullscreenElement)await document.exitFullscreen();else if(document.documentElement.requestFullscreen)await document.documentElement.requestFullscreen();else statusEl.textContent='Full-window preview active; this browser controls its address bar.';}catch(e){statusEl.textContent='Fullscreen unavailable. Continue in full-window mode.';}};
let model=navigator.userAgentData?.platform||(/iPhone/.test(navigator.userAgent)?'iPhone':/Android/.test(navigator.userAgent)?'Android':'Browser');
function resize(){const w=window.visualViewport?.width||innerWidth,h=window.visualViewport?.height||innerHeight;document.documentElement.style.setProperty('--h',h+'px');document.getElementById('device').textContent=model+' · '+Math.round(w)+' × '+Math.round(h)+' CSS px · '+devicePixelRatio+'×';}
async function identify(){try{const data=await navigator.userAgentData?.getHighEntropyValues(['model']);if(data?.model)model+=' '+data.model;}catch(e){}resize();}
window.addEventListener('resize',resize);window.visualViewport?.addEventListener('resize',resize);
async function camera(){if(busy||active)return;busy=true;start.hidden=true;try{if(!isSecureContext||!navigator.mediaDevices)throw Error('Use HTTPS hosting for the camera. Image browsing still works.');statusEl.textContent='Allow camera access…';stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'},width:{ideal:1280},height:{ideal:720}},audio:false});video.srcObject=stream;await video.play();active=true;statusEl.textContent='Live camera · swipe the image';}catch(e){stream?.getTracks().forEach(t=>t.stop());statusEl.textContent=e.name==='NotAllowedError'?'Camera permission needed. You can still browse all five images.':e.message;start.hidden=false;}busy=false;}
start.onclick=camera;window.addEventListener('pagehide',()=>stream?.getTracks().forEach(t=>t.stop()));window.addEventListener('pageshow',e=>{if(e.persisted){active=false;camera();}});
show(0);identify();camera();

// Avoid animation work while the page is hidden.
document.addEventListener('visibilitychange',()=>document.body.classList.toggle('motion-paused',document.hidden));
