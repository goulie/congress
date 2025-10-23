<form action="{{ route('otp.send') }}" method="POST">
    @csrf
    <input type="email" name="email" placeholder="Votre email" required>
    <button type="submit">Envoyer OTP</button>
</form>

<form action="{{ route('otp.verify') }}" method="POST">
    @csrf
    <input type="email" name="email" placeholder="Votre email" required>
    <input type="text" name="otp" placeholder="Code OTP" required>
    <button type="submit">Vérifier</button>
</form>
