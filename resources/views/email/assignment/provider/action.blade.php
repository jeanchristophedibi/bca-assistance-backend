<center><img src="{{ asset('images/logo_eg.jpg') }}" width="200" height="70" alt="logo"></center>
<br>
<p>Bonjour,</p>
<p>Nous vous informons que 
    @if($assignment['status']['code'] == 'pending_for_repairer_quote')
        nous vous informons que la fiche des travaux relative au dossier N° <b>{{ $assignment['reference'] }}</b> vous a été transmise pour la réalisation du devis par <b>{{ $assignment['workSheetEstablishedBy']['name'] }} pour le cabinet <b>BCA-CI</b></b>.<br>
        Nous vous remercions de bien vouloir procéder à l’établissement du devis dans les meilleurs délais et de le soumettre via la plateforme dès finalisation.
    @elseif($assignment['status']['code'] == 'in_editing')
        nous vous informons que le devis relatif au dossier N° <b>{{ $assignment['reference'] }}</b> a été validé par <b>{{ $assignment['quoteValidatedByExpertBy']['name'] }}</b> du cabinet <b>BCA-CI</b>.
    @elseif($assignment['status']['code'] == 'pending_for_repairer_validation')
        nous vous informons que le dossier N° <b>{{ $assignment['reference'] }}</b> a été redigé par <b>{{ $assignment['workSheetEstablishedBy']['name'] }}</b> du cabinet <b>BCA-CI</b> et est en attente votre validation.
    @elseif($assignment['status']['code'] == 'validated')
        nous vous informons que le dossier N° <b>{{ $assignment['reference'] }}</b> a été validé définitivement et envoyé à l'assureur <b>{{ $assignment['insurer']['name'] }}</b> par <b>{{ $assignment['validatedByExpertBy']['name'] }}</b> du cabinet <b>BCA-CI</b>.
    @else
        
    @endif
</p>
<p>Cordialement,</p>
<p>L'équipe BCA-CI</p>