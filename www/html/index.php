<?php
//設定オプションの値を設定する
ini_set('error_reporting', E_ALL);
ini_set('display_errors', "On");

//基本クラスの読み込み及びdispatch関数の入ったコントローラーの読み込み
require_once('./controller/controller.php');

//$urlの定数定義（Viewクラス内render()時に使用）
define('BASE_URL',basename(__FILE__));

//defaultのアクセス
dispatch('top', 'index');