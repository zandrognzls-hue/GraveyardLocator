/* Shared interactive cemetery map: zoom, rotate, pan, plot polygons. Coordinates are percentages (0-100) of the map image. */
class CemMap{
  constructor(box,o){
    Object.assign(this,{box,o,s:1,r:0,tx:0,ty:0,plots:o.plots||[],draft:[],hide:false,hl:null});
    box.innerHTML=`<div class="cm-stage"><img src="${o.img}" alt="Cemetery map" draggable="false"><svg class="cm-svg" viewBox="0 0 100 100" preserveAspectRatio="none"></svg></div>
    <div class="ctl"><button type="button" class="btn" data-a="zi" aria-label="Zoom in"><i class="fas fa-plus"></i></button><small class="z">100%</small><button type="button" class="btn" data-a="zo" aria-label="Zoom out"><i class="fas fa-minus"></i></button><button type="button" class="btn" data-a="reset" aria-label="Reset view"><i class="fas fa-sync"></i></button><button type="button" class="btn" data-a="rl" aria-label="Rotate left"><i class="fas fa-undo"></i></button><small class="rr">0°</small><button type="button" class="btn" data-a="rr" aria-label="Rotate right"><i class="fas fa-redo"></i></button></div>`;
    this.st=box.querySelector('.cm-stage');this.svg=box.querySelector('svg');
    box.querySelector('.ctl').onclick=e=>{const a=e.target.closest('button')?.dataset.a;if(!a)return;
      if(a==='zi')this.s=Math.min(10,this.s*1.25);if(a==='zo')this.s=Math.max(.5,this.s/1.25);if(a==='rl')this.r-=15;if(a==='rr')this.r+=15;if(a==='reset'){this.s=1;this.r=0;this.tx=this.ty=0}this.apply()};
    box.addEventListener('wheel',e=>{e.preventDefault();this.s=Math.min(10,Math.max(.5,this.s*(e.deltaY<0?1.1:.9)));this.apply()},{passive:false});
    let d=null;
    box.addEventListener('pointerdown',e=>{if(e.target.closest('.ctl'))return;d={x:e.clientX,y:e.clientY,tx:this.tx,ty:this.ty,t:e.target,m:false}});
    window.addEventListener('pointermove',e=>{
      if(d){const dx=e.clientX-d.x,dy=e.clientY-d.y;if(d.m||Math.hypot(dx,dy)>5){d.m=true;this.tx=d.tx+dx;this.ty=d.ty+dy;this.apply()}}
      else if(o.onMove&&box.contains(e.target)){const p=this.pt(e);o.onMove(p)}});
    window.addEventListener('pointerup',e=>{if(!d)return;const w=d;d=null;if(w.m)return;
      const id=w.t.dataset&&w.t.dataset.id;
      if(id&&o.onPlot&&!o.onPoint){o.onPlot(this.plots.find(p=>p.id==id));return}
      if(o.onPoint){const p=this.pt(e);if(p)o.onPoint(p)}});
    this.apply();this.render();
  }
  pt(e){const m=this.svg.getScreenCTM();if(!m)return null;const p=new DOMPoint(e.clientX,e.clientY).matrixTransform(m.inverse());
    return p.x<0||p.x>100||p.y<0||p.y>100?null:{x:+p.x.toFixed(2),y:+p.y.toFixed(2)}}
  apply(){this.st.style.transform=`translate(-50%,-50%) translate(${this.tx}px,${this.ty}px) rotate(${this.r}deg) scale(${this.s})`;
    this.box.querySelector('.z').textContent=Math.round(this.s*100)+'%';this.box.querySelector('.rr').textContent=((this.r%360+360)%360)+'°'}
  color(s){return {available:'#4caf50',reserved:'#ff9800',occupied:'#f44336'}[s]||'#9e9e9e'}
  poly(p){try{return JSON.parse(p.map_polygon)||[]}catch(e){return[]}}
  render(){let h='';
    if(!this.hide)for(const p of this.plots){const pts=this.poly(p);if(pts.length<3)continue;
      h+=`<polygon data-id="${p.id}" class="${this.hl==p.id?'hl':''}" points="${pts.map(q=>q.join(',')).join(' ')}" fill="${this.color(p.status)}"><title>${p.section}-${p.plot_no} (${p.status})</title></polygon>`}
    if(this.draft.length)h+=`<polygon class="draft" style="pointer-events:none" points="${this.draft.map(q=>q.join(',')).join(' ')}"/>`+this.draft.map(q=>`<circle cx="${q[0]}" cy="${q[1]}" r=".35" fill="#0d47a1" style="pointer-events:none"/>`).join('');
    this.svg.innerHTML=h}
  focus(id){const p=this.plots.find(x=>x.id==id);if(!p)return;const pts=this.poly(p);if(!pts.length)return;
    const cx=pts.reduce((a,q)=>a+q[0],0)/pts.length,cy=pts.reduce((a,q)=>a+q[1],0)/pts.length;
    const W=this.st.offsetWidth,H=this.st.offsetHeight,dx=(cx-50)/100*W,dy=(cy-50)/100*H,a=this.r*Math.PI/180;this.s=5;
    this.tx=-this.s*(dx*Math.cos(a)-dy*Math.sin(a));this.ty=-this.s*(dx*Math.sin(a)+dy*Math.cos(a));this.hl=id;this.apply();this.render()}
}
