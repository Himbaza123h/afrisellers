<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
public function index(Request $request)
{
    $query = Advertisement::where('user_id', auth()->id());

    if ($request->filled('status'))   $query->where('status', $request->status);
    if ($request->filled('position')) $query->where('position', $request->position);
    if ($request->filled('search'))   $query->where('title', 'like', '%'.$request->search.'%');

    $ads = $query->latest()->paginate(15)->withQueryString();

    $stats = [
        'total'    => Advertisement::where('user_id', auth()->id())->count(),
        'running'  => Advertisement::where('user_id', auth()->id())->where('status','running')->count(),
        'pending'  => Advertisement::where('user_id', auth()->id())->where('status','pending')->count(),
        'rejected' => Advertisement::where('user_id', auth()->id())->where('status','rejected')->count(),
    ];

    $positions = Advertisement::positions();

    // ── Can create more ads? ──────────────────────────────────────
    $canCreate  = true;
    $allowedAds = null;
    $activeSub  = \App\Models\Subscription::where('seller_id', auth()->id())
        ->where('status', 'active')
        ->with('plan.features')
        ->first();

    if ($activeSub && $activeSub->plan) {
        $feature = $activeSub->plan->features
            ->where('feature_key', 'allowed_ads')
            ->first();
        if ($feature) $allowedAds = (int) $feature->feature_value;
    }

    $trial = \App\Models\VendorTrial::where('user_id', auth()->id())
        ->where('is_active', true)
        ->where('ends_at', '>=', now())
        ->first();

    if (!$trial && $allowedAds !== null) {
        $currentCount = Advertisement::where('user_id', auth()->id())
            ->whereNotIn('status', ['rejected', 'expired'])
            ->count();
        $canCreate = $currentCount < $allowedAds;
    }

    return view('vendor.advertisements.index', compact('ads', 'stats', 'positions', 'canCreate', 'allowedAds'));
}

public function create()
{
    // ── Check limit before showing the form ───────────────────────
    $allowedAds = null;
    $activeSub  = \App\Models\Subscription::where('seller_id', auth()->id())
        ->where('status', 'active')
        ->with('plan.features')
        ->first();

    if ($activeSub && $activeSub->plan) {
        $feature = $activeSub->plan->features
            ->where('feature_key', 'allowed_ads')
            ->first();
        if ($feature) $allowedAds = (int) $feature->feature_value;
    }

    $trial = \App\Models\VendorTrial::where('user_id', auth()->id())
        ->where('is_active', true)
        ->where('ends_at', '>=', now())
        ->first();

    if (!$trial && $allowedAds !== null) {
        $currentCount = Advertisement::where('user_id', auth()->id())
            ->whereNotIn('status', ['rejected', 'expired'])
            ->count();

        if ($currentCount >= $allowedAds) {
            return redirect()->route('vendor.advertisements.index')
                ->with('error', "You have reached your plan's advertisement limit of {$allowedAds}. Please upgrade your package to add more ads. <a href='".route('vendor.subscriptions.index')."' class='underline font-semibold'>Click here to upgrade</a>");
        }
    }

    $positions = Advertisement::positions();
    $types     = Advertisement::types();
    return view('vendor.advertisements.create', compact('positions', 'types'));
}

public function store(Request $request)
{
    // ── Check allowed_ads limit from subscription ─────────────────
    $allowedAds = null;
    $activeSub  = \App\Models\Subscription::where('seller_id', auth()->id())
        ->where('status', 'active')
        ->with('plan.features')
        ->first();

    if ($activeSub && $activeSub->plan) {
        $feature = $activeSub->plan->features
            ->where('feature_key', 'allowed_ads')
            ->first();
        if ($feature) {
            $allowedAds = (int) $feature->feature_value;
        }
    }

    // Also check trial
    $trial = \App\Models\VendorTrial::where('user_id', auth()->id())
        ->where('is_active', true)
        ->where('ends_at', '>=', now())
        ->first();

    $currentCount = Advertisement::where('user_id', auth()->id())
        ->whereNotIn('status', ['rejected', 'expired'])
        ->count();

    \Illuminate\Support\Facades\Log::info('=== ADVERTISEMENT STORE — AD LIMIT CHECK ===', [
        'user_id'       => auth()->id(),
        'has_trial'     => $trial ? true : false,
        'trial_ends_at' => $trial?->ends_at,
        'plan_name'     => $activeSub?->plan?->name ?? 'none',
        'allowed_ads'   => $allowedAds ?? 'unlimited',
        'current_ads'   => $currentCount,
        'remaining'     => $allowedAds !== null ? max(0, $allowedAds - $currentCount) : 'unlimited',
    ]);

    if (!$trial && $allowedAds !== null) {
        if ($currentCount >= $allowedAds) {
            return redirect()->back()
                ->withInput()
                ->with('error', "You have reached your plan's advertisement limit of {$allowedAds}. Please upgrade your package to add more ads. <a href='".route('vendor.subscriptions.index')."' class='underline font-semibold'>Click here to upgrade</a>");
        }
    }

    $request->validate([
        'title'           => 'required|string|max:255',
        'type'            => 'required|in:image,gif,video,text',
        'position'        => 'required|in:'.implode(',', array_keys(Advertisement::positions())),
        'destination_url' => 'nullable|url',
        'headline'        => 'nullable|string|max:255',
        'sub_text'        => 'nullable|string|max:255',
        'badge_text'      => 'nullable|string|max:50',
        'accent_color'    => 'nullable|string|max:20',
        'bg_gradient'     => 'nullable|string|max:255',
        'overlay_color'   => 'nullable|string|max:50',
        'duration_days'   => 'required|integer|min:1|max:365',
        'start_date'      => 'nullable|date|after_or_equal:today',
        'media'           => 'required_unless:type,text|file|max:20480',
    ]);

    $data = $request->only([
        'title','type','position','destination_url',
        'headline','sub_text','badge_text',
        'accent_color','bg_gradient','overlay_color',
        'duration_days','start_date',
    ]);

    $data['user_id']   = auth()->id();
    $data['status']    = 'running';
    $data['end_date']  = $request->start_date
        ? now()->parse($request->start_date)->addDays((int) $request->duration_days)
        : now()->addDays((int) $request->duration_days);

    $data['width']  = match($request->position) {
        'homepage_right'   => 110,
        'homepage_sidebar' => 300,
        default            => 800,
    };
    $data['height'] = match($request->position) {
        'homepage_sidebar' => 250,
        'homepage_right'   => null,
        default            => 112,
    };

    if ($request->hasFile('media')) {
        $path = $request->file('media')->store('advertisements', 'public');
        $data['media_path'] = $path;
    }

    $ad = Advertisement::create($data);

    \Illuminate\Support\Facades\Log::info('=== ADVERTISEMENT CREATED ===', [
        'user_id'       => auth()->id(),
        'ad_id'         => $ad->id,
        'position'      => $ad->position,
        'status'        => $ad->status,
        'ads_after'     => $currentCount + 1,
        'allowed_ads'   => $allowedAds ?? 'unlimited',
        'remaining'     => $allowedAds !== null ? max(0, $allowedAds - ($currentCount + 1)) : 'unlimited',
    ]);

    return redirect()->route('vendor.advertisements.index')
        ->with('success', 'Advertisement is now live!');
}

public function show(Advertisement $advertisement)
{
    abort_if($advertisement->user_id !== auth()->id(), 403);
    return view('vendor.advertisements.show', compact('advertisement'));
}

public function edit(Advertisement $advertisement)
{
    abort_if($advertisement->user_id !== auth()->id(), 403);
        $positions = Advertisement::positions();
        $types     = Advertisement::types();
        return view('vendor.advertisements.edit', compact('advertisement', 'positions', 'types'));
    }

public function update(Request $request, Advertisement $advertisement)
{
    abort_if($advertisement->user_id !== auth()->id(), 403);

        $request->validate([
            'title'           => 'required|string|max:255',
            'destination_url' => 'nullable|url',
            'headline'        => 'nullable|string|max:255',
            'sub_text'        => 'nullable|string|max:255',
            'badge_text'      => 'nullable|string|max:50',
            'accent_color'    => 'nullable|string|max:20',
            'bg_gradient'     => 'nullable|string|max:255',
            'media'           => 'nullable|file|max:20480',
        ]);

        $data = $request->only([
            'title','destination_url','headline','sub_text',
            'badge_text','accent_color','bg_gradient','overlay_color',
        ]);

        // Reset to pending if editing after rejection
        if ($advertisement->status === 'rejected') $data['status'] = 'pending';

        if ($request->hasFile('media')) {
            if ($advertisement->media_path) {
                Storage::disk('public')->delete($advertisement->media_path);
            }
            $data['media_path'] = $request->file('media')->store('advertisements', 'public');
        }

        $advertisement->update($data);

        return redirect()->route('vendor.advertisements.index')
            ->with('success', 'Advertisement updated successfully!');
    }

public function destroy(Advertisement $advertisement)
{
    abort_if($advertisement->user_id !== auth()->id(), 403);

        if ($advertisement->media_path) {
            Storage::disk('public')->delete($advertisement->media_path);
        }

        $advertisement->delete();

        return redirect()->route('vendor.advertisements.index')
            ->with('success', 'Advertisement deleted successfully!');
    }
}
