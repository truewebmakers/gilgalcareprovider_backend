<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Name</title>
</head>

<body bgcolor="#0f3462" style="margin-top:20px;margin-bottom:20px">
    <!-- Main table -->
    <table border="0" align="center" cellspacing="0" cellpadding="0" bgcolor="white" width="650">
        <tr>
            <td>
                <!-- Child table -->
                <table border="0" cellspacing="0" cellpadding="0" style="color:#0f3462; font-family: sans-serif;">
                    <tr>
                        <td>
                            <h2 style="text-align:center; margin: 0px; padding-bottom: 25px; margin-top: 25px;">
                                <i>Gilgal Care</i><span style="color:lightcoral">Provider</span>
                            </h2>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="https://gilgalcareprovider.s3.ap-southeast-1.amazonaws.com/logo/logo-bgr.png" height="50px"
                            style="display:block; margin:auto;padding-bottom: 25px; " class="img-fluid" alt="Logo">
                        </td>
                    </tr>
                    <tr>

                        <td style="text-align: center;">
                            <h1 style="margin: 0px;padding-bottom: 25px; text-transform: uppercase;">Email Verification- Gilgal Care Provider
                            </h1>
                            {{-- <h2 style="margin: 0px;padding-bottom: 25px;font-size:22px;"> Please renew your subscription</h2> --}}
                            <p style=" margin: 0px 40px;padding-bottom: 25px;line-height: 2; font-size: 15px;">

                                Hello {{ $user->fname }},

                                Welcome to Gilgal Care Provider

                                To complete your account setup, please confirm your email address by clicking the link
                                below:
                                <a target="_blank" href="{{$verificationUrl}}"
                                    style="background-color:#36b445; color:white; padding:15px 97px; outline: none; display: block; margin: auto; border-radius: 31px;
                                font-weight: bold; margin-top: 25px; margin-bottom: 25px; border: none; text-transform:uppercase;">
                                    Verify Your Email
                                </a>

                                If the link above doesn’t work, copy and paste this URL into your browser: {{$verificationUrl}}

                                Verifying your email helps keep your account secure and ensures you have full access to
                                all our features.

                                Need help?
                                If you didn’t create this account or have questions, feel free to contact us at
                                info@gilgalcareprovider.com.au

                                Thank you,
                               Gilgal Care Provider Team

                                Please do not reply to this email, as it is not monitored.
                            </p>

                            {{-- <h2 style="margin: 0px; padding-bottom: 25px;">Expire: 05 November</h2> --}}
                        </td>

                    </tr>
                    {{-- <tr>
                        <td>
                            <button type="button"
                                style="background-color:#36b445; color:white; padding:15px 97px; outline: none; display: block; margin: auto; border-radius: 31px;
                                font-weight: bold; margin-top: 25px; margin-bottom: 25px; border: none; text-transform:uppercase; ">Renew</button>
                        </td>
                    </tr> --}}
                    <tr>
                        <td style="text-align:center;">
                            <h2 style="padding-top: 25px; line-height: 1; margin:0px;">Need Help?</h2>
                            <div style="margin-bottom: 25px; font-size: 15px;margin-top:7px;">info@gilgalcareprovider.com.au
                            </div>
                        </td>
                    </tr>
                </table>
                <!-- /Child table -->
            </td>
        </tr>
    </table>
    <!-- / Main table -->
</body>

</html>
