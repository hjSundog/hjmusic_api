<?php
    //dir为存放临时音乐文件的目录
    $dir = 'D:\phpStudy\WWW\hjmusic_api\uploads\temp\\';
    while(1){
        if (is_dir($dir)){
            if ($dh = opendir($dir)){
                while (($file = readdir($dh)) !== false){
                    if ($file == '.' || $file == '..') continue;
                    $deadline = fileatime("$dir/$file");
                    if (time()-$deadline > 180){
                        //删除文件
                        unlink($dir.$file);
                    }
                }
                closedir($dh);
            }
        }
        //每5秒扫描一遍目录
        sleep(5);
    }