<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscountRequest extends FormRequest
{
    public function authorize()
    {
        // Update this if you need to add authorization logic
        return true;
    }

    public function rules()
    {
        // Determine if we're updating or creating a discount
        $discountId = $this->route('id'); // For update requests, 'id' will be available in the route

        return [
            'name' => 'required|string|max:255',
            'discountCode' => [
                'required',
                'string',
                'max:255',
                'unique:discounts,discountCode,' . $discountId
            ],
            'discountType' => 'required|in:PERCENTAGE,FIXED',
            'value' => 'required|integer|min:1',
            'applies' => 'required|in:ALL,FAMILY,REPEAT',
            'autoApply' => 'boolean',
            'expiryOn' => 'required|after_or_equal:today',
            'redemptionType' => 'required|in:MAX_USAGE,PER_USER,BOTH',
            // Validation for redemption limits
            'redemptionLimit.PER_USER' => 'nullable|integer|min:1',
            'redemptionLimit.MAX_USAGE' => 'nullable|integer|min:1',
            'redemptionLimit.max_disAmount' => 'nullable|integer|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $perUser = $this->input('redemptionLimit.PER_USER');
            $maxUsage = $this->input('redemptionLimit.MAX_USAGE');

            // Only check if both values are present
            if ($perUser !== null && $maxUsage !== null) {
                // Ensure MAX_USAGE is greater than PER_USER
                if ($maxUsage <= $perUser) {
                    $validator->errors()->add('redemptionLimit.MAX_USAGE', 'Max Usage must be greater than Per User usage.');
                }
            }
        });
    }
}
