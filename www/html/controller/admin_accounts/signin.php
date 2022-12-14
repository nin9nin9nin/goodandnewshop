<?php

require_once(MODEL_DIR . '/Tables/Admin.php');

function execute_action() {
    
    Session::start();
    //認証済みか判定 ($_SESSION['_authenticated']を受け取る)
    if (Session::isAuthenticated() === true) {
        //認証済みであればダッシュボードにリダイレクト
        return View::redirectTo('admin_dashboard', 'index');
        exit;
        
    }

    // CSRF対策(POST投稿を行うフォームに対して必ず行う)
    //トークンの作成
    Session::setCsrfToken();
    
    //ログイン画面へ（初期値はそれぞれ空の状態）
    return View::render('signin');
}