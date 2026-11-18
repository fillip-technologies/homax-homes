<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobOpening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/** Applications sent from /careers, and Admin > Careers > Job Applications. */
class JobApplicationController extends Controller
{
    /** Uploaded CV / cover letter: PDF or Word, up to 2 MB. */
    private const FILE_RULE = 'file|mimes:pdf,doc,docx|max:2048';
    private const DIR = 'job-applications';

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            // Empty means a general application; otherwise a live opening.
            'position' => ['nullable', 'integer', Rule::exists('job_openings', 'id')->where('is_active', true)],
            'resume' => 'required|' . self::FILE_RULE,
            'cover_letter' => 'nullable|' . self::FILE_RULE,
            'agree' => 'accepted',
        ] + $this->captchaRules(), [
            'resume.mimes' => 'The resume must be a PDF or Word (.doc, .docx) file.',
            'cover_letter.mimes' => 'The cover letter must be a PDF or Word (.doc, .docx) file.',
            'resume.max' => 'The resume must be 2 MB or smaller.',
            'cover_letter.max' => 'The cover letter must be 2 MB or smaller.',
            'resume.uploaded' => 'The resume could not be uploaded. Please try a smaller file (2 MB max).',
            'cover_letter.uploaded' => 'The cover letter could not be uploaded. Please try a smaller file (2 MB max).',
        ]);

        $job = !empty($validated['position']) ? JobOpening::find($validated['position']) : null;

        JobApplication::create([
            'job_opening_id' => $job?->id,
            'job_title' => $job?->title,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            // Private disk (storage/app/private): only reachable through the admin download route.
            'resume_path' => $request->file('resume')->store(self::DIR, 'local'),
            'cover_letter_path' => $request->file('cover_letter')?->store(self::DIR, 'local'),
            'terms_accepted' => true,
        ]);

        return redirect()->back()->with('success', 'Thanks for applying! Our team will get back to you soon.');
    }

    /** Admin list. ?job=<opening id> for one opening, ?job=general for general applications. */
    public function index(Request $request)
    {
        $filter = (string) $request->query('job', '');

        $applications = JobApplication::with('jobOpening')
            ->when($filter === 'general', fn ($q) => $q->whereNull('job_title'))
            ->when(ctype_digit($filter), fn ($q) => $q->where('job_opening_id', (int) $filter))
            ->latest()
            ->get();

        return view('admin.careers.applications', [
            'applications' => $applications,
            'openings' => JobOpening::withCount('applications')->orderBy('sort_order')->orderBy('id')->get(),
            'generalCount' => JobApplication::whereNull('job_title')->count(),
            'totalCount' => JobApplication::count(),
            'filter' => $filter,
        ]);
    }

    public function download(JobApplication $application, string $file)
    {
        $column = JobApplication::FILES[$file] ?? abort(404);
        $path = $application->$column;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        $name = Str::slug($application->name ?: 'applicant') . '-' . $file . '.' . pathinfo($path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download($path, $name);
    }

    public function destroy(JobApplication $application)
    {
        $application->delete();

        return back()->with('success', 'Application deleted.');
    }

    /** reCAPTCHA is enforced only when keys are configured, so local setups still work. */
    private function captchaRules(): array
    {
        return config('services.recaptcha.secret_key')
            ? ['g-recaptcha-response' => 'required|recaptcha']
            : [];
    }
}
