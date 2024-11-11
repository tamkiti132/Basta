<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="content-style-type" content="text/css">
    <meta http-equiv="content-language" content="ja">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>サービスの不具合の報告</title>
    <style type="text/css">
        html { width: 100%; height: 100%; }
        body { font-family: Arial, sans-serif; }
        .container { width: 100%; max-width: 600px; }
        .header { text-align: center; padding-bottom: 20px; }
        td { padding: 10px 20px; }
        
        @media only screen and (max-width: 480px) {
            .container {
                width: 100% !important;
                display: block !important;
                box-sizing: border-box !important;
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
    <table class="container" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td class="header" align="center">
                <img src="{{ asset('images/logo.png') }}" alt="LOGO" style="max-width: 100px;">
            </td>
        </tr>
        <tr>
            <td>
                <p>{{ $target_user->nickname }} さん</p>
            </td>
        </tr>
        <tr>
            <td>
                <h2>{{ $group_data->name }}</h2>
            </td>
        </tr>
        <tr>
            <td>
                <p>{!! nl2br(e($group_data->introduction)) !!}</p>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    グループの管理者から招待を受けています。<br>
                    このリンクは、 <strong>発行後24時間</strong> 有効です。<br>
                    <br>
                    ※このメールに心当たりのない方はお手数ですがメールを破棄してください。<br>
                    ※このメールに返信をいただいてもお答えできかねますので、あらかじめご了承ください。<br>
                    ※このメール、ならびに『グループに参加する』のリンクを他人に共有しないでください。
                </p>
            </td>
        </tr>
        <tr>
            <td align="center">
                <a href="{{ $url }}" style="display: inline-block; padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">グループに参加する</a>
            </td>
        </tr>
    </table>
</body>
</html>