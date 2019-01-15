<?php
require_once("Chinwei6_LinePay.php");
session_start();
?>
<html>
    <body>

    <?php
        // Store Webpage -> Store Server
        if(isset($_POST['productName'])) 
        {
            $apiEndpoint   = $_POST['apiEndpoint'];
            $channelId     = $_POST['channelId'];
            $channelSecret = $_POST['channelSecret'];

            $params = [
                "productName"     => $_POST['productName'],
                "productImageUrl" => $_POST['productImageUrl'],
                "amount"          => $_POST['amount'],
                "currency"        => $_POST['currency'],
                "confirmUrl"      => $_POST['confirmUrl'],
                "orderId"         => $_POST['orderId'],
                "confirmUrlType"  => $_POST['confirmUrlType'],
            ];

            try {
                $LinePay = new Chinwei6\LinePay($apiEndpoint, $channelId, $channelSecret);
                
                // Save params in the _SESSION
                $_SESSION['cache'] = [
                    "apiEndpoint"   => $_POST['apiEndpoint'],
                    "channelId"     => $_POST['channelId'],
                    "channelSecret" => $_POST['channelSecret'],
                    "amount"        => $_POST['amount'],
                    "currency"      => $_POST['currency'],
                ];

                $result = $LinePay->reserve($params);

                //連結資料庫
                require_once("database/database.php");

                $DB = new database();
                $table = 'linepay';
                $data = array('ord_id' => $params['orderId'],'ord_status' => false );
                $result = $DB->InsertData($table,$data);
                if($result){
                    if(isset($result['info']['paymentUrl']['web']))
                    echo '<a target="_blank" href="' . $result['info']['paymentUrl']['web'] . '">點此連至 Line 頁面登入帳戶</a>';
                }else{
                    echo '寫入資料庫失敗';
                }
            }
            catch(Exception $e) {
                echo '<pre class="code">';
                echo $e->getMessage();
                echo '</pre>';
            }
        }
        else {
            echo "No Data";
        }
    ?>

    </body>
</html>