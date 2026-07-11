<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutBank;
use Illuminate\Http\Request;

class PayoutBankController extends Controller
{
    public function index()
    {
        $pageTitle = 'Payout Banks';
        $query = PayoutBank::query();

        $payoutBanks = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(getPaginate());

        return view('admin.page.payout_banks', compact('pageTitle', 'payoutBanks'));
    }

    public function store(Request $request, $id = 0)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payout_banks,code,'.$id,
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($id) {
            $payoutBank = PayoutBank::findOrFail($id);
            $message = 'Payout bank successfully updated';
        } else {
            $payoutBank = new PayoutBank;
            $message = 'Payout bank successfully added';
        }

        $payoutBank->name = $request->input('name');
        $payoutBank->code = $request->input('code');
        $payoutBank->description = $request->input('description');
        $payoutBank->sort_order = $request->input('sort_order', 0);
        $payoutBank->save();

        $toast[] = ['success', $message];

        return back()->withToasts($toast);
    }

    public function status($id)
    {
        return PayoutBank::changeStatus($id);
    }

    public function delete($id)
    {
        $payoutBank = PayoutBank::findOrFail($id);

        // Check if any campaign is using this bank
        if ($payoutBank->campaigns()->count() > 0) {
            $toast[] = ['error', 'Cannot delete payout bank that is being used by campaigns'];

            return back()->withToasts($toast);
        }

        $payoutBank->delete();
        $toast[] = ['success', 'Payout bank successfully deleted'];

        return back()->withToasts($toast);
    }
}
