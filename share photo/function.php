<?php
//log
ini_set('log_errors','on');
//log path
ini_set('error_log','php.log');

//debug

//debug     
$debug_flg = true;
//debug log 

function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}

//expand expire session limit
session_save_path("/var/tmp/");

ini_set('session.gc_maxlifetime', 60*60*24*30);

ini_set('session.cookie_lifetime ', 60*60*24*30);

session_start();

session_regenerate_id();

function debugLogStart(){
    debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
    debug('セッションID：'.session_id());
    debug('セッション変数の中身：'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
        debug( 'ログイン期限日時タイムスタンプ：'.( $_SESSION['login_date'] + $_SESSION['login_limit'] ) );
    }
}

define('MSG01','入力必須です');
define('MSG02', 'Emailの形式で入力してください');
define('MSG03','パスワード（再入力）が合っていません');
define('MSG04','半角英数字のみご利用いただけます');
define('MSG05','6文字以上で入力してください');
define('MSG06','256文字以内で入力してください');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08', 'そのEmailは既に登録されています');
define('MSG09', 'メールアドレスまたはパスワードが違います');
define('MSG10', '電話番号の形式が違います');
define('MSG11', '郵便番号の形式が違います');
define('MSG12', '古いパスワードが違います');
define('MSG13', '古いパスワードと同じです');
define('MSG14', '文字で入力してください');
define('MSG15', '正しくありません');
define('MSG16', '有効期限が切れています');
define('MSG17', '半角数字のみご利用いただけます');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');
define('SUC04', '登録しました');
define('SUC05', '購入しました！相手と連絡を取りましょう！');

$err_msg = array();

//validation function

function validRequired($str, $key){
    if($str === ''){
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}

//validation function emailform
function validEmail($str, $key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG02;
    }

}

//validation for email duplication
function validEmailDup($email){
    global $err_msg;

    try {
        //connect DB
        $dbh = dbConnect();
        //creat SQL
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        //excute
        $stmt = queryPost($dbh, $sql, $data);
        //get query result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!empty(array_shift($result))){
            $err_msg['email'] = MSG08;
        }

    
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

//validation Match
function validMatch($str1, $str2, $key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG03;
    }

}
//validation function minimum strengs check
function validMinLen($str, $key, $min = 6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}

//validation max strengs check
function validMaxLen($str, $key, $max = 256){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}
//validation function half
function validHalf($str, $key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}
//phone number form check
function validTel($str, $key){
    if(!preg_match("/0\d{1,4}\d{1,4}\d{4}/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG10;
    }

}

//post number
function validZip($str, $key){
    if(!preg_match("/^\d{7}$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG11;
    }
}

function validNumber($str, $key){
    if(!preg_match("/^[0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG17;
    }
}

function validLength($str, $key, $len = 8){
    if( mb_strlen($str) !== $len ){
        global $err_msg;
        $err_msg[$key] = $len . MSG14;
    }
}

function validPass($str, $key){
    validHalf($str, $key);
    validMaxLen($str, $key);
    validMinLen($str, $key);
}

function validSelect($str, $key){
    if(!preg_match("/^[0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG15;
    }
}

//error message
function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}

//database
//function for db connecting
function isLogin(){
    if( !empty($_SESSION['login_date']) ){
        debug('logined user');

        if( ($_SESSION['login_date'] + $_SESSION['login_limit']) < time()){
            debug('login expiration is over');

            session_destroy();
            return false;
        }else{
            debug('within login expiration');
            return true;
        }
    }else{
        debug('unlogined user');
        return false;
    }
}
function dbConnect(){
    $dsn = 'mysql:dbname=share photo;host=localhost;charset=utf8';
    //ready for db connecting
    $user = 'root';
    $password = 'root';
    $options = array(

        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    //create PDOobject
    $dbh = new PDO($dsn, $user, $password, $options);
    
    return $dbh;
}
function queryPost($dbh, $sql, $data){

    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute($data)){
        debug('query failed');
        debug('failed SQL:'.print_r($stmt,true));
        $err_msg['common'] = MSG07;
        return 0;
    }
    debug('query success');
    return $stmt;
}
function getUser($u_id){
    debug('get user info');

    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM users  WHERE id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
        

    } catch (Exception $e) {
        error_log('error:' . $e->getMessage());
    }
//  return $stmt->fetch(PDO::FETCH_ASSOC);
}


function getProduct($u_id, $p_id){
    debug('商品情報を取得します。');
    debug('ユーザーID：'.$u_id);
    debug('商品ID：'.$p_id);

    try {

        $dbh = dbConnect();
        
        $sql = 'SELECT * FROM product WHERE user_id = :u_id AND id = :p_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id, ':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        } 
    } catch (Exception $e) {
        error_log('error:' . $e->getMessage());
    }
}
function getProductList($currentMinNum = 1, $span = 12){
    debug('写真情報を取得します');

    try {
        $dbh = dbConnect();
        $sql = 'SELECT id FROM product';
        $data = array();

        $stmt = queryPost($dbh, $sql, $data);
        //そうレコード数
        $rst['total'] = $stmt->rowCount(); 
        //総ページ数
        $rst['total_page'] = ceil($rst['total']/$span);
        if(!$stmt){
            return false;
        }

        $sql = 'SELECT * FROM product';

        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array();
        debug('SQL:'.$sql);

        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){

            $rst['data'] = $stmt->fetchAll();
            return $rst;
        }else{
            return false;
        }


    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }
}
function getProductOne($p_id){
    debug('写真情報を取得します。');
    debug('写真ID');
    
    try {
        $dbh = dbConnect();
        $sql = 'SELECT p.id , p.name , p.comment, p.pic1, p.pic2, p.pic3, p.user_id, p.create_date, p.update_date, c.name AS category
                FROM product AS p LEFT JOIN category AS c ON p.category_id = c.id WHERE p.id = :p_id AND p.delete_flg = 0 AND c.delete_flg = 0';
        $data = array(':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
    }

}
function getMyProducts($u_id){
    debug('get product info');
    debug('user id:'.$u_id);

    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM product WHERE user_id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    } catch (Exception $e) {
        error_log('error:' . $e->getMessage());
    }
}



function getCategory(){
    debug('get category info');

    try {

        $dbh = dbConnect();
        $sql = 'SELECT * FROM category';
        $data = array();
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    } catch (Exception $e) {
        error_log('error:' . $e->getMessage());
    }
}

//sanitize
function sanitize($str){
    return htmlspecialchars($str,ENT_QUOTES);
}


function getFormData($str){
    global $dbFormData;

    if(!empty($dbFormData)){

        if(!empty($err_msg[$str])){

            if(isset($_POST[$str])){
                return sanitize($_POST[$str]);

            }else{

                return sanitize($dbFormData[$str]);
            }
        }else{
            if(isset($_POST[$str]) && $_POST[$str] !== $dbFormData[$str]){
                return sanitize($_POST[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
            
        }
    }else{
        if(isset($_POST[$str])){
            return sanitize($_POST[$str]);
        }
    }
}
function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
}

function uploadImg($file, $key){
    debug('image upload process start');
    debug('FILE info：'.print_r($file,true));

    if (isset($file['error']) && is_int($file['error'])) {
        try {
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('you need to chose file');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('check file size');
                default:
                    throw new RuntimeException('other error');
            }

            $type = @exif_imagetype($file['tmp_name']);
            if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                throw new RuntimeException('image form is not available');
            }

            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);

            if (!move_uploaded_file($file['tmp_name'], $path)) {
                throw new RuntimeException('error when keeping file');
            }

            chmod($path, 0644);

            debug('ファイルは正常にアップロードされました');
            debug('ファイルパス：'.$path);
            return $path;
        } catch (RuntimeException $e) {
            debug($e->getMessage());
                    global $err_msg;
                    $err_msg[$key] = $e->getMessage();
                }

        }


}

function pagination( $currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){

    if( $currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
        $minPageNum = $currentPageNum - 4;
        $maxPageNum = $currentPageNum;

    }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum >= $pageColNum){
        $minPageNum = $currentPageNum - 3;
        $maxPageNum = $currentPageNum +1;


    }elseif( $currentPageNum == 2 && $totalPageNum >= $pageColNum){
        $minPageNum = $currentPageNum - 1;
        $maxPageNum = $currentPageNum +3;

    }elseif( $currentPageNum == 1 && $totalPageNum >= $pageColNum){
        $minPageNum = $currentPageNum;
        $maxPageNum = 5;

    }elseif($totalPageNum < $pageColNum){
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
    }else{
        $minPageNum = $currentPageNum - 2;
        $maxPageNum = $currentPageNum + 2;
    }

    echo '<div class="pagination">';
        echo '<ul class="pagination-list">';
            if($currentPageNum != 1){
                echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
            }
            for($i = $minPageNum; $i <= $maxPageNum; $i++){
                echo '<li class="list-item ';
                if($currentPageNum == $i ){ echo 'active'; }
                echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
            }
            if($currentPageNum != $maxPageNum){
                echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
            }       
        echo '</ul>';
    echo '</div>';
}
function showImg($path){
    if(empty($path)){
        return 'img/sample-img.png';
    }else{
        return $path;
    }
}
function appendGetParam($arr_del_key = array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val){
            if(!in_array($key,$arr_del_key,true)){
                $str .= $key.'='.$val.'&';
            }
        }
        $str = mb_substr($str, 0, -1, "UTF-8");
        return $str;
    }
}