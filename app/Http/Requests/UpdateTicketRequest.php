<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255', 
            'description' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id', 
            'status_id' => 'required|exists:statuses,id', 
            'priority' => 'required|in:low,medium,high,urgent', 
            'category' => 'nullable|string|max:100', 
            'due_date' => 'nullable|date', 
            'is_resolved' => 'boolean',
            'resolved_at' => 'nullable|date|after:due_date',
            'resolution_note' => 'nullable|string'
        ];
    }
}
