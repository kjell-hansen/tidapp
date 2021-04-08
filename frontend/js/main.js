window.onload(()=>{
   fetch('/tidapp/backend/setions.php'
           ).then(response => {
               if(response.ok){
                   return response.text;
               } else {
                   throw Error ({status:response.status, text:response.json()});
               }
           }).then(html =>{
             document.getElementsByTagName('main')[0].innerHTML=html;  
           }).catch(err =>{
               alert(err.status + '\n' + err.text);
           });
           
});