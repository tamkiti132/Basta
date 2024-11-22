<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="content-style-type" content="text/css">
    <meta http-equiv="content-language" content="ja">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>その他お問い合わせ</title>
    <style type="text/css">
        html { width: 100%; height: 100%; }
        body { font-family: Arial, sans-serif; }
        .container { width: 100%; max-width: 600px; justify-items: center;}
        .header { text-align: center; padding-bottom: 20px; }
        .section { padding-bottom: 40px; }
        .section-title { font-weight: bold; }
        .content { padding-left: 20px; word-break: break-all;}
        
        @media only screen and (max-width: 480px) {
            .container {
                width: 100% !important;
                display: block !important;
                box-sizing: border-box !important;
            }
            .content {
                padding-left: 5px !important;
                padding-right: 5px !important;
            }
            img {
                width: 100% !important;
                height: auto !important;
            }
        }
    </style>

    <!--[if mso]>
    <style type="text/css"> /* Outlook専用のcss */
      table,td {
        border-collapse:collapse;
        mso-table-lspace:0;
        mso-table-rspace:0;
      }
      table tr td {
        line-height: 1.4;
      }
    </style>
    <![endif]-->

</head>
<body>
    <table class="container" width="100%" align="center" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header" align="center">
                <img src="{{ asset('images/logo.png') }}" alt="LOGO" style="max-width: 100px;">
            </td>
        </tr>
        {{-- タイトル --}}
        <tr>
            <td>
                <table class="section" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="section-title">タイトル</td>
                    </tr>
                    <tr>
                        <td><hr></td>
                    </tr>
                    <tr>
                        <td class="content">
                            {!! nl2br(e($report_data['title_4'])) !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- 詳細 --}}
        <tr>
            <td>
                <table class="section" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="section-title">詳細</td>
                    </tr>
                    <tr>
                        <td><hr></td>
                    </tr>
                    <tr>
                        <td class="content">
                            {!! nl2br(e($report_data['detail_4'])) !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        {{-- 環境 --}}
        <tr>
            <td>
                <table class="section" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="section-title">環境</td>
                    </tr>
                    <tr>
                        <td><hr></td>
                    </tr>
                    <tr>
                        <td class="content">
                            @if ($report_data['environment_4'] == 0)
                                パソコンWindowsブラウザ
                            @elseif ($report_data['environment_4'] == 1)
                                パソコンMacブラウザ
                            @elseif ($report_data['environment_4'] == 2)
                                スマートフォンiPhoneブラウザ
                            @elseif ($report_data['environment_4'] == 3)
                                スマートフォンAndroidブラウザ
                            @elseif ($report_data['environment_4'] == 4)
                                タブレットAndroidブラウザ
                            @elseif ($report_data['environment_4'] == 5)
                                タブレットiPhoneブラウザ
                            @elseif ($report_data['environment_4'] == 6)
                                その他の環境
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- 参考 --}}
        @if($report_data['reference_url_4'])
        <tr>
            <td>
                <table class="section" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="section-title">参考</td>
                    </tr>
                    <tr>
                        <td><hr></td>
                    </tr>
                    <tr>
                        <td class="content">
                            <a href="{{ $report_data['reference_url_4'] }}">{{ $report_data['reference_url_4'] }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        @endif
    </table>
</body>
</html>
