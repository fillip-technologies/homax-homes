<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyInquiry;
use App\Models\Property;
use App\Models\JobOpening;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PropertyInquiryController extends Controller
{

    public function inquiryForm()
    {
        $inquiries = PropertyInquiry::with(['property', 'jobOpening'])->latest()->get();
        return view('admin.enquiryformlist', compact('inquiries'));
    }

    public function enquiryForm()
    {
        return $this->inquiryForm();
    }

    /**
     * Store a general "Contact Us" submission. Reuses the property_inquiries
     * table rather than a dedicated one - property_id has no foreign key
     * constraint, so 0 is used as a "no property" sentinel. The admin list's
     * `@if($inquiry->property_id)` check already treats 0 as falsy, so these
     * rows show "N/A" instead of a broken "View Property" link, with no
     * schema change needed.
     */
    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:5000',
        ] + $this->captchaRules());

        $this->storeGeneralLead('general', 'Contact', $validated['name'], $validated['email'], $validated['phone'] ?? null, $validated['message']);

        return redirect()->back()->with('success', 'Thanks for reaching out! We\'ll get back to you soon.');
    }

    /** Uploaded CV / cover letter: PDF or Word, up to 2 MB. */
    private const APPLICATION_FILE_RULE = 'file|mimes:pdf,doc,docx|max:2048';
    private const APPLICATION_DIR = 'job-applications';

    /** Job application from /careers (or a /careers/{slug} opening page). */
    public function storeJoinUs(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            // Empty means a general application; otherwise a live opening.
            'position' => ['nullable', 'integer', Rule::exists('job_openings', 'id')->where('is_active', true)],
            'resume' => 'required|' . self::APPLICATION_FILE_RULE,
            'cover_letter' => 'nullable|' . self::APPLICATION_FILE_RULE,
            'agree' => 'accepted',
        ], [
            'resume.mimes' => 'The resume must be a PDF or Word (.doc, .docx) file.',
            'cover_letter.mimes' => 'The cover letter must be a PDF or Word (.doc, .docx) file.',
            'resume.max' => 'The resume must be 2 MB or smaller.',
            'cover_letter.max' => 'The cover letter must be 2 MB or smaller.',
            'resume.uploaded' => 'The resume could not be uploaded. Please try a smaller file (2 MB max).',
            'cover_letter.uploaded' => 'The cover letter could not be uploaded. Please try a smaller file (2 MB max).',
        ] + $this->captchaRules());

        $job = !empty($validated['position']) ? JobOpening::find($validated['position']) : null;

        $lead = $this->storeGeneralLead(
            'career',
            'Careers',
            trim($validated['first_name'] . ' ' . $validated['last_name']),
            $validated['email'],
            $validated['phone'] ?? null,
            null,
        );

        // The title is kept too, so the lead still reads right if the opening is deleted later.
        // Files go to the private disk (storage/app/private): only reachable through the admin download route.
        $lead->update([
            'job_opening_id' => $job?->id,
            'job_title' => $job?->title,
            'resume_path' => $request->file('resume')->store(self::APPLICATION_DIR, 'local'),
            'cover_letter_path' => $request->file('cover_letter')?->store(self::APPLICATION_DIR, 'local'),
        ]);

        return redirect()->back()->with('success', 'Thanks for applying! Our team will get back to you soon.');
    }

    /** Admin download of an application's resume or cover letter. */
    public function downloadFile(PropertyInquiry $inquiry, string $file)
    {
        $column = PropertyInquiry::FILES[$file] ?? abort(404);
        $path = $inquiry->$column;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        $name = Str::slug($inquiry->name ?: 'applicant') . '-' . $file . '.' . pathinfo($path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download($path, $name);
    }

    /** "Associate With Us" request from /associates-us. */
    public function storeAssociate(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:5000',
            'agree' => 'accepted',
        ] + $this->captchaRules());

        $this->storeGeneralLead(
            'associate',
            'Associate',
            trim($validated['first_name'] . ' ' . $validated['last_name']),
            $validated['email'],
            $validated['phone'] ?? null,
            $validated['message'] ?? null,
        );

        return redirect()->back()->with('success', 'Thanks for your interest! We will contact you soon.');
    }

    /** reCAPTCHA is enforced only when keys are configured, so local setups still work. */
    private function captchaRules(): array
    {
        return config('services.recaptcha.secret_key')
            ? ['g-recaptcha-response' => 'required|recaptcha']
            : [];
    }

    private function storeGeneralLead(string $intent, string $source, string $name, ?string $email, ?string $phone, ?string $message): PropertyInquiry
    {
        return PropertyInquiry::create([
            'property_id' => 0,
            'name' => $name,
            'email' => $email,
            'phone' => $phone ?? '', // property_inquiries.phone is NOT NULL
            'message' => $message,
            'intent' => $intent,
            'source' => $source,
            'terms_accepted' => in_array($intent, ['career', 'associate'], true),
        ]);
    }

    public function store(Request $request, Property $property)
    {
        // The enquiry form posts the dial code in its own select; fold it into
        // phone before validating so the stored value is dialable and the
        // max:20 rule covers what actually lands in the column.
        if (filled($request->input('country_code'))) {
            $request->merge([
                'phone' => trim($request->input('country_code') . ' ' . $request->input('phone')),
            ]);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'message' => 'nullable|string',
            'terms' => 'required|accepted',
            'intent' => 'nullable|string|max:100',
            'source' => 'nullable|string|max:100',
            'detail_id' => 'nullable|integer',
        ];

        if (config('services.recaptcha.secret_key')) {
            $rules['g-recaptcha-response'] = 'required|recaptcha';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $inquiryMessage = $request->message;
        if ($request->filled('detail_id') && empty($inquiryMessage)) {
            $detail = $property->details()->find($request->input('detail_id'));
            if ($detail) {
                $unitName = $detail->unit_type ?: ($detail->bedrooms ? $detail->bedrooms . ' BHK' : 'Unit #' . $detail->id);
                $inquiryMessage = "Requested details for configuration: {$unitName}";
            }
        }

        PropertyInquiry::create([
            'property_id' => $property->id,
            'property_title' => $property->title,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $inquiryMessage,
            'intent' => $request->input('intent', 'inquiry'),
            'source' => $request->input('source', 'side'),
            'terms_accepted' => true,
        ]);

        // A brochure / detail document request is a gated download: the lead is captured above, so
        // hand the file back on the next page load. Falls through to the plain
        // thank-you when neither detail document nor property brochure exists.
        if ($request->input('intent') === 'brochure') {
            $downloadUrl = null;

            // 1. If a specific property detail was requested and has a document
            if ($request->filled('detail_id')) {
                $detail = $property->details()->find($request->input('detail_id'));
                if ($detail && filled($detail->document)) {
                    $downloadUrl = asset($detail->document);
                }
            }

            // 2. If no detail doc found yet, check if any detail has a document
            if (!$downloadUrl) {
                $detailWithDoc = $property->details()->whereNotNull('document')->where('document', '!=', '')->first();
                if ($detailWithDoc) {
                    $downloadUrl = asset($detailWithDoc->document);
                }
            }

            // 3. Fallback to property brochure
            if (!$downloadUrl && filled($property->brochure)) {
                $downloadUrl = url($property->brochure);
            }

            if ($downloadUrl) {
                return redirect()->back()
                    ->with('success', 'Thanks! Your download will start automatically.')
                    ->with('brochure_url', $downloadUrl);
            }
        }

        return redirect()->back()->with('success', 'Your enquiry has been submitted successfully!');
    }
}
