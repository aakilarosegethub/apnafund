@extends($activeTheme . 'layouts.frontend')

@section('frontend')
    <div class="dashboard py-60">
        <div class="container">
            <div class="card custom--card">
                <div class="card-body">
                    <form action="" method="GET">
                        <div class="row gy-3 align-items-end mb-4">
                            <div class="col-xl-6 col-lg-5 col-sm-6 col-xsm-6">
                                <label class="form--label">@lang('Transaction Number')</label>
                                <input type="text" class="form--control" name="search" value="{{ request('search') }}">
                            </div>
                            <div class="col-xl-4 col-lg-4 col-sm-6 col-xsm-6">
                                <label class="form--label">@lang('Remark')</label>
                                <select class="form--control form-select" name="remark">
                                    <option value="">@lang('Any')</option>

                                    @foreach ($remarks as $remark)
                                        <option value="{{ $remark->remark }}" @selected(request('remark') == $remark->remark)>
                                            {{ __(keyToTitle($remark->remark)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-3">
                                <button type="submit" class="btn btn--base w-100">@lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-striped table-borderless table--responsive--xl">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Trx')</th>
                                <th>@lang('Transacted')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Post Balance')</th>
                                <th>@lang('Details')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td>
                                        {{ @$transactions->firstItem() + $loop->index }}
                                    </td>
                                    <td>{{ @$transaction->trx }}</td>
                                    <td>
                                        <span>
                                            <span class="d-block">{{ showDateTime(@$transaction->created_at) }}</span>
                                            <span class="d-block">{{ diffForHumans(@$transaction->created_at) }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="@if ($transaction->trx_type == '+') text--success @else text--danger @endif">
                                            {{ showAmount(@$transaction->amount) . ' ' . __($setting->site_cur) }}
                                        </span>
                                    </td>
                                    <td>{{ showAmount(@$transaction->post_balance) . ' ' . __($setting->site_cur) }}</td>
                                    <td>{{ __(@$transaction->details) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">
                                        {{ __($emptyMessage) }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($transactions->hasPages())
                        {{ $transactions->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
