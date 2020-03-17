<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('「　写真登録ページ　');
debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debugLogStart();

require('auth.php');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$dbFormData = (!empty($p_id)) ? getProduct($_SESSION['user_id'], $p_id) : '';
$edit_flg = (empty($dbFormData)) ? false : true;
$dbCategoryData = getCategory();

debug('petID:'.$p_id);
debug('formDBDATA:',print_r($dbFormData, true));
debug('categorydata:'.print_r($dbCategoryData,true));

if(!empty($p_id) && empty($dbFormData)){
    debug('get id palameter is deferent from pet ID, head to the mypage.');
    header("Location:mypage.php");
}

if(!empty($_POST)){
    debug('posted');
    debug('postinfo:'.print_r($_POST,true));
    debug('FILEinfo:'.print_r($_FILES,true));

    $name = $_POST['name'];
    $category = $_POST['category_id'];
    $comment = $_POST['comment'];
    $pic1 = ( !empty($_FILES['pic1']['name']) ) ? uploadImg($_FILES['pic1'],'pic1') : '';
    $pic1 = ( empty($pic1) && !empty($dbFormData['pic1']) ) ? $dbFormData['pic1'] : $pic1;
    $pic2 = ( !empty($_FILES['pic2']['name']) ) ? uploadImg($_FILES['pic2'],'pic2') : '';
    $pic2 = ( empty($pic2) && !empty($dbFormData['pic2']) ) ? $dbFormData['pic2'] : $pic2;
    $pic3 = ( !empty($_FILES['pic3']['name']) ) ? uploadImg($_FILES['pic3'],'pic3') : '';
    $pic3 = ( empty($pic3) && !empty($dbFormData['pic3']) ) ? $dbFormData['pic3'] : $pic3;

    if(empty($dbFormData)){

        validRequired($name, 'name');
        validMaxLen($name, 'name');

        validSelect($category, 'category_id');

        validMaxLen($comment, 'comment', 500);

    }else{
        if($dbFormData['name'] !== $name){
            validRequired($name, 'name');
            validMaxLen($name, 'name');
        }
        if($dbFormData['category_id'] !== $category){
            validSelect($category, 'category_id');
        }
        if($dbFormData['comment'] !== $comment){
            validMaxLen($comment, 'comment', 500);
        }
    }




    if(empty($err_msg)){
        debug('validation is done');

        try {
            $dbh = dbConnect();
            if($edit_flg){
                debug('DB update');
                $sql = 'UPDATE product SET name = :name, category_id = :category, comment = :comment, pic1 = :pic1, pic2 = :pic2, pic3 = :pic3 WHERE user_id = :u_id AND id = :p_id';
                $data = array(':name' => $name , ':category' => $category_id,':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
            }else{
                debug('newDB');
                $sql = 'insert into product (name, category_id, comment, pic1, pic2, pic3, user_id, create_date) values (:name, :category, :comment,  :pic1, :pic2, :pic3, :u_id, :date)';
                $data = array(':name' => $name , ':category' => $category, ':comment' => $comment, ':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));

            }
            debug('SQL：'.$sql);
            debug('data:'.print_r($data,true));

            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
            $_SESSION['msg_success'] = SUC04;
            debug(' to mypage');
            header("Location:petDetail.php");
            }

        } catch (Exception $e) {
            error_log('error:' . $e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debug('image display procee is done <<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
$siteTitle = (!$edit_flg) ? 'とうこう' : 'へんしゅう';
require('head.php');
?>

<body class="page-profEdit page-2colum page-logined">
    
    <?php
    require('header.php');
    ?>

    <div id="contents" class="site-width">
        <h1 class="page-title"><?php echo (!$edit_flg) ? 'とうこうする' : 'へんしゅうする'; ?></h1>
        <section id="main" >
            <div class="form-container">
                <form action="" method="post" class="form" enctype="multipart/form-data" style="width:100%;box-sizing:border-box;">
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['common'])) echo $err_msg['common'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['name'])) echo 'err'; ?>">
                        なまえ <span class="label-require">*</span>
                        <input type="text" name="name" value="<?php echo getFormData('name'); ?>">
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['name'])) echo $err_msg['name'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['category_id'])) echo 'err'; ?>">
                        カテゴリ <span class="label-require">*</span>
                        <select name="category_id" id="category_id">
                            <option value="0" <?php if(getFormData('category_id') == 0 ){ echo 'selected'; } ?> >選択してください</option>
                            <?php
                                foreach($dbCategoryData as $key => $val){
                            ?> 
                                <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category_id') == $val['id'] ){ echo 'selected'; } ?> >
                                    <?php echo $val['name']; ?>
                                </option>                               
                            <?php
                                }
                            ?>
                        </select>
                    </label>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['category_id'])) echo $err_msg['category_id'];
                        ?>
                    </div>
                    <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">
                        せつめい
                        <textarea name="comment" id="js-count" cols="30" rows="10" style="height:150px;"><?php echo getFormData('comment'); ?></textarea>
                    </label>
                    <p class="counter-text"><span id="js-count-view">0</span>/500</p>
                    <div class="area-msg">
                        <?php
                        if(!empty($err_msg['comment'])) echo $err_msg['comment'];
                        ?>
                    </div>
                    <div style="overflow:hidden;">
                        <div class="imgDrop-container">
                            しゃしん1
                            <label class="area-drop <?php if(!empty($err_msg['pic1'])) echo 'err'; ?>">
                                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                <input type="file" name="pic1" class="input-file">
                                <img src="<?php echo getFormData('pic1'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic1'))) echo 'display:none;' ?>">
                                    drag&drop
                            </label>
                            <div class="area-msg">
                                <?php
                                if(!empty($err_msg['pic1'])) echo $err_msg['pic1'];
                                ?>
                            </div>
                        </div>
                        <div class="imgDrop-container">
                            しゃしん2
                            <label class="area-drop <?php if(!empty($err_msg['pic2'])) echo 'err'; ?>">
                                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                <input type="file" name="pic2" class="input-file">
                                <img src="<?php echo getFormData('pic2'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic2'))) echo 'display:none;' ?>">
                                    drag&drop
                            </label>
                            <div class="area-msg">
                                <?php
                                if(!empty($err_msg['pic2'])) echo $err_msg['pic2'];
                                ?>
                            </div>

                        </div>
                        <div class="imgDrop-container">
                            しゃしん3
                            <label class="area-drop <?php if(!empty($err_msg['pic3'])) echo 'err'; ?>">
                                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                                <input type="file" name="pic3" class="input-file">
                                <img src="<?php echo getFormData('pic3'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic3'))) echo 'display:none;' ?>">
                                    drag&drop
                            </label>
                            <div class="area-msg">
                                <?php
                                if(!empty($err_msg['pic3'])) echo $err_msg['pic3'];
                                ?>
                            </div>

                        </div>
                    </div>
                    <div class="btn-container">
                        <input type="submit" class="btn btn-mid" value="<?php echo (!$edit_flg) ? 'とうこうする' : 'アップデート';?>">
                    </div>
                </form>
            </div>
        </section>
    </div>

    <?php
    require('footer.php');
    ?>