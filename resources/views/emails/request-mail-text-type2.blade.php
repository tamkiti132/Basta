-サービス機能の追加・改善リクエスト-


【ご要望のタイプ】
@switch($report_data['function_request_type'])
@case(0)
新機能のリクエスト
@break
@case(1)
機能の改善案
@break
@case(2)
既存機能のバグ
@break
@case(3)
サービス全般
@break
@endswitch


【タイトル】
{{ $report_data['title_2'] }}


【詳細】
{{ $report_data['detail_2'] }}


【環境】
@switch($report_data['environment_2'])
@case(0)
パソコンWindowsブラウザ
@break
@case(1)
パソコンMacブラウザ
@break
@case(2)
スマートフォンiPhoneブラウザ
@break
@case(3)
スマートフォンAndroidブラウザ
@break
@case(4)
タブレットAndroidブラウザ
@break
@case(5)
タブレットiPhoneブラウザ
@break
@case(6)
その他の環境
@break
@endswitch


@if($report_data['reference_url_2'])
【参考】
{{ $report_data['reference_url_2'] }}
@endif

