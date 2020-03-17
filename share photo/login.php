<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ログインページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

//login authentication
require('auth.php');

//login display process

//if posted
if(!empty($_POST)){
    debug('posted');

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    validEmail($email, 'email');
    validMaxLen($email, 'email');

    validHalf($pass, 'pass');
    validMaxLen($pass, 'pass');
    validMinLen($pass, 'pass');

    validRequired($email, 'email');
    validRequired($pass, 'pass');

    if(empty($err_msg)){
        debug('validation is done!');

        try {
            //exception process
            $dbh = dbConnect();
            //create sql
            $sql = 'SELECT password,id  FROM users WHERE email = :email';
            $data = array(':email' => $email);
            //execute
            $stmt = queryPost($dbh, $sql, $data);
            //acquire  data of queryresult
            $result = $stmt->fetch(PDO::FETCH_ASSOC);   

            debug('content of query result:'.print_r($result,true));

            //password match
            if(!empty($result) && password_verify($pass, array_shift($result))){
                debug('password matched');

                //login expiration limit
                $sesLimit = 60*60;

                $_SESSION['login_date'] = time();

                if($pass_save){
                    debug('ログイン保持にチェックがあります。');
                    $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                }else{
                    debug('ログイン保持にチェックはありせん。');

                    $_SESSION['login_limit'] = $sesLimit;
                }

                $_SESSION['user_id'] = $result['id'];

                debug('セッション変数の中身：'.print_r($_SESSION,true));
                debug('マイページへ遷移します。');
                header("Location:petDetail.php");
            }else{
                debug('password unmatch');
                $err_msg['common'] = MSG09;
            }




        
        } catch (Exception $e) {
            error_log('エラー発生:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }

}
debug('画面表示処理終了<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<?php
$siteTitle = 'ログイン';
require('head.php');
?>

<body class="page-login page-1colum">
    <?php
        require('header.php');
    ?>

    <div id="contents" class="site-width">
        <section id="main" >
            <div class="form-container">
                <form action="" method="post" class="form">
                    <h2 class="title">ログイン</h2>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['common'])) echo $err_msg['common'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
                    メールアドレス
                    <input type="text" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
                    </label>
                    <div class="area-msg">
                        <?php 
                        if(!empty($err_msg['email'])) echo $err_msg['email'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
                        パスワード
                        <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
                    </label>
                    <div class="area-msg">
                        <?php 
                        if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                        ?>
                    </div>
                    <div class="btn-container">
                    <input type="submit" class="btn btn-mid" value="ログイン">
                    </div>
                </form>
            </div>

        </section>

    </div>
<?php
require('footer.php');
?>