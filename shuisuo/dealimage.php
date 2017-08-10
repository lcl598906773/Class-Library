<?php

// $re = Image::waterMark('mubiao.jpg', 'annpm.png', 9, 50) ;
$re = Image::zoomImage('mubiao.jpg', 700,700) ;
echo $re;
class Image
{
	static protected $savePath;
	static protected $randName;
	static protected $extension;
	static protected $saveFileName;

	static public function waterMark(
				$dstPath, 
				$srcPath,
				$pos = 9, 
				$pct = 100,
				$savePath = './',
				$randName = true, 
				$extension = 'png')
	{
		self::$savePath = $savePath;
	 	self::$randName = $randName;
	 	self::$extension = $extension;
		//检测文件
		if (!is_file($dstPath)){
			return '目标大图不存在';
		} else if(!is_file($srcPath)){
			return '水印图不存在';
		} else if(!is_dir(self::$savePath)){
			return '保存的路径不存在';
		} else if(!is_writable(self::$savePath)){
			return '保存的路径不可写';
		}
		//判断尺寸
		list($dstWidth, $dstHeight) = getimagesize($dstPath);
		list($srcWidth, $srcHeight) = getimagesize($srcPath); 
		if ($srcWidth > $dstWidth || $srcHeight > $dstHeight){
			return '水印图过大';
		}
		if ($pos >= 1 && $pos <= 9){
			$offsetX = ($pos-1) % 3 *ceil(($dstWidth - $srcWidth)/2);
			$offsetY = floor(($pos-1)/3) * (($dstHeight - $srcHeight)/2);
		}else{
			$offsetX = mt_rand(0, $dstWidth- $srcWidth);
			$offsetY = mt_rand(0, $dstHeight - $srcHeight);
		}
		//合并图
		$dstImg = self::openImage($dstPath);
		$srcImg = self::openImage($srcPath);
		imagecopymerge($dstImg, $srcImg, $offsetX, $offsetY, 0, 0, $srcWidth, $srcHeight, $pct);
		self::saveImage($dstImg, $dstPath);
		imagedestroy($dstImg);
		imagedestroy($srcImg);
		return self::$saveFileName;
	}
	static protected function saveImage($image, $path)
	{
		//保存的路径
		self::$saveFileName = rtrim(self::$savePath, '/') . '/';
		$info = pathinfo($path);
		//名字
		if (self::$randName){
			self::$saveFileName .= uniqid();
		}else {
			self::$saveFileName .= $info['filename'];
		}
		//后缀
		if(empty(self::$extension)){
			self::$extension = $info['extension'];
		}else{
			self::$extension = ltrim(self::$extension, '.');
		}
		self::$saveFileName = self::$saveFileName . '.' . self::$extension;
		//保存
		if (self::$extension == 'jpg'){
			self::$extension = 'jpeg';
		}
		$saveFunc = 'image' . self::$extension;
		$saveFunc($image, self::$saveFileName);
	}
	static protected function openImage($imagePath)
	{
		$info = getimagesize($imagePath);
		$extension = image_type_to_extension($info[2], false);
		$openFunc = 'imagecreatefrom' . $extension;
		return $openFunc($imagePath);
	}
	static public function zoomImage(
				$imagePath,
				$width,
				$height,
				$savePath = './', 
				$randName = true, 
				$extension = 'png')
	{
		self::$savePath = $savePath;
	 	self::$randName = $randName;
	 	self::$extension = $extension;
		//检查文件和目录
		if (!file_exists($imagePath)) {
			return '图片不存在';
		} else if(!is_dir(self::$savePath)){
			return '路径不存在';
		} else if(!is_writable(self::$savePath)){	
			return '路径不可写';
		}
		//计算尺寸
		list($srcWidth, $srcHeight) = getimagesize($imagePath);
		$size = self::getSize($width,$height,$srcWidth, $srcHeight);
		$srcImg = self::openImage($imagePath);
		$dstImg = imagecreatetruecolor($width,$height);
		//合并图
		self::mergeImage($dstImg, $srcImg, $size);
		//保存图
		self::saveImage($dstImg,$imagePath);
		//释放资源
		imagedestroy($dstImg);
		imagedestroy($srcImg);
		return self::$saveFileName;
	}
	static protected function mergeImage($dstImg, $srcImg, $size)
	{
		//获取原始图片的透明色
		$lucidColor = imagecolortransparent($srcImg);
		if($lucidColor == -1){
			//没有透明色，准备黑涩
			$lucidColor =  imagecolorallocate($dstImg, 0,0,0);
		}
		
		//填充透明色
		imagefill($dstImg, 0,0,$lucidColor);
		
		imagecolortransparent($dstImg, $lucidColor);
		//合并
		imagecopyresampled($dstImg, $srcImg, $size['offsetX'],$size['offsetY'],0,0,$size['newWidth'],$size['newHeight'],$size['srcWidth'],$size['srcHeight']);
	}

	static protected function sendCode($destImage)
	{
		header("content-type:image/" . 'png');
		//imagepng  imagejpeg  imagewbmp
		$funcName = 'image' . 'png';
		if (function_exists($funcName)) {
			$funcName($destImage);
		} else {
			exit('不支持的图片类型!');
		}

	}

	static protected function getSize($width, $height, $srcWidth, $srcHeight)
	{
		//保存原图尺寸
		$size['srcWidth'] = $srcWidth;
		$size['srcHeight'] = $srcHeight;
		//计算比例
		$scaleWidth = $width / $srcWidth;
		$scaleHeight = $height / $srcHeight;
		$scaleFinal = min($scaleHeight, $scaleWidth);
		//生成新图尺寸
		$size['newWidth'] = $srcWidth * $scaleFinal;
		$size['newHeight'] = $srcHeight * $scaleFinal;
		if ($scaleWidth < $scaleHeight){
			$size['offsetX'] = 0;
			$size['offsetY'] = round(($height-$size['newHeight'])/2);
		}else{
			$size['offsetY'] = 0;
			$size['offsetX'] = round(($width-$size['newWidth'])/2);
		}
		return $size;
	}

}