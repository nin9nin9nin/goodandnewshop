<?php

require_once(MODEL_DIR . '/Models.php');

//itemsテーブル
class Items {
    
    public $item_id;
    public $item_name;
    public $category_id;
    public $brand_id;
    public $shop_id;
    public $price;
    public $description;
    public $icon_img;
    public $img1;
    public $img2;
    public $img3;
    public $img4;
    public $img5;
    public $img6;
    public $img7;
    public $img8;
    public $img9;
    public $img10;
    public $status;
    public $enabled;
    public $create_datetime;
    public $update_datetime;
    
    
    public function __construct() {
        $this -> item_id = null;
        $this -> item_name = null;
        $this -> category_id = null;
        $this -> brand_id = null;
        $this -> shop_id = null;
        $this -> price = null;
        $this -> description = null;
        $this -> icon_img = null;
        $this -> img1 = null;
        $this -> img2 = null;
        $this -> img3 = null;
        $this -> img4 = null;
        $this -> img5 = null;
        $this -> img6 = null;
        $this -> img7 = null;
        $this -> img8 = null;
        $this -> img9 = null;
        $this -> img10 = null;
        $this -> status = null;
        $this -> enabled = null;
        $this -> create_datetime = null;
        $this -> update_datetime = null;
    }
    
    /**
     * 商品名　64文字
     * 
     * Validatorがfalseの場合メッセージを入れて返す
     * エラーがなければ何も返さない
     * return CommonError::errorAdd
     */
    public function checkItemName() {
        Validator::paramClear();
        
        if (!Validator::checkInputempty($this->item_name)) {
            return CommonError::errorAdd('商品名を入力してください');
        } else if (!Validator::checkLength($this->item_name, 0, 64)) {
            return CommonError::errorAdd('ブランド名は64文字以内で入力してください');
        }
    }
    
    /**
     * 値段　半角数字　10桁　必須
     * -- int(11)の11は、カラムの表示幅であり、2147483647まで格納が可能。
     * 
     * Validatorがfalseの場合メッセージを入れて返す
     * エラーがなければ何も返さない
     * return CommonError::errorAdd
     */
    public function checkPrice() {
        Validator::paramClear();
        
        if (!Validator::checkInputempty($this->price)) {
            return CommonError::errorAdd('値段を入力してください');
        } else if (!Validator::checkRange($this->price, 1, 10)) {
            return CommonError::errorAdd('値段は半角数字、10桁以内で入力してください');
        }
    }
    
    /**
     */
    public function checkStatus() {
        Validator::paramClear();
        
        if (!Validator::checkInputempty($this->price)) {
            return CommonError::errorAdd('値段を入力してください');
        } else if (!Validator::checkRange($this->price, 1, 10)) {
            return CommonError::errorAdd('値段は半角数字、10桁以内で入力してください');
        }
    }
    
    /**
     * テーブル一覧の取得
     * 4テーブルの結合
     * 論理有効のみ(enabled = true)
     * 未設定表示なし
     */
    //items-index stocks-edit
    public function indexItems() {
        //A=items,B=stocks,C=brands,D=categorys,E=shops  enabled=trueのみ
        //PHP_EOL 実行環境のOSに対応する改行コードを出力する定数
        $sql = 'SELECT A.item_id, A.item_name, A.price, A.icon_img, A.status,' . PHP_EOL
             . '       B.stock_id, B.stock,' . PHP_EOL
             . '       COALESCE(C.category_id,0) AS category_id, C.category_name,' . PHP_EOL
             . '       COALESCE(D.brand_id,0) AS brand_id, D.brand_name,' . PHP_EOL
             . '       COALESCE(E.shop_id,0) AS shop_id, E.shop_name' . PHP_EOL
             . 'FROM ' .PHP_EOL
             . '    (SELECT * FROM items WHERE enabled = true) AS A' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT stock_id, item_id, stock FROM stocks) AS B' . PHP_EOL
             . 'ON A.item_id = B.item_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT category_id, category_name FROM categorys) AS C' . PHP_EOL
             . 'ON A.category_id = C.category_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT brand_id, brand_name FROM brands) AS D' . PHP_EOL
             . 'ON A.brand_id = D.brand_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT shop_id, shop_name FROM shops) AS E' . PHP_EOL
             . 'ON A.shop_id = E.shop_id';
             
        return Models::findBySql($sql);
    }
    
    
    /**
     * itemsテーブルに新規登録
     */
    public function insertItem() {
        //itemsテーブル
        $sql = 'INSERT INTO items' .PHP_EOL
             . '    (item_name, category_id, brand_id, shop_id, price, description, icon_img, status, create_datetime)' .PHP_EOL
             . 'VALUES' .PHP_EOL
             . '    (:item_name, :category_id, :brand_id, :shop_id, :price, :description, :icon_img, :status, :create_datetime)';
        
        //手前に:は$みたいなイメージ？
        $params = [
            ':item_name' => $this->item_name,
            ':category_id' => $this->category_id,
            ':brand_id' => $this->brand_id,
            ':shop_id' => $this->shop_id,
            ':price' => $this->price,
            ':description' => $this->description,
            ':icon_img' => $this->icon_img,
            ':status' => $this->status,
            ':create_datetime' => $this->create_datetime,
        ];
        
        Models::executeBySql($sql, $params);
    }
    
    
    /**
     * 指定レコードの取得
     * A=items,B=stocks,C=brands,D=categorys,E=shops　結合
     * 未設定表示あり
     */
    //items-edit,items-update,imgs-edit,imgs-update 
    public function editItem() {
        $sql = 'SELECT A.item_id, A.item_name, A.price, A.icon_img, A.status, A.description,' . PHP_EOL
             . '       B.stock_id, B.stock,' . PHP_EOL
             . '       COALESCE(C.category_id,0) AS category_id, COALESCE(C.category_name,:null1) AS category_name,' . PHP_EOL
             . '       COALESCE(D.brand_id,0) AS brand_id, COALESCE(D.brand_name,:null2) AS brand_name,' . PHP_EOL
             . '       COALESCE(E.shop_id,0) AS shop_id, COALESCE(E.shop_name,:null3) AS shop_name' . PHP_EOL
             . 'FROM ' .PHP_EOL
             . '    (SELECT * FROM items WHERE item_id = :item_id) AS A' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT stock_id, item_id, stock FROM stocks) AS B' . PHP_EOL
             . 'ON A.item_id = B.item_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT category_id, category_name FROM categorys) AS C' . PHP_EOL
             . 'ON A.category_id = C.category_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT brand_id, brand_name FROM brands) AS D' . PHP_EOL
             . 'ON A.brand_id = D.brand_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT shop_id, shop_name FROM shops) AS E' . PHP_EOL
             . 'ON A.shop_id = E.shop_id';
        
        //NULLを未設定に代替   
        $params = [':item_id' => $this->item_id, ':null1' => '未設定', ':null2' => '未設定', ':null3' => '未設定'];
        //1レコードのみ
        return Models::retrieveBySql($sql,$params); 
    }
    
    /**
     * 指定レコードの編集（itemsテーブル）
     */
    public function updateItem() 
    {
        $sql = 'UPDATE items' . PHP_EOL
             . 'SET item_name = :item_name,' . PHP_EOL
             . '    category_id = :category_id,' . PHP_EOL
             . '    brand_id = :brand_id,' . PHP_EOL
             . '    shop_id = :shop_id,' . PHP_EOL
             . '    price = :price,' . PHP_EOL
            //  . '    icon_img = :icon_img,' . PHP_EOL
             . '    description = :description,' . PHP_EOL
             . '    status = :status,' . PHP_EOL
             . '    update_datetime = :update_datetime' . PHP_EOL
             . 'WHERE item_id = :item_id' . PHP_EOL;
        
        $params = [
            ':item_name' => $this->item_name,
            ':price' => $this->price,
            ':category_id' => $this->category_id,
            ':brand_id' => $this->brand_id,
            ':shop_id' => $this->shop_id,
            // ':icon_img' => $icon_img,
            ':description' => $this->description,
            ':status' => $this->status,
            ':update_datetime' => $this->update_datetime,
            ':item_id' => $this->item_id,
        ];
        
        Models::executeBySql($sql, $params);
    }
    
    /**
     * icon_imgの更新
     */
    public function updateItemIconImg() 
    {
        $sql = 'UPDATE items' . PHP_EOL
             . 'SET icon_img = :icon_img,' . PHP_EOL
             . '    update_datetime = :update_datetime' . PHP_EOL
             . 'WHERE item_id = :item_id' . PHP_EOL;
        
        $params = [
            ':icon_img' => $this->icon_img,
            ':update_datetime' => $this->update_datetime,
            ':item_id' => $this->item_id,
        ];
    
        Models::executeBySql($sql, $params);
    }
    
    /**
     * 指定レコードの削除
     * 論理削除　enabledをfalseにして有効を解く
     */
    public function deleteItem() 
    {
        $sql = 'UPDATE items SET enabled = false' . PHP_EOL
             . 'WHERE item_id = :item_id';
        
        $params = [':item_id' => $this->item_id, ];
        
        Models::executeBySql($sql, $params);
    }
    
    /**
     * 全レコードの削除
     * 論理削除　enabledをfalseにして有効を解く
     */
    public function deleteAllItem($table)
    {
        $sql = 'UPDATE :table SET enabled = false';
        
        $params = [':table' => $table];
        
        Models::executeBySql($sql, $params);
    }
    
    /**
     * 指定レコードのステータス更新
     */
    public function updateStatus() {
        $sql = 'UPDATE items' . PHP_EOL
             . 'SET status = :status, update_datetime = :update_datetime' . PHP_EOL
             . 'WHERE item_id = :item_id';
        
        $params = [
            ':status' => $this->status,
            ':update_datetime' => $this->update_datetime,
            ':item_id' => $this->item_id,
            ];
            
        Models::executeBySql($sql, $params);
    }
    
    //ユーザー側に使用 (status = 1 )---------------------------------------------------------------------------
    
    private static $monthly;
    private static $genre;
    private static $brand;
    private static $shop;
    private static $keyword;
    private static $id;
    
    /**
     * 初期化
     * 使用する際に必ず行う
     * static使用のため（他で使った値が入ったままになる）
     */
    public static function paramClear() {
        self::$monthly = null;
        self::$genre = null;
        self::$brand = null;
        self::$shop = null;
        self::$keyword = null;
        self::$id = null;
    }
    
    /**
     * ブランドに紐付けたマンスリーを指定して取得
     * RIGHT JOIN categorys + WHERE（指定マンスリーのアイテムのみ取得）
     * Fテーブルでbrands+categorys結合（マンスリー名取得）
     * 
     * @param int category_id
     * @return array object
     */
    public static function indexItems_selectMonthly($monthly) {
        //A=items,B=stocks,C=brands,D=categorys,E=shops  enabled=true status=1のみ
        $sql = 'SELECT A.item_id, A.item_name, A.price, A.icon_img, A.description,' . PHP_EOL
             . '       B.stock,' . PHP_EOL
             . '       COALESCE(C.category_name,:null1) AS genre,' . PHP_EOL //ジャンル
             . '       COALESCE(D.brand_name,:null2) AS brand_name,' . PHP_EOL //ブランド
             . '       COALESCE(E.shop_name,:null3) AS shop_name,' . PHP_EOL //ショップ
             . '       F.category_name AS monthly' . PHP_EOL //マンスリー
             . 'FROM ' .PHP_EOL
             . '    (SELECT * FROM items WHERE enabled = true AND status = 1) AS A' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT item_id, stock FROM stocks) AS B' . PHP_EOL
             . 'ON A.item_id = B.item_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT category_id, category_name FROM categorys WHERE status = 1) AS C' . PHP_EOL
             . 'ON A.category_id = C.category_id' . PHP_EOL
             . 'RIGHT JOIN ' .PHP_EOL
             . '    (SELECT brand_id, brand_name, category_id FROM brands WHERE status = 1 AND category_id = :category_id) AS D' . PHP_EOL
             . 'ON A.brand_id = D.brand_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT shop_id, shop_name FROM shops WHERE status = 1) AS E' . PHP_EOL
             . 'ON A.shop_id = E.shop_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT category_id, category_name FROM categorys WHERE status = 1) AS F' . PHP_EOL
             . 'ON D.category_id = F.category_id';
            
        $params = [':category_id' => $monthly, ':null1' => '未設定', ':null2' => '未設定', ':null3' => '未設定'];
             
        return Models::findBySql($sql, $params);
    }
    
    /**
     * マンスリーを指定してブランド名と説明のみ取得
     * category_id 昇順
     * 
     * @param int category_id
     * @return array object
     */
    public static function indexItems_selectMonthlyBrands($monthly) {
        //A=items,B=stocks,C=brands,D=categorys,E=shops  enabled=true status=1のみ
        $sql = 'SELECT ' . PHP_EOL
             . '    A.brand_id, A.brand_name, COALESCE(A.description, :null) AS description,' . PHP_EOL
             . '    B.category_name' . PHP_EOL
             . 'FROM ' .PHP_EOL
             . '    (SELECT brand_id, brand_name, description, category_id ' . PHP_EOL
             . '     FROM brands WHERE status = 1 AND category_id = :category_id) AS A' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT category_id, category_name FROM categorys WHERE status = 1) AS B' . PHP_EOL
             . 'ON A.category_id = B.category_id';
            
        $params = [':category_id' => $monthly, ':null' => '未設定'];
             
        return Models::findBySql($sql, $params);
    }
    
    /**
     * アイテムに紐付けたジャンルを指定して取得
     * RIGHT JOIN categorys + WHERE（指定ジャンルのアイテムのみ取得）
     * 
     * @param int category_id
     * @return array object
     */
    public static function indexItems_selectGenre($genre) {
        //A=items,B=stocks,C=brands,D=categorys,E=shops  enabled=true status=1のみ
        $sql = 'SELECT A.item_id, A.item_name, A.price, A.icon_img, A.description,' . PHP_EOL
             . '       B.stock,' . PHP_EOL
             . '       COALESCE(C.category_name,:null1) AS genre,' . PHP_EOL //ジャンル
             . '       COALESCE(D.brand_name,:null2) AS brand_name,' . PHP_EOL //ブランド
             . '       COALESCE(E.shop_name,:null3) AS shop_name' . PHP_EOL //ショップ
             . 'FROM ' .PHP_EOL
             . '    (SELECT * FROM items WHERE enabled = true AND status = 1) AS A' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT item_id, stock FROM stocks) AS B' . PHP_EOL
             . 'ON A.item_id = B.item_id' . PHP_EOL
             . 'RIGHT JOIN ' .PHP_EOL
             . '    (SELECT category_id, category_name FROM categorys WHERE status = 1 AND category_id = :category_id) AS C' . PHP_EOL
             . 'ON A.category_id = C.category_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT brand_id, brand_name FROM brands WHERE status = 1) AS D' . PHP_EOL
             . 'ON A.brand_id = D.brand_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT shop_id, shop_name FROM shops WHERE status = 1) AS E' . PHP_EOL
             . 'ON A.shop_id = E.shop_id';
            
        $params = [':category_id' => $genre, ':null1' => '未設定', ':null2' => '未設定', ':null3' => '未設定'];
             
        return Models::findBySql($sql, $params);
    }
    
    /**
     * アイテムに紐付けたブランドを指定して取得
     * RIGHT JOIN brands + WHERE（指定ブランドのアイテムのみ取得）
     * 
     * @param int brand_id
     * @return array object
     */
    public static function indexItems_selectBrand($brand) {
        //A=items,B=stocks,C=brands,D=categorys,E=shops  enabled=true status=1のみ
        $sql = 'SELECT A.item_id, A.item_name, A.price, A.icon_img, A.description,' . PHP_EOL
             . '       B.stock,' . PHP_EOL
             . '       COALESCE(C.category_name,:null1) AS genre,' . PHP_EOL //ジャンル
             . '       COALESCE(D.brand_name,:null2) AS brand_name,' . PHP_EOL //ブランド
             . '       COALESCE(E.shop_name,:null3) AS shop_name' . PHP_EOL //ショップ
             . 'FROM ' .PHP_EOL
             . '    (SELECT * FROM items WHERE enabled = true AND status = 1) AS A' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT item_id, stock FROM stocks) AS B' . PHP_EOL
             . 'ON A.item_id = B.item_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT category_id, category_name FROM categorys WHERE status = 1) AS C' . PHP_EOL
             . 'ON A.category_id = C.category_id' . PHP_EOL
             . 'RIGHT JOIN ' .PHP_EOL
             . '    (SELECT brand_id, brand_name FROM brands WHERE status = 1 AND brand_id = :brand_id) AS D' . PHP_EOL
             . 'ON A.brand_id = D.brand_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT shop_id, shop_name FROM shops WHERE status = 1) AS E' . PHP_EOL
             . 'ON A.shop_id = E.shop_id';
            
        $params = [':brand_id' => $brand, ':null1' => '未設定', ':null2' => '未設定', ':null3' => '未設定'];
             
        return Models::findBySql($sql, $params);
    }
    
    /**
     * アイテムに紐付けたショップを指定して取得
     * RIGHT JOIN shops + WHERE（指定ショップのアイテムのみ取得）
     * 
     * @param int shop_id
     * @return array object
     */
    public static function indexItems_selectShop($brand) {
        //A=items,B=stocks,C=brands,D=categorys,E=shops  enabled=true status=1のみ
        $sql = 'SELECT A.item_id, A.item_name, A.price, A.icon_img, A.description,' . PHP_EOL
             . '       B.stock,' . PHP_EOL
             . '       COALESCE(C.category_name,:null1) AS genre,' . PHP_EOL //ジャンル
             . '       COALESCE(D.brand_name,:null2) AS brand_name,' . PHP_EOL //ブランド
             . '       COALESCE(E.shop_name,:null3) AS shop_name' . PHP_EOL //ショップ
             . 'FROM ' .PHP_EOL
             . '    (SELECT * FROM items WHERE enabled = true AND status = 1) AS A' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT item_id, stock FROM stocks) AS B' . PHP_EOL
             . 'ON A.item_id = B.item_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT category_id, category_name FROM categorys WHERE status = 1) AS C' . PHP_EOL
             . 'ON A.category_id = C.category_id' . PHP_EOL
             . 'RIGHT JOIN ' .PHP_EOL
             . '    (SELECT brand_id, brand_name FROM brands WHERE status = 1 AND brand_id = :brand_id) AS D' . PHP_EOL
             . 'ON A.brand_id = D.brand_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT shop_id, shop_name FROM shops WHERE status = 1) AS E' . PHP_EOL
             . 'ON A.shop_id = E.shop_id';
            
        $params = [':brand_id' => $brand, ':null1' => '未設定', ':null2' => '未設定', ':null3' => '未設定'];
             
        return Models::findBySql($sql, $params);
    }
    
    /**
     * アイテム名から検索
     * $any_keywordで前後に%をつける
     * 
     * @params str 
     * @return array object
     */
    public static function searchItemName($keyword) {
        //A=items,B=stocks,C=brands,D=categorys,E=shops  enabled=true status=1のみ
        $sql = 'SELECT A.item_id, A.item_name, A.price, A.icon_img, A.description,' . PHP_EOL
             . '       B.stock,' . PHP_EOL
             . '       COALESCE(C.category_name,:null1) AS genre,' . PHP_EOL //ジャンル
             . '       COALESCE(D.brand_name,:null2) AS brand_name,' . PHP_EOL //ブランド
             . '       COALESCE(E.shop_name,:null3) AS shop_name' . PHP_EOL //ショップ
             . 'FROM ' .PHP_EOL
             . '    (SELECT * FROM items WHERE enabled = true AND status = 1 AND item_name LIKE :keyword) AS A' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT stock_id, item_id, stock FROM stocks) AS B' . PHP_EOL
             . 'ON A.item_id = B.item_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT category_id, category_name FROM categorys WHERE status = 1) AS C' . PHP_EOL
             . 'ON A.category_id = C.category_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT brand_id, brand_name FROM brands WHERE status = 1) AS D' . PHP_EOL
             . 'ON A.brand_id = D.brand_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT shop_id, shop_name FROM shops WHERE status = 1) AS E' . PHP_EOL
             . 'ON A.shop_id = E.shop_id';
             
        $any_keyword = '%'.$keyword.'%';
             
        $params = [':keyword' => $any_keyword, ':null1' => '未設定', ':null2' => '未設定', ':null3' => '未設定'];
             
        return Models::findBySql($sql, $params);
    }
    
    /**
     * 指定レコードの取得
     * A=items,B=stocks,C=brands,D=categorys,E=shops　結合
     * 未設定表示あり
     */
    //items-edit,items-update,imgs-edit,imgs-update 
    public function detailItem() {
        $sql = 'SELECT A.item_id, A.item_name, A.price, A.icon_img, A.status, A.description,' . PHP_EOL
             . '       B.stock_id, B.stock,' . PHP_EOL
             . '       COALESCE(C.category_id,0) AS category_id, COALESCE(C.category_name,:null1) AS category_name,' . PHP_EOL
             . '       COALESCE(D.brand_id,0) AS brand_id, COALESCE(D.brand_name,:null2) AS brand_name,' . PHP_EOL
             . '       COALESCE(E.shop_id,0) AS shop_id, COALESCE(E.shop_name,:null3) AS shop_name' . PHP_EOL
             . 'FROM ' .PHP_EOL
             . '    (SELECT * FROM items WHERE item_id = :item_id ) AS A' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT stock_id, item_id, stock FROM stocks) AS B' . PHP_EOL
             . 'ON A.item_id = B.item_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT category_id, category_name FROM categorys) AS C' . PHP_EOL
             . 'ON A.category_id = C.category_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT brand_id, brand_name FROM brands) AS D' . PHP_EOL
             . 'ON A.brand_id = D.brand_id' . PHP_EOL
             . 'LEFT JOIN ' .PHP_EOL
             . '    (SELECT shop_id, shop_name FROM shops) AS E' . PHP_EOL
             . 'ON A.shop_id = E.shop_id';
        
        //NULLを未設定に代替   
        $params = [':item_id' => $this->item_id, ':null1' => '未設定', ':null2' => '未設定', ':null3' => '未設定'];
        //1レコードのみ
        return Models::retrieveBySql($sql,$params); 
    }
    
}