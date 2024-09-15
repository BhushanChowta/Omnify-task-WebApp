<div>
    <form action="{{ isset($discount) ? route('discounts.update', $discount->id) : route('discounts.store') }}" method="POST">
        @csrf
        @if (isset($discount))
            @method('PUT')
        @endif

        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $discount->name ?? '') }}" required>
            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="discountCode">Discount Code:</label>
            <input type="text" id="discountCode" name="discountCode" value="{{ old('discountCode', $discount->discountCode ?? '') }}" required>
            @error('discountCode')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="discountType">Discount Type:</label>
            <select id="discountType" name="discountType" required>
                <option value="PERCENTAGE" {{ old('discountType', $discount->discountType ?? '') == 'PERCENTAGE' ? 'selected' : '' }}>Percentage</option>
                <option value="FIXED" {{ old('discountType', $discount->discountType ?? '') == 'FIXED' ? 'selected' : '' }}>Fixed</option>
            </select>
            @error('discountType')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="value">Value:</label>
            <input type="number" id="value" name="value" value="{{ old('value', $discount->value ?? '') }}" required>
            @error('value')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="availableTo">Available to:</label>
            <select id="availableTo" name="availableTo" required>
                @foreach(config('constants.available_to') as $value => $label)
                    <option value="{{ $value }}" {{ old('availableTo', $discount->availableTo ?? '') == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('availableTo')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="autoApply">Auto Apply:</label>
            <input type="hidden" name="autoApply" value="0">
            <input type="checkbox" id="autoApply" name="autoApply" value="1" {{ old('autoApply', $discount->autoApply ?? '') ? 'checked' : '' }}>
            @error('autoApply')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="expiryOn">Expiry Date:</label>
            <input type="date" id="expiryOn" name="expiryOn" value="{{ old('expiryOn', isset($discount) ? \Carbon\Carbon::parse($discount->expiryOn)->format('Y-m-d') : '') }}">
            @error('expiryOn')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="redemptionType">Redemption Type:</label>
            <select id="redemptionType" name="redemptionType" required>
                <option value="MAX_USAGE" {{ old('redemptionType', $discount->redemptionType ?? '') == 'MAX_USAGE' ? 'selected' : '' }}>Max Usage</option>
                <option value="PER_USER" {{ old('redemptionType', $discount->redemptionType ?? '') == 'PER_USER' ? 'selected' : '' }}>Per User Max Usage</option>
                <option value="BOTH" {{ old('redemptionType', $discount->redemptionType ?? '') == 'BOTH' ? 'selected' : '' }}>Apply Both Conditions</option>
            </select>
            @error('redemptionType')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div id="perUserLimitWrapper">
            <label for="redemptionLimit[PER_USER]">Redemption Limit (Per User):</label>
            <input type="number" name="redemptionLimit[PER_USER]" id="redemptionLimit[PER_USER]" value="{{ old('redemptionLimit.PER_USER', $discount->redemptionLimit['PER_USER'] ?? 0) }}">
            @error('redemptionLimit.PER_USER')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div id="maxUsageLimitWrapper">
            <label for="redemptionLimit[MAX_USAGE]">Redemption Limit (Max Usage):</label>
            <input type="number" name="redemptionLimit[MAX_USAGE]" id="redemptionLimit[MAX_USAGE]" value="{{ old('redemptionLimit.MAX_USAGE', $discount->redemptionLimit['MAX_USAGE'] ?? 0) }}">
            @error('redemptionLimit.MAX_USAGE')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="redemptionLimit[max_disAmount]">Max Discount Amount:</label>
            <input type="number" name="redemptionLimit[max_disAmount]" id="redemptionLimit[max_disAmount]" value="{{ old('redemptionLimit.max_disAmount', $discount->redemptionLimit['max_disAmount'] ?? 0) }}">
            @error('redemptionLimit.max_disAmount')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">Save</button>
    </form>
</div>

<!-- JavaScript for Toggling the Fields -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const redemptionTypeSelect = document.getElementById('redemptionType');
        const perUserLimitWrapper = document.getElementById('perUserLimitWrapper');
        const maxUsageLimitWrapper = document.getElementById('maxUsageLimitWrapper');

        function toggleRedemptionLimits() {
            const selectedType = redemptionTypeSelect.value;
            if (selectedType === 'MAX_USAGE') {
                perUserLimitWrapper.style.display = 'none';
                maxUsageLimitWrapper.style.display = 'block';
            } else if (selectedType === 'PER_USER') {
                perUserLimitWrapper.style.display = 'block';
                maxUsageLimitWrapper.style.display = 'none';
            } else if (selectedType === 'BOTH') {
                perUserLimitWrapper.style.display = 'block';
                maxUsageLimitWrapper.style.display = 'block';
            }
        }

        // Call the function on page load to set the correct visibility based on the initial value
        toggleRedemptionLimits();

        // Listen for changes on the redemptionType select field
        redemptionTypeSelect.addEventListener('change', toggleRedemptionLimits);

         // Remove fields that are not visible before form submission
        document.querySelector('form').addEventListener('submit', function () {
            const selectedType = redemptionTypeSelect.value;
            if (selectedType === 'MAX_USAGE') {
                document.querySelector('input[name="redemptionLimit[PER_USER]"]').remove();
            } else if (selectedType === 'PER_USER') {
                document.querySelector('input[name="redemptionLimit[MAX_USAGE]"]').remove();
            }
        });
    });
</script>