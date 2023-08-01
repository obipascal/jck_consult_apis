@section('enquiry_header')
<table class="es-header" cellspacing="0" cellpadding="0" align="center"
    style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
    <tr>
        <td align="center" bgcolor="#eeeeee" style="padding:0;Margin:0;background-color:#eeeeee">
            <table class="es-header-body" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center"
                style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:600px">
                <tr class="es-mobile-hidden">
                    <td align="left" bgcolor="#eeeeee"
                        style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px;background-color:#eeeeee">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="center" valign="top" style="padding:0;Margin:0;width:560px">
                                    <table cellpadding="0" cellspacing="0" width="100%"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="center" style="padding:0;Margin:0;display:none"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding:0;Margin:0">
                        <table cellpadding="0" cellspacing="0" width="100%"
                            style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                            <tr>
                                <td align="center" valign="top" style="padding:0;Margin:0;width:600px">
                                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation"
                                        style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                        <tr>
                                            <td align="left" style="padding:0;Margin:0;padding-top:25px">
                                                <h1
                                                    style="Margin:0;line-height:51px;mso-line-height-rule:exactly;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:34px;font-style:normal;font-weight:normal;color:#1e40af;margin-left:40px;text-align:left">
                                                    {{ $enquirySubject ?? "" }}
                                                </h1>
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
