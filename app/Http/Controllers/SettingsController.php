<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Kunci pengaturan beserta nilai default sistem.
     *
     * @var array<string, int|string>
     */
    private const DEFAULTS = [
        'maksimal_hari_pinjam' => 7,
        'maksimal_buku_anggota' => 3,
        'hari_pengingat' => 3,
        'nama_perpustakaan' => 'Perpustakaan Digital',
        'alamat_perpustakaan' => '',
        'email_perpustakaan' => '',
        'telepon_perpustakaan' => '',
        'maksimal_perpanjangan' => 2,
        'durasi_perpanjangan' => 7,
    ];

    /**
     * Menampilkan halaman pengaturan sistem.
     */
    public function index(): View
    {
        $settings = [];

        foreach (array_keys(self::DEFAULTS) as $key) {
            $settings[$key] = SystemSetting::get($key, self::DEFAULTS[$key]);
        }

        return view('settings.index', [
            'settings' => $settings,
            'defaults' => self::DEFAULTS,
        ]);
    }

    /**
     * Menyimpan pengaturan sistem.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        foreach (self::DEFAULTS as $key => $default) {
            SystemSetting::set($key, $request->input($key, $default));
        }

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
