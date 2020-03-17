<?php
require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　プロフィール編集ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');


$dbFormData = getUser($_SESSION['user_id']);

debug('user informatin:'.print_r($dbFormData, true));

if(!empty($_POST)){
    debug('posted');
    debug('post info:'.print_r($_POST,true));

    $username = $_POST['username'];
    $tel = $_POST['tel'];
    $zip = (!empty($_POST['zip'])) ? $_POST['zip'] : 0;
    $addr = $_POST['addr'];
    $age = $_POST['age'];
    $email = $_POST['email'];

    if($dbFormData['username'] !== $username){
        validMaxLen($username, 'username');
    }
    if($dbFormData['tel'] !== $tel){
        validTel($tel, 'tel');
    }
    if($dbFormData['addr'] !== addr){
        
        validMaxLen($addr, 'addr');
    }
    if((int)$dbFormData['zip'] !== $zip){
        validZip($zip, 'zip');
    }
    if($dbFormData['age']!== $age){
        validMaxLen($age, 'age');

        validNumber($age, 'age');
    }
    if($dbFormData['email'] !== $email){
        validMaxLen($email, 'email');
        if(empty($err_msg['email'])){
            validEmailDup($email);
        }

        validEmail($email, 'email');
        validRequired($email, 'email');
    }
    if(empty($err_msg)){
        debug('validation clear');

        try {

            $dbh = dbConnect();
            $sql = 'UPDATE users SET username = :u_name, tel = :tel, zip = :zip, addr = :addr, age = :age, email = :email WHERE id = :u_id';
            $data = array(':u_name' => $username, ':tel'=> $tel, ':zip' => $zip, ':addr' => $addr, ':age' => $age, 'email' => $email, ':u_id' => $dbFormData['id']);
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                debug('query success');
                debug('to mypage');
                header("Location:mypage.php");
            }else{
                debug('query failed');
                $err_msg['common'] = MSG08;
            }
        } catch (Exception $e) {
            error_log('error:'. $ep->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('screen display process is done');
?>
<?php
$siteTitle = 'profile edit';
require('head.php')
?>
    <div id="contents" class="site-width">
        <h1 class="page-title">prof edit</h1>

        <section id="main">
            <div class="form-container">
                <form action="" method="post" class="form">
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['common'])) echo $err_msg['common'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['username'])) echo 'err;'?>">
                    名前
                        <input type="text" name="username" value="<?php echo getFormData('username');?>">

                    </label>
                    <div class="area-msg">
                        <?php 
                        if(!empty($err_msg['username'])) echo $err_msg['username'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['tel'])) echo 'err'; ?>">
                    TEL <span style="font-size:12px; margin-left:5px;">*ハイフンなしで入力ください</span>
                    <input type="text" name="tel" value="<?php echo getFormData('tel');?>">
                    </label>
                    <div class="area-msg">
                        <?php 
                        if(!empty($err_msg['tel'])) echo $err_msg['tel'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['zip'])) echo 'err';?>">
                        郵便番号　<span style="font-size:12px; margin-left:5px;">＊ハイフン無しで入力してください</span>
                        <input type="text" name="zip" value="<?php if(!empty(getFormData('zip')) ) {echo getFormData('zip'); }?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['zip'])) echo $err_msg['zip'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['addr'])) echo 'err';?>">
                    住所
                    <input type="text" name="addr" value="<?php echo getFormData('addr');?>">
                    </label>
                    <div class="area-msg">
                        <?php 
                        if(!empty($err_msg['addr'])) echo $err_msg['addr'];
                        ?>
                    </div>
                    <label style="text-align:left;" class="<?php if(!empty($err_msg['age'])) echo 'err'; ?>">
                    年齢
                    <input type="number" name="age" value="<?php echo getFormData('age');?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!emtpy($err_msg['age'])) echo $err_msg['age'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['email'])) echo 'err';?>">
                        Email
                        <input type="text" name="email" value="<?php echo getFormData('email');?>">

                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['email'])) echo $err_msg['email'];
                        ?>
                    </div>

                    <div class="btb-container">
                        <input type="submit" class="btn btn-mid" value="変更する">
                    </div>
                </form>
            </div>
        </section>
    <?php
    require('sidebar_mypage.php');
    ?>

    </div>
    <?php
    require('footer.php');
    ?>