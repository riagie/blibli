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

    public function __construct() 
    {
        putenv('WEBDRIVER_CHROME_DRIVER='. CHROME_DRIVER);
        $this->driver       = pathinfo(CHROMIUM)['basename'];
        $this->options      = new ChromeOptions;
        $this->capabilities = new DesiredCapabilities;
        $this->options->setBinary(CHROMIUM);
        $this->options->addArguments([
            '--disable-blink-features=AutomationControlled',
            '--disable-notifications',
            '--disable-infobars',
            '--disable-logging',
            '--window-size=0,700',
            '--window-position=0,0',
            '--force-device-scale-factor=0.5',
            '--user-data-dir='.CACHE,
            '--no-sandbox',
            // '--headless',
        ]);
        $this->capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $this->options);
        $this->chrome_driver = $chrome_driver;

        $this->BASE_URL = URL;
	}

    public function chromium_init($url, $name) 
    {
        if ($this->chrome_driver == false || strpos($this->tasklist(), 'No tasks')) {
            $this->chrome_driver = ChromeDriver::start($this->capabilities);
        }

        $this->chrome_driver->get($this->BASE_URL.$url);
        $this->DEBUG('checkout|'.$name.'|'.$this->Json('encode', $this->element), false);
        $this->element = $this->chrome_driver_by('xpath', CHECKOUT)->click();
        $this->element_loading();
        
        if (strpos($this->chrome_driver_by('xpath', VALIDATION)->getDomProperty('innerText'), LOGIN_PRE)) {
            $this->DEBUG('login-pre|'.$name.'|'.$this->Json('encode', $this->element), false);
            $this->element = $this->chrome_driver_by('xpath', LOGIN_USER)->sendKeys(USER);
            $this->element = $this->chrome_driver_by('xpath', LOGIN_PASSWORD)->sendKeys(PASSWORD);
            $this->element = $this->chrome_driver_by('xpath', LOGIN_SUBMIT)->click();
            $this->element_loading();

            if (strpos($this->chrome_driver_by('xpath', VALIDATION)->getDomProperty('innerText'), LOGIN_OTP)) {
                $this->DEBUG('login-otp|'.$name.'|'.$this->Json('encode', $this->element), false);
                $this->element = $this->chrome_driver_by('xpath', LOGIN_OTP_SUBMIT)->click();
                $this->element_loading();
                sleep(MAX_SLEEP);
                $this->chrome_driver_close();
                
                return false;
            }

            $this->DEBUG('base-url|'.$name.'|'.$this->Json('encode', $this->element), false);
            $this->chrome_driver->get($this->BASE_URL.$url);
            $this->element = $this->chrome_driver_by('xpath', CHECKOUT)->click();
            $this->element_loading();
        }
        
        if (strpos($this->chrome_driver_by('xpath', VALIDATION)->getDomProperty('innerText'), PHONE_VERIFICATION)) {
            $this->DEBUG('phone-verification|'.$name.'|'.$this->Json('encode', $this->element), false);
            $this->element = $this->chrome_driver_by('xpath', PHONE_NOTIFICATION)->click();
            $this->element_loading();
        }

        if (strpos($this->chrome_driver_by('xpath', VALIDATION)->getDomProperty('innerHTML'), ADDRESS_VERIFICATION)) {
            $this->DEBUG('phone-verification|'.$name.'|'.$this->Json('encode', $this->element), false);
            $this->element = $this->chrome_driver_by('xpath', ADDRESS_SUBMIT)->click();
            $this->element_loading();
        }

        if (strpos($this->chrome_driver_by('xpath', VALIDATION)->getDomProperty('innerHTML'), CHECKOUT_OVER)) {
            $this->DEBUG('checkout-over|'.$name.'|'.$this->Json('encode', $this->element), false);
            $this->chrome_driver_close();
            
            return false;
        }

        if (strpos($this->chrome_driver_by('xpath', VALIDATION)->getDomProperty('innerHTML'), VOUCHERS_NOTIFICATION) === false) {
            $this->DEBUG('vouchers-notification|'.$name.'|'.$this->Json('encode', $this->element), false);
            $this->element = $this->chrome_driver_by('xpath', VOUCHERS_PRE)->click();
            $this->element_loading();

            $this->DEBUG('vouchers|'.$name.'|'.$this->Json('encode', $this->element), false);
            $this->element = $this->chrome_driver_by('xpath', VOUCHERS)->sendKeys(VOUCHER);

            $this->DEBUG('vouchers-submit|'.$name.'|'.$this->Json('encode', $this->element), false);
            $this->element = $this->chrome_driver_by('xpath', VOUCHERS_SUBMIT)->click();
            
            $this->refresh();
            $this->element_loading();
        }

        $this->DEBUG('shipment-submit|'.$name.'|'.$this->Json('encode', $this->element), false);
        $this->element = $this->chrome_driver_by('xpath', SHIPMENT_SUBMIT)->click();
        $this->element_loading();

        $this->DEBUG('payment-submit|'.$name.'|'.$this->Json('encode', $this->element), false);
        $this->element = $this->chrome_driver_by('xpath', PAYMENT_SUBMIT)->click();
        $this->element_loading();
        
        if (strpos($this->chrome_driver_by('xpath', VALIDATION)->getDomProperty('innerHTML'), PAYMENT_OPTION) === false) {
            $this->DEBUG('payment-submit|'.$name.'|'.$this->Json('encode', $this->element), false);
            $this->element = $this->chrome_driver_by('xpath', PAYMENT_SELECT)->click();
            
            $this->element = $this->chrome_driver_by('xpath', PAYMENT_SELECT_SUBMIT)->click();
            $this->element_loading();
        }
        $this->DEBUG('payment-now|'.$name.'|'.$this->Json('encode', $this->element), false);
        $this->element = $this->chrome_driver_by('xpath', PAYMENT_NOW)->click();
        $this->element_loading();
        
        return $this;
    }

    public function chrome_driver_by($mechanism, $value = false) 
    {
        $mechanism = (is_string($mechanism)) ? strval($mechanism):$mechanism;
        if ($value == true) {
            return $this->chrome_driver->findElement(WebDriverBy::$mechanism($value));
        }

        return $this->chrome_driver->findElement(WebDriverBy::$mechanism());
    }
    
    public function chrome_driver_element($instance, $attribute = false) 
    {
        $instance = (is_string($instance)) ? strval($instance):$instance;
        if ($attribute == true) {
            return $this->element->$instance($attribute);
        }
        
        return $this->element->$instance();
    }

    public function refresh()
    {
        return $this->chrome_driver->get($this->chrome_driver->getCurrentUrl());
    }

    public function element_loading()
    {
        sleep(1);
        $element = $this->chrome_driver_by('xpath', VALIDATION)->getDomProperty('innerText');
        if (strpos($element, ELEMENT_LOADING)) {
            $this->DEBUG('loading', false);
            $this->element_loading();
        }
        
        return true;
    }

    public function timeouts($string = false, $second = MAX_TIMEOUT)
    {
        if ($string == true) {
            // return $this->chrome_driver->wait(MAX_TIMEOUT, 500)->until($string);
            return $this->chrome_driver->wait()->until($string);
        }

        return $this->chrome_driver->manage()->timeouts()->implicitlyWait($second);
    }

    public function chrome_driver_close()
    {
        if ($this->chrome_driver == true) {
            return $this->chrome_driver->quit();
        }

        return false;
    }

    public function taskkill()
    {
        if (strpos($this->tasklist(), 'No tasks') === false) {
            return shell_exec('taskkill /F /IM "' . $this->driver . '"');
        }

        return true;
    }

    public function tasklist()
    {
        return shell_exec('tasklist /v /fo csv /fi "IMAGENAME eq "' . $this->driver . '"');
    }
}
