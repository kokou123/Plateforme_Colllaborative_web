<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f7; padding: 40px 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="background: #ffffff; border-radius: 8px; padding: 40px;">
                    <tr>
                        <td align="center">
                            <h2 style="color: #3B4A6B; margin-bottom: 8px;">Réinitialisation du mot de passe</h2>
                            <p style="color: #6B7280; margin-bottom: 32px;">
                                Utilisez le code ci-dessous pour choisir un nouveau mot de passe.
                            </p>
                            <div style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #3B4A6B; background: #F3F4F6; padding: 16px 24px; border-radius: 8px; display: inline-block;">
                                {{ $otp }}
                            </div>
                            <p style="color: #9CA3AF; font-size: 13px; margin-top: 32px;">
                                Ce code expire dans 10 minutes. Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>