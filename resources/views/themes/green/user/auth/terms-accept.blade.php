@extends($activeTheme . 'layouts.green-home')

@section('content')
<div class="ks-signup-wrapper">
    <div class="ks-signup-card">
        <h1 class="ks-title">Accept Terms of Use</h1>
        <p class="ks-policy mb-3">Before continuing, please confirm that you agree to our policies.</p>

        <form method="POST" action="{{ route('user.terms.accept') }}">
            @csrf
            <div class="ks-checkbox">
                <input type="checkbox" name="agree" id="agree" value="1" required>
                <label for="agree">
                    I agree to the
                    <a href="{{ route('cookie.policy') }}" target="_blank">Cookie Policy</a>,
                    <a href="{{ route('policy.pages', ['privacy-policy', 11]) }}" target="_blank">Privacy Policy</a>
                    @if($policyPages && count($policyPages))
                        @foreach ($policyPages as $policy)
                            @php
                                $policyId = is_array($policy) ? ($policy['id'] ?? null) : $policy->id;
                                $policyDataInfo = is_array($policy)
                                    ? ($policy['data_info'] ?? [])
                                    : (is_array($policy->data_info) ? $policy->data_info : (array) $policy->data_info);
                                $policyTitle = $policyDataInfo['title'] ?? 'Terms of Use';
                            @endphp
                            and <a href="{{ route('policy.pages', [slug($policyTitle), $policyId]) }}" target="_blank">{{ __($policyTitle) }}</a>
                        @endforeach
                    @else
                        and <a href="{{ url('policy/terms-of-use/12') }}" target="_blank">Terms of Use</a>
                    @endif
                </label>
            </div>
            @error('agree')
                <div class="ks-error">{{ $message }}</div>
            @enderror
            <button type="submit" class="ks-submit mt-3">Continue to dashboard</button>
        </form>
    </div>
</div>
@endsection
