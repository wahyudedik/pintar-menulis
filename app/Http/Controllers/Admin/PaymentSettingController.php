<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $manualBanks = PaymentSetting::manual()->get();
        $midtrans    = PaymentSetting::getMidtrans();
        $xendit      = PaymentSetting::getXendit();

        return view('admin.payment-settings', compact('manualBanks', 'midtrans', 'xendit'));
    }

    // ── Manual Transfer ───────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name'   => 'required|string|max:100',
            'qr_code'        => 'nullable|image|max:2048',
        ]);

        $qrPath = null;
        if ($request->hasFile('qr_code')) {
            $qrPath = $request->file('qr_code')->store('qr-codes', 'public');
        }

        PaymentSetting::create([
            'gateway_type'   => 'manual_transfer',
            'bank_name'      => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name'   => $validated['account_name'],
            'qr_code_path'   => $qrPath,
            'is_active'      => true,
            'is_enabled'     => true,
        ]);

        return back()->with('success', 'Rekening bank berhasil ditambahkan.');
    }

    public function update(Request $request, PaymentSetting $paymentSetting)
    {
        $validated = $request->validate([
            'bank_name'      => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name'   => 'required|string|max:100',
            'qr_code'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('qr_code')) {
            $validated['qr_code_path'] = $request->file('qr_code')->store('qr-codes', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $paymentSetting->update($validated);

        return back()->with('success', 'Rekening bank berhasil diupdate.');
    }

    public function destroy(PaymentSetting $paymentSetting)
    {
        $paymentSetting->delete();
        return back()->with('success', 'Rekening bank berhasil dihapus.');
    }

    // ── Midtrans ──────────────────────────────────────────────────────────────

    public function updateMidtrans(Request $request)
    {
        $request->validate([
            'midtrans_environment' => 'required|in:sandbox,production',
            'midtrans_merchant_id' => 'nullable|string|max:100',
            'midtrans_client_key'  => 'nullable|string|max:200',
            'midtrans_server_key'  => 'nullable|string|max:200',
        ]);

        $enabled = $request->boolean('midtrans_enabled');
        $config  = [
            'environment'  => $request->midtrans_environment,
            'merchant_id'  => $request->midtrans_merchant_id,
            'client_key'   => $request->midtrans_client_key,
            'server_key'   => $request->midtrans_server_key,
        ];

        PaymentSetting::updateOrCreate(
            ['gateway_type' => 'midtrans'],
            [
                'bank_name'      => 'Midtrans',
                'account_number' => '',
                'account_name'   => 'Midtrans Payment Gateway',
                'gateway_config' => $config,
                'is_active'      => true,
                'is_enabled'     => $enabled,
            ]
        );

        // Also sync to .env for SDK usage
        $this->updateEnvFile([
            'MIDTRANS_ENABLED'     => $enabled ? 'true' : 'false',
            'MIDTRANS_ENVIRONMENT' => $request->midtrans_environment,
            'MIDTRANS_MERCHANT_ID' => $request->midtrans_merchant_id ?? '',
            'MIDTRANS_CLIENT_KEY'  => $request->midtrans_client_key ?? '',
            'MIDTRANS_SERVER_KEY'  => $request->midtrans_server_key ?? '',
        ]);

        return back()->with('success', 'Konfigurasi Midtrans berhasil disimpan.');
    }

    // ── Xendit ────────────────────────────────────────────────────────────────

    public function updateXendit(Request $request)
    {
        $request->validate([
            'xendit_secret_key'   => 'nullable|string|max:200',
            'xendit_public_key'   => 'nullable|string|max:200',
            'xendit_webhook_token'=> 'nullable|string|max:200',
            'xendit_environment'  => 'required|in:sandbox,production',
        ]);

        $enabled = $request->boolean('xendit_enabled');
        $config  = [
            'environment'   => $request->xendit_environment,
            'secret_key'    => $request->xendit_secret_key,
            'public_key'    => $request->xendit_public_key,
            'webhook_token' => $request->xendit_webhook_token,
        ];

        PaymentSetting::updateOrCreate(
            ['gateway_type' => 'xendit'],
            [
                'bank_name'      => 'Xendit',
                'account_number' => '',
                'account_name'   => 'Xendit Payment Gateway',
                'gateway_config' => $config,
                'is_active'      => true,
                'is_enabled'     => $enabled,
            ]
        );

        $this->updateEnvFile([
            'XENDIT_ENABLED'        => $enabled ? 'true' : 'false',
            'XENDIT_ENVIRONMENT'    => $request->xendit_environment,
            'XENDIT_SECRET_KEY'     => $request->xendit_secret_key ?? '',
            'XENDIT_PUBLIC_KEY'     => $request->xendit_public_key ?? '',
            'XENDIT_WEBHOOK_TOKEN'  => $request->xendit_webhook_token ?? '',
        ]);

        return back()->with('success', 'Konfigurasi Xendit berhasil disimpan.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function updateEnvFile(array $data): void
    {
        $envFile    = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            // Wrap value in quotes if it contains spaces
            $safeValue = str_contains($value, ' ') ? "\"{$value}\"" : $value;

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$safeValue}", $envContent);
            } else {
                $envContent .= "\n{$key}={$safeValue}";
            }
        }

        file_put_contents($envFile, $envContent);
        \Artisan::call('config:clear');
    }
}
