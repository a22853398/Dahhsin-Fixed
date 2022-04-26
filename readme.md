# 応募用公開するコード
名前：頼威宏

## 概要
日本語教科書出版社、情報システム部門（社内SE）\
公式サイトのURL：https://www.dahhsin.com.tw/   

## 説明
サイトの設立は2013年にウェブサイト制作会社に頼んで、2014年第一期から運営開始する   \
当時、経費が足りなかった為、レスポンシブデザインを使っている   
- 2017年の時、モバイル版のウェブサイトを同じSIerに開発依頼
- 2020年6月ウェブサイトのサーバーをGCPに引っ越し
- 2021年2月にずっと保守業務を担当されていたSIerと解約された。ウェブサイトの保守運用が情シスの業務内容になる

## 主な事件
| 時間 | 説明 |
| ---- | ---- |
| 2021年2月 | 契約されたSIer会社は事業内容変更な為、公式サイトを保守する企業がなくなった |
| 2021年3月 | 物流会社のシステム更新と伴い、利用している物流マップシステムの更新を余儀なくされて、急遽新しいSIerに発注 |
| 2021年5月 | 安全強化な為、Google reCaptcha v2を公式サイトに導入と追加 |
| 2021年7月 | CMSにマーケティング用の統計、分析データを見れるようなダッシュボードを追加 |
| 2021年11月 | ハッカーが社員のパソコンを攻撃して、CMSへアクセスし、顧客の個人情報を取得。お客様から絶えない苦情電話が殺到、会社への不信を募るばかり |
| 2022年4月 | 公式サイトで使われているシステム送信専用メールアドレスがハッキングされた |

## サイト構造説明
2022年4月時点\
cPanel社の「WHM & cPanel features」をGCPのサーバーにインストールし、cPanelを通じて管理している。\
PHP version：7.2.34\
Apache Version	2.4.53\
MySQL version：5.7.37\
Operating System：linux\
\
デプロイはpublic_htmlのフォルダー全体\
構造は公式サイトの中に、CMSのフォルダー、モバイル版のフォルダーがある\
例：\
公式サイトのURLは https://www.dahhsin.com.tw/ に対して 公式サイトのモバイル版は https://www.dahhsin.com.tw/m/  \
安全対策な為、サイト全体のコードを公開してはならない。以下、修正した箇所のコードのみを公開する。\
\
また、PHPのフレームワークを使用していない。PHPとHTMLを分離するには、TemplatePowerを使っている。

## フォルダー説明
| フォルダー名 | 変動時期 | 説明 | URL |
| ---- | ---- | ---- | ---- |
| mailtous | 2022.02 | お問い合わせフォーム | https://www.dahhsin.com.tw/aboutMailUs.php |
| faq | 2021.08 | 元々はサイト上固定されたよくある質問ですが、CMSで添削できるようにした| https://www.dahhsin.com.tw/faq.php |
| security | 2021.05~2021.12 | googleのreCaptchaを使った。\またハッカーにより攻撃された後、- 顧客の個人情報 - 注文情報の暗号化 - CMSログインの二重認証を実装した | https://www.dahhsin.com.tw/processesOrder02.php   https://www.dahhsin.com.tw/manager/01login.php |
| download | 2021.09 | ペンの音声ファイルのみが表示されたが、本の画像、CDの音声ファイルの一緒に表示されるようにした | https://www.dahhsin.com.tw/downLoad.php?lv01_type=EJPN-Basic&mu_type=01 |
| survey | 2021.11 | アンケート機能を新規追加した。CMSはお客様から頂いた回答を見れるように改修\でもCMSに新規アンケートを開始する機能はまだ作っていない | https://www.dahhsin.com.tw/survey.php |
| salesDataSearch | 2021.06 | CMSにて統計データが見れるようにした。以前は情シスが直接phpMyAdminでSQL文を入力してデータを取り出していた | https://www.dahhsin.com.tw/manager/01login.php |
| getCouponSelf | 2021.10 | 以前は情シスが手動でクーポンをお客様のメールアドレスに送付していたが、クーポンの使用率を考えて、またお客様が情シスを待たずにクーポンを取得できるよう、お客様が自分いつでもクーポンを獲得できるようにした\またクーポンの使用履歴ページも作った | https://www.dahhsin.com.tw/memberCoupon.php   https://www.dahhsin.com.tw/memberCouponHave.php |
| sendAnswer | 2021.05 | 教科書の回答がお客様自分でいつでも取れるように自動送信ページを作った\以前はカスタマーサービスが手動で回答を送るそうだ。\代理契約の関係で回答を本に付いてはいけないという。 | https://www.dahhsin.com.tw/sendAnswer_1.php |
| buttons| 2021.05 | 全サイトの右下にお問合せ用のボタンを追加した | https://www.dahhsin.com.tw/ |
| saiyou| 2021.12 | | https://www.dahhsin.com.tw/saiyou.php |





