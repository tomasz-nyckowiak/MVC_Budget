var inputPass2 = document.getElementById('inputPassword'),
    icon = document.getElementById('icon');
 
   icon.onclick = function () {
 
     if(inputPass2.className == 'active') {
        inputPass2.setAttribute('type', 'text');
        icon.className = 'fa fa-eye';
       inputPass2.className = '';
 
     } else {
        inputPass2.setAttribute('type', 'password');
        icon.className = 'fa fa-eye-slash';
       inputPass2.className = 'active';
    }
 
   }