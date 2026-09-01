<?php

namespace App\Http\Requests\AssignmentMessage;

use App\Models\Assignment;
use App\Models\Status;
use Illuminate\Foundation\Http\FormRequest;

class CreateAssignmentMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'assignment_id' => 'required|exists:assignments,id',
            'message' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'assignment_id.exists' => 'Le dossier n\'existe pas.',
            'message.required' => 'Le message est requis.',
        ];
    }
}
