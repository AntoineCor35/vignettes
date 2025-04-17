<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'card_size_id' => 'required|exists:card_sizes,id',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:10240',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm,mkv,flv,m4v,3gp|max:102400',
            'music' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac,flac|max:20480',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = array_merge($rules, [
                'remove_image' => 'nullable|boolean',
                'remove_video' => 'nullable|boolean',
                'remove_music' => 'nullable|boolean',
            ]);
        }

        return $rules;
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasVideo = $this->hasFile('video');
            $hasImage = $this->hasFile('image');
            $hasMusic = $this->hasFile('music');

            if ($hasVideo && ($hasImage || $hasMusic)) {
                $validator->errors()->add('media', 'La vidéo doit être seule.');
            }
        });
    }
}
