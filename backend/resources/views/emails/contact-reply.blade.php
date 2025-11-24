<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ $subject }}</title>
    <style type="text/css">
        /* RTL Support */
        body, table, td, div, p, h1, h2, h3, h4, h5, h6, span, a {
            direction: rtl !important;
            text-align: right !important;
        }
        .center-text {
            text-align: center !important;
        }
        /* Reset */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f4f4f5;
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }
        table {
            border-collapse: collapse !important;
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        /* Mobile Styles */
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }
            .mobile-padding {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .mobile-stack {
                display: block !important;
                width: 100% !important;
            }
        }
    </style>
    <!--[if mso]>
    <style type="text/css">
        table {border-collapse: collapse;}
        .fallback-font {font-family: Arial, sans-serif;}
    </style>
    <![endif]-->
</head>
<body dir="rtl" style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: 'Segoe UI', Tahoma, Arial, sans-serif; direction: rtl; text-align: right;">
    
    <!-- Main Container -->
    <table role="presentation" dir="rtl" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f4f5; direction: rtl;">
        <tr>
            <td align="center" style="padding: 40px 20px; direction: rtl;">
                
                <!-- Email Card -->
                <table role="presentation" dir="rtl" width="600" cellspacing="0" cellpadding="0" class="email-container" style="max-width: 600px; width: 100%; direction: rtl;">
                    
                    <!-- Header with Logo -->
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, {{ $themeColor ?? '#d4af37' }} 0%, #b8962e 100%); padding: 30px 40px; border-radius: 16px 16px 0 0; text-align: center !important; direction: ltr;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="text-align: center !important;">
                                        @if($logoUrl)
                                            <img src="{{ $logoUrl }}" alt="{{ $siteName }}" style="max-height: 60px; max-width: 200px; margin: 0 auto; display: block;">
                                        @else
                                            <h1 style="color: #ffffff; font-size: 28px; margin: 0; font-weight: bold; text-align: center;">{{ $siteName }}</h1>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td dir="rtl" style="background-color: #ffffff; padding: 40px; direction: rtl; text-align: right;" class="mobile-padding">
                            
                            <!-- Greeting -->
                            <h2 dir="rtl" style="color: #1f2937; font-size: 22px; margin: 0 0 20px 0; font-weight: 600; direction: rtl; text-align: right;">
                                مرحباً {{ $recipientName }}
                            </h2>
                            
                            <!-- Message Content -->
                            <div dir="rtl" style="color: #374151; font-size: 16px; line-height: 1.8; margin-bottom: 30px; direction: rtl; text-align: right;">
                                {!! nl2br(e($content)) !!}
                            </div>
                            
                            <!-- Divider -->
                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
                            
                            <!-- Original Message Reference -->
                            @if($originalSubject)
                            <div dir="rtl" style="background-color: #f9fafb; border-right: 4px solid {{ $themeColor ?? '#d4af37' }}; padding: 20px; border-radius: 8px; margin-bottom: 20px; direction: rtl; text-align: right;">
                                <p dir="rtl" style="color: #6b7280; font-size: 13px; margin: 0 0 8px 0; font-weight: 600; direction: rtl; text-align: right;">
                                    ↩️ رداً على رسالتكم:
                                </p>
                                <p dir="rtl" style="color: #1f2937; font-size: 15px; margin: 0; font-weight: 500; direction: rtl; text-align: right;">{{ $originalSubject }}</p>
                            </div>
                            @endif
                            
                            <!-- Signature -->
                            <div dir="rtl" style="margin-top: 30px; direction: rtl; text-align: right;">
                                <p dir="rtl" style="color: #6b7280; font-size: 15px; margin: 0; direction: rtl; text-align: right;">مع أطيب التحيات،</p>
                                <p dir="rtl" style="color: #1f2937; font-size: 17px; font-weight: 600; margin: 8px 0 0 0; direction: rtl; text-align: right;">{{ $siteName }}</p>
                            </div>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #1f2937; padding: 30px 40px; border-radius: 0 0 16px 16px; text-align: center;" class="mobile-padding">
                            
                            <!-- Contact Info -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" align="center">
                                <tr>
                                    <td align="center" style="text-align: center !important;">
                                        
                                        <!-- Social Links -->
                                        @if(!empty($socialLinks))
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom: 20px;">
                                            <tr>
                                                <td align="center" style="text-align: center !important;">
                                                    @foreach($socialLinks as $platform => $url)
                                                        @if($url)
                                                        <a href="{{ $url }}" style="display: inline-block; margin: 0 8px; text-decoration: none;">
                                                            @if($platform == 'facebook')
                                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b9/2023_Facebook_icon.svg/24px-2023_Facebook_icon.svg.png" alt="Facebook" style="width: 28px; height: 28px;">
                                                            @elseif($platform == 'twitter')
                                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/ce/X_logo_2023.svg/24px-X_logo_2023.svg.png" alt="X" style="width: 28px; height: 28px; background: #fff; border-radius: 6px; padding: 4px;">
                                                            @elseif($platform == 'instagram')
                                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Instagram_logo_2016.svg/24px-Instagram_logo_2016.svg.png" alt="Instagram" style="width: 28px; height: 28px;">
                                                            @elseif($platform == 'youtube')
                                                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/YouTube_full-color_icon_%282017%29.svg/24px-YouTube_full-color_icon_%282017%29.svg.png" alt="YouTube" style="width: 28px; height: 28px;">
                                                            @endif
                                                        </a>
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                        </table>
                                        @endif
                                        
                                        <!-- Contact Details -->
                                        @if($contactEmail)
                                        <p style="color: #9ca3af; font-size: 13px; margin: 5px 0; text-align: center !important;">{{ $contactEmail }}</p>
                                        @endif
                                        @if($contactPhone)
                                        <p style="color: #9ca3af; font-size: 13px; margin: 5px 0; text-align: center !important;">{{ $contactPhone }}</p>
                                        @endif
                                        @if($contactAddress)
                                        <p style="color: #9ca3af; font-size: 13px; margin: 5px 0; text-align: center !important;">{{ $contactAddress }}</p>
                                        @endif
                                        
                                        <!-- Copyright -->
                                        <p style="color: #6b7280; font-size: 12px; margin: 15px 0 0 0; text-align: center !important;">© {{ date('Y') }} {{ $siteName }} - جميع الحقوق محفوظة</p>
                                        
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    
                </table>
                
                <!-- Unsubscribe Notice -->
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width: 600px; width: 100%; margin-top: 20px;">
                    <tr>
                        <td align="center" style="text-align: center;">
                            <p style="color: #9ca3af; font-size: 11px; margin: 0; text-align: center;">
                                تم إرسال هذه الرسالة رداً على استفساركم. إذا لم تكن أنت من أرسل الاستفسار، يرجى تجاهل هذه الرسالة.
                            </p>
                        </td>
                    </tr>
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>
