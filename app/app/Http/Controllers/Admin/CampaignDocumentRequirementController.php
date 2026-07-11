<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignDocumentField;
use Illuminate\Support\Str;

class CampaignDocumentRequirementController extends Controller
{
    public function index()
    {
        $pageTitle = 'Campaign Document Requirements';
        $items = CampaignDocumentField::orderBy('sort_order')->orderBy('id')->get();
        $allCountries = function_exists('getAdminDefaultAllCountryNames') ? getAdminDefaultAllCountryNames() : [];

        return view('admin.campaign.document-requirements', compact('pageTitle', 'items', 'allCountries'));
    }

    public function store()
    {
        request()->validate([
            'label' => 'required|string|max:120',
            'field_key' => 'nullable|string|max:80|regex:/^[a-z0-9_]+$/',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_global' => 'nullable|boolean',
            'countries' => 'nullable|array',
            'countries.*' => 'string|max:120',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);

        $label = trim((string) request('label'));
        $fieldKey = trim((string) request('field_key'));
        if ($fieldKey === '') {
            $fieldKey = Str::snake(Str::slug($label, ' '));
        }
        $fieldKey = Str::limit($fieldKey, 80, '');

        if (CampaignDocumentField::where('field_key', $fieldKey)->exists()) {
            $toast[] = ['error', 'Field key already exists. Use a different key.'];

            return back()->withInput()->withToasts($toast);
        }

        $countries = collect((array) request('countries', []))
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $isGlobal = request()->boolean('is_global', true);

        CampaignDocumentField::create([
            'field_key' => $fieldKey,
            'label' => $label,
            'is_required' => request()->boolean('is_required'),
            'is_active' => request()->boolean('is_active', true),
            'is_global' => $isGlobal,
            'countries' => $isGlobal ? [] : $countries,
            'sort_order' => (int) request('sort_order', 0),
        ]);

        $toast[] = ['success', 'Document requirement added successfully'];

        return back()->withToasts($toast);
    }

    public function update(string $id)
    {
        request()->validate([
            'label' => 'required|string|max:120',
            'field_key' => 'required|string|max:80|regex:/^[a-z0-9_]+$/',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_global' => 'nullable|boolean',
            'countries' => 'nullable|array',
            'countries.*' => 'string|max:120',
            'sort_order' => 'nullable|integer|min:0|max:9999',
        ]);

        $row = CampaignDocumentField::findOrFail($id);
        $fieldKey = trim((string) request('field_key'));
        if (CampaignDocumentField::where('field_key', $fieldKey)->where('id', '!=', $row->id)->exists()) {
            $toast[] = ['error', 'Field key already exists. Use a different key.'];

            return back()->withToasts($toast);
        }

        $countries = collect((array) request('countries', []))
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values()
            ->all();
        $isGlobal = request()->boolean('is_global', true);

        $row->label = trim((string) request('label'));
        $row->field_key = $fieldKey;
        $row->is_required = request()->boolean('is_required');
        $row->is_active = request()->boolean('is_active');
        $row->is_global = $isGlobal;
        $row->countries = $isGlobal ? [] : $countries;
        $row->sort_order = (int) request('sort_order', 0);
        $row->save();

        $toast[] = ['success', 'Document requirement updated successfully'];

        return back()->withToasts($toast);
    }

    public function destroy(string $id)
    {
        CampaignDocumentField::where('id', $id)->delete();

        $toast[] = ['success', 'Document requirement deleted successfully'];

        return back()->withToasts($toast);
    }
}
