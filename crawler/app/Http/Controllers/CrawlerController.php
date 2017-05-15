<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CrawlerController extends BaseController
{
    private $_start_url = "https://movie.douban.com/";

    private $_interval = 60;//单位s

    public function start()
    {
        //开始抓取
    }
}
