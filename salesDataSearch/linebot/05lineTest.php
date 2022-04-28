<?php
include "../03lineNotify.php";
include "../commonfile/config.inc.php";

/* 產生日報字串 */
function getSalesDailyMessage(){
    /* 日期處理 */
    $today = date("Y-m-d");
    $today_sql = date("Y-m-d", strtotime("+1 day"));
    $month_first_day = date("Y-m-01");
        
    //如果今天是一號，回上一個月，截止日變今天(因為SQL不包含後者)
    //if($today === $month_first_day){
    //    $month_first_day = date("Y-m-01", strtotime("-1 month"));
    //    $today_sql = date("Y-m-d", strtotime("-1 day"));
    //}
        
    /* 日報SQL */
    $strSql = "SELECT DATE_FORMAT(add_date, '%Y-%m-%d') AS date, SUM(ord_price-tran_price) AS amount
                FROM order_list
                WHERE 1=1
                AND process_type NOT IN ('X1','X2')
                AND (add_date BETWEEN '".$month_first_day."' AND '".$today_sql."')
                GROUP BY date
                ORDER BY date
                ";
        
    $msg = "【本月日營業額】\n日期 日營業額";
    $sum = 0;
    $resDailySales = SqlQuery1($strSql);
    while(!$resDailySales->EOF){
        $date = $resDailySales->fields["date"];
        $day_amount = intval($resDailySales->fields["amount"]);
        $sum += $day_amount;
        $msg .= "\n".$date."\t\t".$day_amount;
        $resDailySales->MoveNext();
    }
    $msg .= "\n\n合計\t\t".$sum;
    //echo "SQL結果訊息：".$msg."<br>";
    return $msg;
}
function getPenSoldMessage($array_prodNum){
    /* 日期處理 */
    $today = date("Y-m-d");
    $today_sql = date("Y-m-d", strtotime("+1 day"));
    $month_first_day = date("Y-m-01");
    //如果今天是一號，回上一個月，截止日變今天(因為SQL不包含後者)
    if($today === $month_first_day){
        $month_first_day = date("Y-m-01", strtotime("-1 month"));
        $today_sql = date("Y-m-d");
    }
    //陣列中的所有商品編號都要查詢，所以形成一個SQL長字串
    $strSql_codition_prodNum = "";
    for($i=0; $i<count($array_prodNum)-1 ; $i++){
        $strSql_codition_prodNum .= "order_listdetail.prod_num LIKE '%".$array_prodNum[$i]."%' OR ";
    }
    $strSql_codition_prodNum .= "order_listdetail.prod_num LIKE '%".$array_prodNum[count($array_prodNum)-1]."%'";
    //要取什麼資料的SQL句
    $strSql = "SELECT order_list.orderid, order_listdetail.prod_num, SUM(order_listdetail.ord_qty), DATE_FORMAT(order_list.add_date, '%Y-%m-%d')
                FROM order_list 
                JOIN order_listdetail ON order_list.orderid = order_listdetail.orderid
                WHERE order_list.process_type NOT IN ('X1','X2')
                AND ( ".$strSql_codition_prodNum." )
                AND order_list.add_date BETWEEN '".$month_first_day."' AND '".$today_sql."'
                GROUP BY DATE_FORMAT(order_list.add_date, '%Y-%m-%d')";
    $resPen32 = SqlQuery1($strSql);
    //迴圈製成字串
    $msg = "\n日期 賣出數量";
    $sum = 0;
    while(!$resPen32->EOF){
        $ord_qty = $resPen32 -> fields['SUM(order_listdetail.ord_qty)'];
        $add_date = $resPen32 -> fields["DATE_FORMAT(order_list.add_date, '%Y-%m-%d')"];
        $msg .= "\n".$add_date."\t\t".$ord_qty;
        $temp_qty = intval($ord_qty);
        $sum += $temp_qty;//合計數量
        $resPen32->MoveNext();
    }
    $msg .= "\n\n合計：".$sum;
    return $msg;            
}
function getSalesQuantityRankMessage(){
    /* 日期處理 */
    $today = date("Y-m-d");
    $today_sql = date("Y-m-d", strtotime("+1 day"));
    $month_first_day = date("Y-m-01");
    //如果今天是一號，回上一個月，截止日變今天(因為SQL不包含後者)
    if($today === $month_first_day){
        $month_first_day = date("Y-m-01", strtotime("-1 month"));
        $today_sql = date("Y-m-d");
    }
    
    $strSql = "SELECT 
                order_listdetail.prod_num AS prod_num,
                order_listdetail.ord_price_add AS prod_price,
                order_listdetail.prod_name AS prod_name, 
                SUM(order_listdetail.ord_qty) AS prod_qty,
                order_listdetail.ord_type AS prod_type
                FROM order_list
                JOIN order_listdetail ON order_list.orderid = order_listdetail.orderid
                WHERE order_list.process_type NOT IN ('X1','X2')
                AND order_listdetail.ord_type NOT IN ('C01','T01')
                AND (order_list.add_date BETWEEN '".$month_first_day."' AND '".$today_sql."')
                GROUP BY order_listdetail.prod_num
                ORDER BY SUM(order_listdetail.ord_qty) DESC
                LIMIT 5";
    $resQuantityRank = SqlQuery1($strSql);
    
    //迴圈產字串
    $msg = "【暢銷排行榜】";
    $rank = 0;//排名
    while(!$resQuantityRank->EOF){
        $prodNum = $resQuantityRank->fields['prod_num'];
        $prodPrice = $resQuantityRank->fields['prod_price'];
        $prodName = strip_tags($resQuantityRank->fields['prod_name']);
        $prodQty = $resQuantityRank->fields['prod_qty'];
        $amount = intval($prodQty) * intval($prodPrice);
        $rank++;
        $msg .= "\n----- No. ".$rank." -----\n產品名稱：".$prodName."\n產品編號：".$prodNum."\n產品單價：".$prodPrice."\n售出數量：".$prodQty."\n售出金額：".$amount;
        $resQuantityRank->MoveNext();
    }
    return $msg;
}
function getSalesAmountRankMessage(){
    /* 日期處理 */
    $today = date("Y-m-d");
    $today_sql = date("Y-m-d", strtotime("+1 day"));
    $month_first_day = date("Y-m-01");
    //如果今天是一號，回上一個月，截止日變今天(因為SQL不包含後者)
    if($today === $month_first_day){
        $month_first_day = date("Y-m-01", strtotime("-1 month"));
        $today_sql = date("Y-m-d");
    }
    $strSql = "SELECT 
                order_listdetail.prod_num AS prod_num,
                order_listdetail.ord_price_add AS prod_price, 
                order_listdetail.prod_name AS prod_name, 
                SUM(order_listdetail.ord_qty) AS prod_qty,
                order_listdetail.ord_price_add * (SUM(order_listdetail.ord_qty)) AS totalAmount, 
                order_listdetail.ord_type AS prod_type 
                FROM order_list 
                JOIN order_listdetail ON order_list.orderid = order_listdetail.orderid 
                WHERE order_list.process_type 
                NOT IN ('X1','X2') AND order_listdetail.ord_type 
                NOT IN ('C01','T01') AND order_list.add_date BETWEEN '".$month_first_day."' AND '".$today_sql."'
                GROUP BY order_listdetail.prod_num 
                ORDER BY totalAmount DESC LIMIT 5";
    $resSalesAmount = SqlQuery1($strSql);
    //迴圈產生訊息字串            
    $msg = "【最賣錢排行榜】";
    $rank = 0;
    while(!$resSalesAmount->EOF){
        $prodNum = $resSalesAmount->fields["prod_num"];
        $prodPrice = $resSalesAmount->fields["prod_price"];
        $prodName = strip_tags($resSalesAmount->fields["prod_name"]);
        $prodQty = $resSalesAmount->fields["prod_qty"];
        $totalAmount = $resSalesAmount->fields["totalAmount"];
        $rank++;
        $msg .= "\n----- No. ".$rank." -----\n產品名稱：".$prodName."\n產品編號：".$prodNum."\n產品單價：".$prodPrice."\n售出數量：".$prodQty."\n售出金額：".$totalAmount;
        $resSalesAmount->MoveNext();
    }
    return $msg;
}
function getRandomEmoji(){
    $array_emoji = ["(*´ω｀*)", "(>_<)", "( *´艸｀)", "(*^▽^*)", "Σ(ﾟДﾟ)", "(^O^)／", "(#^.^#)", "(*'ω'*)", "( ｀ー´)ノ", "(; ･`д･´)"];
    array_push($array_emoji, "(((o(*ﾟ▽ﾟ*)o)))");
    array_push($array_emoji, "(ﾟ∀ﾟ)");
    array_push($array_emoji, "ヽ(•̀ω•́ )ゝ✧");
    array_push($array_emoji, "(;´Д｀)");
    array_push($array_emoji, "(+o+)");
    array_push($array_emoji, "(´ﾟдﾟ｀)");
    $randomNum = rand(0,count($array_emoji)-1);
    return $array_emoji[$randomNum];
}
function getSalesDailyMessage_past($minusTime){
    /* 日期處理 */
    $lastDay = date("Y-m-d");//今天
    if($minusTime === "上月"){
        $lastDay_sql = date("Y-m-01");
        $month_first_day = date("Y-m-01", strtotime("-1 month"));
    }else if($minusTime === "去年同期"){
        $lastDay_sql = date("Y-m-01", strtotime("-1 year"."+1 month"));
        $month_first_day = date("Y-m-01", strtotime("-1 year"));
    }    
        
    /* 日報SQL */
    $strSql = "SELECT DATE_FORMAT(add_date, '%Y-%m-%d') AS date, SUM(ord_price-tran_price) AS amount
                FROM order_list
                WHERE 1=1
                AND process_type NOT IN ('X1','X2')
                AND (add_date BETWEEN '".$month_first_day."' AND '".$lastDay_sql."')
                GROUP BY date
                ORDER BY date
                ";
        
    $msg = "【".$minusTime."日營業額】\n日期 日營業額";
    $sum = 0;
    $resDailySales = SqlQuery1($strSql);
    while(!$resDailySales->EOF){
        $date = $resDailySales->fields["date"];
        $day_amount = intval($resDailySales->fields["amount"]);
        $sum += $day_amount;
        $msg .= "\n".$date."\t\t".$day_amount;
        $resDailySales->MoveNext();
    }
    $msg .= "\n\n合計\t\t".$sum;
    //echo "SQL結果訊息：".$msg."<br>";
    return $msg;
}
function getSalesMonthly($minusTime){
    /* 日期處理 */
    $today = date("Y-m-d");
    if($minusTime === "今年每月"){
        $lastDay_sql = date("Y-m-d" , strtotime("+1 day"));
        $startDay = date("Y-01-01");
    }else if($minusTime === "去年每月"){
        $lastDay_sql = date("Y-12-31", strtotime("-1 year"));
        $startDay = date("Y-01-01", strtotime("-1 year"));
    }
    /* 報表SQL */
    $strSql = "SELECT DATE_FORMAT(add_date, '%Y-%m') AS date, SUM(ord_price-tran_price) AS amount
                FROM order_list
                WHERE 1=1
                AND process_type NOT IN ('X1','X2')
                AND (add_date BETWEEN '".$startDay."' AND '".$lastDay_sql."')
                GROUP BY date
                ORDER BY date
                ";
    $msg = "【".$minusTime."月營業額】\n日期 月營業額";
    $sum = 0;
    $resDailySales = SqlQuery1($strSql);
    while(!$resDailySales->EOF){
        $date = $resDailySales->fields["date"];
        $day_amount = intval($resDailySales->fields["amount"]);
        $sum += $day_amount;
        $msg .= "\n".$date."\t\t".$day_amount;
        $resDailySales->MoveNext();
    }
    $msg .= "\n\n合計\t\t".$sum;
    return $msg;
}
function getOrderCount($minusTime){
    /* 日期處理 */
    $today = date("Y-m-d");
    if($minusTime === "本月每日"){
        $startDay = date("Y-m-01");
        $lastDay_sql = date("Y-m-d", strtotime("+1 day"));
    }else if($minusTime === "上月每日"){
        $startDay = date("Y-m-01", strtotime("-1 month"));
        $lastDay_sql = date("Y-m-01");
    }else if($minusTime === "去年同期每日"){
        $startDay = date("Y-m-01", strtotime("-1 year"));
        $lastDay_sql = date("Y-m-01", strtotime("-1 year +1 month"));
    }
    
    /* 報表SQL */
    $strSql = "SELECT DATE_FORMAT(add_date, '%Y-%m-%d') AS date, COUNT(*) AS qty
                FROM order_list
                WHERE 1=1
                AND process_type NOT IN ('X1','X2')
                AND (add_date BETWEEN '".$startDay."' AND '".$lastDay_sql."')
                GROUP BY date
                ORDER BY date
                ";
    $msg = "【".$minusTime."訂單數】\n日期 訂單數";
    $sum = 0;
    $resDailySales = SqlQuery1($strSql);
    while(!$resDailySales->EOF){
        $date = $resDailySales->fields["date"];
        $day_amount = intval($resDailySales->fields["qty"]);
        $sum += $day_amount;
        $msg .= "\n".$date."\t\t".$day_amount;
        $resDailySales->MoveNext();
    }
    $msg .= "\n\n合計\t\t".$sum;
    return $msg;            
}
/* 錯誤訊息陣列 */
$array_errorMsg = [ "00001" => "Undefined Group",
                    "00002" => "Undefined User",
                    "00003" => "Incorrect HTTP Request, not from Line Server"
                ];
                
/* 群組ID檢查, 使用者ID檢查 */
$array_groupId = array("XXXXXXXXXXXXX","XXXXXXXXXXXXXXXX","XXXXXXXXXXXXXXXX");//
$array_userID = ["XXXXXXXXXXXXXXXX","XXXXXXXXXXXXXXXX"];//只有開發者的威宏可以
//in_array($id, $array_groupId);找前面的值有沒有在陣列中

/* LINE BOT 需求資料區 */
$channelSecret = "901da50a31959021594076b8d5256544";
$accessToken = 'afYwmWq+BSVJZBpeRb2EuOjLuqATv0W+wzpdeH1XiLwXyHabD2nXt0ztAYQYU0K75adzjQUd5r79IitR/KSFcwlTBZuOJqHLQReRcTIv+E7JPMuboXz8j/+lvUIxAz45KC9JDX67kq6A0KQPnfA4wgdB04t89/1O/w1cDnyilFU='; 

/* 驗證訊息來源 */
$jsonString = file_get_contents('php://input');//Json物件，傳過來的Body，簽名的驗證字串也要用到
$recieveSignatures = $_SERVER['HTTP_X_LINE_SIGNATURE'];//驗證是不是Line傳過來的，HTTP簽名
$hash = hash_hmac("sha256", $jsonString, $channelSecret, true);//secret的加密，會變成跟HTTP簽名一樣，代表才是line傳過來的
$signature = base64_encode($hash);//依照line官方網站上的操作方式64加密
if($recieveSignatures !== $signature){
    post_message("SERVER的LINE BOT收到不來自LINE的HTTP REQUEST\n".$recieveSignatures);
    //exit();
}
//post_message($jsonString);

/* 拆解JSON */
$jsonObj = json_decode($jsonString);
$message = $jsonObj->{"events"}[0]->{"message"};//type = object
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};//type = string
$source_type = $jsonObj->{"events"}[0]->{"source"}->{"type"};
$source_usrId = $jsonObj->{"events"}[0]->{"source"}->{"userId"};//使用者ID
$source_groupId = $jsonObj->{"events"}[0]->{"source"}->{"groupId"};//群組ID，要求離開群組的時候必要
$msgRely = $message->{"text"};//判斷輸入來的訊息

/* 機器人要做的事情區 */
if((in_array($source_groupId, $array_groupId) || in_array($source_usrId, $array_userID)) && $msgRely != ""){
    switch($msgRely){
        case "呼叫狗狗":
            $message->{"text"} = getSalesDailyMessage();
            $messageData = [
                "type" => "flex", 
                "altText" => "找狗狗什麼事？汪！",
                "contents" => [
                    "type" => "carousel",
                    "contents" => [
                        [
                            "type" => "bubble",
                            "header" => [
                                "type" => "box",
                                "layout" => "horizontal",
                                "contents" =>   [
                                    array("type"=>"text", "text"=>"找大新官網狗狗什麼事？")
                                ]
                            ],
                            "body" =>   [
                                "type" => "box",
                                "layout" => "vertical",
                                "contents" =>   [
                                    [
                                        "type" => "box",
                                        "layout" => "horizontal",
                                        "contents" =>   [
                                            [
                                                "type" => "button", 
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "日營業額",
                                                    "text" => "本月日營業額"
                                                ],
                                                "style" => "link",
                                                "color" => "#ff00ff"
                                            ],
                                        ]
                                    ],
                                    [
                                        "type" => "separator",
                                        "color" => "#bccae6"
                                    ],
                                    [
                                        "type" => "box",
                                        "layout" => "horizontal",
                                        "contents" =>   [
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "16G點讀筆",
                                                    "text" => "本月16G點讀筆售出數量"
                                                ],
                                                "style" => "link",
                                                "color" => "#0066ff"
                                            ],
                                            [
                                                "type" => "separator",
                                                "color" => "#bccae6"
                                            ],
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "32G點讀筆",
                                                    "text" => "本月32G點讀筆售出數量"
                                                ],
                                                "style" => "link",
                                                "color" => "#cc3300"
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "separator",
                                        "color" => "#bccae6"
                                    ],
                                    [
                                        "type" => "box",
                                        "layout" => "horizontal",
                                        "contents" =>   [
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "暢銷排行榜",
                                                    "text" => "本月暢銷排行榜"
                                                ],
                                                "style" => "link",
                                                "color" => "#009933"
                                            ],
                                            [
                                                "type" => "separator",
                                                "color" => "#bccae6"
                                            ],
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "最賣錢排行榜",
                                                    "text" => "本月最賺的排行榜"
                                                ],
                                                "style" => "link",
                                                "color" => "#ff9900"
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "separator",
                                        "color" => "#bccae6"
                                    ],
                                    [
                                        "type" => "box",
                                        "layout" => "horizontal",
                                        "contents" =>   [
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "搔狗狗肚肚",
                                                    "text" => "搔狗狗肚肚"
                                                ],
                                                "style" => "link",
                                                "color" => "#cc9900"
                                            ],
                                            [
                                                "type" => "separator",
                                                "color" => "#bccae6"
                                            ],
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "離開群組",
                                                    "text" => "叫狗狗離開群組"
                                                ],
                                                "style" => "link",
                                                "color" => "#cccccc"
                                            ]
                                        ]
                                    ],
                                ]
                            ],
                            "footer" => [
                                "type" => "box",
                                "layout" => "vertical",
                                "contents" =>   [
                                    [
                                        "type" => "box",
                                        "layout" => "horizontal", 
                                        "contents" =>   [
                                            ["type" => "text","text" => "グループとユーザーに限定発信中..."]
                                        ]
                                    ],
                                    [
                                        "type" => "box",
                                        "layout" => "horizontal",
                                        "contents" =>   [
                                            ["type" => "text","text" => "追加したい場合はa22853398に連絡"]
                                        ]
                                    ]
                                ]
                            ],
                            "styles" => [
                                "body" =>   [
                                    "separator" => true
                                ],
                                "footer" => [
                                    "separator" => true
                                ]
                            ]
                        ],
                        [
                            "type" => "bubble",
                            "header" => [
                                "type" => "box",
                                "layout" => "horizontal",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => "更多資料在這邊"
                                    ]
                                ]
                            ],
                            "body" => [
                                "type" => "box",
                                "layout" => "vertical",
                                "contents" => [
                                    [
                                        "type" => "box",
                                        "layout" => "horizontal",
                                        "contents" => [
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "上月日營業額",
                                                    "text" => "上個月的日營業額"
                                                ],
                                                "style" => "link",
                                                "color" => "#c034eb",
                                            ],
                                            [
                                                "type" => "separator",
                                                "color" => "#bccae6"
                                            ],
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "去年同期売上",
                                                    "text" => "去年同期的日營業額"
                                                ],
                                                "style" => "link",
                                                "color" => "#22119e",
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "separator",
                                        "color" => "#bccae6"
                                    ],
                                    [
                                        "type" => "box",
                                        "layout" => "horizontal",
                                        "contents" => [
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "今年每月売上",
                                                    "text" => "今年的每月營業額"
                                                ],
                                                "style" => "link",
                                                "color" => "#13d17b",
                                            ],
                                            [
                                                "type" => "separator",
                                                "color" => "#bccae6"
                                            ],
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "去年每月売上",
                                                    "text" => "去年的每月營業額"
                                                ],
                                                "style" => "link",
                                                "color" => "#a81137",
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "separator",
                                        "color" => "#bccae6"
                                    ],
                                    [
                                        "type" => "box",
                                        "layout" => "horizontal",
                                        "contents" => [
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "本月日訂單數",
                                                    "text" => "本月每日訂單數量"
                                                ],
                                                "style" => "link",
                                                "color" => "#e8b50e",
                                            ],
                                            [
                                                "type" => "separator",
                                                "color" => "#bccae6"
                                            ],
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "上月日訂單數",
                                                    "text" => "上個月的每日訂單數量"
                                                ],
                                                "style" => "link",
                                                "color" => "#0addf0",
                                            ]
                                        ]
                                    ],
                                    [
                                        "type" => "separator",
                                        "color" => "#bccae6"
                                    ],
                                    [
                                        "type" => "box",
                                        "layout" => "horizontal",
                                        "contents" => [
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "去年同期訂單",
                                                    "text" => "去年同期每日訂單數量"
                                                ],
                                                "style" => "link",
                                                "color" => "#ed0942",
                                            ],
                                            [
                                                "type" => "separator",
                                                "color" => "#bccae6"
                                            ],
                                            [
                                                "type" => "button",
                                                "action" => [
                                                    "type" => "message",
                                                    "label" => "待補充",
                                                    "text" => "待補充"
                                                ],
                                                "style" => "link",
                                                "color" => "#ff9900",
                                            ]
                                        ]
                                    ],
                                ]
                            ],
                            "footer" => [
                                'type' => 'box',
                                'layout' => 'vertical',
                                'contents' =>   [
                                    [
                                        'type' => 'box',
                                        'layout' => 'horizontal', 
                                        'contents' =>   [
                                            ['type' => 'text','text' => '正常稼働しない場合は']
                                        ]
                                    ],
                                    [
                                        'type' => 'box',
                                        'layout' => 'horizontal',
                                        'contents' =>   [
                                            ['type' => 'text','text' => 'Line ID：a22853398にご連絡ください']
                                        ]
                                    ]
                                ]
                            ],
                            "styles" => [
                                "body" => [
                                    "separator" => true
                                ],
                                "footer" => [
                                    "separator" => true
                                ]
                            ]
                        ]
                    ]
                ]
                
            ];
            
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "狗狗滾":
        case "是的，狗狗請滾":
            //發送遺言
            $messageData = [ 'type' => 'text', 'text' => "狗狗不想滾，不過狗狗不能違逆主人，啊嗚～" ];
            $response = [ 'replyToken' => $replyToken, 'messages' => [$messageData] ]; 
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            curl_setopt($ch, CURLOPT_POST, true); 
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json; charser=UTF-8', 'Authorization: Bearer ' . $accessToken )); 
            $result = curl_exec($ch);
            curl_close($ch);
            //呼叫離開群組API
            $ch = curl_init('https://api.line.me/v2/bot/group/'.$source_groupId.'/leave');
            break;
        case "本月日營業額":
            $messageData = [ 'type' => 'text', 'text' => getSalesDailyMessage() ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "本月32G點讀筆售出數量":
            $messageData = [ 'type' => 'text', 'text' => getPenSoldMessage(array("Y340","Y341")) ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "本月16G點讀筆售出數量":
            $messageData = [ 'type' => 'text', 'text' => getPenSoldMessage(array("Y335","Y336","Y337")) ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "本月暢銷排行榜":
            $messageData = [ 'type' => 'text', 'text' => getSalesQuantityRankMessage() ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "本月最賺的排行榜":
            $messageData = [ 'type' => 'text', 'text' => getSalesAmountRankMessage() ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "搔狗狗肚肚":
            $shy = "不可以色色".getRandomEmoji();
            $messageData = [ 'type' => 'text', 'text' => $shy ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "上個月的日營業額":
            $messageData = [ "type" => "text", "text" => getSalesDailyMessage_past("上月") ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "去年同期的日營業額":
            $messageData = [ "type" => "text", "text" => getSalesDailyMessage_past("去年同期") ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "今年的每月營業額":
            $messageData = [ "type" => "text", "text" => getSalesMonthly("今年每月") ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "去年的每月營業額":
            $messageData = [ "type" => "text", "text" => getSalesMonthly("去年每月") ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "本月每日訂單數量":
            $messageData = [ "type" => "text", "text" => getOrderCount("本月每日") ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "上個月的每日訂單數量":
            $messageData = [ "type" => "text", "text" => getOrderCount("上月每日") ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "去年同期每日訂單數量":
            $messageData = [ "type" => "text", "text" => getOrderCount("去年同期每日") ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "叫狗狗離開群組":
            $messageData =  [   
                        'type' => 'template', 
                        'altText' => '要用手機才可以叫狗狗走',
                        'template' =>   [
                                            'type' => 'confirm',
                                            'text' => '你確定要叫狗狗走嗎？用手機才能叫狗狗走',
                                            'actions' =>    [
                                                                [
                                                                    'type' => 'message',
                                                                    'label' => '是的',
                                                                    'text' => '是的，狗狗請滾'
                                                                ],
                                                                [
                                                                    'type' => 'message',
                                                                    'label' => '不要',
                                                                    'text' => '不要，狗狗留下來'
                                                                ]
                                                            ]
                                        ]
                    ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        case "不要，狗狗留下來":
            $messageData = [ 'type' => 'text', 'text' => '狗狗也很高興可以繼續留下來！汪汪' ];
            $ch = curl_init('https://api.line.me/v2/bot/message/reply');
            break;
        default:
            break;
    }
}else if($message->{"text"} != null || $message->{"text"} != ""){
    if($source_groupId != ""){
        $message->{"text"} = "群組ID不正確，這群組的ID：".$source_groupId."\n";
        $messageData = [ 'type' => 'text', 'text' => $message->{"text"} ];
    }else if($source_groupId == ""){
        $message->{"text"} = "UserID不正確，你的UserID：".$source_usrId."\n";
        $messageData = [ 'type' => 'text', 'text' => $message->{"text"} ];
    }
    $ch = curl_init('https://api.line.me/v2/bot/message/reply');
}




//$messageData = [ 'type' => 'text', 'text' => $message->{"text"} ];
$response = [ 'replyToken' => $replyToken, 'messages' => [$messageData] ]; 

/* 執行區 */
curl_setopt($ch, CURLOPT_POST, true); 
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
error_log(json_encode($response));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));
curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json; charser=UTF-8', 'Authorization: Bearer ' . $accessToken )); 
$result = curl_exec($ch); 
error_log($result); 
curl_close($ch);
?>