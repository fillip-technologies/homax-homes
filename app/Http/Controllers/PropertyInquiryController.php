<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyInquiry;
use App\Models\Property;
use Illuminate\Support\Facades\Validator;

class PropertyInquiryController extends Controller
{

    public function inquiryForm()
    {
        $inquiries = PropertyInquiry::with('property')->latest()->get();
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

    /** "Join Our Team" application from /join-us. */
    public function storeJoinUs(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|in:agent,manager,marketing,intern,other',
            'resume_link' => 'nullable|url|max:500',
            'cover_letter' => 'nullable|string|max:5000',
            'agree' => 'accepted',
        ] + $this->captchaRules());

        $lines = [];
        if (!empty($validated['position'])) {
            $lines[] = 'Position: ' . ucfirst($validated['position']);
        }
        if (!empty($validated['resume_link'])) {
            $lines[] = 'Resume: ' . $validated['resume_link'];
        }
        if (!empty($validated['cover_letter'])) {
            $lines[] = '';
            $lines[] = $validated['cover_letter'];
        }

        $this->storeGeneralLead(
            'career',
            'Join Us',
            trim($validated['first_name'] . ' ' . $validated['last_name']),
            $validated['email'],
            $validated['phone'] ?? null,
            implode("\n", $lines) ?: null,
        );

        return redirect()->back()->with('success', 'Thanks for applying! Our team will get back to you soon.');
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
