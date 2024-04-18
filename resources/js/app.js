import "./bootstrap";

import Alpine from "alpinejs";
import focus from "@alpinejs/focus";
import persist from "@alpinejs/persist";
window.Alpine = Alpine;

Alpine.plugin(focus);
Alpine.plugin(persist);

Alpine.start();

// リクエストを送信画面の処理
$(function () {
    $("#type").change(function () {
        var val = $(this).val();
        var requestId = "#" + val;

        // すべてのリストアイテムを隠す
        $("ul li").hide();

        // 選択されたリストアイテムを表示
        $(requestId).show();

        // 表示されたリストアイテム内の全てのテキストエリアに対して高さを調整
        $(requestId)
            .find("textarea")
            .each(function () {
                adjustTextareaHeight(this);
            });
    });

    // 初期ページロード時にも適用
    $("textarea").each(function () {
        adjustTextareaHeight(this);
    });

    // テキストエリアの入力時に高さを動的に調整
    $("textarea").on("input", function () {
        adjustTextareaHeight(this);
    });
});

function adjustTextareaHeight(textarea) {
    textarea.style.height = "auto";
    textarea.style.height = textarea.scrollHeight + "px";
}
