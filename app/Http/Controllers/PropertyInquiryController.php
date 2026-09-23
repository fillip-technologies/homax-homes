<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PropertyInquiry;
use App\Models\Property;
use Illuminate\Support\Facades\Validator;

class PropertyInquiryController extends Controller
{

    public function enquiryForm(){
        $inquaries= PropertyInquiry::latest()->get();
        // dd($inquaries);
        return view('admin.enquaryformlist', compact('inquaries'));
    }

    /**
     * Store a general "Contact Us" submission. Reuses the property_inquiries
     * table rather than a dedicated one - property_id has no foreign key
     * constraint, so 0 is used as a "no property" sentinel. The admin list's
     * `@if($inquary->property_id)` check already treats 0 as falsy, so these
     * rows show "N/A" instead of a broken "View Property" link, with no
     * schema change needed.
     */
    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'interest' => 'nullable|string|max:100',
            'message' => 'required|string',
        ]);

        PropertyInquiry::create([
            'property_id' => 0,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
            'intent' => $validated['interest'] ?? 'general',
            'source' => 'Contact',
            'terms_accepted' => false,
        ]);

        return redirect()->back()->with('success', 'Thanks for reaching out! We\'ll get back to you soon.');
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
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $inquiryMessage,
            'intent' => $request->input('intent', 'enquiry'),
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

        return redirect()->back()->with('success', 'Your inquiry has been submitted successfully!');
    }
}
