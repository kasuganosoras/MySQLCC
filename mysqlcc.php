<?php
if(php_sapi_name() !== "cli") {
	exit("This program only can running on cli mode!");
}
class MySQLCC extends Thread {
	
	private $ip;
	private $port;
	
	public function __construct($ip, $port = 3306, $randmode = true, $username = 'root', $password = '123456', $database = 'test')
	{
		$this->ip       = $ip;
		$this->port     = $port;
		$this->randmode = $randmode;
		
		if(!$randmode) {
			$this->username = $username;
			$this->password = $password;
			$this->database = $database;
		}
	}
	
	public function run()
	{
		if($this->randmode) {
			while(true) {
				$conn = @mysqli_connect($this->ip, $this->getRand(6), $this->getRand(10), $this->getRand(8), $this->port);
				@mysqli_close($conn);
			}
		} else {
			while(true) {
				$conn = @mysqli_connect($this->ip, $this->username, $this->password, $this->database, $this->port);
				@mysqli_close($conn);
			}
		}
	}
	
	private function getRand($len)
	{
		$src = md5(mt_rand(0, 99999999) . microtime(true));
		return substr($src, 0, $len);
	}
}

if(isset($argv[1]) && ($argv[1] == "--help" || $argv[1] == "-h")) {
	echo <<<EOF
MySQLCC 压力测试工具

使用参数：
    php {$argv[0]} <IP 地址> <端口> <线程> [随机数据模式] [用户名] [密码] [数据库]

随机数据模式是可选的，值必须是 true 或者 false，当设置为 false 时，需要设置用户名、密码和数据库。
如果关闭了随机数据模式又没有输入用户名、密码或者数据库的话，会使用默认数据。


EOF;
	exit;
}

if(!isset($argv[1]) || !isset($argv[2]) || !isset($argv[3])) {
	exit("IP 地址、端口号和线程数是必须的参数，使用 --help 查看帮助");
}

$ip       = $argv[1];
$port     = $argv[2];
$thread   = $argv[3];
$randmode = true;
$username = 'root';
$password = '123456';
$database = 'test';

if(isset($argv[4])) {
	$randmode = (strtolower($argv[4]) == 'true');
	if(!$randmode) {
		if(isset($argv[5]) && !empty($argv[5])) {
			$username = $argv[5];
		}
		if(isset($argv[6]) && !empty($argv[6])) {
			$password = $argv[6];
		}
		if(isset($argv[7]) && !empty($argv[7])) {
			$database = $argv[7];
		}
	}
}

if(!preg_match("/^[0-9\.]{7,15}$/", $ip)) {
	$ip1 = gethostbyname($ip);
	if($ip1 == $ip) {
		exit("IP 地址不正确或无法解析主机名！");
	} else {
		$ip = $ip1;
	}
}

if(!preg_match("/^[0-9]{1,5}$/", $port)) {
	exit("端口号格式不正确！");
} else {
	$port = Intval($port);
}

$threads = Array();
for($i = 0; $i < $thread; $i++) {
	$threads[] = new MySQLCC($ip, $port, $randmode, $username, $password, $database);
}
for($i = 0; $i < $thread; $i++) {
	$threads[$i]->start();
	echo "[Thread] 线程 {$i} 创建成功。\n";
}
while(true) {
	// 一直运行
}
