<?php

namespace lib\Blibli;

// use Curl\Curl;
use lib\Chromium\Chromium;

class Blibli extends Chromium
{
    public $curl;
    const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:105.0) Gecko/20100101 Firefox/105.0';
    public $_headers = array();
    public $response_headers = array();
    public $response = null;
    public $curl_error_code = 0;
    public $curl_error_message = null;
    public $curl_error = false;
    public $http_status_code = 0;
    public $http_error = false;
    public $error = false;
    public $error_code = 0;
    public $request_headers = null;
    public $http_error_message = null;
    public $error_message = null;
    public $option;

    const BASE_TELEGRAM = 'https://api.telegram.org/bot'.TELEGRAM.'/sendMessage?';

    const PAGE = 1;
    const GROUP_ID = GROUP;
    const ITEM_PER_PAGE = 1;
    public $total_item = false;
    public $page = false;
    public $item;


    public function __construct() 
    {
        require('lib/Json.php');
        parent::__construct();
        $this->PRODUCT = FLASHSALE.
            "?page=".self::PAGE.
            "&group_id=".self::GROUP_ID.
            "&item_per_page=".self::ITEM_PER_PAGE;
	}

    public function blibli_init($url = false, $page = 1)
    {
        $url    = $url ? $url:$this->PRODUCT;
        $item_  = $this->curl_init($url);
        $item_  = $this->Json('decode', $item_->response);
        if (empty($item_) || $item_->code != 200) {
            $this->DEBUG($this->Json('encode', $item_), true);
            return $this->Json('encode', $item_->errors);
        }
        
        $this->total_item = ($this->total_item) ? $this->total_item:
        ceil($item_->paging->total_item/3);
        if ($item_->paging->page != $item_->paging->total_page) {
            if ($this->page == true) {
                $this->item[]   = $item_;
                $this->page     = (int) $this->page + 1;
            } else {
                $page       = explode('&', parse_url($url, PHP_URL_QUERY));
                $page       = (int) explode('=', $page[0])[1];
                $this->page = $page;
            }

            $this->PRODUCT = FLASHSALE."?page=".$this->page."&group_id=".self::GROUP_ID.
            "&item_per_page=".$this->total_item;

            $this->blibli_init($this->PRODUCT);
        } else {
            $this->item[] = $item_;
        }

        return $this->item;
    }

    public function curl_init($url) 
    {
        $this->curl = curl_init();
        $this->setUserAgent(self::USER_AGENT);
        // $this->setHeader($header);
        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->setOpt(CURLOPT_ENCODING , '');
        $this->setOpt(CURLOPT_MAXREDIRS, 10);
        $this->setOpt(CURLOPT_TIMEOUT, MAX_TIMEOUT);
        $this->setOpt(CURLOPT_FOLLOWLOCATION, true);
        $this->setOpt(CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        // // $this->setOpt(CURLOPT_AUTOREFERER, true);
        // // $this->setOpt(CURLOPT_VERBOSE, true);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, "GET");
        $this->exec();
        $this->curl_close();

        return $this;
    }

    public function setUserAgent($useragent) 
    {
        $this->setOpt(CURLOPT_USERAGENT, $useragent);   

        return $this;
    }

    public function setHeader($header = false) 
    {
        if ($header) 
        {
            foreach ($header as $key => $value) 
            {
                $key = trim($key);
                $value = trim($value);
                $this->_headers[$key] = $key . ': ' . $value;
            }
            $this->setOpt(CURLOPT_HTTPHEADER, array_values($this->_headers));
        }

        return $this;
    }

    public function setOpt($option, $value) 
    {
        $this->option[$option] = $value;

        return $this;
    }

    public function exec() 
    {
        curl_setopt_array($this->curl, $this->option);

        $this->response_headers = array();
        $this->response = curl_exec($this->curl);
        $this->curl_error_code = curl_errno($this->curl);
        $this->curl_error_message = curl_error($this->curl);
        $this->curl_error = !($this->getErrorCode() === 0);
        $this->http_status_code = intval(curl_getinfo($this->curl, CURLINFO_HTTP_CODE));
        $this->http_error = $this->isError();
        $this->error = $this->curl_error || $this->http_error;
        $this->error_code = $this->error ? ($this->curl_error ? $this->getErrorCode() : $this->getHttpStatus()) : 0;
        $this->request_headers = preg_split('/\r\n/', curl_getinfo($this->curl, CURLINFO_HEADER_OUT), -1, PREG_SPLIT_NO_EMPTY);
        $this->http_error_message = $this->error ? (isset($this->response_headers['0']) ? $this->response_headers['0'] : '') : '';
        $this->error_message = $this->curl_error ? $this->getErrorMessage() : $this->http_error_message;

        return $this;
    }

    public function getErrorCode() 
    {
        return $this->curl_error_code;
    }

    public function isError()
    {
        return $this->getHttpStatus() >= 400 && $this->getHttpStatus() < 600;
    }

    public function getHttpStatus()
    {
        return $this->http_status_code;
    }

    public function getErrorMessage()
    {
        return $this->curl_error_message;
    }

    public function curl_close() 
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }

        return $this;
    }

    public function telegram($string)
    {
        $message = [
            'chat_id'   => T_ID, 
            'text'      => urldecode($string)
        ];
        return $this->curl_init(self::BASE_TELEGRAM.http_build_query($message));
    }

    public function Json($convert, $string, $bool = false, $json = false)
    {
        if (function_exists('json_encode')) {
            $services = 'json_' . $convert;
            if ($bool) {
                $json = $services($string, $bool);
            } else {
                $json = $services($string);
            }
        }

        if (empty($json)) {
            $services = new \Services_JSON(SERVICES_JSON_SUPPRESS_ERRORS);
            $json = $services->$convert($string);
        }

        return $json;
    }

    public function DEBUG($string, $err = false) 
    {
        if ($err) error_log('[' . date("D M j H:i:s Y", time()) . '][ERROR] ' . $string . "\n", ERROR, LOGS_ERROR);
        if (DEBUG == 1) {
            error_log('[' . date("D M j H:i:s Y", time()) . '][INFO] ' . $string . "\n", INFO, LOGS_INFO);
            
            print_r($string);
        }
    }
}
