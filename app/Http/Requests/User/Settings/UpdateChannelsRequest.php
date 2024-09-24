<?php

namespace App\Http\Requests\User\Settings;

use App\Enums\ChannelsEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateChannelsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notification_channels' => 'required|array|in:'.ChannelsEnum::DATABASE->value.','.ChannelsEnum::MAIL->value,
        ];
    }
}
