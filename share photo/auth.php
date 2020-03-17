<?php

if( !empty($_SESSION['login_date']) ){
    debug('logined user');

    if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
        debug('login expiraton is over');
        
    session_destroy();

    header("Location:login.php");

    }else{
        debug('within loging expiration');

        $_SESSION['login_date'] = time();
        debug('to mypage');
    }


}else{
    debug('unlogin user');
    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){

    }
}