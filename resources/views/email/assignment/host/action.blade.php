<center><img src="{{ asset('images/logo_eg.jpg') }}" width="200" height="70" alt="logo"></center>
<br>
<p>Bonjour,</p>
<p>Nous vous informons que 
    @if($assignment['status']['code'] == 'pending_for_repairer_quote')
        nous vous informons que la fiche des travaux du dossier <b>{{ $assignment['reference'] }}</b> a été envoyée au réparateur <b>{{ $assignment['repairer']['name'] }}</b> pour la réalisation d'un devis.<br>
        Vous serez informé(e) dès réception et soumission du devis sur la plateforme.
    @elseif($assignment['status']['code'] == 'pending_for_repairer_quote_validation')
        nous vous informons que le devis du dossier N° <b>{{ $assignment['reference'] }}</b> a été soumis par <b>{{ $assignment['quoteValidatedByRepairerBy']['name'] }}</b> pour le réparateur <b>{{ $assignment['repairer']['name'] }}</b>.
        Vous pouvez le consulter via la plateforme afin de procéder à sa validation.
    @elseif($assignment['status']['code'] == 'pending_for_repairer_validation')
        nous vous informons que le dossier N° <b>{{ $assignment['reference'] }}</b> a été bien envoyé au réparateur <b>{{ $assignment['repairer']['name'] }}</b> pour validation.
    @elseif($assignment['status']['code'] == 'validated')
        nous vous informons que le dossier N° <b>{{ $assignment['reference'] }}</b> a été validé définitivement et envoyé à l'assureur par <b>{{ $assignment['insurer']['name'] }}</b>.
    @else
        
    @endif
</p>
<p>Cordialement,</p>
<p>L'équipe BCA-CI</p>