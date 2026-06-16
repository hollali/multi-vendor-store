<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background: #f3f4f6; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="max-width: 600px; width: 100%; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background: #1D4ED8; padding: 24px 20px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700;">Celer Market</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 32px 30px;">
                            <h2 style="color: #111827; font-size: 20px; margin: 0 0 16px 0;">Order Confirmed!</h2>
                            <p style="color: #374151; font-size: 15px; line-height: 1.6; margin: 0 0 12px 0;">Hi <?= $customer_name ?>,</p>
                            <p style="color: #374151; font-size: 15px; line-height: 1.6; margin: 0 0 12px 0;">
                                Your order <strong style="color: #1D4ED8;">#<?= $order_number ?></strong> has been placed successfully.
                            </p>
                            <p style="color: #374151; font-size: 15px; line-height: 1.6; margin: 0 0 8px 0;">
                                <strong>Total:</strong> GH₵<?= number_format($total, 2) ?>
                            </p>
                            <p style="color: #374151; font-size: 15px; line-height: 1.6; margin: 0 0 20px 0;">
                                We'll notify you when your order ships.
                            </p>
                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background: #1D4ED8; border-radius: 6px; padding: 0;">
                                        <a href="<?= $order_url ?>" style="display: inline-block; background: #1D4ED8; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-size: 15px; font-weight: 600;">View Order</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="background: #f3f4f6; padding: 16px 30px; text-align: center;">
                            <p style="color: #6b7280; font-size: 13px; margin: 0;">&copy; <?= date('Y') ?> Celer Market &mdash; Ghana</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
