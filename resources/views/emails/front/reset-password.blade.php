<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
    <table style="background-color: #ebf2f5;width: 945px;margin: auto;">
        <tr style="display: block;">
            <td style="padding: 0;display: block;">
                <table style="display: block;width: 705px;margin: 50px auto;box-shadow: 0px 0px 125px 0px rgba(194, 206, 210, 0.58);">
                    <tr style="display: block;">
                        <td style="padding: 0;display: block;">
                            <table style="display: block;width: 100%; font-family: Arial, Helvetica, sans-serif;">
                                <thead style="background-color: black;display: block;width: 100%;padding: 35px 0;text-align: center;">
                                <tr style="display: block;">
                                    <th style="display: block;text-align: center;">
                                        <img src="{!! asset('img/be-mobily-app-icon-512x512.png') !!}" />
                                    </th>
                                </tr>
                                </thead>
                                <tbody style="display: block;width: 100%;"></tbody>
                            </table>
                            <table style="display: block;width: 100%; font-family: Arial, Helvetica, sans-serif;">
                                <tbody style="display: block;padding:30px 40px 100px 40px;background-color: #fff;">
                                    <tr style="display: block;">
                                        <td style="padding: 0;display: block;text-align: center; font-size: 18px;">
                                            <p>You have requested a new password. Please use this <b style="background: #ebe7e7;padding: 3px;">{!! isset($data['token']) ? $data['token'] : '' !!}</b> temporary password to continue</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>