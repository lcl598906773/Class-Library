<?php
class Template
{
	protected $tplPath;
	protected $tplCache;
	protected $vars = [];
	protected $validTime;

	public function __construct($tplPath = './view/', $tplCache = './cache/template/', $validTime = 3600)
	{
		$this->tplPath = $this->checkDir($tplPath);
		$this->tplCache = $this->checkDir($tplCache);
		$this->validTime = $validTime;
	}
	//检查目录
	protected function checkDir($dir)
	{
		$dir = rtrim($dir,'/') . '/';
		if(!is_dir($dir)){
			mkdir($dir, 0777, true);
		}
		if(!is_readable($dir) || !is_writable($dir)){
			chmod($dir, 0777);
		}
		return $dir;
	}
	public function display($tplFile, $isExcute = true)
	{
		//生成的缓存名
		$cacheFile = $this->getCacheFile($tplFile);
		//拼接模板文件
		$tplFile = $this->tplPath . $tplFile;
		//判断模板文件是否存在
		if(!file_exists($tplFile)){
			exit("$tplFile 模板文件不存在");
		}
		if(!file_exists($cacheFile) 
			|| filemtime($cacheFile) < filemtime($tplFile) 
			|| (filemtime($cacheFile) + $this->validTime) < time()){
			//读取替换
			$file = $this->compile($tplFile);
			file_put_contents($cacheFile, $file);
		}else{
			$this->updateInclude($tplFile);
		}
		if(!empty($this->vars)){
			extract($this->vars);
		}
		if($isExcute){
			include $cacheFile;
		}
	}
	//分配变量
	public function assign($name, $value)
	{
		$this->vars[$name] = $value;
	}
	//正则匹配替换
	protected function compile($tplFile)
	{
		$file = file_get_contents($tplFile);
		$keys = [
				'{date(%%)}'        => '<?php echo date(\1);?>',
				'{long2ip%%}'       => '<?php echo long2ip(\1);?>',
				'__%%__' 	        => '<?php echo \1;?>',
				'${%%}'             => '<?php echo \1;?>',
				'{elseif %%}'       => '<?php elseif(\1):?>',
				'{$%%}'		        => '<?=$\1; ?>',
				'{if %%}'	        => '<?php if(\1):?>',
				'{else}' 	        => '<?php else:?>',
				'{/if}'		        => '<?php endif;?>',
				'{switch %% case %%}' => '<?php switch(\1): case \2: ?>',
				'{case %%}'         => '<?php case \1:?>',
				'{break}'           => '<?php break;?>',
				'{/switch}'         => '<?php endswitch;?>',
				'{include %%}'      => '<?php include "\1"?>',
				'{for %%}'          => '<?php for(\1):?>',
				'{/for}'            => '<?php endfor;?>',
				'{foreach %%}'      => '<?php foreach(\1): ?>',
				'{/foreach}'        => '<?php endforeach;?>',
				'{elseif %%}'   	=> '<?php elseif(\1):?>',
				'{else if %%}'  	=> '<?php elseif(\1):?>',
				'{while %%}'		=> '<?php while(\1):?>',
				'{/while}'			=> '<?php endwhile;?>',
				'{continue}'		=> '<?php continue;?>',
				'{break}'			=> '<?php break;?>',
				'{$%%++}'			=> '<?php $\1++;?>',
				'{$%%--}'			=> '<?php $\1--;?>',
				'{/*}'				=> '<?php /*',
				'{*/}'				=> '*/?>',
				'{section}'			=> '<?php ',
				'{/section}'		=> '?>',
				'{$%% = $%%}'		=> '<?php $\1 = $\2;?>',
				'{default}'			=> '<?php default:?>',
		]; 
		foreach ($keys as $key => $value) {
			//添加转义
			$key = preg_quote($key, '#');
			$reg = '#' . str_replace('%%', '(.+)',$key) . '#U';
			if (strpos($reg, 'include')){
				$file = preg_replace_callback($reg, [$this,'compileInclude'], $file);
			}else{
				$file = preg_replace($reg, $value,$file);
			}
		}
		return $file;
	}
	//处理include
	protected function compileInclude($matches)
	{
		$fileName = $matches[1];
		$this->display($fileName, false);
		$cacheFile = $this->getCacheFile($fileName);
		return "<?php include '$cacheFile' ;?>";
	}
	protected function updateInclude()
	{
		$con = file_get_contents($tplFile);
		$reg = '/\{include (.+)\}/U';
		if (preg_match_all($reg, $con, $matches)){
			$this->display($matches[1][0], false);
		}
	}
	//获取缓存文件的路径
	protected function getCacheFile($tplFile)
	{
		return $this->tplCache . str_replace('.','_',$tplFile) . '.php';
	}
	//递归删除目录
	public function clearCache()
	{
		$this->clearDir($this->tplCache);
	}
	protected function clearDir($dir)
	{
		$dp = opendir($dir);
		while ($file = readdir($dp)) {
			if($file == '.' || $file == '..'){
				continue;
			}
			$fileName = $dir . $file;
			if(is_dir($fileName)){
				$this->clearDir($fileName);
			}else{
				unlink($fileName);
			}
		}
		closedir($dp);
		rmdir($dir);
	}
}
