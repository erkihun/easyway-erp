<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateBrandingSettingsRequest;
use App\Http\Requests\Settings\UpdateGeneralSettingsRequest;
use App\Models\Currency;
use App\Services\SystemSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(private readonly SystemSettingsService $settingsService)
    {
    }

    public function index(): View
    {
        $this->settingsService->normalizeStoredBrandingPaths();
        $settings = $this->settingsService->getUiPayload();

        $currencies = Currency::query()->orderBy('code')->pluck('code')->all();
        if ($currencies === []) {
            $currencies = ['ETB', 'USD', 'EUR', 'GBP'];
        }

        return view('admin.settings.index', [
            'settings' => $settings,
            'currencies' => $currencies,
            'timezones' => timezone_identifiers_list(),
            'dateFormats' => ['Y-m-d', 'd-m-Y', 'm/d/Y'],
        ]);
    }

    public function updateGeneral(UpdateGeneralSettingsRequest $request): RedirectResponse
    {
        $this->settingsService->updateGeneral($request->validated());

        return redirect()->route('admin.settings.index')->with('status', __('messages.general_settings_saved'));
    }

    public function updateBranding(UpdateBrandingSettingsRequest $request): RedirectResponse
    {
        $this->settingsService->updateBranding([
            'system_logo' => $request->file('system_logo'),
            'system_favicon' => $request->file('system_favicon'),
        ]);

        return redirect()->route('admin.settings.index')->with('status', __('messages.branding_settings_saved'));
    }
}


