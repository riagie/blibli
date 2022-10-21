blibli-auto-checkout-flash-sale
==

Auto Checkout Flash Sale Blibli

### Getting started
1. blibli login username and password in the edit file **inc/config** name *USER* and *PASSWORD*
2. get token and userid telegram
```
- in the search menu enter @botFather, then please click on the account.
- enter command /start
- select /newbot
- then we fill in the name for the bot that we made
- then we have to fill in the username for the bot that we created must end with the word bot
- after confirmation that the bot account has been successfully created, you will be given a token which will be used for later access to the Telegram API. Don't forget to save the token in the edit file **inc/config** name *TELEGRAM*
- go to the search menu and type get_id_bot, click the account and click start. Then we will get our account chat_id information and save it in the edit file **inc/config** name *T_ID*
- chat with the username that was created by the /start command
```
3. if you have a voucher code, save it in the edit file **inc/config** name *VOUCHER*
4. double click blibli.bat

### Description
1. download PHP 7.3.9 portable and save it in the **lib/** folder with the folder name *php*
2. download chromium Version 92.0.4515.0 portable and save it in the **lib/** folder with the name *chromium* folder
3. download chromedriver.exe according to the downloaded chromium version and save it in the **lib/chromium** folder
4. voucher code can only use 1
5. untuk pemilihan kategori flashsale silakan masukan pada file **inc/config** name *PRODUCT_GROUP* apabila kosong maka akan mengambil semua product flashsale, contoh kategori
```
Z10K = 10RB zona zone
2HD-1 = 2 Hours until
BLK-1 = Local Bill
GAD-1 = Gadgets & Electronics
RUM-1 = Household
FAS-1 = Fashion & Sport
KKE-1 = Health & Beauty
HOB-1 = Toys & Automotive
VOU-1 = Vouchers & Investment
```
```
- all products define('PRODUCT_GROUP', "");
- selected products, for example **Gadgets & Electronics** define('PRODUCT_GROUP', "GAD-1");
```
6. when using it for the first time, please monitor the browser screen because there will be OTP verification for new users/devices/devices from blibli.
7. monitor multiple trx on browser screen to save browser cache when new using
8. use a very fast and at least stable internet connection
9. there is a daily limit order or pending payment order from blibli
10. reuse the new user information, please delete the *cache* folder in the **lib/chromium** folder

### Developer mode
```
use lib\Blibli\Blibli;
require('lib/chromium.php');
require('lib/blibli.php');
$blibli = new Blibli();
```

take all flashsale product data
```
$blibli->blibli_init();
```

order flashsale products from the url and name you get from get product
```
$blibli->chromium_init(url, name);
```

notification to telegram chat
```
$blibli->telegram($message);
```
