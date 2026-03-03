<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $settings = PaymentSetting::all();
        return view('admin.payment-settings', compact('settings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'qr_code' => 'nullable|image|max:2048',
        ]);

        $qrPath = null;
        if ($request->hasFile('qr_code')) {
            $qrPath = $request->file('qr_code')->store('qr-codes', 'public');
        }

        PaymentSetting::create([
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_name' => $validated['account_name'],
            'qr_code_path' => $qrPath,
            'is_active' => true,
        ]);

        return back()->with('success', 'Payment setting berhasil ditambahkan');
    }

    public function update(Request $request, PaymentSetting $setting)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'qr_code' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('qr_code')) {
            $qrPath = $request->file('qr_code')->store('qr-codes', 'public');
            $validated['qr_code_path'] = $qrPath;
        }

        $setting->update($validated);

        return back()->with('success', 'Payment setting berhasil diupdate');
    }

    public function destroy(PaymentSetting $setting)
    {
        $setting->delete();
        return back()->with('success', 'Payment setting berhasil dihapus');
    }

    public function updateMidtrans(Request $request)
    {
        $validated = $request->validate([
            'midtrans_enabled' => 'nullable|boolean',
            'midtrans_environment' => 'required|in:sandbox,production',
            'midtrans_merchant_id' => 'nullable|string',
            'midtrans_client_key' => 'nullable|string',
            'midtrans_server_key' => 'nullable|string',
        ]);

        // Update .env file
        $this->updateEnvFile([
            'MIDTRANS_ENABLED' => $request->has('midtrans_enabled') ? 'true' : 'false',
            'MIDTRANS_ENVIRONMENT' => $validated['midtrans_environment'],
            'MIDTRANS_MERCHANT_ID' => $validated['midtrans_merchant_id'] ?? '',
            'MIDTRANS_CLIENT_KEY' => $validated['midtrans_client_key'] ?? '',
            'MIDTRANS_SERVER_KEY' => $validated['midtrans_server_key'] ?? '',
        ]);

        return back()->with('success', 'Midtrans configuration berhasil diupdate! Aplikasi akan restart.');
    }

    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            // Check if key exists
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                // Add new key
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envFile, $envContent);

        // Clear config cache
        \Artisan::call('config:clear');
    }
}
