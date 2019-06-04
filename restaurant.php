<?php
session_start();
include("pdoInc.php");     //PDO
include('reply.php');
?>

<html>
<head>
    <title>大Food翁</title>
    <meta charset="utf-8">
    <!-- bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/jxiu.css"> <!-- jxiu 需要的css -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

    <!-- bootstrap 版面的css -->
    <style>
    /* Add a gray background color and some padding to the footer */
    footer {
      background-color: #f2f2f2;
      padding: 25px;
    }

    .carousel-inner img {
      width: 100%; /* Set width to 100% */
      min-height: 200px;
    }

    /* Hide the carousel text when the screen is less than 600 pixels wide */
    @media (max-width: 600px) {
      .carousel-caption {
        display: none; 
      }
    }

    </style>
  
    <!-- 聊天室相關 -->
    <style>
        table,tr,td{width:100%; font-family: 微軟正黑體; font-size: 15px;}
        table{height:100%}
        #showMsgHere{width:100%;height:200px;font-size:15px;resize:none;}
    </style>
    <script>
    function sendMsg(){
        $.post(
            "ajax_chatroom_insert.php",
            {
                nickname: $("#nickname").val(),
                msg: $("#msg").val()
            }
        );
        $("#msg").val("");
    }
     
    function showMsg(t){
        $.post(
            "ajax_chatroom_disp.php",
            {
                'getNewMsgOnly': t
            }
        ).done(function(data){
            $("#showMsgHere").append(data);
            setTimeout("showMsg(1);", 1000);
        });
    }
     
    $(function(){
        // 網頁載入，抓取全部訊息
        showMsg(0);
        // 按下 enter 後送出訊息
        $("#msg").bind("keydown",
            function(e){
                if(e.which == 13){
                    sendMsg();
                }
            }
        )
    })
    </script>

    <!-- index頁面 -->
    <style>
        /* img hover */   
        img{
            z-index: 0;
            opacity: 1;
            transition: .5s ease;
            backface-visibility: hidden;
        }

        /* 圖片上的文字 */
        .centered {
          z-index:1;
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          color: white;
          font-family: 微軟正黑體;
          /*font-size: 30px;*/
          text-shadow: 2px 2px rgb(0,0,0,0.8);
        }
        

        .imageHover:hover img{
            opacity: 0.5;
        }


    </style>

   

    

</head>
<body style="font-family: 微軟正黑體;">


<!-- https://www.w3schools.com/bootstrap4/tryit.asp?filename=trybs_navbar_color&stacked=h -->
    <nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
        <!-- Brand/logo -->
        <a class="navbar-brand" href="index.php">大Food翁</a>
        <!-- Links -->
        <ul class="nav navbar-nav">
            <!-- <li class="nav-item active">
              <a class="nav-link" href="#mapView">地圖檢視</a>
            </li> -->
            <li class="nav-item">
              <a class="nav-link" href="index.php">店家總覽</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="random_select.php">餐廳推薦</a>
            </li>
            
        </ul>
        <ul class="navbar-nav ml-auto" >
            <?php
                if(isset($_SESSION['account']) && $_SESSION['account'] != null){ 
                    //修改會員資料
                    echo '<a href="member_info.php"><img src="pic/profile.png" style="height:32px; margin:0px 5px;" onmouseover="this.src=\'pic/profile_hover.png\'" onmouseleave="this.src=\'pic/profile.png\'"></a>';
                    //會員登出
                    echo '<li class="nav-item"><a href="php_sess_logout.php"><img src="pic/logout.png" style="height:32px; margin:0px 5px;" onmouseover="this.src=\'pic/logout_hover.png\'" onmouseleave="this.src=\'pic/logout.png\'" onclick="<?php echo \'<meta http-equiv=REFRESH CONTENT=0;url=index.php>\';?>"></a></li>';
                } 
                else{
                    echo '<li class="nav-item"><a href="member.php"><img src="pic/login.png" style="height:32px;" onmouseover="this.src=\'pic/login_hover.png\'" onmouseleave="this.src=\'pic/login.png\'"></a></li>';
                }
            ?>
            <!-- <li class="nav-item">
              <a class="nav-link" href="member.php"><img src="pic/login.png" style="height:32px;" onmouseover="this.src='pic/login_hover.png'" onmouseleave="this.src='pic/login.png'"></a>
            </li>   -->  
        </ul>
    </nav>


<!-- jxiupart start -->
<div class="container">

    <div class="container" id="restaurantImg">
        <img src="pic/food_category/pot.jpg" style="width: 80%">
    </div>

    <div class="container" id="restaurantInfo">
        <h4 style="text-align: center;">餐廳資訊</h4>
        <?php
            $rid = 1;
            $select = mysqli_query($con, "SELECT *
                                        FROM restaurant
                                        WHERE restaurant.restaurant_id = $rid");
            while($row = mysqli_fetch_assoc($select)){
                echo "<h3>".$row['name']."</h3></br>";
                echo "<p> 評價：".$row['grade']."</p>";
                echo "<p> 類型：".$row['category']."</p>";
                echo "<p> 地址：".$row['address']."</p>";
                echo "<p> 電話：".$row['phone']."</p>";
                echo "<p> 營業時間：".$row['open_hour']."</p>";
            };
        ?>
    </div>
</div>
<div class="container" style="clear: both;">
    <div style="clear: both;"><br/><br/></div>
    <!--以下之後為用php改寫-->
    <div id="reply">
        <br/>

        <?php
        $rid = 1;
            $select = mysqli_query($con, "SELECT *
                                        FROM restaurant
                                        WHERE restaurant.restaurant_id = $rid");
            while($row = mysqli_fetch_assoc($select)){
                echo "<h3>大家對於".$row['name']."的評價</h3>";
            };
        ?>


        <p>2019/5/3 寫了一篇食記</p>
        <p>評分：4分</p>
        <p>我覺得超難吃，但飲料杯笑話很好笑，所以我給4分</p>
        <div class="like">Like!</div>
        <img src="pic/food_category/dessert.jpg">
    </div>
</div>

<!--jxiupart end-->



<div id='chatroom' class="container" style="position: fixed; right:30px; bottom: 100px; height:400px; width: 400px; display: none; z-index:10;">
    <div class="card">
        <div class="card-header bg-dark text-white">聊天室</div>
        <div class="card-body">
            <table>
                <tr><td><textarea id="showMsgHere" disabled="disabled"></textarea></td></tr>
                <tr><td>
                    <form>
                        <!-- <input type="text" id="nickname" placeholder="暱稱" style="width:5em;height:2em"> -->
                        <input type="text" id="msg" placeholder="訊息" style="width:100%;height:2em">
                        <input type="button" value="送出" onclick="sendMsg();">
                    </form>
                </td></tr>
            </table>
        </div> 
    </div>
</div>


</body>
</html>
