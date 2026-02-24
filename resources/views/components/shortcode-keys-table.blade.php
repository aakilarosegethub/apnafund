@props(['items' => []])

@php
    $defaultItems = [
        ['label' => 'Background Image', 'name' => 'bg_img'],
        ['label' => 'Top Heading', 'name' => 'top_heading'],
        ['label' => 'Main Heading', 'name' => 'main_head'],
        ['label' => 'Paragraph', 'name' => 'detail'],
    ];
    $rows = !empty($items) ? $items : $defaultItems;
@endphp

<div class="shortcode-keys-table-wrapper">
    <div class="table-responsive">
        <table class="table table--shortcode-keys mb-0">
            <thead>
                <tr>
                    <th class="text-uppercase fw-bold">@lang('Label')</th>
                    <th class="text-uppercase fw-bold">@lang('Name')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $index => $row)
                    @php
                        $label = is_array($row) ? ($row['label'] ?? $row[0] ?? '') : $row->label ?? '';
                        $name = is_array($row) ? ($row['name'] ?? $row[1] ?? '') : $row->name ?? '';
                    @endphp
                    <tr class="shortcode-key-row">
                        <td class="fw-semibold">{{ __($label) }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <code class="shortcode-key-name">{{ $name }}</code>
                                <button type="button" class="btn btn--sm btn--icon btn-outline--base shortcode-copy-btn" 
                                        data-copy="{{ $name }}" title="@lang('Copy')">
                                    <i class="las la-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.shortcode-keys-table-wrapper { border-radius: 12px; overflow: hidden; border: 1px solid rgba(0,0,0,.08); }
.table--shortcode-keys { margin: 0 !important; }
.table--shortcode-keys thead tr { background: linear-gradient(135deg, #05ce78 0%, #04b367 100%); color: #fff; }
.table--shortcode-keys thead th { padding: 14px 20px; font-size: 0.8rem; letter-spacing: 0.5px; border: none; }
.table--shortcode-keys tbody tr { transition: background .15s; }
.table--shortcode-keys tbody tr:nth-child(even) { background: #f8fafb; }
.table--shortcode-keys tbody tr:hover { background: rgba(5, 206, 120, 0.06) !important; }
.table--shortcode-keys tbody td { padding: 14px 20px; vertical-align: middle; border-color: rgba(0,0,0,.06); }
.table--shortcode-keys .shortcode-key-name { 
    flex: 1; min-width: 0; padding: 8px 14px; font-size: 0.9rem; 
    background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; 
    color: #334155; display: block;
}
.table--shortcode-keys .shortcode-copy-btn { flex-shrink: 0; padding: 8px 10px; border-radius: 8px; }
.table--shortcode-keys .shortcode-copy-btn:hover { background: rgba(5, 206, 120, 0.1); border-color: #05ce78; color: #05ce78; }
.table--shortcode-keys .shortcode-copy-btn.copied { background: #05ce78; border-color: #05ce78; color: #fff; }
</style>

<script>
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.shortcode-copy-btn');
    if (!btn) return;
    var text = btn.dataset.copy;
    if (!text) return;
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            var icon = btn.querySelector('i');
            var orig = icon ? icon.className : '';
            btn.classList.add('copied');
            if (icon) icon.className = 'las la-check';
            setTimeout(function() { btn.classList.remove('copied'); if (icon) icon.className = orig; }, 1500);
        });
    }
});
</script>
