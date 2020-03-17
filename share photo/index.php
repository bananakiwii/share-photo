<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「.Top Page');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

$currentPageNum =(!empty($_GET['p'])) ? $_GET['p'] : 1;

if(!is_int((int)$currentPageNum)){
    error_log('エラー発生：指定ページに不正な値が入りました');
    header("Location:index.php");
}
//表示件数N
$listSpan = 12;
//現在の表示レコード先頭を計算
$currentMinNum = (($currentPageNum-1)*$listSpan);
//get Product Data from DB
$dbProductData = getProductList($currentMinNum);
//get category Data from DB
$dbCategoryData = getCategory();
debug('current page:'.$currentPageNum);

debug('image display process is done');

?>
<?php
$siteTitle = 'INDEX';
require('head.php');
?>

    <body class="page-home page-2colum">
        
    <?php
        require('header.php');
    ?>

    <div id="contents" class="site-width">
        <section id="sidebar">
            <form>
                <h1 class="title"></h1>
                <div class="selectbox">
                    <span class="icn_select"></span>
                    <select name="category">
                        <option value="1">ネコ</option>
                        <option value="2">イヌ</option>
                    </select>
                </div>

            </form>
        </section>

        <section id="main">
            <div class="search-title">
                <div class="search-left">
                    <span class="total-num"><?php echo sanitize($dbProductData['total']); ?></span>匹のペットが投稿されています。
                </div>
                <div class="search-right">
                    <span class="num"><?php echo $currentMinNum+1; ?></span> - <span class="num"><?php echo $currentMinNum+$listSpan; ?></span>匹 / <span class="num"><?php echo sanitize($dbProductData['total']); ?></span>匹中
                </div>
            </div>
            

        </section>
        <section id="panel">
            <div class="panel-list">
                <?php
                    foreach((array)$dbProductData['data'] as $key => $val):
                ?>
                    <a href="petDetail.php?p_id=<?php echo $val['id'].'&p='.$currentPageNum; ?>" class="panel">
                        <div class="panel-head">
                            <img src="<?php echo sanitize($val['pic1']); ?>" alt="<?php echo sanitize($val['name']); ?>">
                        </div>
                    </a>
                <?php
                    endforeach;
                ?>
            </div>
            <?php pagination($currentPageNum, $dbProductData['total_page']);?>

        </section>

    </div>

    <?php
    require('footer.php');
    ?>

    