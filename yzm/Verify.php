<?php
class Verify
{
	protected $width; //宽
	protected $height; //高
	protected $length;//验证码长度
	protected $img; //画布资源
	protected $code;//验证码
	protected $type;//格式
	public function __construct($width = 200, $height = 50, $length = 4, $type = 1)
	{
		$this->width  = $width;
		$this->height = $height;
		$this->length = $length;
		$this->type   = $type;
		$this->outPut();
	}
	public static function ver($width = 200, $height = 50, $length = 4, $type = 1)
	{
		$ver = new Verify($width, $height, $length, $type);
		return $ver->code;
	} 
	protected function outPut()
	{
		//创建画布
		$this->createImg();
		//画字符串
		$this->verCode();
		//干扰元素
		$this->setDisturb();
		//发送图片
		$this->sendImg();
	}

	//创建画布 填充背景颜色
	protected function createImg()
	{
		$this->img = imagecreatetruecolor($this->width, $this->height);
		$lightColor = $this->getColor(true);
		imagefill($this->img, 0, 0, $lightColor);
	}
	protected function getColor($isLight = false)
	{  
		$start = (int)$isLight * 128; //0 * 128  1*128
		$end = $start + 127;
		$red = mt_rand($start, $end);
		$green = mt_rand($start, $end);
		$blue = mt_rand($start, $end);
		return imagecolorallocate($this->img, $red, $green, $blue);
	}
	//画随机字符串
	protected function verCode()
	{
		$this->code = $this->randString();
		$fontSize = $this->height / 2;
		$perWidth = $this->width / $this->length;
		$delta = $perWidth - $fontSize; 
		$offsetY = ($this->height + $fontSize) / 2;
		for ($i=0; $i < $this->length ; $i++) {
			$angle = mt_rand(-30, 30); 
			$color = $this->getColor();
			$offsetX = $i * $perWidth + $delta; 
			imagettftext($this->img, $fontSize, $angle, $offsetX, $offsetY, $color, 'lxkmht.ttf', $this->code[$i]);
		}
		

	}
	//干扰元素
	protected function setDisturb()
	{
		$total = $this->width * $this->height / 50;
		for ($i = 0; $i < $total; $i++) {
			$x = mt_rand(0, $this->width);
			$y = mt_rand(0, $this->height);
			$color = $this->getColor();
			imagesetpixel($this->img, $x, $y, $color);
		}
		for ($i=0; $i < 5; $i++) { 
			$color = $this->getColor();
			imagearc($this->img, mt_rand(10,$this->width - 10),
					mt_rand(10,$this->height - 10),
					mt_rand(0,$this->width),
					mt_rand(0,$this->height),
					mt_rand(0,180),
					mt_rand(181,360), $color);
		}
		
	}
	protected function randString()
	{
		switch ($this->type) {
			case 1://纯数字
				$str = $this->randNum();
				break;
			case 2://纯字母
				$str = $this->randAlpha();
				break;
			case 3://字母数字混合
				$str = $this->randMixed();
				break;
			default:
				$str = $this->randNum();
				break;
		}
		return $str;
	}
	protected function randNum()
	{
		$str = '1234567890';
		return substr(str_shuffle($str), 0, $this->length);
	}
	protected function randAlpha()
	{
		$arr = range('a', 'z');
		$str = join('',$arr);
		return substr(str_shuffle($str), 0, $this->length);
	}
	protected function randMixed()
	{
		return substr(md5(mt_rand(9,99)), 0, $this->length);
	}
	protected function sendImg()
	{
		header('content-type:image/png');
		imagepng($this->img);
	}
	public function  __get($code)
	{
		return $this->code;
	}
	
}

// $code = Verify::ver(200,50,4,2);

// $_SESSION['code'] = $code;

