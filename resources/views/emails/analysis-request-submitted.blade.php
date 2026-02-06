<section style="background:#0a0a0a;color:#f5f5f5;font-family:Arial, sans-serif;padding:24px;">
    <div style="max-width:600px;margin:0 auto;background:#111;border-radius:12px;padding:24px;">
        <h2 style="margin:0 0 12px;color:#fff;">Votre demande a bien été reçue</h2>
        <p style="margin:0 0 16px;color:#cfcfcf;">Bonjour {{ $name }}, merci pour votre confiance. Voici le
            récapitulatif :</p>

        <ul style="padding-left:18px;color:#cfcfcf;line-height:1.7;">
            <li><strong>Nom:</strong> {{ $name }}</li>
            <li><strong>Email:</strong> {{ $email }}</li>
            <li><strong>Téléphone:</strong> {{ $phone }}</li>
            <li><strong>Université:</strong> {{ $university ?: 'Non renseigné' }}</li>
            <li><strong>Sujet:</strong> {{ $subject }}</li>
            <li><strong>Fichier:</strong> {{ $originalFilename }}</li>
        </ul>

        <p style="margin-top:16px;color:#cfcfcf;">
            Si une information est incorrecte, répondez simplement à ce mail pour nous le signaler.
        </p>

        <footer style="margin-top: 2rem; text-align: center;">
            <p style="margin-top: 0.75rem; color: #6b7280">© {{ date('Y') }} Vinify. Tous les droits sont réservés.
            </p>
        </footer>
    </div>
</section>