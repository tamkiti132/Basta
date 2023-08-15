import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import persist from '@alpinejs/persist'
window.Alpine = Alpine;

Alpine.plugin(focus);
Alpine.plugin(persist);

Alpine.start();


// リクエストを送信画面の処理
$(function(){
  $('#type').change(function () {
    //選択したoptionのvalueを取得
    var val = $(this).val();
    //先頭に#を付けてvalueの値をidに変換
    var requestId = '#' + val;
    //一度すべてのブロックを非表示にする
    $('ul li').hide();
    //選択したブロックのみを表示
    $(requestId).show();
  });
});
