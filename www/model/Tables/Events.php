<?php

require_once(MODEL_DIR . '/Messages.php');

//events テーブル
class Events {
    
    public $page_id; //ページ番号
    public $display_record = 10; //１ページの表示件数
    public $event_id;
    public $event_name;
    public $description;
    public $event_date;
    public $event_tag; //（0:ポップアップ、1:イベント）
    public $event_svg; // monthly text
    public $event_png; // baby illustration
    public $img1;
    public $img2;
    public $img3;
    public $img4;
    public $img5;
    public $img6;
    public $img7;
    public $img8;
    public $status;
    public $create_datetime;
    public $update_datetime;
    
    
    public function __construct() {
        $this -> page_id = null;
        $this -> event_id = null;
        $this -> event_name = null;
        $this -> description = null;
        $this -> event_date = null;
        $this -> event_tag = null;
        $this -> event_svg = null;
        $this -> event_png = null;
        $this -> img1 = null;
        $this -> img2 = null;
        $this -> img3 = null;
        $this -> img4 = null;
        $this -> img5 = null;
        $this -> img6 = null;
        $this -> img7 = null;
        $this -> img8 = null;
        $this -> status = null;
        $this -> create_datetime = null;
        $this -> update_datetime = null;
    }

    /**
     * イベント名　varchar(64)
     * 入力確認と文字数確認
     * 
     * Validatorがfalseの場合メッセージを入れて返す
     */
    public function checkEventName() {
        Validator::paramClear();
        
        if (!Validator::checkInputempty($this->event_name)) {
            return CommonError::errorAdd('イベント名を入力してください');
        } else if (!Validator::checkLength($this->event_name, 0, 64)) {
            return CommonError::errorAdd('イベント名は64文字以内で入力してください');
        }
    }

    /**
     * 開催期間　varchar(64)
     * 入力確認と文字数確認
     * 表示のためだけなので文字列として扱う
     * 
     * Validatorがfalseの場合メッセージを入れて返す
     */
    public function checkEventDate() {
        Validator::paramClear();
        
        if (!Validator::checkInputempty($this->event_date)) {
            return CommonError::errorAdd('開催期間を入力してください');
        } else if (!Validator::checkLength($this->event_date, 0, 64)) {
            return CommonError::errorAdd('開催期間は64文字以内で入力してください');
        }
    }

    /**
     * イベントタグ　int(11)
     * 入力確認と数字確認
     * 
     * Validatorがfalseの場合メッセージを入れて返す
     */
    public function checkEventTag() {
        Validator::paramClear();
        
        if (!Validator::checkInputempty($this->event_tag)) {
            return CommonError::errorAdd('イベントタグを選択してください');
        } else if (!Validator::checkNumeric($this->event_tag)) {
            return CommonError::errorAdd('イベントタグが正しくありません');
        }
    }

    /**
     * アップロードファイルのチェック (アップロードがなければNULL)
     * 拡張子の確認とファイル名(ユニーク)の確認     * 
     * file_dir 保存先フォルダ指定
     * @param array
     */
    public function checkFileName($files = [], $default = NULL) {
        Validator::paramClear();
        $new_file_name = $default;
        $file_dir = EVENTS_VISUAL_DIR;
        
        // is_uploaded_file($_FILES[] === true)
        if (empty($files) !== true) {
            // 内部で正しくアップロードされたか確認
            // 拡張子の確認とユニークなファイル名の生成
            $new_file_name = Validator::checkFileName($files, $file_dir);
        }
        //アップロード自体なければNULLを返す
        return $new_file_name;
    }

    /**
     * 複数ファイルのアップロード
     * reArrayされたファイルのエラーチェック
     * 
     */
    public function checkMultipleFileName($re_files = []) {
        Validator::paramClear();
        $new_file_names = [];
        $file_dir = EVENTS_IMG_DIR;
        
        if (!Validator::checkFileCount($re_files)) {
            CommonError::errorAdd('画像のアップロードは最大８枚までです');
        } else {
            // is_uploaded_file($_FILES[] === true)であれば
            if (empty($re_files) !== true) {
                foreach ($re_files as $files) {
                    //順番にファイルのチェックを行うと同時にファイル名を生成
                    $new_file_names[] = Validator::checkFileName($files, $file_dir);
                }
            }
        }
        //アップロード自体なければ空の配列を返す
        return $new_file_names;
    }

    /**
     * 更新時のチェック
     * 更新のあったファイルのみファイル名の生成
     * 更新がなければ既存のファイル名を使用
     */
    public function checkUpdateFileName($files = [], $exists_file_names = []) {
        Validator::paramClear();
        $new_file_names = [];
        $file_dir = EVENTS_IMG_DIR;
        $file_count = count($files); //int(10)

        for ($i=0; $i < $file_count; $i++) {
            if (isset($files[$i]) === true) {
                $new_file_names[$i] = Validator::checkFileName($files[$i], $file_dir);
            } else {
                $new_file_names[$i] = $exists_file_names[$i];
            }
        }
        
        return $new_file_names;//（ファイルがなければ空文字が代入される）
    }

    // paginations ------------------------------------------------------------------------
    /**
     * トータルレコードを取得し、ページネーションの値をセットして返す
     * return array
     */
    public function getPaginations() {
        //トータルレコードの取得
        $total_record = self::getTotalRecord();
        
        //page_idを取得してページネーションを取得してくる
        return Messages::setPaginations($total_record, $this->display_record, $this->page_id);
        
    }
    
    /**
     * 各テーブルのトータルレコード数を返す
     * return $record['cnt']
     */
    public static function getTotalRecord() {
        // テーブルから全レコードの数をカウント
        $sql ='SELECT COUNT(*) as cnt' . PHP_EOL
            . 'FROM events';
        
        //テーブル名はプレースホルダーに使用できない
        // $params = [':table_name' => $this->table_name];
        
        $record = Messages::retrieveBySql($sql);
        
        // カウントした数を返す
        return $record->cnt;
    }

    /**
     * 検索時のページネーション
     * トータルレコードを取得し、ページネーションの値をセットして返す
     * return array
     */
    public function getSearchPaginations($search = []) {
        //トータルレコードの取得
        $total_record = self::getSearchRecord($search);
        
        //page_idを取得してページネーションを取得してくる
        return Messages::setPaginations($total_record, $this->display_record, $this->page_id);
        
    }
    
    /**
     * 検索時のページネーション
     * トータルレコード数の取得
     * 
     */
    public static function getSearchRecord($search = []) {
        // テーブルから全レコードの数をカウント
        $searchSql ='SELECT COUNT(*) as cnt' . PHP_EOL
                  . 'FROM events';
        //$sqlに結合代入
        $searchSql .= self::setSearchSql($search);

        //bindValue
        $searchParams = self::setSearchParams($search);
        
        //トータルレコード数の取得
        $record = Messages::retrieveBySql($searchSql, $searchParams);
        
        // カウントした数を返す
        return $record->cnt;
    }

    // index ------------------------------------------------------------------------
    /**
     * テーブル一覧の取得
     * ページ表示分のみ取得(LIMIT/OFFSET)
     */
    public function indexEvents() {
        // 1ページに表示する件数
        $display_record = $this -> display_record;
        // 配列の何番目から取得するか決定(OFFSET句:除外する行数)
        $start_record = ($this->page_id - 1) * $display_record;
        
        //PHP_EOL 実行環境のOSに対応する改行コードを出力する定数
        $sql = 'SELECT event_id, event_name, event_date, event_tag, event_png, status' . PHP_EOL
        . 'FROM events' . PHP_EOL
        . 'ORDER BY event_id DESC' . PHP_EOL 
        . 'LIMIT :display_record OFFSET :start_record'; //OFFSET １件目からの取得は[0]を指定、11件目からの取得は[10]まで除外
        
        $params = [
            ':display_record' => $display_record,
            ':start_record' => $start_record,
        ];
        
        return Messages::findBySql($sql,$params); 
    } 

    // search ------------------------------------------------------------------------
    /**
     * 検索・絞り込み
     * 
     */
    public function searchEvents($search = []) {
        // 1ページに表示する件数
        $display_record = $this -> display_record;
        // 配列の何番目から取得するか決定(OFFSET句:除外する行数)
        $start_record = ($this->page_id - 1) * $display_record;

        //ベースとなるSQL文を準備
        $searchSql = 'SELECT event_id, event_name, event_date, event_tag, event_png, status FROM events';

        //検索項目を確認　SQL文作成し結合代入
        $searchSql .= self::setSearchSql($search);
        
        //さらにページネーション用のSQL文を結合代入
        $searchSql .= ' ORDER BY event_id DESC LIMIT :display_record OFFSET :start_record';
        
        //検索項目を確認　bindする配列を作成
        $searchParams = self::setSearchParams($search);
        
        //searchParamsにページネーション用の配列追加
        $searchParams += [':display_record' => $display_record, ':start_record' => $start_record];

        //検索・絞り込みに応じたレコードの取得
        return Messages::findBySql($searchSql,$searchParams); 
    }
    
    /**
     * SQL文
     * getで受け取った値からSQL文を作成
     * 
     * カラムはテーブルによって異なる(カラム名はbindできない)
     */
    public static function setSearchSql ($search = []) {
        // 指定したキーが配列にあるか調べる
        if (array_key_exists('keyword', $search)) { // keywordの場合
            $searchSql = ' WHERE event_name LIKE :search_value';
        } else if (array_key_exists('filter', $search)) { //filterの場合
            $searchSql = ' WHERE event_tag = :search_value';
        } 
        return $searchSql;
    }

    /**
     * bindValue
     * getで受け取った値からbindする配列を作成
     * 
     */
    public static function setSearchParams ($search = []) {
        // 指定したキーが配列にあるか調べる
        if (array_key_exists('keyword', $search)) {
            foreach ($search as $key => $value) {
                $value = "%{$value}%"; //前後0文字以上検索
                $searchParams = [':search_value' => $value,];
            } 
        } else if (array_key_exists('filter', $search)) {
            foreach ($search as $key => $value) {
                $value = (int)$value; //intに変換
                $searchParams = [':search_value' => $value,];
            }
        }
        return $searchParams;
    }

    // sorting ------------------------------------------------------------------------
    /**
     * 並べ替え
     */
    public function sortingEvents($sorting = []) {
        // 1ページに表示する件数
        $display_record = $this -> display_record;
        // 配列の何番目から取得するか決定(OFFSET句:除外する行数)
        $start_record = ($this->page_id - 1) * $display_record;

        //PHP_EOL 実行環境のOSに対応する改行コードを出力する定数
        $sortingSql = 'SELECT event_id, event_name, event_date, event_tag, event_png, status FROM events';

        //sortingのSQL文を結合代入
        $sortingSql .= self::setSortingSql($sorting);

        //sortingのbindはしない(直接SQL文に書き込む)
        $params = [':display_record' => $display_record, ':start_record' => $start_record];

        return Messages::findBySql($sortingSql,$params);
    }

    /**
     * 0:イベント名順
     * 1:昇順
     * 2:降順
     * 
     */
    public static function setSortingSql($sorting = []) {
        if ($sorting === '0') {
            $sortingSql = ' ORDER BY event_name ASC';
        } else if ($sorting === '1') {
            $sortingSql = ' ORDER BY event_id ASC';
        } else if ($sorting === '2') {
            $sortingSql = ' ORDER BY event_id DESC';
        } 
        $sortingSql .= ', event_id DESC LIMIT :display_record OFFSET :start_record';

        return $sortingSql;
    }

    // insert ------------------------------------------------------------------------
    /**
     * eventsテーブルに新規登録
     */
    public function insertEvent() {

        $sql = 'INSERT INTO events' .PHP_EOL
             . '    (event_name, description, event_date, event_tag, event_svg, event_png,' .PHP_EOL
             . '    img1, img2, img3, img4, img5, img6, img7, img8, status, create_datetime)' .PHP_EOL
             . 'VALUES' .PHP_EOL
             . '    (:event_name, :description, :event_date, :event_tag, :event_svg, :event_png,' .PHP_EOL
             . '    :img1, :img2, :img3, :img4, :img5, :img6, :img7, :img8, :status, :create_datetime)';
        
        $params = [
            ':event_name' => $this->event_name,
            ':description' => $this->description,
            ':event_date' => $this->event_date,
            ':event_tag' => $this->event_tag,
            ':event_svg' => $this->event_svg,
            ':event_png' => $this->event_png,
            ':img1' => $this->img1,
            ':img2' => $this->img2,
            ':img3' => $this->img3,
            ':img4' => $this->img4,
            ':img5' => $this->img5,
            ':img6' => $this->img6,
            ':img7' => $this->img7,
            ':img8' => $this->img8,
            ':status' => $this->status,
            ':create_datetime' => $this->create_datetime,
        ];
        
        Messages::executeBySql($sql, $params);
    }
    
    /**
     * 画像のファイルアップロード
     * アップロードできなければロールバック(コミットさせない)
     */
    public function uploadFiles($files, $new_file_name) {
        $file_dir = EVENTS_VISUAL_DIR;
        $to = $file_dir . $new_file_name;
        
        if (empty($files) !== true) {
            Messages::uploadFiles($files, $to);
        }
    }

    /**
     * 複数ファイルのアップロード
     */
    public function uploadMultipleFiles($re_files = [], $new_file_names = []) {
        $file_dir = EVENTS_IMG_DIR;

        if (empty($re_files) !== true) {
            $file_count = count($re_files);

            for ($i=0; $i<$file_count; $i++) {
                //
                $to = $file_dir . $new_file_names[$i];
                $files = $re_files[$i];
                //エラーがあればロールバックを行う  
                Messages::uploadFiles($files, $to);
            }
        }
    }    

    /**
     * 複数ファイルのファイル名プロパティ登録
     */
    public function registerMultipleFiles($new_file_names = []) {
        $file_count = count($new_file_names); //配列の数をカウント
        
        for ($i=0; $i<$file_count; $i++) {
            //プロパティ名が1から始まるため変更
            $no = $i+1;
            //参照プロパティ
            $property = 'img'.$no;
            //プロパティに格納
            $this -> $property = $new_file_names[$i];
        }
    }

    // edit ------------------------------------------------------------------------
    /**
     * 指定レコードの取得
     * img2~10を除く
     */
    public function editEvent() {
        $sql = 'SELECT event_id, event_name, description, event_date, event_tag,' . PHP_EOL
             . '       event_svg, event_png, img1,' . PHP_EOL
             . '       status, create_datetime, update_datetime' . PHP_EOL
             . 'FROM events' .PHP_EOL
             . 'WHERE event_id = :event_id';
        
        $params = [
            ':event_id' => $this->event_id, 
        ];
        //1レコードのみ
        return Messages::retrieveBySql($sql,$params); 
    }

    /**
     * 指定レコードの画像取得
     * 
     */
    public function editEventImg() {
        $sql = 'SELECT event_id, event_name, img1, img2, img3, img4, img5, img6, img7, img8' . PHP_EOL
             . 'FROM events' .PHP_EOL
             . 'WHERE event_id = :event_id';
        
          
        $params = [
            ':event_id' => $this->event_id, 
        ];
        
        return Messages::retrieveBySql($sql,$params); 
    }

    // update ------------------------------------------------------------------------
    /**
     * 指定レコードの更新
     */
    public function updateEvent() 
    {
        $sql = 'UPDATE events' . PHP_EOL
             . 'SET event_name = :event_name,' . PHP_EOL
             . '    description = :description,' . PHP_EOL
             . '    event_date = :event_date,' . PHP_EOL
             . '    event_tag = :event_tag,' . PHP_EOL
             . '    event_svg = :event_svg,' . PHP_EOL
             . '    event_png = :event_png,' . PHP_EOL
             . '    status = :status,' . PHP_EOL
             . '    update_datetime = :update_datetime' . PHP_EOL
             . 'WHERE event_id = :event_id' . PHP_EOL;
        
        $params = [
            ':event_name' => $this->event_name,
            ':description' => $this->description,
            ':event_date' => $this->event_date,
            ':event_tag' => $this->event_tag,
            ':event_svg' => $this->event_svg,
            ':event_png' => $this->event_png,
            ':status' => $this->status,
            ':update_datetime' => $this->update_datetime,
            ':event_id' => $this->event_id,
        ];
        
        Messages::executeBySql($sql, $params);
    }

    /**
     * imgの更新
     */
    public function updateEventImg() 
    {
        $sql = 'UPDATE events' . PHP_EOL
             . 'SET img1 = :img1,' . PHP_EOL
             . '    img2 = :img2,' . PHP_EOL
             . '    img3 = :img3,' . PHP_EOL
             . '    img4 = :img4,' . PHP_EOL
             . '    img5 = :img5,' . PHP_EOL
             . '    img6 = :img6,' . PHP_EOL
             . '    img7 = :img7,' . PHP_EOL
             . '    img8 = :img8,' . PHP_EOL
             . '    update_datetime = :update_datetime' . PHP_EOL
             . 'WHERE event_id = :event_id' . PHP_EOL;
        
        $params = [
            ':img1' => $this->img1,
            ':img2' => $this->img2,
            ':img3' => $this->img3,
            ':img4' => $this->img4,
            ':img5' => $this->img5,
            ':img6' => $this->img6,
            ':img7' => $this->img7,
            ':img8' => $this->img8,
            ':update_datetime' => $this->update_datetime,
            ':event_id' => $this->event_id,
        ];
    
        Messages::executeBySql($sql, $params);
    }

    /**
     * 複数ファイルの更新(更新のあったファイルのみ)
     * 
     */
    public function updateMultipleFiles($files = [], $new_file_names = []) {
        $file_dir = EVENTS_IMG_DIR;

        if (empty($files) !== true) {
            $file_count = count($files);
            for ($i=0; $i<$file_count; $i++) {
                //アップロードのあったファイルのみ処理を行う
                if (isset($files[$i]) === true) {
                    $to = $file_dir . $new_file_names[$i];

                    //エラーがあればロールバックを行う  
                    Messages::uploadFiles($files[$i], $to);
                }
            }
        }
    }

    /**
     * 指定レコードのステータス更新
     */
    public function updateEventStatus() {
        $sql = 'UPDATE events' . PHP_EOL
             . 'SET status = :status, update_datetime = :update_datetime' . PHP_EOL
             . 'WHERE event_id = :event_id';
        
        $params = [
            ':status' => $this->status,
            ':update_datetime' => $this->update_datetime,
            ':event_id' => $this->event_id,
            ];
            
        Messages::executeBySql($sql, $params);
    }
    
    // delete ------------------------------------------------------------------------
    /**
     * 指定レコードの削除
     */
    public function deleteEvent() {
        $sql = 'DELETE FROM events' . PHP_EOL
             . 'WHERE event_id = :event_id';
        
        $params = [':event_id' => $this->event_id];
        
        Messages::executeBySql($sql, $params);
    }
    
    // select ------------------------------------------------------------------------
    /**
     * items
     * 商品管理に使用 static
     * 
     * select option用　テーブルの取得
     */
    public static function selectOption_Events() {
        $sql = 'SELECT event_id, event_name' . PHP_EOL 
             . 'FROM events';
        
        return Messages::findBySql($sql);
    }

    // ショップ画面設定 ------------------------------------------------------------------------
    /**
     * 公開中イベントの取得(description,event_svg,img1-8は除く)
     */
    public function releaseEvent() {
        $sql = 'SELECT event_id, event_name, event_date, event_tag, event_png, status' . PHP_EOL
             . 'FROM events' . PHP_EOL
             . 'WHERE status = 1'; //公開中

        return Messages::findBySql($sql);
    }

    // ユーザー画面 ------------------------------------------------------------------------
    /**
     * トップ画面
     * 公開中イベントの取得(全データ)
     */
    public function getReleaseEvent() {
        $sql = 'SELECT event_id, event_name, description, event_date, event_tag, event_svg, event_png, status,' . PHP_EOL
             . '       img1, img2, img3, img4, img5, img6, img7, img8' . PHP_EOL
             . 'FROM events' . PHP_EOL
             . 'WHERE status = 1'; //公開中

        return Messages::retrieveBySql($sql);
    }

    /**
     * items画面/gallery画面
     * イベント詳細
     * 
     * status=0の場合[在庫数][カート]ボタンを非表示にする
     */
    public function getEventDetail() {
        $sql = 'SELECT event_id, event_name, description, event_date, event_tag, event_svg, event_png, status,' . PHP_EOL
             . '       img1, img2, img3, img4, img5, img6, img7, img8' . PHP_EOL
             . 'FROM events' .PHP_EOL
             . 'WHERE event_id = :event_id';
        
        $params = [
            ':event_id' => $this->event_id, 
        ];
        
        return Messages::retrieveBySql($sql,$params); 
    }

    /**
     * トップ画面下部
     * スケジュール一覧(一部)
     * 
     */
    public function getEventSchedulePart() {
        $sql = 'SELECT event_id, event_name, event_date, event_tag, event_png, img1' . PHP_EOL
             . 'FROM events' . PHP_EOL
             . 'ORDER BY event_id DESC' . PHP_EOL 
             . 'LIMIT 5'; //LIMIT 取得レコード数
        
        return Messages::findBySql($sql);
    }

    /**
     * schedule画面
     * スケジュール一覧
     */
    public function getEventSchedule() {
        // 1ページに表示する件数
        $display_record = $this -> display_record;
        // 配列の何番目から取得するか決定(OFFSET句)
        $start_record = ($this->page_id - 1) * $display_record;

        $sql = 'SELECT event_id, event_name, event_date, event_tag, event_png, img1' . PHP_EOL
             . 'FROM events' . PHP_EOL
             . 'ORDER BY event_id DESC' . PHP_EOL // 新しいイベント順
             . 'LIMIT :display_record OFFSET :start_record'; 
        
        $params = [
            ':display_record' => $display_record,
            ':start_record' => $start_record,
        ];

        return Messages::findBySql($sql, $params);
    }

}