<?php

namespace Support;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\Storage;

class File{
	
public static function file(){
	return new Filesystem();
}

public static function finder(){
	return new Finder();
}

public static function storage(){
	return new Storage();
}

public static function getDirectorySize($path){
    $bytestotal = 0;
    $path = realpath($path);
    if($path!==false && $path!='' && file_exists($path)){
        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
            $bytestotal += $object->getSize();
        }
    }
    return $bytestotal;
}

public static function convertFormatBytes($size, $precision = 4){
    $base = log($size, 1024);
    $suffixes = array('Bytes', 'KB', 'MB', 'GB', 'TB');
    $byte = round(pow(1024, $base - floor($base)), $precision);
    $prefix = $suffixes[floor($base)];

    return (is_nan($byte)) ? '0.00 Bytes' : vsprintf('%1s %2s', [$byte,  $prefix]);
}

public static function getDirectoryBytes($path, $size){
        return convertFormatBytes(getDirectorySize($path));
}

}