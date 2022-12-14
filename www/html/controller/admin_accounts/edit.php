<?php

require_once(MODEL_DIR . '/Tables/Admin.php');

function execute_action() {

    Session::start();
    // CSRF対策トークンの作成(POST投稿を行うフォームに対して必ず行う)
    Session::setCsrfToken();
    
    // $_REQUEST[]
    $admin_id = Request::get('admin_id');
    
    //getの値を確認
    if (preg_match('/^\d+$/', $admin_id) !== 1) {
        return View::render404();
    }
    
    //クラス生成
    $classAdmin = new Admin();
    
    //プロパティに値を入れる
    $classAdmin -> admin_id = $admin_id;
    
    //admin_idからデータを取得(全データ)
    $record = $classAdmin -> selectAdminId();
    
    
    return View::render('edit', ['record' => $record]);

}