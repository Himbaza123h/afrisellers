<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AdController extends Controller
{
    // ─── Check subscription access ───────────────────────────────

    private function getActiveSubscription()
    {
        return Subscription::where('seller_id', auth()->id())
            ->where('status', 'active')
            ->with('plan.features')
            ->first();
    }

    private function canRunAds(?Subscription $sub): bool
    {
        if (!$sub) return false;
        $val = strtolower($sub->plan->getFeature('has_ads', 'false'));
        return in_array($val, ['true', '1', 'yes']);
    }

    // ─── Index ───────────────────────────────────────────────────

    public function index(Request $request)
    {
        $subscription = $this->getActiveSubscription();
        $canAds       = $this->canRunAds($subscription);

        $query = Ad::forUser(auth()->id())->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('media_type')) {
            $query->where('media_type', $request->media_type);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $ads = $query->paginate(12)->withQueryString();

        $stats = [
            'total'       => Ad::forUser(auth()->id())->count(),
            'active'      => Ad::forUser(auth()->id())->where('status', 'active')->count(),
            'impressions' => Ad::forUser(auth()->id())->sum('impressions'),
            'clicks'      => Ad::forUser(auth()->id())->sum('clicks'),
        ];

        return view('vendor.ads.index', compact('ads', 'stats', 'subscription', 'canAds'));
    }

    // ─── Create ──────────────────────────────────────────────────

    public function create()
    {
        $subscription = $this->getActiveSubscription();
        if (!$this->canRunAds($subscription)) {
            return redirect()->route('vendor.ads.index')
                ->with('error', 'Your current plan does not include Ads. Please upgrade.');
        }

        return view('vendor.ads.form', compact('subscription'));
    }

    // ─── Store ───────────────────────────────────────────────────

    public function store(Request $request)
    {
        $subscription = $this->getActiveSubscription();
        if (!$this->canRunAds($subscription)) {
            return back()->with('error', 'Your current plan does not include Ads. Please upgrade.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'media_type'  => 'required|in:image,video',
            'media'       => [
                'required',
                'file',
                $request->media_type === 'image'
                    ? 'mimes:jpeg,jpg,png,gif,webp|max:2048'   // 2 MB
                    : 'mimes:mp4,mov,avi,webm|max:4096',       // 4 MB
            ],
            'target_url'  => 'nullable|url|max:500',
            'placement'   => 'required|in:homepage,sidebar,banner,popup,feed',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
            'status'      => 'required|in:draft,active',
        ]);

        try {
            $file = $request->file('media');
            $folder = $request->media_type === 'image' ? 'ads/images' : 'ads/videos';
            $path = $file->store($folder, 'public');

            $slug = Str::slug($validated['title']);
            $base = $slug;
            $i = 1;
            while (Ad::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $i++;
            }

            Ad::create([
                'user_id'             => auth()->id(),
                'subscription_id'     => $subscription->id,
                'title'               => $validated['title'],
                'slug'                => $slug,
                'description'         => $validated['description'],
                'media_type'          => $validated['media_type'],
                'media_path'          => $path,
                'media_original_name' => $file->getClientOriginalName(),
                'media_size'          => $file->getSize(),
                'target_url'          => $validated['target_url'],
                'placement'           => $validated['placement'],
                'status'              => $validated['status'],
                'starts_at'           => $validated['starts_at'],
                'ends_at'             => $validated['ends_at'],
            ]);

            return redirect()->route('vendor.ads.index')
                ->with('success', 'Ad "' . $validated['title'] . '" created successfully!');
        } catch (\Exception $e) {
            Log::error('Ad store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create ad. Please try again.');
        }
    }

    // ─── Show ────────────────────────────────────────────────────

    public function show(Ad $ad)
    {
        if ($ad->user_id !== auth()->id()) abort(403);
        $subscription = $this->getActiveSubscription();
        return view('vendor.ads.show', compact('ad', 'subscription'));
    }

    // ─── Edit ────────────────────────────────────────────────────

    public function edit(Ad $ad)
    {
        if ($ad->user_id !== auth()->id()) abort(403);

        $subscription = $this->getActiveSubscription();
        if (!$this->canRunAds($subscription)) {
            return redirect()->route('vendor.ads.index')
                ->with('error', 'Your current plan does not include Ads. Please upgrade.');
        }

        return view('vendor.ads.form', compact('ad', 'subscription'));
    }

    // ─── Update ──────────────────────────────────────────────────

    public function update(Request $request, Ad $ad)
    {
        if ($ad->user_id !== auth()->id()) abort(403);

        $subscription = $this->getActiveSubscription();
        if (!$this->canRunAds($subscription)) {
            return back()->with('error', 'Your current plan does not include Ads. Please upgrade.');
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'media'       => [
                'nullable',
                'file',
                $ad->media_type === 'image'
                    ? 'mimes:jpeg,jpg,png,gif,webp|max:2048'
                    : 'mimes:mp4,mov,avi,webm|max:4096',
            ],
            'target_url'  => 'nullable|url|max:500',
            'placement'   => 'required|in:homepage,sidebar,banner,popup,feed',
            'starts_at'   => 'nullable|date',
            'ends_at'     => 'nullable|date|after_or_equal:starts_at',
            'status'      => 'required|in:draft,active,paused',
        ]);

        try {
            $updateData = [
                'title'       => $validated['title'],
                'description' => $validated['description'],
                'target_url'  => $validated['target_url'],
                'placement'   => $validated['placement'],
                'status'      => $validated['status'],
                'starts_at'   => $validated['starts_at'],
                'ends_at'     => $validated['ends_at'],
            ];

            if ($request->hasFile('media')) {
                // Delete old file
                if ($ad->media_path && Storage::disk('public')->exists($ad->media_path)) {
                    Storage::disk('public')->delete($ad->media_path);
                }
                $file = $request->file('media');
                $folder = $ad->media_type === 'image' ? 'ads/images' : 'ads/videos';
                $updateData['media_path']          = $file->store($folder, 'public');
                $updateData['media_original_name'] = $file->getClientOriginalName();
                $updateData['media_size']          = $file->getSize();
            }

            $ad->update($updateData);

            return redirect()->route('vendor.ads.show', $ad)
                ->with('success', 'Ad updated successfully!');
        } catch (\Exception $e) {
            Log::error('Ad update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update ad. Please try again.');
        }
    }

    // ─── Destroy ─────────────────────────────────────────────────

    public function destroy(Ad $ad)
    {
        if ($ad->user_id !== auth()->id()) abort(403);

        try {
            if ($ad->media_path && Storage::disk('public')->exists($ad->media_path)) {
                Storage::disk('public')->delete($ad->media_path);
            }
            $ad->delete();
            return redirect()->route('vendor.ads.index')->with('success', 'Ad deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Ad delete error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete ad.');
        }
    }

    // ─── Toggle Status ───────────────────────────────────────────

    public function toggleStatus(Ad $ad)
    {
        if ($ad->user_id !== auth()->id()) abort(403);

        $subscription = $this->getActiveSubscription();
        if (!$this->canRunAds($subscription)) {
            return back()->with('error', 'Your current plan does not include Ads.');
        }

        $ad->status = $ad->status === 'active' ? 'paused' : 'active';
        $ad->save();

        return back()->with('success', 'Ad status updated to ' . $ad->status . '.');
    }
}
