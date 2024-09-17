<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Transaction;
use App\Http\Requests\API\DiscountRequest;
use config;
use Carbon\Carbon;

class DiscountController extends Controller
{

    public function applyDiscount(DiscountRequest $request)
    {
        $discount_code = $request->discount_code ?? null;
        $price = $request->price;
        $customer_id = $request->customer_id; //Payer
        $services = $request->services;

        if(!isset($discount_code)){
            // Applies a discount if any family member has previously purchased the same schedule.
            $isOldCusFamilyMember = self::isOldCusFamilyMember($services, $customer_id);
            if($isOldCusFamilyMember){
                $discount = Discount::where('availableTo',config('constants.available_to.FAMILY'))->first();
                $discount_code = $discount ? $discount->discountCode : null;
            }

            // Applies a discount When an attendee books the same schedule or subscription again.
            $isRepeatCustomer = self::isRepeatCustomer($services, $customer_id);
            if($isRepeatCustomer){
                $discount = Discount::where('availableTo',config('constants.available_to.REPEAT'))->first();
                $discount_code = $discount ? $discount->discountCode : null;
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
                'error' => config('constants.messages.discount.notFound'),
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

        // Apply the discount
        $discountData = $this->calculateDiscount($discountInfo, $price);

        return response()->json([
            'code' => 200,
            'success' => true,
            'data' => $discountData,
        ], 200);
    }

    //1. Family Member Discount Check
    private function isOldCusFamilyMember($services, $customer_id)
    {
        $familyCustomerIDs = Customer::where('family_id', Customer::find($customer_id)->family_id)->pluck('id');
        
        // Decode the services JSON object from the request
        $servicesObject = json_decode($services, true);
        $key = key($servicesObject);
        $value = $servicesObject[$key];

        $hasSuccessfulTransaction = Transaction::whereIn('customer_id', $familyCustomerIDs)
                                            ->where(function($query) use ($key, $value) {
                                                if ($key && $value) {
                                                    // Comparing the services
                                                    $query->whereRaw('services LIKE ?', ['%{"' . $key . '":"' . $value . '"}%']);
                                                }
                                            })
                                            ->where('status', config('constants.status.success'))
                                            ->exists();

        return $hasSuccessfulTransaction;
    }

    //2. Recurring Discount Check
    private function isRepeatCustomer($services, $customer_id)
    {
        // Decode the services JSON object from the request
        $servicesObject = json_decode($services, true);
        $key = key($servicesObject);
        $value = $servicesObject[$key];

        $hasSuccessfulTransaction = Transaction::where('customer_id', $customer_id)
                                            ->where(function($query) use ($key, $value) {
                                                if ($key && $value) {
                                                    // Comparing the services
                                                    $query->whereRaw('services LIKE ?', ['%{"' . $key . '":"' . $value . '"}%']);
                                                }
                                            })
                                            ->where('status', config('constants.status.success')) // Assuming you have a 'status' field
                                            ->exists();

        return $hasSuccessfulTransaction;
    }

    //3. Discount Rules - date, number of uses and maximum discount amount validation
    private function isOkDiscountRules($discountInfo, $customer_id)
    {
        // Check if the discount code is expired or not.
        $expiryDate = Carbon::parse($discountInfo->expiryOn);
        if (Carbon::now()->greaterThan($expiryDate)) {
            return [
                'success' => false,
                'message' => config('constants.messages.discount.expired'),
            ];
        }

        // Fetch all transactions for the given discount code
        $query = Transaction::where('discount_id', $discountInfo->id)->where('status', config('constants.status.success'));
        $rawTransactions = $query->get();

        // Include customer_id filter only if redemptionType is not MAX_USAGE
        if ($discountInfo->redemptionType != 'MAX_USAGE') {
            $query->where('customer_id', $customer_id);
        }
        $transactions = $query->get();

        // Sum the total discount amount for this customer using this discount code
        $totalDiscountUsed = $rawTransactions->sum('discountAmount');

        // Count how many times this discount code has been used
        $timesDiscountUsedByCus = $transactions->count();
        $timesDiscountUsedByAll = $rawTransactions->count();

        // Check if the total discount amount exceeds the maximum allowed amount
        if ($totalDiscountUsed >= $discountInfo->redemptionLimit['max_disAmount']) {
            return [
                'success' => false,
                'message' => config('constants.messages.discount.maxAmountLimit'),
            ];
        }

        // Check if PER_USER and MAX_USAGE keys exist in redemptionLimit array
        $perUserLimit = isset($discountInfo->redemptionLimit['PER_USER']) ? $discountInfo->redemptionLimit['PER_USER'] : null;
        $maxUsageLimit = isset($discountInfo->redemptionLimit['MAX_USAGE']) ? $discountInfo->redemptionLimit['MAX_USAGE'] : null;

        // Handle the case when both limits are present
        if (($perUserLimit !== null && $timesDiscountUsedByCus >= $perUserLimit) || 
            ($maxUsageLimit !== null && $timesDiscountUsedByAll >= $maxUsageLimit)) {
            return [
                'status' => 'error',
                'message' => config('constants.messages.discount.maxUsageLimit'),
            ];
        }

        return [
            'success' => true,
            'message' => config('constants.messages.discount.valid'),
        ];
    }
    
    // Discount Calculation
    private function calculateDiscount($discountInfo, $price) {
        $discountedAmount = 0;
        $amountAfterDiscount = $price;

        if ($discountInfo->discountType === 'FIXED') {
            $discountedAmount = $discountInfo->value;
            $amountAfterDiscount = $price - $discountedAmount;
        } elseif ($discountInfo->discountType === 'PERCENTAGE') {
            $discountedAmount = ($price * $discountInfo->value) / 100;
            $amountAfterDiscount = $price - $discountedAmount;
        }

        return [
            'amountAfterDiscount' => $amountAfterDiscount,
            'discountedAmount' => $discountedAmount,
            'discount_name' => $discountInfo->name ?? null,
            'discount_code' => $discountInfo->discountCode ?? null,
        ];
    } 

}
