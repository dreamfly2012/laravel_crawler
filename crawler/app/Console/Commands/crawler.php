<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class crawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler douban movie';

    /**
     * 查询电影年份
     *
     * @var int
     */
    protected $keyword = 2014;

    /**
     * cookie
     *
     * @var string
     */
    protected $cookie = 'bid=DDIGbIpffnk; ll="118137"; gr_user_id=9312f0e3-27f7-4a7d-b819-ed0bc555f9be; viewed="25710590"; _vwo_uuid_v2=74B01F86211CFF37C8D691104BCAAF30|e3b261ef4e4d8a29bb9e241000e57afb; __utma=30149280.206142211.1479954870.1494771822.1494830905.14; __utmc=30149280; __utmz=30149280.1494771822.13.6.utmcsr=kanmeizi.cn|utmccn=(referral)|utmcmd=referral|utmcct=/detail_82357037.html; __utmv=30149280.13850';

    /**
     * url中上一页,即访问来源页
     *
     * @var string
     */
    protected $referer = 'https://movie.douban.com/';

    /**
     * http传递方式 0:get 1:post
     *
     * @var int
     */
    protected $is_post = 0;
    
    /**
     * 传递的参数
     *
     * @var string
     */
    protected $curlPost = '';

    /**
     * 浏览器代理字符串，用来识别是手机还是网页等
     *
     * @var string
     */
    protected $userAgent = 'Mozilla/5.0 (Linux; Android 5.0.2; Redmi Note 3 Build/LRX22G; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/45.0.2454.95 Mobile Safari/537.36  AliApp(DY/6.0.0) TBMovie/6.0.0 1080X1920';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 处理导演字符串
     *
     * @param string $directors 导演字符串
     * 
     * @return string
     */
    public function directorString($directors)
    {
        $director = "";
        foreach ($directors as $key=>$val) {
            $director .= $val->name . ',';
        }
        $director = trim($director, ','); 
        return $director;
    }

    /**
     * 处理演员字符串
     *
     * @param string $casts 演员字符串
     * 
     * @return string
     */
    public function actorString($casts)
    {
        $cast = "";
        foreach ($casts as $key=>$val) {
            $cast .= $val->name . ',';
        }
        $cast = trim($cast, ','); 
        return $cast;
    }

    /**
     * 处理类型字符串
     *
     * @param string $types 类型
     * 
     * @return string
     */
    public function typeString($types)
    {
        $type = "";
        foreach ($types as $key=>$val) {
            $type .= $val . ',';
        }
        $type = trim($type, ','); 
        return $type;
    }

    /**
     * 处理地区字符串
     *
     * @param string $countries 地区
     * 
     * @return string 将地区数组进行合并
     */
    public function regionString($countries)
    {
        $country = "";
        foreach ($countries as $key=>$val) {
            $country .= $val . ',';
        }
        $country = trim($country, ','); 
        return $country;
    }

    /**
     * Http访问请求
     * 
     * @param string $url       请求url
     * @param int    $is_post   get/post请求
     * @param string $curlPost  post请求参数
     * @param string $referer   请求源
     * @param string $cookie    请求cookie
     * @param string $userAgent 请求代理字符串
     * 
     * @return resource
     */
    public function getcurl($url, $is_post, $curlPost, $referer, $cookie, $userAgent)
    {

    }


    public function writeLog()
    {
        
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //处理爬虫程序
        for ($i=170;$i<200;$i++) {
            $start = $i*20;
            var_dump($i);
            $url = "https://api.douban.com/v2/movie/search?tag=".$keyword."&start=".$start."&count=20";
            $info = $this->get_curl($url, $sthi->is_post, $this->curlPost, $this->referer, $this->cookie, $this->userAgent);
            var_dump($info);
            $obj = json_decode($info);

            if (!isset($obj->subjects)) {
                die($i);
                file_put_contents('douban_movie_2014.log', 'r\n爬取到第'.$i.'页', FILE_APPEND);
            }

            $movies = $obj->subjects;

            foreach($movies as $key=>$val){
                $mid = $val->id;
                $url2 = "https://api.douban.com/v2/movie/subject/".$mid;
                $info2 = get_curl($url2, $is_post, $curlPost, $referer, $cookie, $userAgent);
                $movie = json_decode($info2);
                if(isset($movie->title)){
                    $name = $movie->title;
                    $director = director_string($movie->directors);
                    //$screenwriter = strip_tags($screenwriter_matches[1]);
                    $actor = actor_string($movie->casts);
                    $type = type_string($movie->genres);
                    $region = region_string($movie->countries);;
                    $publishdate = $movie->year;
                    $avgrating = $movie->rating->average;
                    $commentcount = $movie->ratings_count;
                    $addtime = date('Y-m-d H:i:s',time());

                    $movie = new ApiMovie();
                    $movie->mid = $mid;
                    $movie->name = $name;
                    $movie->director = $director;
                    //$movie->screenwriter = $screenwriter;
                    $movie->actor = $actor;
                    $movie->type = $type;
                    $movie->region = $region;
                    $movie->publishdate = $publishdate;
                    $movie->avgrating = $avgrating;
                    $movie->commentcount = $commentcount;
                    $movie->addtime = $addtime;
                    $movie->save();
                }
                
            }
            $rand = mt_rand(60,180);
            sleep($rand);
        }
    }
}
