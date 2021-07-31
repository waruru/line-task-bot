<?php
  // ライブラリ読み込み
  require_once __DIR__ . '/vendor/autoload.php';
  require_once 'functions.php';

  // POST内容表示
  $inputString = file_get_contents('php://input');
  error_log($inputString);

  $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

  $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

  $signature = $_SERVER['HTTP_' . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

  $events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);

  foreach($events as $event) {
    if ($event instanceof \LINE\LINEBot\Event\FollowEvent) {
      replyTextMessage($bot, $event->getReplyToken(), "Follow受信\nフォローありがとうございます");
      continue;
    } elseif ($event instanceof \LINE\LINEBot\Event\PostbackEvent) {
      replyTextMessage($bot, $event->getReplyToken(), 'Postback受信「' . $event->getPostbackData() . '」');
      continue;
    } elseif ($event instanceof \LINE\LINEBot\Event\MessageEvent\ImageMessage) {
      // イベントがImageMessage型であれば
      // ユーザーから送信された画像ファイルを取得し、サーバーに保存する
      // イベントのコンテンツを取得
      $content = $bot->getMessageContent($event->getMessageId());
      // コンテンツヘッダーを取得
      $headers = $content->getHeaders();
      // 画像の保存先フォルダ
      $directory_path = 'tmp';
      // 保存するファイル名
      $filename = uniqid();
      // コンテンツの種類を取得
      $extension = explode('/', $headers['Content-Type'])[1];
      // 保存先フォルダが存在しなければ
      if(!file_exists($directory_path)) {
        // フォルダを作成
        if(mkdir($directory_path, 0777, true)) {
          // 権限を変更
          chmod($directory_path, 0777);
        }
      }
      // 保存先フォルダにコンテンツを保存
      file_put_contents($directory_path . '/' . $filename . '.' . $extension, $content->getRawBody());
      // 保存したファイルのURLを返信
      replyTextMessage($bot, $event->getReplyToken(), 'http://' . $_SERVER['HTTP_HOST'] . '/' . $directory_path. '/' . $filename . '.' . $extension);
    }

    // テキストを返信
    // replyTextMessage($bot, $event->getReplyToken(), 'TextMessage');

    // 画像を返信
    // replyImageMessage($bot, $event->getReplyToken(),
    //                       'https://' . $_SERVER['HTTP_HOST'] . '/imgs/original.jpg',
    //                       'https://' . $_SERVER['HTTP_HOST'] . '/imgs/preview.jpg');

    // 位置情報を返信
    // replyLocationMessage($bot, $event->getReplyToken(), 'CirKit ロゴス',
    //                       '石川県野々市市 金沢工業大学 扇が丘キャンパス',
    //                       36.5308217, 136.6270967);

    // スタンプを返信
    // replyStickerMessage($bot, $event->getReplyToken(), 11538, 51626498);

    // 動画を返信
    // replyVideoMessage($bot, $event->getReplyToken(),
    //                       'https://' . $_SERVER['HTTP_HOST'] . '/videos/sample.mp4',
    //                       'https://' . $_SERVER['HTTP_HOST'] . '/videos/sample.jpg');

    // オーディオを返信
    // replyAudioMessage($bot, $event->getReplyToken(),
    //                       'https://' . $_SERVER['HTTP_HOST'] . '/audios/sample2.m4a', 244000);

    // 複数のメッセージを返信
    // replyMultiMessage($bot, $event->getReplyToken(),
    //     new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('返信テスト'),
    //     new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder('https://' . $_SERVER['HTTP_HOST'] . '/imgs/original.jpg',
    //                                                           'https://' . $_SERVER['HTTP_HOST'] . '/imgs/preview.jpg'),
    //     new \LINE\LINEBot\MessageBuilder\StickerMessageBuilder(11538, 51626498));

    // Buttonテンプレートメッセージを返信
    // replyButtonTemplate(
    //   $bot,
    //   $event->getReplyToken(),
    //   '天気のお知らせ - 今日の天気予報',
    //   'https://' . $_SERVER['HTTP_HOST'] . '/imgs/template.jpg',
    //   '天気の知らせ',
    //   '今日の天気予報は晴れ',
    //   new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder ('明日の天気', 'tomorrow'),
    //   new LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder('週末の天気', 'weekend'),
    //   new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder('webで見る', 'https://google.jp')
    // );

    // Confirmテンプレートを返信
    // replyConfirmTemplate(
    //   $bot,
    //   $event->getReplyToken(),
    //   'webで詳しく見ますか?',
    //   'webで詳しく見ますか?',
    //   new LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder (
    //     '見る', 'http://google.jp'),
    //   new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
    //     '見ない', 'ignore')
    // );

    // Carouselテンプレートメッセージを返信
    // ダイアログの配列
    // $columnArray = array();
    // for($i = 0; $i < 5; $i++) {
    //   // アクションの配列
    //   $actionArray = array();
    //   array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
    //     'ボタン' . $i . '-' . 1, 'c-' . $i . '-' . 1));
    //   array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
    //     'ボタン' . $i . '-' . 2, 'c-' . $i . '-' . 2));
    //   array_push($actionArray, new LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder (
    //     'ボタン' . $i . '-' . 3, 'c-' . $i . '-' . 3));
    //   // CarouselColumnTemplateBuilderの引数はタイトル、本文、
    //   // 画像URL、アクションの配列
    //   $column = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder (
    //     ($i + 1) . '日後の天気',
    //     '晴れ',
    //     'https://' . $_SERVER['HTTP_HOST'] .  '/imgs/template.jpg',
    //     $actionArray
    //   );
    //   // 配列に追加
    //   array_push($columnArray, $column);
    // }
    // replyCarouselTemplate($bot, $event->getReplyToken(),'今後の天気予報', $columnArray);

    // ユーザーのプロフィールを取得しメッセージを作成後返信
    $profile = $bot->getProfile($event->getUserId())->getJSONDecodedBody();
    $bot->replyMessage($event->getReplyToken(),
      (new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder())
        ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('現在のプロフィールです。'))
        ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('表示名：' . $profile['displayName']))
        ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('画像URL：' . $profile['pictureUrl']))
        ->add(new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('ステータスメッセージ：' . $profile['statusMessage']))
    );
  }
?>