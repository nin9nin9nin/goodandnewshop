<?php

require_once(MODEL_DIR . '/Tables/Admin.php');

function execute_action() {
    
    Session::start();
    //認証済みか判定 ($_SESSION['_authenticated']を受け取る)
    if (Session::isAuthenticated() !== true) {
        //認証済みでなければサインアップにリダイレクト (ログイン画面に移行される)
        return View::redirectTo('admin_accounts', 'signin');
        exit;
    }

    //認証済みであれば$_SESSION['admin_id']を取得
    $admin_id = Session::get('admin_id', false);

    // $admin_idの値を確認
    if (preg_match('/^\d+$/', $admin_id) !== 1) {
        return View::render404();
    }
    
    //クラス生成
    $classAdmin = new Admin();
    
    //プロパティに値を入れる
    $classAdmin -> admin_id = $admin_id;
    
    //adminレコードを取得(全データ)
    $record = $classAdmin -> selectAdminId();
    
    
    return View::render('index', ['record' => $record]);
}