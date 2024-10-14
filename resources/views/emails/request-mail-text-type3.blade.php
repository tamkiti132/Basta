-セキュリティ脆弱性の報告-


【タイトル】
{{ $report_data['title_3'] }}


【詳細】
{{ $report_data['detail_3'] }}


@if($report_data['explanation'])
【対象の脆弱性に関する技術的な説明】
{{ $report_data['explanation'] }}
@endif


【対象の脆弱性の再現手順】
{{ $report_data['steps_to_reproduce'] }}


@if($report_data['abuse_method'])
【対象の脆弱性の悪用方法】
{{ $report_data['abuse_method'] }}
@endif


@if($report_data['workaround'])
【対象の脆弱性の回避策】
{{ $report_data['workaround'] }}
@endif


【環境】
@switch($report_data['environment_3'])
@case(0)
パソコンWindowsブラウザ
@break
@case(1)
パソコン
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