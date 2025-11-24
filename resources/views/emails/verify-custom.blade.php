@if(app()->getLocale() ==='fr')
<x-mail::message>
# Vérification de votre adresse email

Bonjour Cher participant,

Merci d'avoir créé un compte sur **{{ config('app.name') }}**. Pour finaliser votre inscription et sécuriser votre compte, veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous.

<x-mail::button :url="$url">
Vérifier mon email
</x-mail::button>

**Informations importantes :**
- Ce lien de vérification expirera dans 60 minutes
- La vérification est nécessaire pour accéder à toutes les fonctionnalités
- Vous pourrez réinitialiser votre mot de passe si nécessaire

**Problèmes de vérification ?**  
Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :

<div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin: 16px 0;">
    <code style="color: #dc3545; word-break: break-all; font-size: 12px;">{{ $url }}</code>
</div>

Si vous n'avez pas créé ce compte, vous pouvez ignorer cet email en toute sécurité.

**Besoin d'aide ?**  
Contactez notre équipe de support à [{{ config('mail.support_email', 'event@afwasa.org') }}](mailto:{{ config('mail.support_email', 'event@afwasa.org') }})

Cordialement,<br>
**L'équipe {{ config('app.name') }}**

<small style="color: #6c757d;">
Ceci est un message automatique, merci de ne pas y répondre.
</small>
</x-mail::message>
@else
<x-mail::message>
# Verify Your Email Address

Hello Dear Participant,

Thank you for creating an account. To complete your registration and secure your account, please verify your email address by clicking the button below.

<x-mail::button :url="$url">
Verify My Email
</x-mail::button>

**Important Information:**
- This verification link will expire in 60 minutes
- Verification is required to access all platform features
- You'll be able to reset your password if needed

**Trouble verifying?**  
If the button doesn't work, copy and paste this URL into your browser:

<div style="background: #f8f9fa; padding: 12px; border-radius: 6px; margin: 16px 0;">
    <code style="color: #dc3545; word-break: break-all; font-size: 12px;">{{ $url }}</code>
</div>

If you didn't create this account, you can safely ignore this email.

**Need assistance?**  
Contact our support team at [{{ config('mail.support_email', 'event@afwasa.org') }}](mailto:{{ config('mail.support_email', 'event@afwasa.org') }})

Best regards,<br>
**The {{ config('app.name') }} Team**

<small style="color: #6c757d;">
This is an automated message, please do not reply to this email.
</small>
</x-mail::message>
@endif