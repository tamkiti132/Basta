<div>
    <div>
        <button type="button"
            class="py-2 w-full text-center rounded-lg border border-gray-500 cursor-pointer file-add-button"
            data-target="file-list-{{ $uniqueId }}">ファイルを追加</button>
        <div class="mt-2 w-full file-list" id="file-list-{{ $uniqueId }}"></div>
    </div>



    {{-- 以下、CSS --}}
    <style>
        .file-display-element {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            width: 100%;
        }

        .file-name {
            flex-grow: 1;
            margin-right: 10px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .delete-button {
            padding: 0 5px;
            border: none;
            background: transparent;
            cursor: pointer;
        }

        /* ボタンのホバー効果 */
        .delete-button:hover {
            background: #f2f2f2;
        }
    </style>
</div>