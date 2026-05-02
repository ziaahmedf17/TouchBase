<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;

class PaymentAccountController extends Controller
{
    public function index()
    {
        $accounts = PaymentAccount::latest()->get();
        return view('superadmin.payment-accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('superadmin.payment-accounts.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        PaymentAccount::create($data);
        return redirect()->route('superadmin.payment-accounts.index')
            ->with('success', 'Payment account added.');
    }

    public function edit(PaymentAccount $paymentAccount)
    {
        return view('superadmin.payment-accounts.edit', compact('paymentAccount'));
    }

    public function update(Request $request, PaymentAccount $paymentAccount)
    {
        $data = $this->validated($request);
        $paymentAccount->update($data);
        return redirect()->route('superadmin.payment-accounts.index')
            ->with('success', 'Payment account updated.');
    }

    public function destroy(PaymentAccount $paymentAccount)
    {
        $paymentAccount->delete();
        return redirect()->route('superadmin.payment-accounts.index')
            ->with('success', 'Payment account removed.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'          => 'required|string|max:100',
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:100',
            'iban'           => 'nullable|string|max:50',
            'instructions'   => 'nullable|string|max:500',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        return $data;
    }
}
