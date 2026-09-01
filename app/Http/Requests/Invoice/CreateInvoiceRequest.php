<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // 'assignment_id' => 'required|exists:assignments,id|unique:invoices,assignment_id',
            'assignment_id' => 'required|exists:assignments,id',
            'date' => 'required|date_format:Y-m-d',
            'object' => 'required|string',
            'type' => 'required|in:sale,credit_bill',
            'invoice_reference' => 'nullable|required_if:type,credit_bill|exists:invoices,reference|string',
            'payment_method' => 'required|in:cash,card,check,mobile-money,transfer,deferred',
            'template' => 'required|in:B2C,B2B,B2F,B2G',
            'is_fne' => 'nullable|boolean',
            'address' => 'nullable|string',
            'taxpayer_account_number' => 'nullable|required_if:template,B2B|string',
        ];
    }

    public function messages(): array
    {
        return [
            'date.date' => 'La date est invalide.',
            'date.date_format' => 'Le format de la date est invalide.',
            'address.string' => 'L\'adresse est invalide.',
            'taxpayer_account_number.string' => 'Le numéro de compte contribuable est invalide.',
            'type.in' => 'Le type de facture est invalide. Les types valides sont : sale, credit_bill.',
            'payment_method.in' => 'La méthode de paiement est invalide. Les méthodes valides sont : cash, card, check, mobile-money, transfer, deferred.',
            'template.in' => 'Le template est invalide. Les templates valides sont : B2C, B2B, B2F, B2G.',
            'is_fne.boolean' => 'Le statut FNE est invalide. Les statuts valides sont : true, false.',
        ];
    }
}
