<header>
    <div class="site-width">
            <h1><a href="home.php">share photo</a></h1>
        <nav id="top-nav">
            <ul>
                <?php
                if(empty($_SESSION['user_id'])){
                ?>    
                    <li><a href="signup.php" class="btn btn-primary">ユーザーとうろく</a></li>
                    <li><a href="login.php">ログイン</a></li>
                    <li><a href="index.php">しゃしんいちらん</a></a></li>

                <?php
                }else{
                ?>
                    <li><a href="logout.php">ログアウト</a></li>
                    <li><a href="mypage.php">マイページ</a></li>
                    <li><a href="registProduct.php">しゃしんをとうこうする</a></li>
                    <li><a href="index.php">しゃしんいちらん</a></a></li>

                <?php
                    }
                ?>
            </ul>
        </nav>
    </div>
</header>
