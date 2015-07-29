<?php namespace Util\Util;
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * 共通処理クラス
 *
 * 各プログラムから利用される共通処理メソッドを定義する
 *
 * Developed on PHP versions 5.2.5
 *
 * @category	extension
 * @package		techfund
 * @author	 	松山 雄太 <yuta_matsuyama@techfund.jp>
 * @copyright	techfund
 * @license		別紙契約内容を参照
 * @version		$Id$
 * @link		http://techfund.jp/
 * @see
 * @since
 * @deprecated
 *
 * 修正履歴:
 * 2015/03/10 新規作成
 */

/**
 * 共通処理クラス
 */
class CommonFunctions {

	/**
	 * ログイン処理
	 * ログイン用のセッション情報を登録する
	 * @param	string	$uid		ユーザID
	 * @param	string	$fid		facebookID
	 * @param	string	$user_name		ユーザ名
	 * @param	string	$img_path		ユーザのプロフィール写真
	 * @param	string	$type		ユーザのタイプ（2歌い手 1作曲家）
	 * @return	void
	 */
	function login($uid = "", $fid = "", $user_name = "", $img_path = "") {
		$_SESSION['user_id'] = $uid;
		$_SESSION["facebook_id"] = $fid;
		$_SESSION["user_name"] = $user_name;
		$_SESSION["img_path"] = $img_path;
	}


	/**
	 * ログアウト処理
	 * ログアウトのためにセッション情報を削除する
	 * @return	void
	 */
	function logout() {
		$_SESSION = array();
	}


	/**
	 * ログインチェック処理
	 * ログイン状態かをチェックし、ログイン状態でなければログインページへ飛ばす
	 * @param	string		$redirect_flg	true:ログインチェック後元ページにリダイレクト, false:リダイレクトせずにログインチェック
	 * @param	array		$redirect		リダイレクト先URL
	 * @return	bool(void)					true:ログイン済み, false:未ログイン, void:未ログイン＋リダイレクト
	 */
	function checkLogin($redirect_flg = true, $redirect = '') {

		// リダイレクト先初期値
		if ('' == $redirect) {
			$redirect = ROOT . 'login.php';
		}

		// セッションに格納されている情報を確認
		if (isset($_SESSION['user_id'])) {
			$ret = true;
		} else {
			$ret = false;
		}

		if (false == $ret) {
			// セッション情報が空の場合はログインページへリダイレクト
			if (true == $redirect_flg) {
				// ログインページにリダイレクト
				$this->redirect($redirect);
			}
		}

		return $ret;
	}


	/**
	 * ページ移動
	 * @param	string		$page		移動先画面
	 */
	function redirect($page) {

		// 移動先URLを生成
		if (isset($_SERVER['SSL_PROTOCOL'])) {
			$url = 'https:';
		} else {
			$url = 'http:';
		}
		$url .= '//' . $_SERVER['HTTP_HOST'];
		if (preg_match("/^\//", $page)) {
			// 「/」で始まる場合は絶対パス扱い
		} else {
			// それ以外は同一ディレクトリにジャンプ扱い
			$url .= dirname($_SERVER['SCRIPT_NAME']);
			if ('/' != dirname($_SERVER['SCRIPT_NAME'])) {
				$url .= '/';
			}
		}
		$url .= $page;

		// セッションを引き継ぐ必要がある場合、引数を追加
		if ('' != SID and defined('SID')) {
			if (false === strpos($page, '?')) {
				$url .= '?' . SID;
			} else {
				$url .= '&' . SID;
			}
		}

		header("Location: " . $url);		// 指定ページに移動
		exit;
	}


	/**
	 * 現在時刻取得メソッド
	 * 現在の時刻を取得する。ただしキューからの実行の場合はキュー実行時刻が利用される。
	 * キュー実行時刻は引数を与えることでセットされる。
	 * @param	int			$set_time		セットしたい時刻を表す整数
	 * @return	int							時刻を表す整数
	 */
	function getNowTime($set_time = null) {

		static $now_time;		// 静的変数を利用

		if (isset($set_time)) {
			// 時刻の更新
			$now_time = $set_time;
		}

		if (0 == intval($now_time)) {
			$now_time = time();
		}

		return $now_time;
	}


	/**
	 * メール送付文字列変換メソッド
	 * 注意点 変換元文字列の前後の「%」は自動補完
	 * @param	arr		$replace_str_arr		key:変換元文字列 value:変換後文字列
	 * @param	str		$mail_body				変換元文字列を含むメール本文
	 * @return	str								変換後の文字列
	 */
	function mailStrReplace($replace_str_arr, $mail_body) {

		// 変換用配列の作成
		foreach ($replace_str_arr as $key => $value) {
			$body_key[] = "%" . $key . "%";
			$body_value[] = $value;
		}

		// 改行コードをメール用に変換
		$body_key[] = "<br />";
		$body_value[] = "";
		$body_key[] = "<br>";
		$body_value[] = "";

		// 変換
		$mail_body = str_replace($body_key, $body_value, $mail_body);

		return $mail_body;
	}


	/**
	 * ランダム文字列生成メソッド (英数字)
	 * @param	int		$length					文字列の長さ
	 * @return	str								ランダム文字列
	 */
	function makeRandStr($length) {

			$str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
			$r_str = null;
			for ($i = 0; $i < $length; $i++) {
					$r_str .= $str[rand(0, count($str))];
			}
			return $r_str;
	}


	/**
	 * 郵便番号から住所取得メソッド
	 * @param	str		$zipcode				郵便番号（000-0000）
	 * @return	arr		$address				失敗:空配列　成功:住所配列（state:都道府県、city:市区町村）
	 */
	function zipcodeToAddress($zipcode) {

		// 郵便番号から住所取得
		$google_maps_api_data = json_decode(@file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?sensor=false&language=ja&address=' . $zipcode), true);	// APIからデータを取得～配列に変換

		// APIから正しくデータを取得できたかチェック
		if($google_maps_api_data['status'] !== 'OK'){
			// 住所取得エラー
			return array();
		} else {
			// 都道府県を取得
			$address = $google_maps_api_data['results'][0]['address_components'];
			unset($address[0]);	// 郵便番号部分を削除
			array_pop($address);	// 国部分を削除
			$address = array_reverse($address);	// 取得用に配列を逆順にする
			$state = $address[0]['long_name'];	// 都道府県
			unset($address[0]);	//市区町村の取得用に都道府県部分を削除
			//市区町村を取得
			$city = '';
			foreach($address as $v){
				$city .= $v['long_name'];	//市区町村部分の文字列を連結
			}
		}

		return array("state" => $state, "city" => $city);
	}


	/**
	 * 画像アップロードメソッド
	 * @param	arr			$file				アップロードファイル
	 * @param	string		$key				ファイルのキー（<img>に設定しているname）
	 * @param	bool		$through_flg		ファイル未選択を許すか（true:許す false:許さない）
	 * @return	array()							成功：true（配列内にファイル名）　失敗：false（配列内にエラー文）
	 */
	function imageUpload($file, $key, $through_flg = true) {

		$data = array("ret" => $through_flg);

		if ($file) {

			// 初期値設定
			$upload_key = $key;
			$maxfileize = UPLOAD_MAX_IMAGE_SIZE;
			$max_width = UPLOAD_MAX_IMAGE_WIDTH;
			$max_height = UPLOAD_MAX_IMAGE_HEIGHT;

			if ($file[$upload_key]['error'] == UPLOAD_ERR_NO_FILE) {
				// ファイル未選択
				if (false == $through_flg) {
					// ファイル未選択をスルーしない場合エラー
					$data = array('ret' => false, 'err' => "ファイルが選択されていません。");
				} else {
					$data = array('ret' => true, 'img_name' => NULL);
				}
			} else {
				try {
					$error = $file[$upload_key]['error'];

					// 配列は除外
					if (is_array($error)) {
						throw new RuntimeException('複数ファイルの同時アップロードは許可されていません。');
					}

					// エラーチェック
					switch ($error) {
						case UPLOAD_ERR_INI_SIZE:
							throw new RuntimeException('許可されている最大サイズを超過しました。');
						case UPLOAD_ERR_FORM_SIZE:
							throw new RuntimeException('フォームで許可されている最大サイズを超過しました。');
						case UPLOAD_ERR_PARTIAL:
							throw new RuntimeException('ファイルが壊れています。');
						case UPLOAD_ERR_NO_TMP_DIR:
							throw new RuntimeException('テンポラリディレクトリが見つかりません。');
						case UPLOAD_ERR_CANT_WRITE:
							throw new RuntimeException('テンポラリデータの生成に失敗しました。');
						case UPLOAD_ERR_EXTENSION:
							throw new RuntimeException('エクステンションでエラーが発生しました。');
					}

					// 一時ファイル名
					$tmp_name = $file[$upload_key]['tmp_name'];

					// ファイルサイズ
					$size = $file[$upload_key]['size'];

					// 不正なファイルでないかチェック
					if (!is_uploaded_file($tmp_name)) {
						throw new RuntimeException('不正なファイルです。');
					}

					// このスクリプトで定義されたサイズ上限のオーバーチェック
					if ($size > $maxfileize) {
						throw new RuntimeException("{$maxfileize}バイトを超過するファイルは受理できません。");
					}

					// 画像ファイル情報取得
					$info = getimagesize($tmp_name);

					// 取得に失敗したときは画像ファイルではない
					if ($info === false) {
						throw new RuntimeException('画像ファイルではありません。');
					}

					// MimeTypeを調べる
					switch ($info['mime']) {
						case 'image/gif':
							$mime = $ext = 'gif';
							break;
						case 'image/png':
							$mime = $ext = 'png';
							break;
						case 'image/jpeg':
							$mime = 'jpeg';
							$ext	= 'jpg';
							break;
						default:
							throw new RuntimeException('この種類の画像形式は受理できません。');
					}

					// もとの画像の幅と高さ
					$width	= $info[0];
					$height = $info[1];

					// ユニークなファイル名を拡張子を含めて生成
					$rand = sha1(mt_rand() . microtime());
					$name = "{$rand}.{$ext}";

					// 最大幅・高さを超過していないかチェック・縦横比を維持して新しいサイズを定義
					$resize = false;
					if ($width > $height && $width > $max_width) {
						$resize = true;
					} elseif ($height > $max_height) {
						$resize = true;
					}

					// 取得に失敗したときは画像ファイルではない
					if ($resize === true) {
						throw new RuntimeException('サイズを超過しています。サイズは縦' . $max_height . 'px, 横' . $max_width . 'pxまでです（アップロードした画像ファイルのサイズ：縦' . $height . 'px, 横' . $width . 'px）。');
					}

					// エラーが無ければアップロード
					if (move_uploaded_file($file[$upload_key]["tmp_name"], IMAGE_DIR . $name)) {
						chmod(IMAGE_DIR . $name, 0644);
						$data = array('ret' => true, 'img_name' => $name);
					} else {
						throw new RuntimeException('ファイルをアップロードできませんでした。サーバーダウンの可能性がありますので後ほど再送してください。');
					}
				} catch (Exception $e) {
					// エラー
					$data = array('ret' => false, 'err' => $e->getMessage());
				}
			}
		} else {
			// ファイルが選択されていないとき
			if (false == $through_flg) {
				// ファイル未選択をスルーしない場合エラー
				$data = array('ret' => false, 'err' => "ファイルが選択されていません。");
			}
		}
		return $data;
	}


	/**
	 * 画像アップロードメソッド
	 * @param	arr			$file				アップロードファイル
	 * @param	string		$key				ファイルのキー（<img>に設定しているname）
	 * @param	bool		$through_flg		ファイル未選択を許すか（true:許す false:許さない）
	 * @return	array()							成功：true（配列内にファイル名）　失敗：false（配列内にエラー文）
	 */
	function imageUploadExifRotateAndResizeSquare($file, $key, $through_flg = true) {

		$data = array("ret" => $through_flg);

		if ($file) {

			// 初期値設定
			$upload_key = $key;
			$maxfileize = UPLOAD_MAX_IMAGE_SIZE;
			$max_width = UPLOAD_MAX_IMAGE_WIDTH;
			$max_height = UPLOAD_MAX_IMAGE_HEIGHT;

			if ($file[$upload_key]['error'] == UPLOAD_ERR_NO_FILE) {
				// ファイル未選択
				if (false == $through_flg) {
					// ファイル未選択をスルーしない場合エラー
					$data = array('ret' => false, 'err' => "ファイルが選択されていません。");
				} else {
					$data = array('ret' => true, 'img_name' => NULL);
				}
			} else {
				try {
					$error = $file[$upload_key]['error'];

					// 配列は除外
					if (is_array($error)) {
						throw new RuntimeException('複数ファイルの同時アップロードは許可されていません。');
					}

					// エラーチェック
					switch ($error) {
						case UPLOAD_ERR_INI_SIZE:
							throw new RuntimeException('許可されている最大サイズを超過しました。');
						case UPLOAD_ERR_FORM_SIZE:
							throw new RuntimeException('フォームで許可されている最大サイズを超過しました。');
						case UPLOAD_ERR_PARTIAL:
							throw new RuntimeException('ファイルが壊れています。');
						case UPLOAD_ERR_NO_TMP_DIR:
							throw new RuntimeException('テンポラリディレクトリが見つかりません。');
						case UPLOAD_ERR_CANT_WRITE:
							throw new RuntimeException('テンポラリデータの生成に失敗しました。');
						case UPLOAD_ERR_EXTENSION:
							throw new RuntimeException('エクステンションでエラーが発生しました。');
					}

					// 一時ファイル名
					$tmp_name = $file[$upload_key]['tmp_name'];

					// ファイルサイズ
					$size = $file[$upload_key]['size'];

					// 不正なファイルでないかチェック
					if (!is_uploaded_file($tmp_name)) {
						throw new RuntimeException('不正なファイルです。');
					}

					// このスクリプトで定義されたサイズ上限のオーバーチェック
					if ($size > $maxfileize) {
						throw new RuntimeException("{$maxfileize}バイトを超過するファイルは受理できません。");
					}

					// 画像ファイル情報取得
					$info = getimagesize($tmp_name);

					// 取得に失敗したときは画像ファイルではない
					if ($info === false) {
						throw new RuntimeException('画像ファイルではありません。');
					}

					// MimeTypeを調べる
					switch ($info['mime']) {
						case 'image/gif':
							$mime = $ext = 'gif';
							break;
						case 'image/png':
							$mime = $ext = 'png';
							break;
						case 'image/jpeg':
							$mime = 'jpeg';
							$ext	= 'jpg';
							break;
						default:
							throw new RuntimeException('この種類の画像形式は受理できません。');
					}

					// もとの画像の幅と高さ
					$width	= $info[0];
					$height = $info[1];

					// ユニークなファイル名を拡張子を含めて生成
					$rand = sha1(mt_rand() . microtime());
					$name = "{$rand}.{$ext}";

					// 最大幅・高さを超過していないかチェック・縦横比を維持して新しいサイズを定義
					$resize = false;
					if ($width > $height && $width > $max_width) {
						$resize = true;
					} elseif ($height > $max_height) {
						$resize = true;
					}

					// 取得に失敗したときは画像ファイルではない
					if ($resize === true) {
						throw new RuntimeException('サイズを超過しています。サイズは縦' . $max_height . 'px, 横' . $max_width . 'pxまでです（アップロードした画像ファイルのサイズ：縦' . $height . 'px, 横' . $width . 'px）。');
					}

					// エラーが無ければアップロード
					if ($this->imageExifRotateAndResizeSquare($file[$upload_key]["tmp_name"], IMAGE_DIR . $name)) {
						chmod(IMAGE_DIR . $name, 0644);
						$data = array('ret' => true, 'img_name' => $name);
					} else {
						throw new RuntimeException('ファイルをアップロードできませんでした。サーバーダウンの可能性がありますので後ほど再送してください。');
					}
				} catch (Exception $e) {
					// エラー
					$data = array('ret' => false, 'err' => $e->getMessage());
				}
			}
		} else {
			// ファイルが選択されていないとき
			if (false == $through_flg) {
				// ファイル未選択をスルーしない場合エラー
				$data = array('ret' => false, 'err' => "ファイルが選択されていません。");
			}
		}
		return $data;
	}


	/**
	 * 画像正方形整形メソッド(imageUploadの拡張機能のため通常は使わない)
	 * ファイルアップロードの際、画像を正方形に切り取る（短い方の幅に合わせ、画像の中心から切り取り）
	 * @param	arr			$file				アップロードファイル
	 * @param	arr			$dir				保存先パス
	 * @return	bool							成功：true　失敗：false
	 */
	function imageResizeSquare($orig_file, $dir) {

	  // 初期値
	  $background_x = 0;  // 背景画像のx座標
	  $background_y = 0;  // 背景画像のy座標
	  $image_x = 0;  // コピー元画像のx座標
	  $image_y = 0;  // コピー元画像のy座標

	  // 画像サイズを判別
	  list($width, $height, $image_type) = getimagesize($orig_file);

	  // 短い方に合わせる
	  if ($width > $height) {
	    // 縦が短い場合
	    $square = $height;
	    // x座標(横)の開始位置を合わせ、切り取り時に正方形になるように設定
	    $image_x = ($width - $height) / 2;
	  } else {
	    // 横が短い場合
	    $square = $width;
	    // y座標(縦)の開始位置を合わせ、切り取り時に正方形になるように設定
	    $image_y = ($height - $width) / 2;
	  }

	  // 画像のインスタンスを作成
	  switch ($image_type) {
	    case 1: $image = imagecreatefromgif($orig_file); break;
	    case 2: $image = imagecreatefromjpeg($orig_file);  break;
	    case 3: $image = imagecreatefrompng($orig_file); break;
	    default: //エラー処理
	      return false;
	  }
	  $background = imagecreatetruecolor($square, $square);
	  imagefill($background , 0 , 0 , 0xFFFFFF);  // 背景は白色

	  // 画像をメモリ上にコピー
	  imagecopy($background, $image, $background_x, $background_y, $image_x, $image_y, $square, $square);

	  // 出力
	  switch ($image_type) {
	    case 1: imagegif($background, $dir); break;
	    case 2: imagejpeg($background, $dir);  break;
	    case 3: imagepng($background, $dir); break;
	  }

	  // メモリから解放
	  imagedestroy($background);
	  imagedestroy($image);

	  return true;
	}


	/**
	 * 画像角度整形メソッド(imageUploadの拡張機能のため通常は使わない)
	 * ファイルアップロードの際、画像の角度を正す
	 * ※スマホで撮った写真には角度情報が付いており、アップロード時に回転してしまうことがあるため、それを防ぐ
	 * @param	arr			$file				アップロードファイル
	 * @param	arr			$dir				保存先パス
	 * @return	bool							成功：true　失敗：false
	 */
	function imageExifRotate($file, $dir) {

	  // 初期値
	  $background_x = 0;  // 背景画像のx座標
	  $background_y = 0;  // 背景画像のy座標
	  $image_x = 0;  // コピー元画像のx座標
	  $image_y = 0;  // コピー元画像のy座標

	  // 画像サイズを判別
	  list($width, $height, $image_type) = getimagesize($file);

	  // 画像のインスタンスを作成
	  switch ($image_type) {
	    case 1: $image = imagecreatefromgif($file); break;
	    case 2: $image = imagecreatefromjpeg($file);  break;
	    case 3: $image = imagecreatefrompng($file); break;
	    default: //エラー処理
	      return false;
	  }

	  // Exif情報を読み込み、画像の向きを修正する
	  $exif = exif_read_data($file);
	  if(!empty($exif['Orientation'])) {
	    switch($exif['Orientation']) {
	      case 8:
	        $image = imagerotate($image, 90, 0);
	        $tmp = $image_x;
	        $image_x = $image_y;
	        $image_y = $tmp;
	        $tmp = $width;
	        $width = $height;
	        $height = $tmp;
	        break;
	      case 3:
	        $image = imagerotate($image, 180, 0);
	        break;
	      case 6:
	        $image = imagerotate($image, -90, 0);
	        $tmp = $image_x;
	        $image_x = $image_y;
	        $image_y = $tmp;
	        $tmp = $width;
	        $width = $height;
	        $height = $tmp;
	        break;
	    }
	  }

	  $background = imagecreatetruecolor($width, $height);
	  imagefill($background , 0 , 0 , 0xFFFFFF);  // 背景は白色

	  // 画像をメモリ上にコピー
	  imagecopy($background, $image, $background_x, $background_y, $image_x, $image_y, $width, $height);

	  // 出力
	  switch ($image_type) {
	    case 1: imagegif($background, $dir); break;
	    case 2: imagejpeg($background, $dir);  break;
	    case 3: imagepng($background, $dir); break;
	  }

	  // メモリから解放
	  imagedestroy($background);
	  imagedestroy($image);

	  return true;
	}


	/**
	 * 画像角度整形メソッドと画像正方形整形メソッドを1処理で行うメソッド
	 * @param	arr			$file				アップロードファイル
	 * @param	arr			$dir				保存先パス
	 * @return	bool							成功：true　失敗：false
	 */
	function imageExifRotateAndResizeSquare($file, $dir) {

	  // 初期値
	  $background_x = 0;  // 背景画像のx座標
	  $background_y = 0;  // 背景画像のy座標
	  $image_x = 0;  // コピー元画像のx座標
	  $image_y = 0;  // コピー元画像のy座標

	  // 画像サイズを判別
	  list($width, $height, $image_type) = getimagesize($file);

	  // 短い方に合わせる
	  if ($width > $height) {
	    // 縦が短い場合
	    $square = $height;
	    // x座標(横)の開始位置を合わせ、切り取り時に正方形になるように設定
	    $image_x = ($width - $height) / 2;
	  } else {
	    // 横が短い場合
	    $square = $width;
	    // y座標(縦)の開始位置を合わせ、切り取り時に正方形になるように設定
	    $image_y = ($height - $width) / 2;
	  }

	  // 画像のインスタンスを作成
	  switch ($image_type) {
	    case 1: $image = imagecreatefromgif($file); break;
	    case 2: $image = imagecreatefromjpeg($file);  break;
	    case 3: $image = imagecreatefrompng($file); break;
	    default: //エラー処理
	      return false;
	  }

	  // Exif情報を読み込み、画像の向きを修正する
	  $exif = exif_read_data($file);
	  if(!empty($exif['Orientation'])) {
	    switch($exif['Orientation']) {
	      case 8:
	        $image = imagerotate($image, 90, 0);
	        $tmp = $image_x;
	        $image_x = $image_y;
	        $image_y = $tmp;
	        break;
	      case 3:
	        $image = imagerotate($image, 180, 0);
	        break;
	      case 6:
	        $image = imagerotate($image, -90, 0);
	        $tmp = $image_x;
	        $image_x = $image_y;
	        $image_y = $tmp;
	        break;
	    }
	  }

	  $background = imagecreatetruecolor($square, $square);
	  imagefill($background , 0 , 0 , 0xFFFFFF);  // 背景は白色

	  // 画像をメモリ上にコピー
	  imagecopy($background, $image, $background_x, $background_y, $image_x, $image_y, $square, $square);

	  // 出力
	  switch ($image_type) {
	    case 1: imagegif($background, $dir); break;
	    case 2: imagejpeg($background, $dir);  break;
	    case 3: imagepng($background, $dir); break;
	  }

	  // メモリから解放
	  imagedestroy($background);
	  imagedestroy($image);

	  return true;
	}


	/**
	 * ファイルアップロードメソッド
	 * @param	arr			$file				アップロードファイル
	 * @param	string		$file_name			ファイル名
	 * @return	array()							成功：true（配列内にファイル名）　失敗：false（配列内にエラー文）
	 */
	function fileUpload($file) {

		$upload_key = "file_path";
		$max_filesize = UPLOAD_MAX_IMAGE_SIZE;
		$path = FILE_DIR;

		$ret = array();

		if ($file) {
			if ($file[$upload_key]['error'] == UPLOAD_ERR_NO_FILE) {
				// ファイル未選択
				$data = array('ret' => true, 'file_name' => NULL);
			} else {
				try {
					$error = $file[$upload_key]['error'];

					// 配列は除外
					if (is_array($error)) {
						throw new RuntimeException('複数ファイルの同時アップロードは許可されていません。');
					}

					// エラーチェック
					switch ($error) {
						case UPLOAD_ERR_INI_SIZE:
							throw new RuntimeException('許可されている最大サイズを超過しました。');
						case UPLOAD_ERR_FORM_SIZE:
							throw new RuntimeException('フォームで許可されている最大サイズを超過しました。');
						case UPLOAD_ERR_PARTIAL:
							throw new RuntimeException('ファイルが壊れています。');
						case UPLOAD_ERR_NO_TMP_DIR:
							throw new RuntimeException('テンポラリディレクトリが見つかりません。');
						case UPLOAD_ERR_CANT_WRITE:
							throw new RuntimeException('テンポラリデータの生成に失敗しました。');
						case UPLOAD_ERR_EXTENSION:
							throw new RuntimeException('エクステンションでエラーが発生しました。');
					}

					// 一時ファイル名
					$tmp_name = $file[$upload_key]['tmp_name'];

					// ファイルサイズ
					$size = $file[$upload_key]['size'];

					// 不正なファイルでないかチェック
					if (!is_uploaded_file($tmp_name)) {
						throw new RuntimeException('この種類のファイル形式はアップロードできません。');
					}

					// このスクリプトで定義されたサイズ上限のオーバーチェック
					if ($size > $max_filesize) {
						throw new RuntimeException("{$max_filesize}バイトを超過するファイルは受理できません。");
					}


					// MimeTypeを調べる
					switch ($file[$upload_key]['type']) {
						case 'audio/mp3':
							$mime = $ext = 'mp3';
							break;
						case 'audio/mpeg':
							$mime = $ext = 'mp3';
							break;
						case 'audio/mpg':
							$mime = $ext = 'mp3';
							break;
						case 'audio/x-mpg':
							$mime = $ext = 'mp3';
							break;
						case 'audio/mpeg3':
							$mime = $ext = 'mp3';
							break;
						case 'audio/x-mpeg':
							$mime = $ext = 'mp3';
							break;
						case 'audio/x-mp3':
							$mime = $ext = 'mp3';
							break;
						case 'audio/x-mpeg3':
							$mime = $ext = 'mp3';
							break;


						case 'audio/m4a':
							$mime = $ext = 'm4a';
							break;
						case 'audio/mp4':
							$mime = $ext = 'm4a';
							break;
						case 'audio/aac':
							$mime = $ext = 'm4a';
							break;
						case 'audio/x-m4a':
							$mime = $ext = 'm4a';
							break;
						case 'video/mp4':
							$mime = $ext = 'm4a';
							break;
						case 'application/mp4':
							$mime = $ext = 'm4a';
							break;

						default:
							throw new RuntimeException('この種類のファイル形式はアップロードできません。');
					}

					$name = sha1(mt_rand() . microtime());		// ユニークなファイル名
					$file_name = "{$name}.{$ext}";

					// ファイルアップロード
					if (move_uploaded_file($file["file_path"]["tmp_name"], $path . $file_name)) {
						chmod($path . $file_name, 0644);
						$data = array('ret' => true, 'file_name' => $file_name);
					} else {
						throw new RuntimeException('ファイルをアップロードできませんでした。サーバーダウンの可能性がありますのでお手数ですが後ほど再送してください。');
					}
				} catch (Exception $e) {
					// エラー
					$data = array('ret' => false, 'err' => $e->getMessage());
				}
			}
		}
		return $data;
	}
}
