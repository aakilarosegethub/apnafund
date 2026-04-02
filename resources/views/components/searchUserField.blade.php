@props(['donorUsers' => []])

<div class="input--group">
    <select class="form--control form--control--sm" name="user_id">
        <option value="">@lang('All Donors')</option>
        @foreach($donorUsers as $u)
            <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>
                {{ '@' . $u->username }}@if($u->email) — {{ $u->email }} @endif
            </option>
        @endforeach
    </select>
    <button type="submit" class="btn btn--sm btn--icon btn--base">
        <i class="ti ti-search"></i>
    </button>
</div>
