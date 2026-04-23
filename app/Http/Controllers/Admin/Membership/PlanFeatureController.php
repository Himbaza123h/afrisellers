<?php

namespace App\Http\Controllers\Admin\Membership;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\MembershipPlan;
use App\Models\PlanFeature;
use App\Support\MembershipPlanFeatureValueRules;
use Illuminate\Http\Request;

class PlanFeatureController extends Controller
{
    public function index(MembershipPlan $membershipPlan)
    {
        $features = $membershipPlan->features()
            ->with('feature')
            ->orderByDesc('created_at')
            ->orderBy(
                Feature::query()->select('feature_key')->whereColumn('features.id', 'plan_features.feature_id')
            )
            ->paginate(15);

        $planFeaturesByFeatureId = $membershipPlan->features()->get()->keyBy('feature_id');

        $catalogFeatures = Feature::query()
            ->active()
            ->where('is_supported', true)
            ->leftJoin('plan_features', function ($join) use ($membershipPlan) {
                $join->on('plan_features.feature_id', '=', 'features.id')
                    ->where('plan_features.plan_id', '=', $membershipPlan->id);
            })
            ->select('features.*')
            ->orderByRaw('CASE WHEN plan_features.id IS NOT NULL THEN 0 ELSE 1 END')
            ->orderBy('features.name')
            ->get();

        return view('admin.membership.features.index', compact(
            'membershipPlan',
            'features',
            'catalogFeatures',
            'planFeaturesByFeatureId',
        ));
    }

    public function store(Request $request, MembershipPlan $membershipPlan)
    {
        $allowedIds = Feature::query()
            ->active()
            ->where('is_supported', true)
            ->whereNotIn('id', $membershipPlan->features()->pluck('feature_id'))
            ->pluck('id')
            ->all();

        $selected = array_values(array_unique(array_map('intval', $request->input('selected', []))));
        $selected = array_values(array_intersect($selected, $allowedIds));

        if ($selected === []) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Select at least one feature and enter a value for each.',
                    'errors' => ['selected' => ['Select at least one feature and enter a value for each.']],
                ], 422);
            }

            return back()
                ->withErrors(['selected' => 'Select at least one feature and enter a value for each.'])
                ->withInput();
        }

        $values = $request->input('values', []);

        $featureRows = Feature::query()
            ->whereIn('id', $selected)
            ->get()
            ->keyBy('id');

        $valuesToSave = [];
        foreach ($selected as $featureId) {
            $feature = $featureRows->get($featureId);
            if (! $feature) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Invalid feature selection.',
                        'errors' => ['selected' => ['Invalid feature selection.']],
                    ], 422);
                }

                return back()->withErrors(['selected' => 'Invalid feature selection.'])->withInput();
            }

            $raw = $values[$featureId] ?? $values[(string) $featureId] ?? null;
            $result = MembershipPlanFeatureValueRules::validateForNewAssignment($feature, $raw);

            if (! $result['ok']) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => $result['message'] ?? 'Invalid value.',
                        'errors' => ['values.'.$featureId => [$result['message'] ?? 'Invalid value.']],
                    ], 422);
                }

                return back()
                    ->withErrors(['values.'.$featureId => $result['message'] ?? 'Invalid value.'])
                    ->withInput();
            }

            $valuesToSave[$featureId] = $result['value'];
        }

        $createdPayload = [];
        foreach ($valuesToSave as $featureId => $savedValue) {
            $planFeature = $membershipPlan->features()->create([
                'feature_id' => $featureId,
                'feature_value' => $savedValue,
            ]);
            $planFeature->load('feature');
            $createdPayload[] = [
                'id' => $planFeature->id,
                'feature_id' => $planFeature->feature_id,
                'feature_value' => $planFeature->feature_value,
                'feature_key' => $planFeature->feature?->feature_key ?? '',
                'resolved_value_type' => $planFeature->feature?->resolvedValueType() ?? 'text',
                'created_at_display' => $planFeature->created_at->format('M d, Y'),
            ];
        }

        $count = count($createdPayload);
        $message = $count === 1
            ? 'Feature added successfully!'
            : "{$count} features added successfully.";

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'plan_features' => $createdPayload,
            ]);
        }

        return back()->with('success', $message);
    }

    public function update(Request $request, PlanFeature $planFeature)
    {
        $planFeature->load('feature');
        $feature = $planFeature->feature;
        if (! $feature) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Feature definition is missing.',
                    'errors' => ['feature_value' => ['Feature definition is missing.']],
                ], 422);
            }

            return back()->withErrors(['feature_value' => 'Feature definition is missing.']);
        }

        $result = MembershipPlanFeatureValueRules::validateForUpdate($feature, $request->input('feature_value'));
        if (! $result['ok']) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $result['message'] ?? 'Invalid value.',
                    'errors' => [
                        'feature_value' => [$result['message'] ?? 'Invalid value.'],
                    ],
                ], 422);
            }

            return back()
                ->withErrors(['feature_value' => $result['message'] ?? 'Invalid value.'])
                ->withInput();
        }

        $planFeature->update(['feature_value' => $result['value']]);
        $message = 'Feature updated successfully!';

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'feature_value' => $result['value'],
            ]);
        }

        return back()->with('success', $message);
    }

    public function destroy(Request $request, PlanFeature $planFeature)
    {
        $catalogFeatureId = $planFeature->feature_id;
        $planFeature->delete();
        $message = 'Feature deleted successfully!';

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'feature_id' => $catalogFeatureId,
            ]);
        }

        return back()->with('success', $message);
    }
}
