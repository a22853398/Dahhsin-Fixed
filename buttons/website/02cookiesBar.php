<style>
    .cookie-banner {
        position: fixed;
        right: 0;
        bottom: 0;
        min-width: 100%;
        background: rgb(240,240,240);
        color: black;
        text-align: center;
        border-top: 3px solid silver;
        z-index: 20;
        -webkit-user-select: none;  /* Chrome all / Safari all */
        -moz-user-select: none;     /* Firefox all */
        -ms-user-select: none;      /* IE 10+ */
        user-select: none;          /* Likely future */
    }
    
    .cookie-banner p {
        font-size: 1rem;
        color: darkred;
        padding: 0.5% 3% 0.5% 3%;
        font-weight: bold;
    }
            
    .cookie-banner a {
        color: red;
        cursor: pointer;
    }
    .cookie-banner a:hover{
        color: magenta;
    }
            
    .cookie-btn {
        background: navy;
        border: 0;
        color: white;
        padding: 0.3rem 1rem;
        font-size: 1rem;
        font-weight: bold;
        margin-bottom: 0.5%;
        border-radius: 0.5rem;
        cursor: pointer;
    }
    @media only screen and (max-width: 768px){
        .cookie-banner p{
            padding: 5% 3% 0% 3%;
        }
        .cookie-btn{
            margin-bottom: 3%;
        }
    }
</style>
<div class="cookie-banner js-cookie-banner">
    <!--p>本公司網站使用 Cookies 做行銷行為分析使用。欲知詳情請閱覽 <a>Cookies Policy</a> 。</p-->
    <p>最近有詐騙集團冒充本公司員工打電話給客人要求操作ATM或信用卡，請勿依照指示操作！查看<a href="newsDetail.php?lv01_id=M211107000158eb">防詐騙聲明</a></p>
    <button class="cookie-btn">確定</button>
</div>
<script>
    if (localStorage.getItem("cookieBannerDisplayed")) {
        document.querySelector('.js-cookie-banner').remove();
    } else {
        function dismiss() {
            document.querySelector('.js-cookie-banner').remove();
            localStorage.setItem("cookieBannerDisplayed", "true");
        }
        const buttonElement = document.querySelector('.cookie-btn');
        if (buttonElement) {
            buttonElement.addEventListener('click', dismiss);
        }
    }
</script>