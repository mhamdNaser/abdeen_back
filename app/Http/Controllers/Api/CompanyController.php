<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyInfo;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_name' => 'nullable|string|max:255',
            'company_description_en' => 'nullable|string',
            'company_description_ar' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|string|email|max:100',
            'tax_number' => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
            'license_number' => 'nullable|string|max:50',
            'license_date' => 'nullable|date',
            'license_expiry' => 'nullable|date',
        ]);

        $companyInfo = CompanyInfo::first();

        if (!$companyInfo) {
            $companyInfo = new CompanyInfo;
        }

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/company-data'), $imageName);
            $companyInfo->logo = 'images/company-data/' . $imageName;
        } elseif (!$companyInfo->logo) {
            return response()->json(['error' => 'Logo is required'], 422);
        }

        $companyInfo->company_name = $request->input('company_name');
        $companyInfo->company_description_en = $request->input('company_description_en');
        $companyInfo->company_description_ar = $request->input('company_description_ar');
        $companyInfo->location = $request->input('location', 'Jordan / Aqaba');
        $companyInfo->phone_number = $request->input('phone_number');
        $companyInfo->email = $request->input('email');
        $companyInfo->tax_number = $request->input('tax_number');
        $companyInfo->commercial_register = $request->input('commercial_register');
        $companyInfo->license_number = $request->input('license_number');
        $companyInfo->license_date = $request->input('license_date');
        $companyInfo->license_expiry = $request->input('license_expiry');

        $companyInfo->save();

        return response()->json(['message' => 'Company information saved successfully!']);
    }



    public function getCompanyInfo()
    {
        $companyInfo = CompanyInfo::first();

        if (!$companyInfo) {
            return response()->json(['message' => 'No company information found!'], 404);
        }

        if ($companyInfo->logo) {
            $companyInfo->logo = asset($companyInfo->logo);
        }

        return response()->json($companyInfo);
    }


}
