<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\ProductUserReview;
use Illuminate\Http\Request;

class CompanyShowController extends Controller
{
    // ─────────────────────────────────────────────
    //  Shared helper: load profile + ratings
    // ─────────────────────────────────────────────
    private function loadProfile(int $id): BusinessProfile
    {
        return BusinessProfile::where('id', $id)
            ->with([
                'user',
                'country',
                'user.products' => fn ($q) => $q
                    ->where('status', 'active')
                    ->where('is_admin_verified', true)
                    ->with('images')
                    ->latest()
                    ->limit(12),
            ])
            ->firstOrFail();
    }

    private function ratings(BusinessProfile $profile): array
    {
        $reviews = ProductUserReview::whereHas('product', fn ($q) => $q
                ->where('user_id', $profile->user_id)
                ->where('status', 'active')
                ->where('is_admin_verified', true))
            ->where('status', true)
            ->get();

        return [
            'avg'   => $reviews->count() ? round($reviews->avg('mark'), 1) : 0,
            'count' => $reviews->count(),
            'items' => $reviews->take(5),
        ];
    }

    // ─────────────────────────────────────────────
    //  1. Profile Overview  (default / landing tab)
    // ─────────────────────────────────────────────
    public function overview(int $id)
    {
        $profile = $this->loadProfile($id);
        $ratings = $this->ratings($profile);

        return view('frontend.company.tabs.overview', compact('profile', 'ratings'));
    }

    // ─────────────────────────────────────────────
    //  2. Company Info
    // ─────────────────────────────────────────────
    public function companyInfo(int $id)
    {
        $profile = $this->loadProfile($id);

        return view('frontend.company.tabs.company-info', compact('profile'));
    }

    // ─────────────────────────────────────────────
    //  3. Branding & Content
    // ─────────────────────────────────────────────
    public function branding(int $id)
    {
        $profile = $this->loadProfile($id);

        return view('frontend.company.tabs.branding', compact('profile'));
    }

    // ─────────────────────────────────────────────
    //  4. Contact Details
    // ─────────────────────────────────────────────
    public function contact(int $id)
    {
        $profile = $this->loadProfile($id);

        return view('frontend.company.tabs.contact', compact('profile'));
    }

    // ─────────────────────────────────────────────
    //  5. Social Media
    // ─────────────────────────────────────────────
    public function social(int $id)
    {
        $profile = $this->loadProfile($id);

        return view('frontend.company.tabs.social', compact('profile'));
    }

    // ─────────────────────────────────────────────
    //  6. Business Type
    // ─────────────────────────────────────────────
    public function businessType(int $id)
    {
        $profile = $this->loadProfile($id);

        return view('frontend.company.tabs.business-type', compact('profile'));
    }

    // ─────────────────────────────────────────────
    //  7. Operations
    // ─────────────────────────────────────────────
    public function operations(int $id)
    {
        $profile = $this->loadProfile($id);

        return view('frontend.company.tabs.operations', compact('profile'));
    }
}
