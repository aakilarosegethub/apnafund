@extends($activeTheme . 'layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Manage Rewards')</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Description')</th>
                                    <th>@lang('Minimum Amount')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rewards as $reward)
                                    <tr>
                                        <td>{{ $reward->title }}</td>
                                        <td>{{ strLimit($reward->description, 50) }}</td>
                                        <td>{{ $setting->cur_sym . showAmount($reward->minimum_amount) }}</td>
                                        <td>
                                            @if($reward->is_active)
                                                <span class="badge badge--success">@lang('Active')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Inactive')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('user.rewards.edit', [$campaign->id, $reward->id]) }}" class="btn btn-sm btn--primary">
                                                <i class="la la-pencil"></i> @lang('Edit')
                                            </a>
                                            <form action="{{ route('user.rewards.toggle.status', [$campaign->id, $reward->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn--{{ $reward->is_active ? 'warning' : 'success' }}">
                                                    {{ $reward->is_active ? __('Deactivate') : __('Activate') }}
                                                </button>
                                            </form>
                                            <form action="{{ route('user.rewards.destroy', [$campaign->id, $reward->id]) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn--danger" onclick="return confirm('@lang('Are you sure?')')">
                                                    @lang('Delete')
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">@lang('No rewards found')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 