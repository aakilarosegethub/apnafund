@php
    $documentFilename = normalizeCampaignVerificationDocumentFilename($filename ?? null);
    $documentUrl = $documentFilename
        ? campaignVerificationDocumentUrl($campaignId, $documentFilename, $forAdmin ?? false)
        : null;
@endphp

@if($documentUrl)
    <a href="{{ $documentUrl }}" target="_blank" rel="noopener" @if(!empty($linkClass)) class="{{ $linkClass }}" @endif>{{ $label ?? __('View current file') }}</a>
@endif
