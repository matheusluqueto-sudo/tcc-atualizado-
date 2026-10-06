(() => {
 const open = document.getElementById('open-camera'); if (!open) return;
 const panel=document.getElementById('camera-panel'), video=document.getElementById('camera-video'), status=document.getElementById('camera-status');
 const preview=document.getElementById('selfie-preview'), data=document.getElementById('selfie-data'), option=document.getElementById('selfie-option');
 let stream=null, generation=0;
 function stop(){ generation++; stream?.getTracks().forEach(track=>track.stop()); stream=null; video.srcObject=null; panel.hidden=true; }
 function photo(source,width,height){
  if(!width || !height) throw Error('A câmera ainda não está pronta. Tente novamente.');
  const canvas=document.createElement('canvas');canvas.width=canvas.height=480;
  const size=Math.min(width,height);canvas.getContext('2d').drawImage(source,(width-size)/2,(height-size)/2,size,size,0,0,480,480);
  data.value=canvas.toDataURL('image/jpeg',.85);preview.src=data.value;option.checked=true;
  status.textContent='Foto pronta. Clique em Salvar para confirmar ou tire outra foto.';
 }
 open.addEventListener('click',async()=>{
  stop();const request=generation;
  if(!navigator.mediaDevices?.getUserMedia){status.textContent='Câmera indisponível neste endereço. Use localhost ou HTTPS, ou escolha uma foto.';return;}
  try{
   const result=await navigator.mediaDevices.getUserMedia({video:{facingMode:'user',width:640,height:640},audio:false});
   if(request!==generation){result.getTracks().forEach(track=>track.stop());return;}
   stream=result;video.srcObject=stream;panel.hidden=false;await video.play();status.textContent='Posicione seu rosto e clique em Capturar foto.';
  }catch(error){stop();status.textContent='Não foi possível abrir a câmera. Confira a permissão do navegador ou escolha uma foto.';}
 });
 document.getElementById('take-photo').addEventListener('click',()=>{try{photo(video,video.videoWidth,video.videoHeight);stop();}catch(error){status.textContent=error.message;}});
 document.getElementById('close-camera').addEventListener('click',()=>{stop();status.textContent='Câmera fechada.';});
 document.getElementById('photo-file').addEventListener('change',async event=>{
  const file=event.target.files[0];if(!file)return;
  if(!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>10*1024*1024){status.textContent='Escolha uma imagem JPG, PNG ou WebP de até 10 MB.';return;}
  stop();const url=URL.createObjectURL(file);
  try{const img=new Image();img.src=url;await img.decode();photo(img,img.naturalWidth,img.naturalHeight);}catch(error){status.textContent='Não foi possível ler esta imagem. Escolha outra foto.';}finally{URL.revokeObjectURL(url);event.target.value='';}
 });
 document.querySelectorAll('[name="foto_url"]').forEach(radio=>radio.addEventListener('change',stop));
 window.addEventListener('pagehide',stop);
 document.addEventListener('visibilitychange',()=>{if(document.hidden)stop();});
})();
