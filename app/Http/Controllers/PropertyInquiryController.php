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

        PropertyInquiry::create([
            'property_id' => $property->id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'intent' => $request->input('intent', 'enquiry'),
            'source' => $request->input('source', 'side'),
            'terms_accepted' => true,
        ]);

        // A brochure request is a gated download: the lead is captured above, so
        // hand the file back on the next page load. Falls through to the plain
        // thank-you when the property has no brochure on file.
        if ($request->input('intent') === 'brochure' && filled($property->brochure)) {
            return redirect()->back()
                ->with('success', 'Thanks! Your brochure download will start automatically.')
                ->with('brochure_url', url($property->brochure));
        }

        return redirect()->back()->with('success', 'Your inquiry has been submitted successfully!');
    }
}
