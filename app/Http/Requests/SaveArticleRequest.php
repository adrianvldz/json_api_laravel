<?php

namespace App\Http\Requests;

use App\Rules\Slug;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveArticleRequest extends FormRequest
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
            'data.attributes.title' => ['required', 'min:4'],
            'data.attributes.slug' => ['required', 
            Rule::unique('articles', 'slug')->ignore($this->route('article')),
            'alpha_dash',
            new Slug(),

        ],
            'data.attributes.content' => ['required'],
            'data.relationships.category.data.id' => [
                Rule::requiredIf(! $this->route('article')),
                Rule::exists('categories', 'slug')
                ]
        ];
    }

    public function validated($key = 'data.attributes', $default = null)
    {
        $data = parent::validated()['data'];
        $attributes = $data['attributes'];

        if(isset($data['relationships'])){
            
            $relationships = $data['relationships'];
            $categorySlug = $relationships['category']['data']['id'];
            $category = Category::where('slug', $categorySlug)->first();
            $attributes['category_id'] = $category->id; 
        }

       return $attributes;
    }
}
