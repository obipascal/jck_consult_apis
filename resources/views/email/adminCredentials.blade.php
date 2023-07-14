@extends('include.layout')
@section('content')
<table class="es-content" cellspacing="0" cellpadding="0" align="center"
    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
    <tr>
        <td align="center" bgcolor="#eeeeee" style="padding:0;Margin:0;background-color:#eeeeee">
            <table class="es-content-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"
                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                <tr>
                    <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
                        <table width="100%" cellspacing="0" cellpadding="0"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td valign="top" align="center" style="padding:0;Margin:0;width:560px">
                                    <table width="100%" cellspacing="0" cellpadding="0"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;border-radius:16px"
                                        role="presentation">
                                        <tr>
                                            <td align="left" class="es-m-txt-l" style="padding:0;Margin:0">
                                                <p
                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:27px;color:#333333;font-size:18px">
                                                    Hello there!
                                                    <br />
                                                    <br />
                                                    Please find below the login credentials for the administrative
                                                    console. <strong>NOTE:</strong> This credentials are generated
                                                    automatically, you can change your password via the application in
                                                    settings. Thank you!
                                                </p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" style="padding:20px;Margin:0;font-size:0">
                                                <table border="0" width="100%" height="100%" cellpadding="0"
                                                    cellspacing="0" role="presentation"
                                                    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                    <tr>
                                                        <td
                                                            style="padding:0;Margin:0;border-bottom:1px solid #cccccc;background:unset;height:1px;width:100%;margin:0px">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" style="padding:10px;Margin:0">
                                                <p
                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:tahoma, verdana, segoe, sans-serif;line-height:36px;color:#333333;font-size:24px">
                                                    <strong>Username / Email:</strong> {{ $email }}
                                                    <br />
                                                    <strong>Password:</strong> {{ $password }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection