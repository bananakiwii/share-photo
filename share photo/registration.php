<?php
//require('function.php');
require('function.php');
//if posted
if(!empty($_POST)){
//plug the user //infomation into variable

$email =$_POST['email'];
$pass = $_POST['pass'];
$pass_re = $_POST['pass_re'];
//check the unput
validRequired($email, 'email');
validRequired($pass, 'pass');
validRequired($pass_re, 'pass_re');

//if err_msg is empty
if(empty($email)){
//email form check
//email max stlengs check
//email duplicatiobn check
//password validHalf check
//password MaxLen
//password MinLen
//re_password MaxLen
//re_password M
}  



//?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>user registragion</title>
    </head>
    <body class="page-signup page-1colum">
            <header>
                <div class="site-width">
                    <h1><a href="index.html">share photo</a></h1>
                    <nav id="top-nav">
                        <ul>
                            <li><a href="signup.php" class="btn btn-primary">user registration</a></li>
                            <li><a href="login.html">login</a></li>
                        </ul>
                    </nav>


                </div>
            </header>

            <div id="contents" class="site-width">[

                <section id="main">
                    
                    <div class="form-container">
                        <form action="" methos="post" class="form">
                            <h2 class="title">user registration</h2>
                            <div class="area-msg">
                                <?php
                                if(!empty($_msg['common'])) echo $err_msg['(common'];
                                ?>
                            </div>
                            <label cl   ass="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                                Email
                                <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
                             </label>
                             <div class="area-msg">
                                 <?php
                                 if(!empty($err_msg['email'])) echo $err_msg['email'];
                                 ?>
                             </div>
                             <label for="" class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                                password <span style="font-size:12px"></span>
                            </label>
                        </form>
                    </div>

                </section>

            </div>
        
    </body>
</html>