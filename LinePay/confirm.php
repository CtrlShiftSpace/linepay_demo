<?php
require_once("Chinwei6_LinePay.php");
session_start();
?>
<html>
    <body>
        <?php 
            // LinePay Server -> Store Server (calling confirmUrl)
            if(isset($_GET['transactionId']) && isset($_SESSION['cache'])) {
                $apiEndpoint   = $_SESSION['cache']['apiEndpoint'];
                $channelId     = $_SESSION['cache']['channelId'];
                $channelSecret = $_SESSION['cache']['channelSecret'];

                $params = [
                    "amount"   => $_SESSION['cache']['amount'],
                    "currency" => $_SESSION['cache']['currency'],
                ];

                try {
                    $LinePay = new Chinwei6\LinePay($apiEndpoint, $channelId, $channelSecret);

                    $result = $LinePay->confirm($_GET['transactionId'], $params);

                    //成功付款
                    if($result['returnCode'] === '0000'){
                        require_once("database/database.php");
                        $DB = new database();
                        $table = 'linepay';
                        $modifiedData = array('ord_status' => true,'transactionId' => $result['info']['transactionId'],'method' => $result['info']['payInfo']['method']);
                        $conditionData = array('ord_id' => $result['info']['orderId']);

                        $DB->ModifyDataByCondition($table,$modifiedData,$conditionData);
                    }
                }
                catch(Exception $e) {
                    echo '<pre class="code">';
                    echo $e->getMessage();
                    echo '</pre>';
                }

                unset($_SESSION['cache']);
            }
            else {
                echo '<pre class="code">';
                echo "No Params";
                echo '</pre>';
            }
        ?>
    </body>
</html>