<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Outlet;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Pengaturan bisnis
     */
    public function business()
    {
        // Resolve bisnis dari session, atau dari outlet aktif, atau dari business_id user
        $currentBusiness = Business::find(session('business_id'));

        if (!$currentBusiness) {
            $outlet = \App\Models\Outlet::find(session('outlet_id'));
            $currentBusiness = $outlet?->business;
            if ($currentBusiness) {
                session(['business_id' => $currentBusiness->id]);
            }
        }

        if (!$currentBusiness && auth()->user()->business_id) {
            $currentBusiness = auth()->user()->business;
            if ($currentBusiness) {
                session(['business_id' => $currentBusiness->id]);
            }
        }

        return view('settings.business', compact('currentBusiness'));
    }

    public function updateBusiness(Request $request)
    {
        $business = Business::find(session('business_id'));

        if (!$business) {
            return back()->with('error', 'Bisnis tidak ditemukan.');
        }

        $this->authorize('update', $business);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:retail,fnb,service',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'npwp' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:2048',
            'settings' => 'nullable|array',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('business/logos', 'public');
            $validated['logo'] = $path;
        }

        $business->update($validated);
        session(['business_id' => $business->id]);

        return back()->with('success', 'Pengaturan bisnis berhasil disimpan');
    }

    /**
     * Pengaturan outlet
     */
    public function outlet()
    {
        $user = auth()->user();
        $outlets = $user->outlets;
        $currentOutlet = Outlet::find(session('outlet_id'))
            ?? $user->outlets->first();

        return view('settings.outlet', compact('outlets', 'currentOutlet'));
    }

    public function updateOutlet(Request $request)
    {
        $outlet = \App\Models\Outlet::find(session('outlet_id'));

        if (!$outlet) {
            return back()->with('error', 'Outlet tidak ditemukan.');
        }

        $this->authorize('update', $outlet);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'tax_settings' => 'nullable|array',
            'receipt_settings' => 'nullable|array',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('outlet/logos', 'public');
            $validated['logo'] = $path;
        }

        $outlet->update($validated);
        session(['outlet_id' => $outlet->id]);

        return back()->with('success', 'Pengaturan outlet berhasil disimpan');
    }

    /**
     * Pengaturan receipt/struk
     */
    public function receipt()
    {
        $outlet = Outlet::find(session('outlet_id'));
        return view('settings.receipt', compact('outlet'));
    }

    public function updateReceipt(Request $request)
    {
        $outlet = \App\Models\Outlet::find(session('outlet_id'));

        if (!$outlet) {
            return back()->with('error', 'Outlet tidak ditemukan.');
        }

        $this->authorize('update', $outlet);

        $validated = $request->validate([
            'receipt_settings' => 'required|array',
            'receipt_settings.header' => 'nullable|string|max:500',
            'receipt_settings.footer' => 'nullable|string|max:500',
            'receipt_settings.paper_size' => 'required|in:58mm,80mm,A4',
            'receipt_settings.show_logo' => 'boolean',
            'receipt_settings.show_tax' => 'boolean',
            'receipt_settings.show_payment_method' => 'boolean',
        ]);

        $outlet->update(['receipt_settings' => $validated['receipt_settings']]);

        return back()->with('success', 'Pengaturan struk berhasil disimpan');
    }

    /**
     * Toggle fitur F&B
     */
    public function toggleFnb(Request $request)
    {
        $business = Business::find(session('business_id'));
        $settings = $business->settings ?? [];
        $settings['enable_fnb'] = $request->boolean('enable_fnb');
        $business->update(['settings' => $settings]);

        return back()->with('success', 'Pengaturan F&B berhasil diperbarui');
    }
}
