<style>
    /* 展開的按鈕 */
    #btn_tenkai{
        display: block;
        position: fixed;
        right: 18px;
        bottom: 20px;    
        /* padding: 10px 15px; */    
        /* font-size: 20px; */
        /* background: #777; */
        color: white;
        cursor: pointer;
        opacity: 100%;
    }
    
    /*黑黑的那一片(設定隱形，內部搭載按鈕)*/
    .btn_modal{
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 10; /* Sit on top */
        /* padding-top: 0; */ /* Location of the box */
        right: 0px;
        bottom: 80px;
        width: 100px; /* Full width */
        height: 220px; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.0); /* Black w/ opacity */
        margin-right: auto;
        margin-top: auto; 
        float: right;
        
        
    }
    .btn_modal_out{
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 10; /* Sit on top */
        /* padding-top: 0; */ /* Location of the box */
        right: 0px;
        bottom: 80px;
        width: 100px; /* Full width */
        height: 260px; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.0); /* Black w/ opacity */
        margin-right: auto;
        margin-top: auto; 
        float: right;
        
        
    }
    
    /* Line圖案 */
    .btn_modal_content{
        display: block;
        padding-right: 18px;
        padding-top: 10px;
        float: right;
        opacity: 100%;
        cursor: pointer;
        
        -webkit-animation-name: zoom;
        -webkit-animation-duration: 0.5s;
        animation-name: zoom;
        animation-duration: 0.5s;
    }
    
    .btn_modal_switch{
        display: block;
        padding-right: 18px;
        padding-top: 10px;
        float: right;
        opacity: 100%;
        cursor: pointer;
        
        -webkit-animation-name: zoomout;
        -webkit-animation-duration: 0.5s;
        animation-name: zoomout;
        animation-duration: 0.5s;
    }
    
    /* The Close Button 關掉的按鈕*/
    .close {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #f1f1f1;
        font-size: 20px;
        font-weight: bold;
        transition: 0.3s;
    }
    .close:hover,
    .close:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }
    
    
    /* Add Animation 出現時動畫*/
    /*
    .btn_modal_content{  
        -webkit-animation-name: zoom;
        -webkit-animation-duration: 0.5s;
        animation-name: zoom;
        animation-duration: 0.5s;
    }
    */
    
    /* 由下往上飄移 */
    @-webkit-keyframes zoom {
      from {-webkit-transform:translate(0px, 150px)} 
      to {-webkit-transform:translate(0px, 0px)}
    }
    @keyframes zoom {
      from {transform:translate(0px, 150px)} 
      to {transform:translate(0px, 0px)}
    }
    /*
    .btn_modal_out{
      -webkit-animation-name: zoomout;
      -webkit-animation-duration: 0.5s;
      animation-name: zoomout;
      animation-duration: 0.5s; 
    }
    */
    @-webkit-keyframes zoomout {
      from {-webkit-transform:translate(0px, 0px)} 
      to {-webkit-transform:translate(0px, 150px)}
    }
    @keyframes zoomout {
      from {transform:translate(0px, 0px)} 
      to {transform:translate(0px, 150px)}
    }
    
</style>

<img src="images/btn_msg.png" id="btn_tenkai" class="btn_tenkai" onclick="act_tenkai();">
<div class="btn_modal" id="div_modal">
    <!--span class="close" id="myClose" onclick="spanClick();">&times;</span-->
    <a href="https://www.messenger.com/t/dahhsin" target="_blank" onclick="sendLineNotify('mobile FB button', 5000);"><img class="" src="images/btn_fbmsg_2.png" id="btn_fb"></a>
    <a href="https://line.me/R/ti/p/@dahhsin" target="" onclick="sendLineNotify('mobile FB button', 5000);"><img class="" src="images/btn_line.png" id="btn_line"></a>
    <a target="" onclick="sendLineNotify('mobile FB button', 5000);"><img class="" src="../images/faq_icon_yellow_caution.png" id="sagi"></a>
</div>
<script type="text/javascript">
    window.onload = function(){
        act_tenkai();
        document.getElementById("sagi").addEventListener("click", function(){window.location="newsDetail.php?lv01_id=M211107000158eb";});
    }
    let isClicked = true;
    function sendLineNotify(name, times){
        if(isClicked){
            isClicked = false;
            //$.post("https://www.dahhsin.com.tw/03lineNotify.php", {name: name, comefrom: location.href});
            setTimeout(function(){
                isClicked = true;
            }, times);
        }
    }
    function act_tenkai(){
        var btn_fb = document.getElementById("btn_fb");
        var btn_line = document.getElementById("btn_line");
        var div_modal = document.getElementById("div_modal");
        var btn_tenkai= document.getElementById("btn_tenkai");
        var sagi = document.getElementById("sagi");
        //alert('按鈕維修中'+btn_fb);
        if(div_modal.style.display=='none'){
            btn_tenkai.style.opacity ="70%";
            div_modal.style.display='block';
            btn_fb.classList.add('btn_modal_content');
            btn_fb.classList.remove('btn_modal_switch');
            btn_line.classList.add('btn_modal_content');
            btn_line.classList.remove('btn_modal_switch');
            sagi.classList.add('btn_modal_content');
            sagi.classList.remove('btn_modal_switch');
            sendLineNotify('mobile FAQ button', 500);
        }else{
            btn_tenkai.style.opacity ="100%";
            btn_fb.classList.add('btn_modal_switch');
            btn_fb.classList.remove('btn_modal_content');
            btn_line.classList.add('btn_modal_switch');
            btn_line.classList.remove('btn_modal_content');
            sagi.classList.add('btn_modal_switch');
            sagi.classList.remove('btn_modal_content');
            setTimeout(function(){
                div_modal.style.display='none';
            }, 450);
        }
    }
</script>
