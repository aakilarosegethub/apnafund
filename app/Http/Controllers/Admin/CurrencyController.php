<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Http;

class CurrencyController extends Controller
{
    private function updateRates(): array
    {
        $response = Http::timeout(10)->get('https://open.er-api.com/v6/latest/' . CurrencyService::BASE_CURRENCY);

        if (!$response->ok()) {
            return [false, 'Failed to fetch exchange rates'];
        }

        if ($response->json('result') !== 'success') {
            return [false, 'Exchange rate API returned an error'];
        }

        $rates = $response->json('rates');
        if (!is_array($rates) || empty($rates)) {
            return [false, 'No exchange rate data available'];
        }

        foreach ($rates as $code => $rateFromUsd) {
            $rateFromUsd = (float) $rateFromUsd;
            if ($rateFromUsd <= 0) {
                continue;
            }

            $rateToUsd = strtoupper($code) === CurrencyService::BASE_CURRENCY ? 1 : (1 / $rateFromUsd);

            Currency::updateOrCreate(
                ['code' => strtoupper($code)],
                ['rate_to_usd' => $rateToUsd, 'source' => 'api']
            );
        }

        return [true, 'Exchange rates updated successfully'];
    }

    public function index()
    {
        $pageTitle = 'Currencies';
        $query = Currency::query();

        if ($search = trim((string) request('search'))) {
            $query->where('code', 'like', '%' . strtoupper($search) . '%');
        }

        if ($source = trim((string) request('source'))) {
            $query->where('source', $source);
        }

        $currencies = $query->orderBy('code')->paginate(getPaginate());

        return view('admin.page.currencies', compact('pageTitle', 'currencies'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rate_to_usd' => 'required|numeric|gt:0',
        ]);

        $currency = Currency::findOrFail($id);
        $currency->rate_to_usd = $request->rate_to_usd;
        $currency->source = 'manual';
        $currency->save();

        $toast[] = ['success', 'Currency rate updated successfully'];

        return back()->withToasts($toast);
    }

    public function syncRates()
    {
        [$ok, $message] = $this->updateRates();

        $toast[] = [$ok ? 'success' : 'error', $message];

        return back()->withToasts($toast);
    }

    public function syncRatesLink()
    {
        return $this->syncRates();
    }

    public function syncRatesPublic()
    {
        [$ok, $message] = $this->updateRates();

        return response()->json(['status' => $ok ? 'success' : 'error', 'message' => $message], $ok ? 200 : 500);
    }

    public function store(Request $request, CurrencyService $currencyService)
    {
        $request->validate([
            'country' => 'required|string|max:100',
            'rate_to_usd' => 'required|numeric|gt:0',
        ]);

        $currencyCode = $currencyService->resolveCurrencyCodeFromCountry($request->country);
        if (!$currencyCode) {
            $toast[] = ['error', 'Unable to detect a currency code for the provided country'];
            return back()->withToasts($toast);
        }

        $currency = Currency::firstOrNew(['code' => $currencyCode]);
        $wasCreated = !$currency->exists;

        $currency->rate_to_usd = $request->rate_to_usd;
        $currency->source = 'manual';
        $currency->save();

        $toast[] = ['success', $wasCreated ? 'Currency added successfully' : 'Currency updated successfully'];

        return back()->withToasts($toast);
    }
}
