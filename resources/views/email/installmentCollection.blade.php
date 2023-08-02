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
                                                    Hello,
                                                    <br />
                                                    <br />
                                                    This is a reminder for your installmental payment for
                                                    <strong>{{ $course }} </strong> course
                                                    enrollment. Please make hast to complete your payment within the
                                                    shortest time possible by following the instruction below, thank
                                                    you!
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
                                            <td align="center" style="padding:0;Margin:0">
                                                <p
                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:tahoma, verdana, segoe, sans-serif;line-height:36px;color:#333333;font-size:24px">
                                                    Clicking below to complete payment.
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
<table class="es-footer" cellspacing="0" cellpadding="0" align="center"
    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
    <tr>
        <td align="center" bgcolor="#eeeeee" style="padding:0;Margin:0;background-color:#eeeeee">
            <table class="es-footer-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"
                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                <tr>
                    <td align="left" style="padding:40px;Margin:0">
                        <table cellspacing="0" cellpadding="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:520px">
                                    <table width="100%" cellspacing="0" cellpadding="0" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center" style="padding:0;Margin:0"><span class="es-button-border"
                                                    style="border-style:solid;border-color:#1e40af;background:#1e40af;border-width:0px;display:inline-block;border-radius:30px;width:auto"><a
                                                        href="{{ $paymentLink }}" class="es-button" target="_blank"
                                                        style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#FFFFFF;font-size:28px;display:inline-block;background:#1e40af;border-radius:30px;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-weight:bold;font-style:normal;line-height:34px;width:auto;text-align:center;padding:10px 20px 10px 20px;mso-padding-alt:0;mso-border-alt:10px solid #1e40af">
                                                        Make Payment
                                                    </a></span></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
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
                                            <td align="left" style="padding:0;Margin:0">
                                                <p
                                                    style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;font-size:14px">
                                                    <br>
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