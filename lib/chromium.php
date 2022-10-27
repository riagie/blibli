<?php

namespace lib\Chromium;

// use Curl\Curl;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

use Facebook\WebDriver\WebDriverKeys;

class Chromium 
{
    public $driver = false;
    public $element;
    public $BASE_URL;
    public $username = false;
    public $productName;

    public function __construct() 
    {
        $this->taskKill();
        putenv('WEBDRIVER_CHROME_DRIVER='. CHROME_DRIVER);
        foreach (unserialize(ACCOUNT) as $number => $value) {
            // $number = $number + 1;
            if (fopen(str_replace('##', $number, CHROMIUM), 'r') == false) {
                copy(str_replace('##', '', CHROMIUM), str_replace('##', $number, CHROMIUM));
            }
            
            $options[$number] = new ChromeOptions;
            $options[$number]->setBinary(str_replace('##', $number, CHROMIUM));
            $options[$number]->addArguments([
                '--disable-blink-features=AutomationControlled',
                '--disable-notifications',
                '--disable-infobars',
                '--disable-logging',
                '--window-size=0,700',
                '--window-position=0,0',
                '--force-device-scale-factor=0.5',
                '--user-data-dir='.CACHE.'/'.DRIVER.$number,
                '--no-sandbox',
                // '--headless',
            ]);
            
            $capabilities[$number] = new DesiredCapabilities;
            $capabilities[$number]->setCapability(ChromeOptions::CAPABILITY_W3C, $options[$number]);
            $this->driver[$number] = $driver;
            if ($this->driver[$number] == false) {
                $this->driver[$number] = ChromeDriver::start($capabilities[$number]);
            }
        }
    }

    public function chromium_init($url, $name) 
    {
        foreach (unserialize(ACCOUNT) as $number => $value) {
            // $number = $number + 1;
            $account = explode('|', $value);
            if (count($account) != 2 || $this->username) continue;

            $this->BASE_URL = $url;
            $this->username = $account[0];
            $this->productName = $name;

            if ($this->checkout($number, $account[1])) {
                $data[] = $name;
                $this->telegram("order success ".$name."\n");
                unset($this->username);
            } else {
                $this->telegram("order failed ".$name.".\n");
                unset($this->username);
            }
        }

        if ($data) return array ('total' => count($data), 'data'  => $this->Json('encode', $data));

        return false;
    }

    public function login($driver, $password) 
    {
        if (strpos($this->Element($driver), LOGIN)) {
            $driver->findElement(WebDriverBy::xpath(USER))->sendKeys($this->username);
            $driver->findElement(WebDriverBy::xpath(PASSWORD))->sendKeys($password);
            $driver->findElement(WebDriverBy::xpath(LOGIN_IN))->click();
            $this->Element($driver);
            if (strpos($this->Element($driver), LOGIN_ERR) || 
                strpos($this->Element($driver), LOGIN_ERR2) || 
                strpos($this->Element($driver), LOGIN_ERR3)) {
                $this->DEBUG($this->username." LOGIN_ERR/LOGIN_ERR2/LOGIN_ERR3.\n", true);
                $this->telegram($this->username." LOGIN_ERR/LOGIN_ERR2/LOGIN_ERR3.\n");

                return false;
            }

            if (strpos($this->Element($driver), OTP)) {
                $driver->findElement(WebDriverBy::xpath(OTP_IN))->click();
                $this->Element($driver);
                $this->telegram($this->username." OTP_IN.\n");
                if (MAX_RETRY == 0) {
                    return false;
                }

                sleep(MAX_SLEEP);
                $driver->get($driver->getCurrentUrl());
                $this->Element($driver);
                $this->login($driver, $password);
            }
            
            return true;
        }

        $element = $driver->findElement(WebDriverBy::xpath(VALIDATION));
        $element = $element->getDomProperty('baseURI');
        if (strpos($element, '/verify-phone-number')) {
            if (strpos($this->Element($driver), PHONE_PASS)) {
                $driver->findElement(WebDriverBy::xpath(PHONE_PASS_IN))->click();
                $this->Element($driver);

                return true;
            }
        }

        return true;
    }

    public function checkout($number, $password)
    {
        $driver = $this->driver[$number];
        $driver->get($this->BASE_URL);
        $driver->manage()->timeouts()->implicitlyWait(MAX_TIMEOUT);
        if ($this->Loading($driver, $this->BASE_URL) == false) {
            return false;
        }
        
        if (strpos($this->Element($driver), OUT_OF_STOCK) || 
            strpos($this->Element($driver), OUT_OF_READY)) {
            $this->telegram($number." ".$this->productName." OUT_OF_STOCK/OUT_OF_READY.\n");
            return false;
        }

        if (PRODUCT == '') {
            $driver->findElement(WebDriverBy::xpath(CHECKOUT))->click();
            $this->Element($driver);
            if ($this->login($driver, $password) == false) {
                return false;
            }
            $this->Element($driver);
        } else {
            $driver->findElement(WebDriverBy::xpath(ADD_TO_BAG))->click();
            $driver->findElement(WebDriverBy::xpath(BAG_IN))->click();
            $driver->get(CART);
            $driver->manage()->timeouts()->implicitlyWait(MAX_TIMEOUT);
            $this->Element($driver);
            if ($this->login($driver, $password) == false) {
                return false;
            }
            $this->Element($driver);
            if ($this->Loading($driver, '/cart') == false) {
                return false;
            }
            $cart = $driver->findElement(WebDriverBy::xpath(CART_QTY));
            $cart->click();
            $cart->sendKeys(WebDriverKeys::END);
            $cart->sendKeys(WebDriverKeys::BACKSPACE);
            $cart->sendKeys(WebDriverKeys::BACKSPACE);
            $cart->sendKeys(WebDriverKeys::BACKSPACE);
            $cart->sendKeys(WebDriverKeys::BACKSPACE);
            $cart->sendKeys(QTY);
            $driver->findElement(WebDriverBy::xpath(CART_CHECKOUT))->click();
            $this->Element($driver);
        }
        
        if (strpos($this->Element($driver), ADDRESS_ERR)) {
            $driver->findElement(WebDriverBy::xpath(ADDRESS_IN))->click();
            $this->Element($driver);
        }

        if (strpos($this->Element($driver), CHECKOUT_OVER)) {
            $this->DEBUG($this->username." CHECKOUT_OVER.\n", true);
            $this->telegram($this->username." CHECKOUT_OVER.\n");

            return false;
        }
        
        if ($this->Loading($driver, '/shipment') == false) {
            return false;
        }

        if (strpos($this->Element($driver), PHONE_ERR)) {
            if (strpos($this->Element($driver), PHONE_ERR2)) {
                $driver->findElement(WebDriverBy::xpath(PHONE_IN))->click();
                $this->Element($driver);
            }
        }

        if (strpos($this->Element($driver), VOUCHERS_ERR) || 
            strpos($this->Element($driver), VOUCHERS_ERR2)) {
                $driver->findElement(WebDriverBy::xpath(VOUCHERS_IN))->click();
                $this->Element($driver);
                $driver->findElement(WebDriverBy::xpath(VOUCHERS))->sendKeys(VOUCHER);
                $driver->findElement(WebDriverBy::xpath(PAKAI))->click();
                $driver->get($driver->getCurrentUrl());
                $this->Element($driver);
        }

        if (strpos($this->Element($driver), SHIPMENT_ERR)) {
            $driver->findElement(WebDriverBy::xpath(SHIPMENT_IN))->click();
            $this->Element($driver);
        }

        if ($this->Loading($driver, '/payment') == false) {
            return false;
        }

        if (strpos($this->Element($driver), PAYMENT_METHOD_ERR)) {
            $driver->findElement(WebDriverBy::xpath(PAYMENT_METHOD_IN))->click();
            $this->Element($driver);
            $shipment = $driver->findElement(WebDriverBy::xpath(PAYMENT_METHOD_SELECT))->click();
            $this->Element($driver);
            $shipment = $driver->findElement(WebDriverBy::xpath(PAYMENT_SELECT_IN))->click();
            $this->Element($driver);
        }

        if (strpos($this->Element($driver), PAYMENT_ERR)) {
            $driver->findElement(WebDriverBy::xpath(PAYMENT_IN))->click();
            $this->Element($driver);
        }

        if (strpos($this->Element($driver), THANKYOU)) {
            return true;
        }

        return false;
    }

    public function Loading($driver, $URI, $maxRetry = 0)
    {
        $driver->manage()->timeouts()->implicitlyWait(MAX_TIMEOUT);
        $element = $driver->findElement(WebDriverBy::xpath(VALIDATION));
        $element = urldecode($element->getDomProperty('baseURI'));
        $element = preg_replace('/\s+/', '+', $element);
        if (strpos($element, $URI) === false) {
            if ($maxRetry == MAX_RETRY) {
                return false;
            }
            sleep(MIN_SLEEP);
            $maxRetry   = $maxRetry + 1;
            $element    = $this->Loading($driver, $URI, $maxRetry);
        }

        return $element;
    }

    public function Element($driver)
    {
        $driver->manage()->timeouts()->implicitlyWait(MAX_TIMEOUT);
        $element = $driver->findElement(WebDriverBy::xpath(VALIDATION));
        $element = $element->getDomProperty('innerText');
        if (strpos($element, LOADING)) {
            sleep(MIN_SLEEP);
            $element = $this->Element($driver);
        }
        
        return $element;
    }

    public function driverClose($driver)
    {
        return $driver->quit();
    }

    public function taskKill()
    {
        $taskkill = 'taskkill /F /IM "' . DRIVER . '*"';
        if (strpos($this->taskList(), 'No tasks')) {
            return true;
        }
        
        return shell_exec($taskkill);
    }

    public function taskList()
    {
        $tasklist = 'tasklist /v /fo csv /fi "IMAGENAME eq ' . DRIVER . '*"';
        return shell_exec($tasklist);
    }
}
