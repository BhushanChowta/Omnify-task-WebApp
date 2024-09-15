<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Transaction;

class DiscountController extends Controller
{

    public function applyDiscount(Request $request)
    {
        $discount_code = $request->discount_code ?? null;
        $amountAfterDiscount = $price = $request->price ?? 0;
        $discountedAmount = 0;
        $customer_id = $request->customer_id; //Payer
        $schedule_ids = $request->services;

        if(!isset($discount_code)){
            // Applies a discount if any family member has previously purchased the same schedule.
            $isOldCusFamilyMember = self::isOldCusFamilyMember($request);

            if($isOldCusFamilyMember){
                $discount_code = "FAMILY5";
            }

            // Applies a discount When an attendee books the same schedule or subscription again.
            $isRepeatCustomer = self::isRepeatCustomer($request);
            if($isRepeatCustomer && !isset($discount_code)){
                $discount_code = "REPEAT5";
            }

            $discountInfo =  Discount::where('discountCode', $discount_code)->where('autoApply', true)->first();
        } else {
            $discountInfo =  Discount::where('discountCode', $discount_code)->first();
        }

        if (!$discountInfo) {
            // Return 400 error if discount is not found
            return response()->json([
                'code' => 400,
                'success' => false,
                'error' => 'No Discount Found',
            ], 400);
        }

        // Check if discount rules are OK with respect to transactions
        $isOkDiscountRules = $this->isOkDiscountRules($discountInfo, $customer_id);
        if (!$isOkDiscountRules['success']) {
            // Return an error response if discount rule validation fails
            return response()->json([
                'code' => 400,
                'success' => false,
                'error' => $isOkDiscountRules['message'],
            ], 400);
        }

        // Apply the discount based on the discount type
        if ($discountInfo->discountType == 'FIXED') {
            $discountedAmount = $discountInfo->value;
            $amountAfterDiscount = $price - $discountedAmount;
        } else if ($discountInfo->discountType == 'PERCENTAGE') {
            $discountedAmount = ($price * $discountInfo->value) / 100;
            $amountAfterDiscount = $price - $discountedAmount;
        } else {
            $amountAfterDiscount = $amountAfterDiscount;
            $discountedAmount = $discountedAmount;
        }

        return response()->json([
            'code' => 200,
            'success' => true,
            'data' => [
                'amountAfterDiscount' => $amountAfterDiscount,
                'discountedAmount' => $discountedAmount,
                'discount_name' => $discountInfo->name ?? null,
                'discount_code' => $discountInfo->discountCode ?? null,
            ]
        ], 200);
    }

    private function isOldCusFamilyMember(Request $request)
    {
        $familyCustomerIDs = Customer::where('family_id', Customer::find($request->customer_id)->family_id)->pluck('id');
        
        // Decode the services JSON object from the request
        $servicesObject = json_decode($request->services, true);
        $key = key($servicesObject);
        $value = $servicesObject[$key];

        $hasSuccessfulTransaction = Transaction::whereIn('customer_id', $familyCustomerIDs)
                                            ->where(function($query) use ($key, $value) {
                                                if ($key && $value) {
                                                    // Use string manipulation to account for curly braces and quotes
                                                    $query->whereRaw('services LIKE ?', ['%{"' . $key . '":"' . $value . '"}%']);
                                                }
                                            })
                                            ->where('status', 'SUCCESS')
                                            ->exists();

        return $hasSuccessfulTransaction;
    }

    private function isRepeatCustomer(Request $request)
    {
        // Decode the services JSON object from the request
        $servicesObject = json_decode($request->services, true);
        $key = key($servicesObject);
        $value = $servicesObject[$key];

        $hasSuccessfulTransaction = Transaction::where('customer_id', $request->customer_id)
                                            ->where(function($query) use ($key, $value) {
                                                if ($key && $value) {
                                                    // Use string manipulation to account for curly braces and quotes
                                                    $query->whereRaw('services LIKE ?', ['%{"' . $key . '":"' . $value . '"}%']);
                                                }
                                            })
                                            ->where('status', 'SUCCESS') // Assuming you have a 'status' field
                                            ->exists();

        return $hasSuccessfulTransaction;
    }

    private function isOkDiscountRules($discountInfo, $customer_id)
    {
        // Fetch all transactions for the given discount code
        $query = Transaction::where('discount_id', $discountInfo->id)->where('status', 'SUCCESS');

        // Include customer_id filter only if redemptionType is not MAX_USAGE
        if ($discountInfo->redemptionType != 'MAX_USAGE') {
            $query->where('customer_id', $customer_id);
        }

        $transactions = $query->get();

        // Sum the total discount amount for this customer using this discount code
        $totalDiscountUsed = $transactions->sum('discount_amount');

        // Count how many times this discount code has been used
        $timesDiscountUsed = $transactions->count();

        // Check if the total discount amount exceeds the maximum allowed amount
        if ($totalDiscountUsed >= $discountInfo->redemptionLimit['max_disAmount']) {
            return [
                'success' => false,
                'message' => 'Maximum allowed discount amount has been reached.',
            ];
        }

        // Check if PER_USER and MAX_USAGE keys exist in redemptionLimit array
        $perUserLimit = isset($discountInfo->redemptionLimit['PER_USER']) ? $discountInfo->redemptionLimit['PER_USER'] : null;
        $maxUsageLimit = isset($discountInfo->redemptionLimit['MAX_USAGE']) ? $discountInfo->redemptionLimit['MAX_USAGE'] : null;

        // Handle the case when both limits are present
        if (($perUserLimit !== null && $timesDiscountUsed >= $perUserLimit) || 
            ($maxUsageLimit !== null && $timesDiscountUsed >= $maxUsageLimit)) {
            return [
                'status' => 'error',
                'message' => 'Maximum allowed discount amount has been reached.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Discount rules are valid.',
        ];
    }

}
