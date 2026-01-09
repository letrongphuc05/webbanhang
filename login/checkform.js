function checkform(){
    var loginname=document.getElementById("loginname")
    var phone = document.getElementById("phone")
    var email = document.getElementById("email")
    var password = document.getElementById("password")
    var checkpassword=document.getElementById("checkpassword")
    

    if(loginname.value !=""){

    }else{
        alert("Vui lòng nhập tên đăng nhập")
        loginname.focus();
        return false;
    }
    if(phone.value !=""){

    }else{
        alert("Vui lòng nhập số điện thoại")
        phone.focus();
        return false;
    }
    if(email.value !=""){

    }else{
        alert("Vui lòng nhập số điện thoại")
        email.focus();
        return false;
    }
    if(password.value != ""){

    }else{
        alert("Vui lòng nhập mật khẩu")
        password.focus();
        return false;
    }
    if(checkpassword.value != ""){
        if(checkpassword.value != password.value){
            alert("Mật khẩu không trùng khớp")
            checkpassword.focus();
            return false;
        }
    }else{
        alert("Vui lòng nhập lại mật khẩu")
        checkpassword.focus();
        return false;
    } 
    return true;
}