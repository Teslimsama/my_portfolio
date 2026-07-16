<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $productName }} License Code</title>
</head>
<body style="font-family: system-ui, -apple-system, sans-serif; background-color: #f9fafb; margin: 0; padding: 40px 0;">
    <div style="max-width: 480px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        <!-- Header -->
        <div style="background-color: #d97706; padding: 24px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 20px;">⚡ Bolakaz Licensing</h1>
        </div>

        <!-- Body -->
        <div style="padding: 32px;">
            <h2 style="color: #111827; font-size: 18px; margin: 0 0 8px 0;">
                Thank you for your purchase!
            </h2>
            <p style="color: #6b7280; font-size: 14px; line-height: 1.6; margin: 0 0 24px 0;">
                Your license code for <strong style="color: #111827;">{{ $productName }}</strong> is ready.
            </p>

            <!-- Code Box -->
            <div style="background-color: #fffbeb; border: 2px dashed #d97706; border-radius: 8px; padding: 20px; text-align: center; margin-bottom: 24px;">
                <p style="color: #92400e; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 8px 0;">Your License Code</p>
                <code style="font-family: 'Courier New', monospace; font-size: 22px; font-weight: 700; color: #111827; letter-spacing: 0.05em;">{{ $code }}</code>
            </div>

            <!-- Details -->
            <div style="background-color: #f9fafb; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
                <table style="width: 100%; font-size: 14px;">
                    <tr>
                        <td style="color: #6b7280; padding: 4px 0;">Product</td>
                        <td style="text-align: right; color: #111827; font-weight: 500; padding: 4px 0;">{{ $productName }}</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7280; padding: 4px 0;">Duration</td>
                        <td style="text-align: right; color: #111827; font-weight: 500; padding: 4px 0;">{{ $durationDays }} day{{ $durationDays > 1 ? 's' : '' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7280; padding: 4px 0;">Price</td>
                        <td style="text-align: right; color: #111827; font-weight: 500; padding: 4px 0;">₦{{ number_format($pricePaid, 2) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Instructions -->
            <p style="color: #374151; font-size: 14px; line-height: 1.6; margin: 0 0 8px 0;">
                <strong>Next steps:</strong> Provide this license code to your site administrator. They will use it to activate the {{ $productName }} license on your website.
            </p>
            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                If you did not make this purchase, please contact support immediately.
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 16px 32px; text-align: center;">
            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                &copy; {{ date('Y') }} Bolakaz. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
