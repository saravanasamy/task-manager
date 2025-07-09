<?php

namespace App\Http\Requests;

use App\Services\TaskValidationService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    protected TaskValidationService $validationService;

    public function __construct()
    {
        parent::__construct();
        $this->validationService = app(TaskValidationService::class);
    }

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
        return $this->validationService->getApiValidationRules(false);
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return $this->validationService->getApiValidationMessages();
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'due_date' => 'due date',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean and prepare data before validation
        if ($this->has('title')) {
            $this->merge([
                'title' => trim($this->title)
            ]);
        }

        if ($this->has('description')) {
            $this->merge([
                'description' => $this->description ? trim($this->description) : null
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $task = $this->route('task');
            
            // Add custom validation logic for updates
            if ($this->title && !$this->validationService->validateTitleUniqueness($this->title, $task->id)) {
                $validator->errors()->add('title', 'A task with this title already exists.');
            }
        });
    }
}
