<?php
//loading common function
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　ユーザー登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();
if(!empty($_POST)){

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_re'];
    var_dump($email);
    var_dump($pass);
    var_dump($pass_re);
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($pass_re, 'pass_re');

    if(empty($err_msg)){

        validEmail($email, 'email');
        validMaxLen($email, 'email');
        validEmailDup($email);

        validHalf($pass, 'pass');
        validMaxLen($pass, 'pass');
        validMinLen($pass, 'pass');

        validMaxLen($pass_re, 'pass_re');
        validMinLen($pass_re, 'pass_re');

        if(empty($err_msg)){
            validMatch($pass, $pass_re, 'pass_re');

            if(empty($err_msg)){

                try {
                    $dbh = dbConnect();
                    $sql = 'INSERT INTO users (email,password,login_time,create_date) VALUES(:email,:pass,:login_time,:create_date)';
                    $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT),
                                ':login_time' => date('Y-m-d H:i:s'),
                                ':create_date' => date('Y-m-d H:i:s'));
                    $stmt = queryPost($dbh, $sql, $data);
                    var_dump($dbh, $sql, $data);
                    header('Location:index.php');
                    
                        
                    
                    
                } catch (Exception $e) {
                    error_log('エラー発生:' . $e->getMessage());
                    $err_msg['common'] = MSG07;
                }
                
            }
        }
    }
}
?>
<?php
    $siteTitle = 'user resistration';
    require('head.php');
?>
    <body class="page-signup page-1colum">
        
    </body>
    <?php
        require('header.php');
    ?>
            <!-- メインコンテンツ -->
        <div id="contents" class="site-width">

        <!-- Main -->
            <section id="main" >

                <div class="form-container">

                    <form action="" method="post" class="form">
                    <h2 class="title">ユーザーとうろく</h2>
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
                        パスワード <span style="font-size:12px">※英数字６文字以上</span>
                        <input type="password" name="pass" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
                    </label>
                    <div class="area-msg">
                        <?php 
                        if(!empty($err_msg['pass'])) echo $err_msg['pass'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
                        パスワード（再入力）
                        <input type="password" name="pass_re" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
                    </label>
                    <div class="area-msg">
                        <?php 
                        if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re'];
                        ?>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="とうろくする">
                    </div>
                    </form>
                </div>
                

            </section>

        </div>

        <!-- footer -->
        <?php
            require('footer.php');
        ?>