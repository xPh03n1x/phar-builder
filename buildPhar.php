#!/usr/bin/env php
<?php
if(PHP_SAPI!=='cli'){return;}

$options=getopt('s:o:c:m:a:h::', array(
	'source:',
	'output:',
	'compress:',
	'main:',
	'alias:',
	'help::'
));
$self=$_SERVER['SCRIPT_FILENAME'];
if(
	(!$options && $argc==1)
	||
	!isset($argv[1])
	||
	isset($options['h'])||isset($options['help'])
){
	echo <<< EOF
	PHAR Archive Builder
	Usage: {$self} [OPTIONS]
	Required arguments:
	\t-s || --source\tSource directory or file\n
	Optional arguments:
	\t-o || --output\tOutput PHAR archive filename
	\t-c || --compress\tCompression to be used for the archive :GZ, BZ2, NONE
	\t-m || --main\tMain file to be executed from the Phar archive
	\t-a || --alias\tAlias for the PHAR archive, so you can use your project files directly from the archive\n
	Example usage:
	{$self} -s ~/myApp -o ~/myApp.phar -m src/main_file.php -c gz -a myApp\n
	EOF;
	exit;
}

try{
	if(!class_exists('Phar')){throw new Exception("Phar extension is not installed!");}
	if(ini_get('phar.readonly')){ini_set('phar.readonly', 0);}

	if(!isset($options['s']) && !isset($options['source'])){throw new Exception("Source is not defined!");}
	$source=isset($options['s'])?$options['s']:$options['source'];
	if(!file_exists($source)||!is_readable($source)){throw new Exception("Source does not exist or it is not readable!");}

	$compression=0;
	if(isset($options['c'])||isset($options['compress'])){
		$compression=isset($options['c'])?$options['c']:$options['compress'];
		if(!in_array($compression, array('gz', 'bz2', 'none'))){throw new Exception("Invalid compression type requested!");}
		$compression=constant("Phar::".strtoupper($compression));
	}

	$mainFile=false;
	if(isset($options['m'])||isset($options['main'])){
		$mainFile=isset($options['m'])?$options['m']:$options['main'];
		if(is_dir($source)){
			if(!file_exists($source.'/'.$mainFile)||!is_readable($source.'/'.$mainFile)){throw new Exception("Main file does not exist or it is not readable!");}
		}
		if(is_file($source)){
			if(!file_exists($mainFile)||!is_readable($mainFile)){throw new Exception("Main file does not exist or it is not readable!");}
		}
	}

	
	$out=basename($source).'.'.time().'.phar';
	$final=$out;
	if(isset($options['o'])||isset($options['output'])){
		$final=isset($options['o'])?$options['o']:$options['outfile'];
	}
	$alias=basename($out);
	if(isset($options['a'])||isset($options['alias'])){
		$alias=isset($options['a'])?$options['a']:$options['alias'];
	}

	try{
		$p=new PharArchiver($source, $compression, $mainFile, $out, $alias);

		if($p->archive()){
			rename($out, $final);

			# echo "[\e[92mSUCCESS\e[0m] ".$final.PHP_EOL;
			die($final.PHP_EOL);
		}
		
		throw new \Exception('Creating archive "'.$out.'" failed!');
	}
	catch(Exception $e){
		die("[\e[91mERROR\e[0m]: ".$e->getMessage().PHP_EOL);
	}

}
catch(Exception $e){
	die("[\e[91mERROR\e[0m]: ".$e->getMessage().PHP_EOL);
}

class PharArchiver{
	protected $source, $compression, $mainFile, $out, $appName;

	public function __construct($source, $compression, $mainFile, $out, $alias){
		$this->source=$source;
		$this->compression=$compression;
		$this->mainFile=$mainFile;
		$this->out=$out;
		$this->appName=$alias;
	}

	private function addDirToPhar(\Phar $phar, $dir){
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir,RecursiveDirectoryIterator::SKIP_DOTS)) as $i){
			if($i instanceof \SplFileInfo){
				if($i->isFile()){
					$phar->addFile($i->getPathname(), mb_substr($i->getPathname(), mb_strlen($dir)));
				}
			}
		}
	}

	public function archive(){
		try{
			$p=new Phar($this->out, Phar::CURRENT_AS_FILEINFO|Phar::KEY_AS_FILENAME, $this->appName);
	
			if(is_dir($this->source)){$this->addDirToPhar($p, $this->source);}
			elseif(is_file($this->source)){$p->addFile($this->source);}

			if($this->mainFile){
				$p->setStub('<?php Phar::mapPhar(); require_once("phar://'.$this->appName.'/'.$this->mainFile.'");__HALT_COMPILER();');
			}

			if($this->compression && Phar::canCompress($this->compression)){
				$p->compressFiles($this->compression);
			}
	
			return $p;
		}
		catch(Exception $e){
			die("[\e[91mERROR\e[0m]: ".$e->getMessage().PHP_EOL);
		}
	}
}