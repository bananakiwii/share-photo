<?php

require('function.php');


debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('[pet詳細ページ');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart('');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$viewData = getProductOne($p_id);

if(empty($viewData)){
    error_log('エラー発生:指定ページに不正な値が入りました');
    header("Location:index.php");
}
debug('取得したDBデータ：'.print_r($viewData,true));

if(!empty($_POST['submit'])){
    debug('POST送信があります。');

    require('auth.php');

    try {
        $dbh = dbConnect();

        $sql = 'INSERT INTO board (sale_user,buy_user,product_id, create_date) VALUES (:s_uid, :b_uid, :p_id, :date)';
        $data = array(':s_uid' => $viewData['user_id'], ':b_uid' => $_SESSION['user_id'], ':p_id' => $p_id, ':date' => date('Y-m-d H:i:s'));
        $stmt = queryPost($dbh, $sql, $data);

    } catch (Exception $e) {
        error_log('エラー発生:' . $e->getMessage());
        $err_msg['common'] = MSG07;
    }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = 'ペット詳細';
require('head.php');
?>

    <body class="page-productDetail page-1colum">
        
        <style>
            .badge{
                padding: 5px 10px;
                color: white;
                background: #7acee6;
                margin-right: 10px;
                font-size: 16px;
                vertical-align: middle;
                position: relative;
                top: -4px;
            }
            #main .title{
                font-size: 28px;
                padding: 10px 0;
            }
            .pet-img-container{
                overflow: hidden;
            }
            .pet-img-container img{
                width: 100%;
            }

            .pet-img-container .img-main{
                width: 750px;
                float: left;
            }
            .pet-img-container .img-sub{
                width: 230px;
                float: left;
                padding: 15px;
                background: #f6f5f4;
                box-sizing: border-box;
            }

        </style>
        <?php
            require('header.php');
        ?>
        <div id="contents" class="site-width">
            <section id="main" >
                <div class="title">
                    <span class="badge"><?php echo sanitize($viewData['category']); ?></span>
                    <?php echo sanitize($viewData['name']); ?>
                </div>
                <div class="pet-img-container">
                    <div class="img-main">
                        <img src="<?php echo showImg(sanitize($viewData['pic1'])); ?>" alt="main picuture:<?php echo sanitize($viewData['name']); ?>" id="js-switch-img-main">
                    </div>
                </div>
                <div class="product-detail">
                    <p><?php echo sanitize($viewData['comment']); ?></p>
                </div>
                <div class="product-buy">
                    <div class="item-left">
                        <a href="index.php<?php appendGetParam(array('p_id')); ?>">&lt; ペット一覧に戻る</a>
                    </div>
                </div>
            </section>

        </div>

        <?php
        require('footer.php');
        ?>