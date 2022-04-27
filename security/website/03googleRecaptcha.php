<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- reCaptcha -->
<div style="font-weight:bold; color:rgb(2,14,140);">下方為驗證區，如無法顯示請換瀏覽器</div>
<div class="g-recaptcha" 
    data-sitekey="6LdGyskaAAAAAKaDo31mJolZ2-aP7ysqSkpUprRQ" 
    data-theme="light" 
    data-size="normal"
    data-callback="verify"
    data-expired-callback="expired"
    data-error-callback="errorExpired">
</div>
<div id="captcha_answer" style="font-weight:bold; font-size:16px; margin-top:2%; font-weight:bold; color:red;"></div>
<script>
    var captcha_answer = document.getElementById("captcha_answer");
    function verify(){
        captcha_answer.innerHTML = "驗證成功";
    }
    function expired(){
        captcha_answer.innerHTML = "驗證過期";
    }
    function errorExpired(){
        captcha_answer.innerHTML = "驗證失敗";
    }
    //前端驗證我不是機器人有沒有打勾
    function captchaCheck(){
        var gResponse = grecaptcha.getResponse();
        if(typeof(gResponse) !== 'string' || gResponse === '') {
            captcha_answer.innerHTML = "請進行驗證";
            return false;
        }else{
            return true;
        }
    }
</script>