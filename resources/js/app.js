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



window.adjustTextareaHeight = function(textarea){
  textarea.style.height = 'auto';
  textarea.style.height = textarea.scrollHeight + 'px';
}

// 全てのtextareaタグに対してイベントリスナーを設定
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        adjustTextareaHeight(this);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('textarea').forEach(textarea => {
        adjustTextareaHeight(textarea);
    });
});
