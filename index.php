<html>
<head>
<meta charset="utf-8"/>
<title>简易·短网址</title>

<!-- 防止post重复提交 -->
<script type="text/javascript">
if(window.history.replaceState)
{
    window.history.replaceState(null, null, window.location.href)
}
</script>
</head>


<?php
//防止频繁提交

session_start();
$seconds = '60';     //时间段[秒]
$refresh = '6';      //提交次数
$blocktime = '600';  //封禁时间

$cur_time = time();

//超过提交频率封禁浏览器 重启浏览器可解封
if($_SESSION['refresh_times'] > 999 && $cur_time - $_SESSION['last_time'] < $blocktime ){
	exit('<p><b>请勿频繁提交，设备已封禁！稍后再试。</b></p>');
}

if(isset($_SESSION['last_time'])){
    $_SESSION['refresh_times'] += 1;
}else{
    $_SESSION['refresh_times'] = 1;
    $_SESSION['last_time'] = $cur_time;
}

if($cur_time - $_SESSION['last_time'] < $seconds){
    if($_SESSION['refresh_times'] >= $refresh){
        //超过提交频率 标记X
        $_SESSION['refresh_times'] = 9999;
    }
}else{
    $_SESSION['refresh_times'] = 0;
    $_SESSION['last_time'] = $cur_time;
}
?>


<?php
//生成随机文件夹（短链接）名称

function GetRandStr($len)
{
$chars = array(
"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
"l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
"w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
"H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
"S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
"3", "4", "5", "6", "7", "8", "9"
);
$charsLen = count($chars) - 1;
shuffle($chars);
$output = "";
for ($i=0; $i<$len; $i++)
{
$output .= $chars[mt_rand(0, $charsLen)];
}
return $output;
}

//$dirabc = xxxxx;
$dirabc = GetRandStr(4);
$dir123 = rand(0,9);

//当文件夹（短链接）存在时 增加一位
if(!is_dir($dirabc)){
    $shortdir = $dirabc;
} else {
    $shortdir = $dirabc.$dir123;
}
?>


<?php
//判断程序根目录路径

$dirpath = dirname($_SERVER['PHP_SELF']);
$rootpath = dirname($_SERVER['PHP_SELF'])."/";
if ($dirpath == "/"){$rootpath = "/";}
?>


<body>
<p><b>要缩短的 URL（必须包含 http:// 或 https://）</b></p>

<form method="post">
<textarea rows='3' name="url" style="width:100%"></textarea><br/><br/>
<input type="submit" style="font-size:16px;font-weight:900" value="缩短网址"/>
<form><br/><br/>

<?php
if (isset($_POST['url'])){
$origin = $_POST['url'];
    
    if (strlen($origin) > 10 && strpos($origin, strval("http"))!==false && !is_dir($shortdir)){
    mkdir(iconv("UTF-8", "GBK", $shortdir),0755,true);
    $filename = $shortdir."/index.html";
    
    file_put_contents($filename,
    '<script type="text/javascript">location.href="'.$origin.'"</script>');
    $shortened = "http://".$_SERVER['HTTP_HOST'].$rootpath.$shortdir;

    echo '<p>原来的 URL：<br/><a href="'.$origin.'" target="_blank">'.$origin.'</a></p>'
         .'<p>缩短的 URL：<br/><a href="'.$shortened.'" target="_blank">'.$shortened.'</a></p>';

    } else {
            echo "<p><b>您输入的URL无效！</b></p>";
    }
}
?>

</body>
</html>
