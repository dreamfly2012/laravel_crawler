<?php
/**
 * 命令行抓取豆瓣电影
 * 
 * @category Crawler豆瓣电影抓取
 * @package  Category
 * @author   jfu <jia.fu2013@gmail.com>
 * @license  MIT http://www.80shihua.com/
 * @link     http://www.80shihua.com/
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

/**
 * 爬虫通过豆瓣api获取电影信息
 * 
 * @category Php7
 * @package  Crawler豆瓣电影抓取
 * @author   jfu <jia.fu2013@gmail.com>
 * @license  MIT http://www.80shihua.com/
 * @link     http://www.80shihua.com/
 */
class Crawler extends Command
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
     * 抓取间隔
     * 
     * @var int 秒
     */
    protected $interval = 60;

    /**
     * 查询电影年份
     *
     * @var int
     */
    protected $keyword = 2014;

    /**
     * Cookie
     *
     * @var string
     */
    protected $cookie = 'bid=DDIGbIpffnk; ll="118137"; gr_user_id=9312f0e3-27f7-4a7d-b819-ed0bc555f9be; viewed="25710590"; _vwo_uuid_v2=74B01F86211CFF37C8D691104BCAAF30|e3b261ef4e4d8a29bb9e241000e57afb; __utma=30149280.206142211.1479954870.1494771822.1494830905.14; __utmc=30149280; __utmz=30149280.1494771822.13.6.utmcsr=kanmeizi.cn|utmccn=(referral)|utmcmd=referral|utmcct=/detail_82357037.html; __utmv=30149280.13850';

    /**
     * Url中上一页,即访问来源页
     *
     * @var string
     */
    protected $referer = 'https://movie.douban.com/';

    /**
     * Http传递方式 0:get 1:post
     *
     * @var int
     */
    protected $is_post = 0;

    /**
     * Log日志位置
     * 
     * @var string
     */
    protected $logpath = 'logs/';
    
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
     * 获取最大图片
     * 
     * @param string $images 图片
     *
     * @return void
     */
    public function imagesString($images)
    {
        $image = "";
        foreach ($images as $key=>$val) {
            $image .= $val->large . ',';
        }
        $image = trim($image, ','); 
        return $image;
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
        $ch = curl_init();
        //$ip = array('101.68.44.61','218.202.111.10','218.202.111.11','218.202.111.12','218.202.101.10','218.202.102.10','218.202.111.10','218.192.101.10','218.192.101.15','112.5.220.199','112.5.220.198','112.5.220.197','112.5.220.196','112.5.220.195','112.5.220.193','112.5.220.192','112.5.220.62');
        //$postip = $ip[array_rand($ip,1)];
        //curl_setopt($ch, CURLOPT_PROXY, $ip); //代理IP
        //curl_setopt($ch, CURLOPT_PROXYPORT, $port); //代理端口
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        } else {
            curl_setopt($ch, CURLOPT_POST, 0);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查 
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); // 从证书中检查SSL加密算法是否存在
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        $page_content = curl_exec($ch);
        curl_close($ch);
        return $page_content;
    }


    /**
     * 日志写入
     *
     * @param string $loginfo 日志信息字符串
     * 
     * @return void
     */
    public function writeLog($loginfo)
    {
        $logname = date('Y-m-d').'.log';
        file_put_contents(storage_path() . '/' . $this->logpath.$logname, $loginfo, FILE_APPEND);
    }

    /**
     * 格式化打印数组对象结构
     *
     * @param string $arr 对象/数组 
     * 
     * @return void
     */
    public function dump($arr)
    {
        echo "<pre>";
        var_dump($arr);
        echo "</pre>";
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //处理爬虫程序
        for ($i=6;$i<10;$i++) {
            $start = $i*20;
            var_dump($i);
            $url = "https://api.douban.com/v2/movie/search?tag=" . $this->keyword . "&start=" . $start . "&count=20";
            $info = $this->getcurl($url, $this->is_post, $this->curlPost, $this->referer, $this->cookie, $this->userAgent);
            $obj = json_decode($info);

            if (!isset($obj->subjects)) {
                die($i);
                $info = '爬虫抓取到'. $i .'发生错误';
                $this->writeLog($info);
            }

            $movies = $obj->subjects;

            foreach ($movies as $key=>$val) {
                $mid = $val->id;
                $url2 = "https://api.douban.com/v2/movie/subject/".$mid;
                $info2 = $this->getcurl($url2, $this->is_post, $this->curlPost, $this->referer, $this->cookie, $this->userAgent);
                $movie = json_decode($info2);
                if (isset($movie->title)) {
                    $name = $movie->title;
                    $summary = $movie->summary;
                    $director = $this->directorString($movie->directors);
                    $actor = $this->actorString($movie->casts);
                    $type = $this->typeString($movie->genres);
                    $region = $this->regionString($movie->countries);
                    $images = $movie->images->large;
                    $publishdate = $movie->year;
                    $avgrating = isset($movie->rating->average) ? $movie->rating->average : 0;
                    $commentcount = isset($movie->comments_count) ? $movie->comments_count : 0;
                    $ratingcount = isset($movie->ratings_count) ? $movie->ratings_count : 0;
                    $addtime = date('Y-m-d H:i:s', time());

                    $info = DB::table('douban')
                        ->where('mid', $mid)
                        ->first();

                    if (empty($info)) {
                        $arr = ['mid'=>$mid, 'summary'=>$summary, 'ratingcount'=>$ratingcount, 'addtime'=>$addtime, 'name'=>$name, 'director'=>$director, 'actor'=>$actor, 'type'=>$type, 'region'=>$region, 'publishdate'=>$publishdate, 'avgrating'=>$avgrating, 'commentcount'=>$commentcount, 'images'=>$images];
                        DB::table('douban')->insert(
                            $arr
                        );
                    } else {
                        $arr = ['mid'=>$mid, 'summary'=>$summary, 'ratingcount'=>$ratingcount, 'addtime'=>$addtime, 'name'=>$name, 'director'=>$director, 'actor'=>$actor, 'type'=>$type, 'region'=>$region, 'publishdate'=>$publishdate, 'avgrating'=>$avgrating, 'commentcount'=>$commentcount, 'images'=>$images];
                        DB::table('douban')
                        ->where('mid', $mid)
                        ->update(
                            $arr
                        );
                    }
                }
                
            }
            $rand = mt_rand($this->interval, 3*$this->interval);
            sleep($rand);
        }
    }
}
