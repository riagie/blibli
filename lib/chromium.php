<?php

namespace lib\Chromium;

// use Curl\Curl;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Chromium 
{
    public $options;
    public $capabilities;
    public $chrome_driver = false;
    public $element;
    public $instance = array();

    public function __construct() 
    {
        $this->BASE_URL = URL;
        $this->taskkill();
        putenv('WEBDRIVER_CHROME_DRIVER='. CHROME_DRIVER);

        foreach (unserialize(ACCOUNT) as $instance => $value) {
            if (fopen(str_replace('##', $instance, CHROMIUM), 'r') == false) {
                copy(str_replace('##', '', CHROMIUM), str_replace('##', $instance, CHROMIUM));
            }

            $this->chrome_driver[$instance]  = $chrome_driver;
            $this->options[$instance]        = new ChromeOptions;
            $this->options[$instance]->setBinary(str_replace('##', $instance, CHROMIUM));
            $this->options[$instance]->addArguments([
                '--disable-blink-features=AutomationControlled',
                '--disable-notifications',
                '--disable-infobars',
                '--disable-logging',
                '--window-size=0,700',
                '--window-position=0,0',
                '--force-device-scale-factor=0.5',
                '--user-data-dir='.CACHE.'/'.DRIVER.$instance,
                '--no-sandbox',
                // '--headless',
            ]);

            $this->capabilities[$instance]   = new DesiredCapabilities;
            $this->capabilities[$instance]->setCapability(ChromeOptions::CAPABILITY_W3C, $this->options[$instance]);

            if ($this->chrome_driver[$instance] == false || strpos($this->tasklist(), 'No tasks')) {
                $this->chrome_driver[$instance] = ChromeDriver::start($this->capabilities[$instance]);
            }
        }
    }

    public function chromium_init($url, $name) 
    {
        foreach (unserialize(ACCOUNT) as $instance => $value) {
            $account = explode('|', $value);
            if (count($account) != 2 || array_search($instance, $this->instance)) continue;
            $this->instance[$instance] = $instance;
            
            try {
                if ($this->checkout($instance, $url, $name, $account[0], $account[1])) {
                    $data[]     = $name;
                    $message    = "order success ".$name."\n";
                    echo    $message;
                    $this->telegram($message);

                    $this->instance[$instance] = $name;
                    unset($this->instance[$instance]);
                }
            } catch (Exception $error) {
                $message = "order failed ".$name." description ".$error->getMessage().".\n";
                echo     $message;
                $this->telegram($message);
                $this->chrome_driver_close($instance);
            }
        }

        if ($data == true) {
            return array (
                'total' => count($data),
                'data'  => $this->Json('encode', $data),
            );
        }

        return false;
    }

    public function checkout($instance, $url, $name, $username, $password)
    {
        $this->chrome_driver[$instance]->get($this->BASE_URL.$url);
        $this->timeouts($instance);
        $this->element = $this->chrome_driver_by($instance, 'xpath', CHECKOUT)->click();
        $this->element_loading($instance);
        
        if (strpos($this->chrome_driver_by($instance, 'xpath', VALIDATION)->getDomProperty('innerText'), LOGIN_PRE)) {
            $this->element = $this->chrome_driver_by($instance, 'xpath', LOGIN_USER)->sendKeys($username);
            $this->element = $this->chrome_driver_by($instance, 'xpath', LOGIN_PASSWORD)->sendKeys($password);
            $this->element = $this->chrome_driver_by($instance, 'xpath', LOGIN_SUBMIT)->click();
            $this->element_loading($instance);

            if (strpos($this->chrome_driver_by($instance, 'xpath', VALIDATION)->getDomProperty('innerText'), LOGIN_OTP)) {
                $this->element = $this->chrome_driver_by($instance, 'xpath', LOGIN_OTP_SUBMIT)->click();
                $this->element_loading($instance);
                sleep(MAX_SLEEP);
                if (RETRY == 1) { 
                    $this->checkout($instance, $url, $name, $username, $password);
                }

                return false;
            }

            $this->chrome_driver[$instance]->get($this->BASE_URL.$url);
            $this->element = $this->chrome_driver_by($instance, 'xpath', CHECKOUT)->click();
            $this->element_loading($instance);
        }
        
        if (strpos($this->chrome_driver_by($instance, 'xpath', VALIDATION)->getDomProperty('innerText'), PHONE_VERIFICATION)) {
            $this->element = $this->chrome_driver_by($instance, 'xpath', PHONE_NOTIFICATION)->click();
            $this->element_loading($instance);
        }

        if (strpos($this->chrome_driver_by($instance, 'xpath', VALIDATION)->getDomProperty('innerHTML'), ADDRESS_VERIFICATION)) {
            $this->element = $this->chrome_driver_by($instance, 'xpath', ADDRESS_SUBMIT)->click();
            $this->element_loading($instance);
        }

        if (strpos($this->chrome_driver_by($instance, 'xpath', VALIDATION)->getDomProperty('innerHTML'), CHECKOUT_OVER)) {
            $this->DEBUG('instance '.$instance.' checkout CHECKOUT_OVER', true);
            
            return false;
        }

        if (strpos($this->chrome_driver_by($instance, 'xpath', VALIDATION)->getDomProperty('innerHTML'), VOUCHERS_NOTIFICATION) === false) {
            $this->element = $this->chrome_driver_by($instance, 'xpath', VOUCHERS_PRE)->click();
            $this->element_loading($instance);

            $this->element = $this->chrome_driver_by($instance, 'xpath', VOUCHERS)->sendKeys(VOUCHER);
            $this->element = $this->chrome_driver_by($instance, 'xpath', VOUCHERS_SUBMIT)->click();
            $this->refresh();
            $this->element_loading($instance);
        }

        $this->element = $this->chrome_driver_by($instance, 'xpath', SHIPMENT_SUBMIT)->click();
        $this->element_loading($instance);

        $this->element = $this->chrome_driver_by($instance, 'xpath', PAYMENT_SUBMIT)->click();
        $this->element_loading($instance);
        
        if (strpos($this->chrome_driver_by($instance, 'xpath', VALIDATION)->getDomProperty('innerHTML'), PAYMENT_OPTION) === false) {
            $this->element = $this->chrome_driver_by($instance, 'xpath', PAYMENT_SELECT)->click();
            $this->element = $this->chrome_driver_by($instance, 'xpath', PAYMENT_SELECT_SUBMIT)->click();
            $this->element_loading($instance);
        }

        $this->element = $this->chrome_driver_by($instance, 'xpath', PAYMENT_NOW)->click();
        $this->element_loading($instance);
        
        return true;
    }

    public function chrome_driver_by($instance, $mechanism, $value = false) 
    {
        $mechanism = (is_string($mechanism)) ? strval($mechanism):$mechanism;
        if ($value == true) {
            return $this->chrome_driver[$instance]->findElement(WebDriverBy::$mechanism($value));
        }

        return $this->chrome_driver[$instance]->findElement(WebDriverBy::$mechanism());
    }

    public function refresh()
    {
        return $this->chrome_driver->get($this->chrome_driver->getCurrentUrl());
    }

    public function element_loading($instance)
    {
        if ($this->instance[$instance] == false) {
            sleep(MIN_SLEEP);
        }
        sleep(1);
        $element = $this->chrome_driver_by($instance, 'xpath', VALIDATION)->getDomProperty('innerText');
        if (strpos($element, ELEMENT_LOADING)) {
            $this->element_loading($instance);
        }
        
        return true;
    }

    public function timeouts($instance, $string = false, $second = MAX_TIMEOUT)
    {
        if ($string == true) {
            return $this->chrome_driver[$instance]->wait()->until($string);
        }

        return $this->chrome_driver[$instance]->manage()->timeouts()->implicitlyWait($second);
    }

    public function chrome_driver_close($instance)
    {
        if ($this->chrome_driver[$instance] == true) {
            return $this->chrome_driver[$instance]->quit();
        }

        return false;
    }

    public function taskkill()
    {
        if (strpos($this->tasklist(), 'No tasks') === false) {
            return shell_exec('taskkill /F /IM "' . DRIVER . '*"');
        }

        return true;
    }

    public function tasklist()
    {
        return shell_exec('tasklist /v /fo csv /fi "IMAGENAME eq ' . DRIVER . '*"');
    }
}
