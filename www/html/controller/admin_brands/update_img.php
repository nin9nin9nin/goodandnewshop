<?php

require_once(MODEL_DIR . '/Tables/Brands.php');

function execute_action() {
    if (!Request::isPost()) {
        return View::render404();
    }

    // postされたトークンの取得
    $token = Request::get('token');
    
    Session::start();
    // postとsessionのトークンを照合（有効か確認）
    if (Session::isValidCsrfToken($token) !== true) {
        // 有効でなければリダイレクト
        Session::setFlash('不正な処理が行われました');

        return View::redirectTo('admin_brands', 'index');
        exit;
    }
    
    //hidden
    $id = Request::get('brand_id');

    if (preg_match('/^\d+$/', $id) !== 1) {
        return View::render404();
    }

    //ファイルの取得
    $imgs[] = Request::getFiles('img1'); //初期値NULL
    $imgs[] = Request::getFiles('img2'); 
    $imgs[] = Request::getFiles('img3'); 
    $imgs[] = Request::getFiles('img4'); 
    $imgs[] = Request::getFiles('img5'); 
    $imgs[] = Request::getFiles('img6'); 
    $imgs[] = Request::getFiles('img7'); 
    $imgs[] = Request::getFiles('img8'); 

    //アップロード自体がないものの「変更」ボタンを押された際の処理
    if (count(array_filter($imgs)) === 0 ) {
        Session::setFlash('変更はありませんでした');

        return View::redirectTo('admin_brands', 'edit_img', ['brand_id' => $id]);
        exit;
    }

    //既存のファイル名の取得
    $exists_img_names[] = Request::get('exists_img1'); // $_POST['exists_img1']既存のファイル名
    $exists_img_names[] = Request::get('exists_img2');
    $exists_img_names[] = Request::get('exists_img3');
    $exists_img_names[] = Request::get('exists_img4');
    $exists_img_names[] = Request::get('exists_img5');
    $exists_img_names[] = Request::get('exists_img6');
    $exists_img_names[] = Request::get('exists_img7');
    $exists_img_names[] = Request::get('exists_img8');
    
    //クラス生成（初期化）
    $classBrands = new Brands();

    //プロパティに値をセット
    $classBrands -> brand_id = $id;   

    //エラーチェック
    try {
        //エラークラス初期化　$e = null
        CommonError::errorClear();
        
        //アップロードのあったファイルは新規のファイル名を生成（なければ既存のファイル名を使用）
        $img_names = $classBrands -> checkUpdateFileName($imgs, $exists_img_names);

        //エラーがあればthrow
        CommonError::errorThrow();
        
    } catch (Exception $e) {
        //エラーメッセージ取得
        $errors = CommonError::errorWhile();

        $record = $classBrands -> editBrandImg(); 

        //エラーメッセージを添えて再度render
        return View::render('edit_img', ['record' => $record, 'errors' => $errors]);
        exit;
    }
    
    //更新処理------------------------------------------------------------------
    //データベース接続と画像のアップロード
    Database::beginTransaction();
    try {
        $now_date = date('Y-m-d H:i:s');
        
        //更新日時のプロパティ登録
        $classBrands -> update_datetime = $now_date;

        //複数ファイルのファイル名をプロパティ登録
        $classBrands -> registerMultipleFiles($img_names);

        //指定レコードの編集（brandsテーブル）executeBySql()
        $classBrands -> updateBrandImg();
        
        //アップロードあったファイルのみ処理を行う（できなければrollback）
        $classBrands -> updateMultipleFiles($imgs, $img_names);

        Database::commit();
      
    } catch (Exception $e) {
        $e = new Exception('データベースに接続できませんでした', 0, $e);
        //トランザクションでのエラーはcontrollerでキャッチしてもらう(error.tpl.phpへ)
        throw $e;
        
        Database::rollback();
    }
        
    //フラッシュメッセージ
    Session::setFlash('ID' . h($id) .':ブランド画像を変更しました');
    
    //画像確認のため再度画像ページへ
    return View::redirectTo('admin_brands', 'edit_img', ['brand_id' => $id]);
}
