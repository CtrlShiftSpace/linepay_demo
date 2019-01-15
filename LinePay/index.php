<html>
    <body>

        <form id="reserveForm" method="POST" action="reserve.php">

            <!--LINE Pay API Server-->
            <!-- 測試用 -->
            <input type="hidden" name="apiEndpoint" value="https://sandbox-api-pay.line.me/v2/payments/">
            <!-- 正式用 -->
            <!-- <input type="hidden" name="apiEndpoint" value="https://api-pay.line.me/v2/payments/request"> -->

            商家 ID
            <input type="text" name="channelId" value="YOUR CHANNEL ID" required>
            <br>

            商家密鑰
            <input type="text" name="channelSecret" value="YOUR CHANNEL SECRET" required>
            <br>

            商品名稱
            <input type="text" name="productName" value="Product A" required>
            <br>

            訂單編號
            <input type="text" name="orderId" value="test000001" required>
            <br>

            訂單照片
            <input type="text" name="productImageUrl" value="image.jpeg" >
            <br>

            訂單金額
            <input type="text" name="amount" value="20" required>
            <input type="hidden" name="currency" value="TWD">
            <br>

            confirmUrl
            <input type="text" name="confirmUrl" value="confirm.php" required>
            <br>

            confirmUrl 類型
            <input type="radio" name="confirmUrlType" value="CLIENT" checked>
            <br>

            使用 <input type="image" src="linepay_logo_119x39.png"> 支付

        </form>
    </body>
</html>
