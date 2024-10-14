-その他お問い合わせ-


【タイトル】
{{ $report_data['title_4'] }}


【詳細】
{{ $report_data['detail_4'] }}

【環境】
@switch($report_data['environment_4'])
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


@if($report_data['reference_url_4'])
【参考】
{{ $report_data['reference_url_4'] }}
@endif

