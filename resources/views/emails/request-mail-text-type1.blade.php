-サービスの不具合の報告-


【タイトル】
{{ $report_data['title_1'] }}


【詳細】
{{ $report_data['detail_1'] }}


【環境】
@switch($report_data['environment_1'])
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


@if($report_data['additional_information'])
【ご利用環境の補足】
{{ $report_data['additional_information'] }}
@endif


@if($report_data['reference_url_1'])
【参考】
{{ $report_data['reference_url_1'] }}
@endif

