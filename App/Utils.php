<?php


namespace App;

class Utils
{
    public static function isConnected(){
        if (session_status() === PHP_SESSION_NONE){
            session_start();
        }
        return !empty($_SESSION["connected"]) ? $_SESSION["connected"] : false ;
    }

    public static function connected(){
        if (!self::isConnected()){
            header("Location: /devoir-idc2019/login");
            exit();
        }
    }
}