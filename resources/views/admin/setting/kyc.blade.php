@extends('admin.layouts.master')

@section('master')
    <form action="" method="POST">
        @csrf

        @include('admin.partials.formData', [$formHeading])

        <div class="col-12 mt-4">
            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn--base px-4">@lang('Submit')</button>
            </div>
        </div>
    </form>

    <x-formGenerator/>
@endsection
