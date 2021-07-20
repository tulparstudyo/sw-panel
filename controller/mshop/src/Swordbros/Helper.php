<?php
namespace Swordbros;
class Frigian {
    const  name = 'frigian';
    public static function composerUpdate() {

// rotes files
        echo "::: ROUTES SETUP :::\r\n";
        $dst = base_path() . '/routes';
        if(!is_dir($dst)){
            @mkdir($dst); 
        }
        $src = dirname( __FILE__ ) . '/../view/routes';
        if(is_dir($src)){
            self::recurse_copy($src, $dst);
            echo " - Required files copied to $dst \r\n";
        } else{
            echo " ! $src not found \r\n";
        }    
// blade files
        echo "::: BLADE FILES SETUP :::\r\n";
        $dst = resource_path() ;
        if(!is_dir($dst)){
            @mkdir($dst); 
        }
        $src = dirname( __FILE__ ) . '/../view/resources';
        if(is_dir($src)){
            self::recurse_copy($src, $dst);
            echo " - Required files copied to $dst \r\n";
        } else{
            echo " ! $src not found \r\n";
        }   
// ai-client-html files replace
        echo "::: ai-client-html FILES REPLACE :::\r\n";
        $dst = base_path() . '/ext';
        if(!is_dir($dst)){
            @mkdir($dst); 
        }
        $src = dirname( __FILE__ ) . '/../view/ext';
        if(is_dir($src)){
            self::recurse_copy($src, $dst);
            echo " - Required files copied to $dst \r\n";
        } else{
            echo " ! $src not found \r\n";
        }   
// theme package files
        echo "::: PUBLIC FILES SETUP :::\r\n";
        $dst = public_path() . '/packages/swordbros';
        if(!is_dir($dst)){
            @mkdir($dst); 
        }
        $src = dirname( __FILE__ ) . '/../view/swordbros';
        if(is_dir($dst.'/shop/themes/'.self::name)){
            echo "$dst allready exists \r\n";
        } else{
            if(is_dir($src)){
                self::recurse_copy($src, $dst);
                echo " - Required files copied to $dst \r\n";
            } else{
                echo " ! $src not found \r\n";
            }
        }
    }
    private static function recurse_copy($src, $dst) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    self::recurse_copy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else {
                    if(is_file($dst . '/' . $file.'.bak')){
                        echo " - ". $file." allready copied\r\n";
                    } else{
                        if(is_file($dst . '/' . $file)){
                            copy($dst . '/' . $file, $dst . '/' . $file.'.bak'); 
                            echo " * ". $file." backed up\r\n";
                        }
                        copy($src . '/' . $file, $dst . '/' . $file); 
                        echo " - ". $file." copied\r\n";
                    }
                } 
            } 
        } 
        closedir($dir); 
    }
}