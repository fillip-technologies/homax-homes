<?php

namespace App\Http\Controllers;

use App\Models\JobOpening;
use Illuminate\Http\Request;

/** Public /careers pages. Applications are stored by PropertyInquiryController::storeJoinUs. */
class CareerController extends Controller
{
    public function index(Request $request)
    {
        $all = JobOpening::visible()->get();

        // Old "?position=<id>" apply links: send them to that opening's own form.
        if ($job = $all->firstWhere('id', (int) $request->query('position'))) {
            return redirect()->to($job->url . '#apply');
        }

        $department = (string) $request->query('department', '');
        $type = (string) $request->query('type', '');

        $openings = $all
            ->when($department !== '', fn ($c) => $c->where('department', $department))
            ->when($type !== '', fn ($c) => $c->where('employment_type', $type))
            ->values();

        return view('pages.careers.index', [
            'openings' => $openings,
            'allOpenings' => $all,
            'departments' => $all->pluck('department')->filter()->unique()->sort()->values(),
            'types' => array_intersect_key(JobOpening::TYPES, $all->pluck('employment_type')->flip()->all()),
            'department' => $department,
            'type' => $type,
        ]);
    }

    public function show(string $slug)
    {
        $opening = JobOpening::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('pages.careers.show', [
            'opening' => $opening,
            'others' => JobOpening::visible()->where('id', '!=', $opening->id)->limit(3)->get(),
        ]);
    }
}
