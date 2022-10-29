<?php

require_once(MODEL_DIR . '/Tables/Events.php');

function execute_action() {
    Session::start();
    //認証済みか判定 ($_SESSION['_authenticated'](bool)を受け取る)
    if (Session::isAuthenticated() !== true) {
        //認証済みでなければサインアップにリダイレクト (ログイン画面に移行される)
        return View::redirectTo('admin_accounts', 'signin');
        exit;
    }

    // CSRF対策(POST投稿を行うフォームに対して必ず行う)
    //トークンの作成
    Session::setCsrfToken();

    //ページIDの取得（なければ1が格納される）
    $page_id = Request::getPageId('page_id');

    //クラスの生成（初期化）
    $classEvents = new Events();

    //プロパティに値をセット(ページネーション)
    $classEvents -> page_id = $page_id;

    //recordの取得　（page_idから指定した分だけ/10アイテムのみ）
    $records['events'] = $classEvents -> indexEvents();
    print 'records';
    var_dump($records);

    //ページネーションに必要な値一式
    $paginations = $classEvents -> getPaginations();
    var_dump($paginations);

    //index.tpl.phpにrecords,page_id,paginationsを渡す
    return View::render('index', ['records' => $records, 'paginations' => $paginations]);
}