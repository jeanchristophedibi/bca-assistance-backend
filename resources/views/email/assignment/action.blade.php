<center><img src="{{ asset('images/logo_eg.jpg') }}" width="200" height="70" alt="logo"></center>
<br>
<p>Bonjour,</p>
<p>Nous vous informons que 
<b>
    @if($assignment['status']['code'] == 'pending_for_opening')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été créé par <b>{{ $assignment['createdBy']['name'] }}</b>.
    @elseif($assignment['status']['code'] == 'opened')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été ouvert par <b>{{ $assignment['openedBy']['name'] }}</b>.
    @elseif($assignment['status']['code'] == 'realized')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été réalisé par <b>{{ $assignment['realizedBy']['name'] }}</b>.
    @elseif($assignment['status']['code'] == 'pending_for_repairer_quote')
        la fiche des travaux du dossier N° <b>{{ $assignment['reference'] }}</b> a été envoyée au réparateur <b>{{ $assignment['repairer']['name'] }}</b> pour la réalisation d'un devis.
    @elseif($assignment['status']['code'] == 'pending_for_repairer_quote_validation')
        le devis du dossier N° <b>{{ $assignment['reference'] }}</b> a été validé par <b>{{ $assignment['quoteValidatedByRepairerBy']['name'] }}</b> pour le réparateur <b>{{ $assignment['repairer']['name'] }}</b>.
    @elseif($assignment['status']['code'] == 'in_editing')
        le devis du dossier N° <b>{{ $assignment['reference'] }}</b> a été validé par <b>{{ $assignment['quoteValidatedByExpertBy']['name'] }}</b> et est en cours de rédaction.
    @elseif($assignment['status']['code'] == 'in_first_correction')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été edité par <b>{{ $assignment['editedBy']['name'] }}</b> en première correction.
    @elseif($assignment['status']['code'] == 'in_second_correction')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été corrigé dans un premier temps par <b>{{ $assignment['firstCorrectionValidatedBy']['name'] }}</b> et est en deuxième correction.
    @elseif($assignment['status']['code'] == 'for_correction')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été renvoyé en correction par <b>{{ $assignment['editionUnvalidatedBy']['name'] }}</b>.
    @elseif($assignment['status']['code'] == 'pending_for_repairer_validation')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été redigé par BCA-CI et est en attente de validation du réparateur <b>{{ $assignment['repairer']['name'] }}</b>.
    @elseif($assignment['status']['code'] == 'pending_for_expert_validation')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été validé par <b>{{ $assignment['validatedByRepairerBy']['name'] }}</b> pour le réparateur <b>{{ $assignment['repairer']['name'] }}</b> et est en attente de validation finale par BCA-CI.
    @elseif($assignment['status']['code'] == 'validated')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été validé définitivement par <b>{{ $assignment['validatedByExpertBy']['name'] }}</b>.
    @elseif($assignment['status']['code'] == 'paid')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été facturé et payé par <b>{{ $assignment['paidBy']['name'] }}</b>.
    @elseif($assignment['status']['code'] == 'cancelled')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été annulé par <b>{{ $assignment['cancelledBy']['name'] }}</b>.
    @elseif($assignment['status']['code'] == 'classified_without_further_action')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été classé sans suite par <b>{{ $assignment['classifiedWithoutFurtherActionBy']['name'] }}</b>.
    @elseif($assignment['status']['code'] == 'closed')
        le dossier N° <b>{{ $assignment['reference'] }}</b> a été clôturé par <b>{{ $assignment['closedBy']['name'] }}</b>.
    @else
    @endif
</b>
</p>
<p>Cordialement,</p>
<p>L'équipe BCA-CI</p>