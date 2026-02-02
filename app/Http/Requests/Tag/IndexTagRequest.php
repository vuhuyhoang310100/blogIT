<?php

namespace App\Http\Requests\Tag;

use App\Constants\Pagination;
use App\DTOs\Tag\TagFilterDTO;
use App\Enums\TrashedFilter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTagRequest extends FormRequest
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
            'q' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', Rule::in(['id', 'name'])],
            'direction' => ['nullable', 'string', 'max:255', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', Rule::in(Pagination::OPTIONS)],
            'trashed' => ['nullable', Rule::enum(TrashedFilter::class)],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function toQueryDTO(): TagFilterDTO
    {
        return TagFilterDTO::fromRequest($this->validated());
    }
}
