<?php

namespace App\Http\Controllers;

use App\Models\ApplicationRequest;
use App\Models\PersonalAddressInfo;
use App\Models\PersonalFamilyProfile;
use App\Models\ParentalCreditInfo;
use App\Models\EmploymentPaymentDetail;
use App\Models\CoMakerEmploymentDetail;
use App\Models\CreditInquiryAuthorization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ApplicationRequestController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Log the request data for debugging
            Log::info('Application request data:', $request->all());
            
            $validated = $request->validate([
                'personal_first_name' => 'required|string',
                'personal_middle_name' => 'nullable|string',
                'personal_last_name' => 'required|string',
                'personal_age' => 'required|integer',
                'personal_nb_rb' => 'nullable|string',
                'personal_sex' => 'nullable|string|in:Male,Female',
                'personal_citizenship' => 'nullable|string',
                'personal_birth_date' => 'nullable|date',
                'personal_religion' => 'nullable|string',
                'personal_civil_status' => 'nullable|string|in:Single,Married,Separated,Widowed',
                'personal_tin' => 'nullable|string',
                'personal_res_cert_no' => 'nullable|string',
                'personal_date_issued' => 'nullable|date',
                'personal_place_issued' => 'nullable|string',
                
                // Present Address
                'present_block_street' => 'nullable|string',
                'present_zone_purok' => 'nullable|string',
                'present_barangay' => 'nullable|string',
                'present_municipality_city' => 'nullable|string',
                'present_province' => 'nullable|string',
                'present_length_of_stay' => 'nullable|string',
                'present_house_ownership' => 'nullable|string|in:Owned,Rented,Mortgaged',
                'present_lot_ownership' => 'nullable|string|in:Owned,Rented,Mortgaged',
                'present_other_properties' => 'nullable|array',
                'present_other_properties.*' => 'nullable|string',
                
                // Provincial Address
                'provincial_block_street' => 'nullable|string',
                'provincial_zone_purok' => 'nullable|string',
                'provincial_barangay' => 'nullable|string',
                'provincial_municipality_city' => 'nullable|string',
                'provincial_province' => 'nullable|string',
                
                // Contact Information
                'contact_home_phone' => 'required|string',
                'contact_office_phone' => 'required|string',
                'contact_mobile_phone' => 'required|string',
                'contact_email' => 'required|email',
                'contact_spouse_name' => 'nullable|string',
                'contact_age' => 'nullable|string',
                'contact_dependents' => 'nullable|string',
                'contact_provincial_spouse' => 'nullable|string',
                'contact_mobile_no' => 'required|string',
                'information_email' => 'required|email',
                'dependents_info' => 'nullable|json',
                
                // Applicant's Parents
                'applicant_father_name' => 'nullable|string',
                'applicant_mother_name' => 'nullable|string',
                'applicant_occupation' => 'nullable|string',
                'applicant_mobile_no' => 'nullable|string',
                'applicant_address' => 'nullable|string',
                
                // Spouse's Parents
                'spouse_father_name' => 'nullable|string',
                'spouse_mother_name' => 'nullable|string',
                'spouse_occupation' => 'nullable|string',
                'spouse_mobile_no' => 'nullable|string',
                'spouse_address' => 'nullable|string',
                
                // Step 3 - Individual fields
                'creditStoreBank' => 'nullable|string',
                'creditItemLoanAmount' => 'nullable|string',
                'creditTerm' => 'nullable|string',
                'creditDate' => 'nullable|string',
                'creditBalance' => 'nullable|string',
                'referencesFullName' => 'nullable|string',
                'referencesRelationship' => 'nullable|string',
                'referencesTelNo' => 'nullable|string',
                'referencesAddress' => 'nullable|string',
                'sourceOfIncome' => 'nullable|json',

                'spouse_first_name' => 'nullable|string',
                'spouse_age' => 'nullable|integer',
                'co_makers' => 'nullable|json',
                'sketch_residence' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                'sketch_residence_comaker' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                'applicant_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                'spouse_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                'comaker_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',

                'applicant_employer' => 'nullable|string',
                'applicant_position' => 'nullable|string',
                'applicant_block_street' => 'nullable|string',
                'applicant_zone_purok' => 'nullable|string',
                'applicant_barangay' => 'nullable|string',
                'applicant_municipality_city' => 'nullable|string',
                'applicant_province' => 'nullable|string',
                'applicant_telno' => 'nullable|string',
                'applicant_date_started' => 'nullable|date',
                'applicant_name_immediate' => 'nullable|string',
                'applicant_salary_gross' => 'nullable|numeric',

                'spouse_employer' => 'nullable|string',
                'spouse_position' => 'nullable|string',
                'spouse_block_street' => 'nullable|string',
                'spouse_zone_purok' => 'nullable|string',
                'spouse_barangay' => 'nullable|string',
                'spouse_municipality' => 'nullable|string',
                'spouse_province' => 'nullable|string',
                'spouse_telno' => 'nullable|string',
                'spouse_date_started' => 'nullable|date',
                'spouse_name_immediate' => 'nullable|string',
                'spouse_salary_gross' => 'nullable|numeric',

                'personal_use' => 'nullable|boolean',
                'business_use' => 'nullable|boolean',
                'gift' => 'nullable|boolean',
                'use_by_relative' => 'nullable|boolean',

                'post_dated_checks' => 'nullable|boolean',
                'cash_paid_to_office' => 'nullable|boolean',
                'cash_for_collection' => 'nullable|boolean',
                'credit_card' => 'nullable|boolean',
            ]);

            // Add detailed logging after validation
            Log::info('Validated data:', $validated);
            Log::info('Contact fields received:', [
                'contact_home_phone' => $validated['contact_home_phone'] ?? 'Not provided',
                'contact_office_phone' => $validated['contact_office_phone'] ?? 'Not provided',
                'contact_mobile_phone' => $validated['contact_mobile_phone'] ?? 'Not provided',
                'contact_email' => $validated['contact_email'] ?? 'Not provided',
                'contact_spouse_name' => $validated['contact_spouse_name'] ?? 'Not provided',
                'contact_age' => $validated['contact_age'] ?? 'Not provided',
                'contact_dependents' => $validated['contact_dependents'] ?? 'Not provided',
                'contact_provincial_spouse' => $validated['contact_provincial_spouse'] ?? 'Not provided',
                'contact_mobile_no' => $validated['contact_mobile_no'] ?? 'Not provided',
                'information_email' => $validated['information_email'] ?? 'Not provided',
            ]);
            
            // Add specific logging for dependents_info to debug
            Log::info('Dependents info:', [
                'raw' => $request->input('dependents_info'),
                'validated' => $validated['dependents_info'] ?? 'Not provided',
                'type' => $validated['dependents_info'] ? gettype($validated['dependents_info']) : 'N/A'
            ]);

            $signaturePath = null;
            if ($request->hasFile('signature')) {
                // Store the file and get the path
                $file = $request->file('signature');
                $filename = time() . '_' . $file->getClientOriginalName();
                $signaturePath = $file->storeAs('signatures', $filename, 'public');
                
                // Log for debugging
                Log::info('Signature uploaded:', [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $signaturePath,
                    'full_url' => asset('storage/' . $signaturePath)
                ]);
            }

            // Handle the new file uploads
            $sketchResidencePath = null;
            if ($request->hasFile('sketch_residence')) {
                $file = $request->file('sketch_residence');
                $filename = time() . '_sketch_residence_' . $file->getClientOriginalName();
                $sketchResidencePath = $file->storeAs('sketches', $filename, 'public');
                Log::info('Sketch residence uploaded:', [
                    'stored_path' => $sketchResidencePath
                ]);
            }
            
            $sketchResidenceComakerPath = null;
            if ($request->hasFile('sketch_residence_comaker')) {
                $file = $request->file('sketch_residence_comaker');
                $filename = time() . '_sketch_comaker_' . $file->getClientOriginalName();
                $sketchResidenceComakerPath = $file->storeAs('sketches', $filename, 'public');
                Log::info('Sketch residence comaker uploaded:', [
                    'stored_path' => $sketchResidenceComakerPath
                ]);
            }
            
            $applicantSignaturePath = null;
            if ($request->hasFile('applicant_signature')) {
                $file = $request->file('applicant_signature');
                $filename = time() . '_applicant_signature_' . $file->getClientOriginalName();
                $applicantSignaturePath = $file->storeAs('signatures', $filename, 'public');
                Log::info('Applicant signature uploaded:', [
                    'stored_path' => $applicantSignaturePath
                ]);
            }
            
            $spouseSignaturePath = null;
            if ($request->hasFile('spouse_signature')) {
                $file = $request->file('spouse_signature');
                $filename = time() . '_spouse_signature_' . $file->getClientOriginalName();
                $spouseSignaturePath = $file->storeAs('signatures', $filename, 'public');
                Log::info('Spouse signature uploaded:', [
                    'stored_path' => $spouseSignaturePath
                ]);
            }
            
            $comakerSignaturePath = null;
            if ($request->hasFile('comaker_signature')) {
                $file = $request->file('comaker_signature');
                $filename = time() . '_comaker_signature_' . $file->getClientOriginalName();
                $comakerSignaturePath = $file->storeAs('signatures', $filename, 'public');
                Log::info('Co-maker signature uploaded:', [
                    'stored_path' => $comakerSignaturePath
                ]);
            }

            $currentTimestamp = now()->format('Y-m-d H:i:s');

            // Use a database transaction to ensure all related records are created
            DB::beginTransaction();
            
            try {
                // Create the main application request
                $application = ApplicationRequest::create([
                    'user_id' => Auth::id(),
                    'status' => 'Pending|' . $currentTimestamp,
                ]);

                Log::info('Application created with ID: ' . $application->id);

                // Create step 1: Personal & Address Information
                $personalAddressInfo = PersonalAddressInfo::create([
                    'application_request_id' => $application->id,
                    'personal_first_name' => $validated['personal_first_name'],
                    'personal_middle_name' => $validated['personal_middle_name'] ?? null,
                    'personal_last_name' => $validated['personal_last_name'],
                    'personal_age' => $validated['personal_age'],
                    'personal_nb_rb' => $validated['personal_nb_rb'] ?? null,
                    'personal_sex' => $validated['personal_sex'] ?? null,
                    'personal_citizenship' => $validated['personal_citizenship'] ?? null,
                    'personal_birth_date' => $validated['personal_birth_date'] ?? null,
                    'personal_religion' => $validated['personal_religion'] ?? null,
                    'personal_civil_status' => $validated['personal_civil_status'] ?? null,
                    'personal_tin' => $validated['personal_tin'] ?? null,
                    'personal_res_cert_no' => $validated['personal_res_cert_no'] ?? null,
                    'personal_date_issued' => $validated['personal_date_issued'] ?? null,
                    'personal_place_issued' => $validated['personal_place_issued'] ?? null,
                    
                    // Present Address
                    'present_block_street' => $validated['present_block_street'] ?? null,
                    'present_zone_purok' => $validated['present_zone_purok'] ?? null,
                    'present_barangay' => $validated['present_barangay'] ?? null,
                    'present_municipality_city' => $validated['present_municipality_city'] ?? null,
                    'present_province' => $validated['present_province'] ?? null,
                    'present_length_of_stay' => $validated['present_length_of_stay'] ?? null,
                    'present_house_ownership' => $validated['present_house_ownership'] ?? null,
                    'present_lot_ownership' => $validated['present_lot_ownership'] ?? null,
                    'present_other_properties' => $validated['present_other_properties'] ?? [],
                    
                    // Provincial Address
                    'provincial_block_street' => $validated['provincial_block_street'] ?? null,
                    'provincial_zone_purok' => $validated['provincial_zone_purok'] ?? null,
                    'provincial_barangay' => $validated['provincial_barangay'] ?? null,
                    'provincial_municipality_city' => $validated['provincial_municipality_city'] ?? null,
                    'provincial_province' => $validated['provincial_province'] ?? null,
                ]);

                Log::info('PersonalAddressInfo created with ID: ' . $personalAddressInfo->id);

                // Create step 2: Personal & Family Profile
                $personalFamilyProfile = PersonalFamilyProfile::create([
                    'application_request_id' => $application->id,
                    'contact_home_phone' => $validated['contact_home_phone'],
                    'contact_office_phone' => $validated['contact_office_phone'],
                    'contact_mobile_phone' => $validated['contact_mobile_phone'],
                    'contact_email' => $validated['contact_email'],
                    'contact_spouse_name' => $validated['contact_spouse_name'] ?? null,
                    'contact_age' => $validated['contact_age'] ?? null,
                    'contact_dependents' => $validated['contact_dependents'] ?? null,
                    'contact_provincial_spouse' => $validated['contact_provincial_spouse'] ?? null,
                    'contact_mobile_no' => $validated['contact_mobile_no'],
                    'information_email' => $validated['information_email'],
                    'dependents_info' => !empty($validated['dependents_info']) ? $validated['dependents_info'] : json_encode([]),
                    // Applicant's Parents
                    'applicant_father_name' => $validated['applicant_father_name'] ?? null,
                    'applicant_mother_name' => $validated['applicant_mother_name'] ?? null,
                    'applicant_occupation' => $validated['applicant_occupation'] ?? null,
                    'applicant_mobile_no' => $validated['applicant_mobile_no'] ?? null,
                    'applicant_address' => $validated['applicant_address'] ?? null,
                    // Spouse's Parents
                    'spouse_father_name' => $validated['spouse_father_name'] ?? null,
                    'spouse_mother_name' => $validated['spouse_mother_name'] ?? null,
                    'spouse_occupation' => $validated['spouse_occupation'] ?? null,
                    'spouse_mobile_no' => $validated['spouse_mobile_no'] ?? null,
                    'spouse_address' => $validated['spouse_address'] ?? null,
                ]);

                Log::info('PersonalFamilyProfile created with ID: ' . $personalFamilyProfile->id);

                // Create step 3: Parental & Credit Information
                $parentalCreditInfo = ParentalCreditInfo::create([
                    'application_request_id' => $application->id,
                    // Credit References - individual fields
                    'credit_store_bank' => $validated['creditStoreBank'] ?? null,
                    'credit_item_loan_amount' => $validated['creditItemLoanAmount'] ?? null,
                    'credit_term' => $validated['creditTerm'] ?? null,
                    'credit_date' => $validated['creditDate'] ?? null,
                    'credit_balance' => $validated['creditBalance'] ?? null,
                    // Personal References - individual fields
                    'references_full_name' => $validated['referencesFullName'] ?? null,
                    'references_relationship' => $validated['referencesRelationship'] ?? null,
                    'references_tel_no' => $validated['referencesTelNo'] ?? null,
                    'references_address' => $validated['referencesAddress'] ?? null,
                    // Source of Income - still as JSON
                    'source_of_income' => $validated['sourceOfIncome'] ?? json_encode([]),
                ]);

                Log::info('ParentalCreditInfo created with ID: ' . $parentalCreditInfo->id);

                // Create step 4: Employment & Payment Details
                $employmentPaymentDetail = EmploymentPaymentDetail::create([
                    'application_request_id' => $application->id,
                    
                    // Applicant Employer Information
                    'applicant_employer' => $validated['applicant_employer'] ?? null,
                    'applicant_position' => $validated['applicant_position'] ?? null,
                    'applicant_block_street' => $validated['applicant_block_street'] ?? null,
                    'applicant_zone_purok' => $validated['applicant_zone_purok'] ?? null,
                    'applicant_barangay' => $validated['applicant_barangay'] ?? null,
                    'applicant_municipality_city' => $validated['applicant_municipality_city'] ?? null,
                    'applicant_province' => $validated['applicant_province'] ?? null,
                    'applicant_telno' => $validated['applicant_telno'] ?? null,
                    'applicant_date_started' => $validated['applicant_date_started'] ?? null,
                    'applicant_name_immediate' => $validated['applicant_name_immediate'] ?? null,
                    'applicant_employer_mobile_no' => $validated['applicant_employer_mobile_no'] ?? null,
                    'applicant_salary_gross' => $validated['applicant_salary_gross'] ?? null,
                    
                    // Spouse Employer Information
                    'spouse_employer' => $validated['spouse_employer'] ?? null,
                    'spouse_position' => $validated['spouse_position'] ?? null,
                    'spouse_block_street' => $validated['spouse_block_street'] ?? null,
                    'spouse_zone_purok' => $validated['spouse_zone_purok'] ?? null,
                    'spouse_barangay' => $validated['spouse_barangay'] ?? null,
                    'spouse_municipality' => $validated['spouse_municipality'] ?? null,
                    'spouse_province' => $validated['spouse_province'] ?? null,
                    'spouse_telno' => $validated['spouse_telno'] ?? null,
                    'spouse_date_started' => $validated['spouse_date_started'] ?? null,
                    'spouse_name_immediate' => $validated['spouse_name_immediate'] ?? null,
                    'spouse_employer_mobile_no' => $validated['spouse_employer_mobile_no'] ?? null,
                    'spouse_salary_gross' => $validated['spouse_salary_gross'] ?? null,
                    
                    // Unit to be Used For
                    'personal_use' => $validated['personal_use'] ?? false,
                    'business_use' => $validated['business_use'] ?? false,
                    'gift' => $validated['gift'] ?? false,
                    'use_by_relative' => $validated['use_by_relative'] ?? false,
                    
                    // Mode of Payment
                    'post_dated_checks' => $validated['post_dated_checks'] ?? false,
                    'cash_paid_to_office' => $validated['cash_paid_to_office'] ?? false,
                    'cash_for_collection' => $validated['cash_for_collection'] ?? false,
                    'credit_card' => $validated['credit_card'] ?? false,
                ]);

                Log::info('EmploymentPaymentDetail created with ID: ' . $employmentPaymentDetail->id);

                // Create step 5: Co-Maker & Employment Details
                $coMakerEmploymentDetail = CoMakerEmploymentDetail::create([
                    'application_request_id' => $application->id,
                    'co_makers' => $validated['co_makers'],
                ]);

                Log::info('CoMakerEmploymentDetail created with ID: ' . $coMakerEmploymentDetail->id);

                // Create step 6: Credit Inquiry Authorization
                $creditInquiryAuthorization = CreditInquiryAuthorization::create([
                    'application_request_id' => $application->id,
                    'sketch_residence_path' => $sketchResidencePath,
                    'sketch_residence_comaker_path' => $sketchResidenceComakerPath,
                    'applicant_signature_path' => $applicantSignaturePath,
                    'spouse_signature_path' => $spouseSignaturePath,
                    'comaker_signature_path' => $comakerSignaturePath,
                ]);

                Log::info('CreditInquiryAuthorization created with ID: ' . $creditInquiryAuthorization->id);

                DB::commit();
                Log::info('Transaction committed successfully');

                // Return the full application data including all related data
                return response()->json([
                    'id' => $application->id,
                    'user_id' => $application->user_id,
                    'status' => $application->status,
                    'created_at' => $application->created_at,
                    'updated_at' => $application->updated_at,
                    'personal_first_name' => $validated['personal_first_name'],
                    'personal_middle_name' => $validated['personal_middle_name'] ?? null,
                    'personal_last_name' => $validated['personal_last_name'],
                    'personal_age' => $validated['personal_age'],
                    'personal_nb_rb' => $validated['personal_nb_rb'] ?? null,
                    'personal_sex' => $validated['personal_sex'] ?? null,
                    'personal_citizenship' => $validated['personal_citizenship'] ?? null,
                    'personal_birth_date' => $validated['personal_birth_date'] ?? null,
                    'personal_religion' => $validated['personal_religion'] ?? null,
                    'personal_civil_status' => $validated['personal_civil_status'] ?? null,
                    'personal_tin' => $validated['personal_tin'] ?? null,
                    'personal_res_cert_no' => $validated['personal_res_cert_no'] ?? null,
                    'personal_date_issued' => $validated['personal_date_issued'] ?? null,
                    'personal_place_issued' => $validated['personal_place_issued'] ?? null,
                    
                    // Present Address
                    'present_block_street' => $validated['present_block_street'] ?? null,
                    'present_zone_purok' => $validated['present_zone_purok'] ?? null,
                    'present_barangay' => $validated['present_barangay'] ?? null,
                    'present_municipality_city' => $validated['present_municipality_city'] ?? null,
                    'present_province' => $validated['present_province'] ?? null,
                    'present_length_of_stay' => $validated['present_length_of_stay'] ?? null,
                    'present_house_ownership' => $validated['present_house_ownership'] ?? null,
                    'present_lot_ownership' => $validated['present_lot_ownership'] ?? null,
                    'present_other_properties' => $validated['present_other_properties'] ?? [],
                    
                    // Provincial Address
                    'provincial_block_street' => $validated['provincial_block_street'] ?? null,
                    'provincial_zone_purok' => $validated['provincial_zone_purok'] ?? null,
                    'provincial_barangay' => $validated['provincial_barangay'] ?? null,
                    'provincial_municipality_city' => $validated['provincial_municipality_city'] ?? null,
                    'provincial_province' => $validated['provincial_province'] ?? null,
                    
                    // Contact Information
                    'contact_home_phone' => $validated['contact_home_phone'],
                    'contact_office_phone' => $validated['contact_office_phone'],
                    'contact_mobile_phone' => $validated['contact_mobile_phone'],
                    'contact_email' => $validated['contact_email'],
                    'contact_spouse_name' => $validated['contact_spouse_name'] ?? null,
                    'contact_age' => $validated['contact_age'] ?? null,
                    'contact_dependents' => $validated['contact_dependents'] ?? null,
                    'contact_provincial_spouse' => $validated['contact_provincial_spouse'] ?? null,
                    'contact_mobile_no' => $validated['contact_mobile_no'],
                    'information_email' => $validated['information_email'],
                    'dependents_info' => !empty($validated['dependents_info']) ? $validated['dependents_info'] : json_encode([]),
                    
                    // Applicant's Parents
                    'applicant_father_name' => $validated['applicant_father_name'] ?? null,
                    'applicant_mother_name' => $validated['applicant_mother_name'] ?? null,
                    'applicant_occupation' => $validated['applicant_occupation'] ?? null,
                    'applicant_mobile_no' => $validated['applicant_mobile_no'] ?? null,
                    'applicant_address' => $validated['applicant_address'] ?? null,
                    
                    // Spouse's Parents
                    'spouse_father_name' => $validated['spouse_father_name'] ?? null,
                    'spouse_mother_name' => $validated['spouse_mother_name'] ?? null,
                    'spouse_occupation' => $validated['spouse_occupation'] ?? null,
                    'spouse_mobile_no' => $validated['spouse_mobile_no'] ?? null,
                    'spouse_address' => $validated['spouse_address'] ?? null,
                    
                    // Step 3 - Parental & Credit Information - updated to individual fields
                    'credit_store_bank' => $validated['creditStoreBank'] ?? null,
                    'credit_item_loan_amount' => $validated['creditItemLoanAmount'] ?? null,
                    'credit_term' => $validated['creditTerm'] ?? null,
                    'credit_date' => $validated['creditDate'] ?? null,
                    'credit_balance' => $validated['creditBalance'] ?? null,
                    'references_full_name' => $validated['referencesFullName'] ?? null,
                    'references_relationship' => $validated['referencesRelationship'] ?? null,
                    'references_tel_no' => $validated['referencesTelNo'] ?? null,
                    'references_address' => $validated['referencesAddress'] ?? null,
                    'source_of_income' => !empty($application->parentalCreditInfo->source_of_income) ? 
                        (is_string($application->parentalCreditInfo->source_of_income) ? 
                            json_decode($application->parentalCreditInfo->source_of_income, true) : 
                            $application->parentalCreditInfo->source_of_income) : 
                        [],
                    
                    // Step 4
                    'applicant_employer' => $validated['applicant_employer'] ?? null,
                    'applicant_position' => $validated['applicant_position'] ?? null,
                    'applicant_block_street' => $validated['applicant_block_street'] ?? null,
                    'applicant_zone_purok' => $validated['applicant_zone_purok'] ?? null,
                    'applicant_barangay' => $validated['applicant_barangay'] ?? null,
                    'applicant_municipality_city' => $validated['applicant_municipality_city'] ?? null,
                    'applicant_province' => $validated['applicant_province'] ?? null,
                    'applicant_telno' => $validated['applicant_telno'] ?? null,
                    'applicant_date_started' => $validated['applicant_date_started'] ?? null,
                    'applicant_name_immediate' => $validated['applicant_name_immediate'] ?? null,
                    'applicant_employer_mobile_no' => $validated['applicant_employer_mobile_no'] ?? '',
                    'applicant_salary_gross' => $validated['applicant_salary_gross'] ?? null,

                    'spouse_employer' => $validated['spouse_employer'] ?? null,
                    'spouse_position' => $validated['spouse_position'] ?? null,
                    'spouse_block_street' => $validated['spouse_block_street'] ?? null,
                    'spouse_zone_purok' => $validated['spouse_zone_purok'] ?? null,
                    'spouse_barangay' => $validated['spouse_barangay'] ?? null,
                    'spouse_municipality' => $validated['spouse_municipality'] ?? null,
                    'spouse_province' => $validated['spouse_province'] ?? null,
                    'spouse_telno' => $validated['spouse_telno'] ?? null,
                    'spouse_date_started' => $validated['spouse_date_started'] ?? null,
                    'spouse_name_immediate' => $validated['spouse_name_immediate'] ?? null,
                    'spouse_employer_mobile_no' => $validated['spouse_employer_mobile_no'] ?? '',
                    'spouse_salary_gross' => $validated['spouse_salary_gross'] ?? null,

                    'personal_use' => $this->toBooleanValue($validated['personal_use'] ?? false),
                    'business_use' => $this->toBooleanValue($validated['business_use'] ?? false),
                    'gift' => $this->toBooleanValue($validated['gift'] ?? false),
                    'use_by_relative' => $this->toBooleanValue($validated['use_by_relative'] ?? false),

                    'post_dated_checks' => $this->toBooleanValue($validated['post_dated_checks'] ?? false),
                    'cash_paid_to_office' => $this->toBooleanValue($validated['cash_paid_to_office'] ?? false),
                    'cash_for_collection' => $this->toBooleanValue($validated['cash_for_collection'] ?? false),
                    'credit_card' => $this->toBooleanValue($validated['credit_card'] ?? false),

                    'co_makers' => $validated['co_makers'],
                    'sketch_residence_path' => $sketchResidencePath,
                    'sketch_residence_url' => $sketchResidencePath ? asset('storage/' . $sketchResidencePath) : null,
                    'sketch_residence_comaker_path' => $sketchResidenceComakerPath,
                    'sketch_residence_comaker_url' => $sketchResidenceComakerPath ? asset('storage/' . $sketchResidenceComakerPath) : null,
                    'applicant_signature_path' => $applicantSignaturePath,
                    'applicant_signature_url' => $applicantSignaturePath ? asset('storage/' . $applicantSignaturePath) : null,
                    'spouse_signature_path' => $spouseSignaturePath,
                    'spouse_signature_url' => $spouseSignaturePath ? asset('storage/' . $spouseSignaturePath) : null,
                    'comaker_signature_path' => $comakerSignaturePath,
                    'comaker_signature_url' => $comakerSignaturePath ? asset('storage/' . $comakerSignaturePath) : null,
                ], 201);
            } catch (Exception $e) {
                DB::rollBack();
                // Add more detailed error logging
                Log::error('Error in database transaction: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                return response()->json([
                    'message' => 'Failed to create application request', 
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        } catch (Exception $e) {
            // Log validation errors or other exceptions
            Log::error('Exception in store method: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'message' => 'Failed to process application request', 
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function index()
    {
        $applications = ApplicationRequest::with([
            'user',
            'personalAddressInfo',
            'personalFamilyProfile',
            'parentalCreditInfo',
            'employmentPaymentDetail',
            'coMakerEmploymentDetail',
            'creditInquiryAuthorization'
        ])->get()->map(function ($application) {
            // Create a flattened response similar to the old structure
            return [
                'id' => $application->id,
                'user_id' => $application->user_id,
                'status' => $application->status,
                'created_at' => $application->created_at,
                'updated_at' => $application->updated_at,
                'user' => $application->user,
                
                // Step 1
                'personal_first_name' => $application->personalAddressInfo->personal_first_name ?? null,
                'personal_middle_name' => $application->personalAddressInfo->personal_middle_name ?? null,
                'personal_last_name' => $application->personalAddressInfo->personal_last_name ?? null,
                'personal_age' => $application->personalAddressInfo->personal_age ?? null,
                'personal_nb_rb' => $application->personalAddressInfo->personal_nb_rb ?? null,
                'personal_sex' => $application->personalAddressInfo->personal_sex ?? null,
                'personal_citizenship' => $application->personalAddressInfo->personal_citizenship ?? null,
                'personal_birth_date' => $application->personalAddressInfo->personal_birth_date ?? null,
                'personal_religion' => $application->personalAddressInfo->personal_religion ?? null,
                'personal_civil_status' => $application->personalAddressInfo->personal_civil_status ?? null,
                'personal_tin' => $application->personalAddressInfo->personal_tin ?? null,
                'personal_res_cert_no' => $application->personalAddressInfo->personal_res_cert_no ?? null,
                'personal_date_issued' => $application->personalAddressInfo->personal_date_issued ?? null,
                'personal_place_issued' => $application->personalAddressInfo->personal_place_issued ?? null,
                
                // Present Address
                'present_block_street' => $application->personalAddressInfo->present_block_street ?? null,
                'present_zone_purok' => $application->personalAddressInfo->present_zone_purok ?? null,
                'present_barangay' => $application->personalAddressInfo->present_barangay ?? null,
                'present_municipality_city' => $application->personalAddressInfo->present_municipality_city ?? null,
                'present_province' => $application->personalAddressInfo->present_province ?? null,
                'present_length_of_stay' => $application->personalAddressInfo->present_length_of_stay ?? null,
                'present_house_ownership' => $application->personalAddressInfo->present_house_ownership ?? null,
                'present_lot_ownership' => $application->personalAddressInfo->present_lot_ownership ?? null,
                'present_other_properties' => $application->personalAddressInfo->present_other_properties ?? [],
                
                // Provincial Address
                'provincial_block_street' => $application->personalAddressInfo->provincial_block_street ?? null,
                'provincial_zone_purok' => $application->personalAddressInfo->provincial_zone_purok ?? null,
                'provincial_barangay' => $application->personalAddressInfo->provincial_barangay ?? null,
                'provincial_municipality_city' => $application->personalAddressInfo->provincial_municipality_city ?? null,
                'provincial_province' => $application->personalAddressInfo->provincial_province ?? null,
                
                // Step 2
                'contact_home_phone' => $application->personalFamilyProfile->contact_home_phone ?? null,
                'contact_office_phone' => $application->personalFamilyProfile->contact_office_phone ?? null,
                'contact_mobile_phone' => $application->personalFamilyProfile->contact_mobile_phone ?? null,
                'contact_email' => $application->personalFamilyProfile->contact_email ?? null,
                'contact_spouse_name' => $application->personalFamilyProfile->contact_spouse_name ?? null,
                'contact_age' => $application->personalFamilyProfile->contact_age ?? null,
                'contact_dependents' => $application->personalFamilyProfile->contact_dependents ?? null,
                'contact_provincial_spouse' => $application->personalFamilyProfile->contact_provincial_spouse ?? null,
                'contact_mobile_no' => $application->personalFamilyProfile->contact_mobile_no ?? null,
                'information_email' => $application->personalFamilyProfile->information_email ?? null,
                'dependents_info' => !empty($application->personalFamilyProfile->dependents_info) ? 
                    $application->personalFamilyProfile->dependents_info : 
                    json_encode([]),
                
                // Applicant's Parents
                'applicant_father_name' => $application->personalFamilyProfile->applicant_father_name ?? null,
                'applicant_mother_name' => $application->personalFamilyProfile->applicant_mother_name ?? null,
                'applicant_occupation' => $application->personalFamilyProfile->applicant_occupation ?? null,
                'applicant_mobile_no' => $application->personalFamilyProfile->applicant_mobile_no ?? null,
                'applicant_address' => $application->personalFamilyProfile->applicant_address ?? null,
                
                // Spouse's Parents
                'spouse_father_name' => $application->personalFamilyProfile->spouse_father_name ?? null,
                'spouse_mother_name' => $application->personalFamilyProfile->spouse_mother_name ?? null,
                'spouse_occupation' => $application->personalFamilyProfile->spouse_occupation ?? null,
                'spouse_mobile_no' => $application->personalFamilyProfile->spouse_mobile_no ?? null,
                'spouse_address' => $application->personalFamilyProfile->spouse_address ?? null,
                
                // Step 3 - Parental & Credit Information - updated to individual fields
                'credit_store_bank' => $application->parentalCreditInfo->credit_store_bank ?? null,
                'credit_item_loan_amount' => $application->parentalCreditInfo->credit_item_loan_amount ?? null,
                'credit_term' => $application->parentalCreditInfo->credit_term ?? null,
                'credit_date' => $application->parentalCreditInfo->credit_date ?? null,
                'credit_balance' => $application->parentalCreditInfo->credit_balance ?? null,
                'references_full_name' => $application->parentalCreditInfo->references_full_name ?? null,
                'references_relationship' => $application->parentalCreditInfo->references_relationship ?? null,
                'references_tel_no' => $application->parentalCreditInfo->references_tel_no ?? null,
                'references_address' => $application->parentalCreditInfo->references_address ?? null,
                'source_of_income' => !empty($application->parentalCreditInfo->source_of_income) ? 
                    (is_string($application->parentalCreditInfo->source_of_income) ? 
                        json_decode($application->parentalCreditInfo->source_of_income, true) : 
                        $application->parentalCreditInfo->source_of_income) : 
                    [],
                
                // Step 4
                'applicant_employer' => $application->employmentPaymentDetail->applicant_employer ?? null,
                'applicant_position' => $application->employmentPaymentDetail->applicant_position ?? null,
                'applicant_block_street' => $application->employmentPaymentDetail->applicant_block_street ?? null,
                'applicant_zone_purok' => $application->employmentPaymentDetail->applicant_zone_purok ?? null,
                'applicant_barangay' => $application->employmentPaymentDetail->applicant_barangay ?? null,
                'applicant_municipality_city' => $application->employmentPaymentDetail->applicant_municipality_city ?? null,
                'applicant_province' => $application->employmentPaymentDetail->applicant_province ?? null,
                'applicant_telno' => $application->employmentPaymentDetail->applicant_telno ?? null,
                'applicant_date_started' => $application->employmentPaymentDetail->applicant_date_started ?? null,
                'applicant_name_immediate' => $application->employmentPaymentDetail->applicant_name_immediate ?? null,
                'applicant_employer_mobile_no' => $application->employmentPaymentDetail->applicant_employer_mobile_no ?? '',
                'applicant_salary_gross' => $application->employmentPaymentDetail->applicant_salary_gross ?? null,

                'spouse_employer' => $application->employmentPaymentDetail->spouse_employer ?? null,
                'spouse_position' => $application->employmentPaymentDetail->spouse_position ?? null,
                'spouse_block_street' => $application->employmentPaymentDetail->spouse_block_street ?? null,
                'spouse_zone_purok' => $application->employmentPaymentDetail->spouse_zone_purok ?? null,
                'spouse_barangay' => $application->employmentPaymentDetail->spouse_barangay ?? null,
                'spouse_municipality' => $application->employmentPaymentDetail->spouse_municipality ?? null,
                'spouse_province' => $application->employmentPaymentDetail->spouse_province ?? null,
                'spouse_telno' => $application->employmentPaymentDetail->spouse_telno ?? null,
                'spouse_date_started' => $application->employmentPaymentDetail->spouse_date_started ?? null,
                'spouse_name_immediate' => $application->employmentPaymentDetail->spouse_name_immediate ?? null,
                'spouse_employer_mobile_no' => $application->employmentPaymentDetail->spouse_employer_mobile_no ?? '',
                'spouse_salary_gross' => $application->employmentPaymentDetail->spouse_salary_gross ?? null,

                'personal_use' => $this->toBooleanValue($application->employmentPaymentDetail->personal_use ?? false),
                'business_use' => $this->toBooleanValue($application->employmentPaymentDetail->business_use ?? false),
                'gift' => $this->toBooleanValue($application->employmentPaymentDetail->gift ?? false),
                'use_by_relative' => $this->toBooleanValue($application->employmentPaymentDetail->use_by_relative ?? false),

                'post_dated_checks' => $this->toBooleanValue($application->employmentPaymentDetail->post_dated_checks ?? false),
                'cash_paid_to_office' => $this->toBooleanValue($application->employmentPaymentDetail->cash_paid_to_office ?? false),
                'cash_for_collection' => $this->toBooleanValue($application->employmentPaymentDetail->cash_for_collection ?? false),
                'credit_card' => $this->toBooleanValue($application->employmentPaymentDetail->credit_card ?? false),
                
                // Step 5
                'co_makers' => $application->coMakerEmploymentDetail->co_makers ?? null,
                
                // Step 6
                'sketch_residence_path' => $application->creditInquiryAuthorization->sketch_residence_path ?? null,
                'sketch_residence_url' => $application->creditInquiryAuthorization->sketch_residence_path ? 
                    asset('storage/' . $application->creditInquiryAuthorization->sketch_residence_path) : null,
                'sketch_residence_comaker_path' => $application->creditInquiryAuthorization->sketch_residence_comaker_path ?? null,
                'sketch_residence_comaker_url' => $application->creditInquiryAuthorization->sketch_residence_comaker_path ? 
                    asset('storage/' . $application->creditInquiryAuthorization->sketch_residence_comaker_path) : null,
                'applicant_signature_path' => $application->creditInquiryAuthorization->applicant_signature_path ?? null,
                'applicant_signature_url' => $application->creditInquiryAuthorization->applicant_signature_path ? 
                    asset('storage/' . $application->creditInquiryAuthorization->applicant_signature_path) : null,
                'spouse_signature_path' => $application->creditInquiryAuthorization->spouse_signature_path ?? null,
                'spouse_signature_url' => $application->creditInquiryAuthorization->spouse_signature_path ? 
                    asset('storage/' . $application->creditInquiryAuthorization->spouse_signature_path) : null,
                'comaker_signature_path' => $application->creditInquiryAuthorization->comaker_signature_path ?? null,
                'comaker_signature_url' => $application->creditInquiryAuthorization->comaker_signature_path ? 
                    asset('storage/' . $application->creditInquiryAuthorization->comaker_signature_path) : null,
            ];
        });
        
        return response()->json($applications);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Approved,Disapproved',
        ]);

        $application = ApplicationRequest::findOrFail($id);

        // Replace the 'Pending' status with the new status and timestamp
        $currentTimestamp = now()->format('Y-m-d H:i:s');
        $newStatusEntry = $validated['status'] . '|' . $currentTimestamp;

        if ($application->status) {
            $statuses = explode(',', $application->status);
            $statuses = array_filter($statuses, function ($statusEntry) {
                return !str_starts_with($statusEntry, 'Pending|');
            });
            $statuses[] = $newStatusEntry;
            $application->status = implode(',', $statuses);
        } else {
            $application->status = $newStatusEntry;
        }

        $application->save();

        // Prepare a flattened response with all related data
        $applicationData = $this->getApplicationWithDetails($application);

        // Include full application details in the response
        return response()->json([
            'status' => $application->status,
            'details' => $applicationData
        ]);
    }

    public function userApplications(Request $request)
    {
        $user = $request->user();
        $applications = ApplicationRequest::where('user_id', $user->id)
            ->with([
                'personalAddressInfo',
                'personalFamilyProfile',
                'parentalCreditInfo',
                'employmentPaymentDetail',
                'coMakerEmploymentDetail',
                'creditInquiryAuthorization'
            ])
            ->get()
            ->map(function ($application) {
                return $this->getApplicationWithDetails($application);
            });

        return response()->json($applications);
    }

    /**
     * Helper method to get a flattened application with all details
     */
    private function getApplicationWithDetails($application)
    {
        // Force loading of the employment payment detail relation if not already loaded
        if (!$application->relationLoaded('employmentPaymentDetail')) {
            $application->load('employmentPaymentDetail');
        }
        
        // Helper function to ensure string values for mobile numbers
        $ensureString = function($value) {
            return $value === null ? '' : (string)$value;
        };
        
        return [
            'id' => $application->id,
            'user_id' => $application->user_id,
            'status' => $application->status,
            'created_at' => $application->created_at,
            'updated_at' => $application->updated_at,
            
            // Step 1
            'personal_first_name' => $application->personalAddressInfo->personal_first_name ?? null,
            'personal_middle_name' => $application->personalAddressInfo->personal_middle_name ?? null,
            'personal_last_name' => $application->personalAddressInfo->personal_last_name ?? null,
            'personal_age' => $application->personalAddressInfo->personal_age ?? null,
            'personal_nb_rb' => $application->personalAddressInfo->personal_nb_rb ?? null,
            'personal_sex' => $application->personalAddressInfo->personal_sex ?? null,
            'personal_citizenship' => $application->personalAddressInfo->personal_citizenship ?? null,
            'personal_birth_date' => $application->personalAddressInfo->personal_birth_date ?? null,
            'personal_religion' => $application->personalAddressInfo->personal_religion ?? null,
            'personal_civil_status' => $application->personalAddressInfo->personal_civil_status ?? null,
            'personal_tin' => $application->personalAddressInfo->personal_tin ?? null,
            'personal_res_cert_no' => $application->personalAddressInfo->personal_res_cert_no ?? null,
            'personal_date_issued' => $application->personalAddressInfo->personal_date_issued ?? null,
            'personal_place_issued' => $application->personalAddressInfo->personal_place_issued ?? null,
            
            // Present Address
            'present_block_street' => $application->personalAddressInfo->present_block_street ?? null,
            'present_zone_purok' => $application->personalAddressInfo->present_zone_purok ?? null,
            'present_barangay' => $application->personalAddressInfo->present_barangay ?? null,
            'present_municipality_city' => $application->personalAddressInfo->present_municipality_city ?? null,
            'present_province' => $application->personalAddressInfo->present_province ?? null,
            'present_length_of_stay' => $application->personalAddressInfo->present_length_of_stay ?? null,
            'present_house_ownership' => $application->personalAddressInfo->present_house_ownership ?? null,
            'present_lot_ownership' => $application->personalAddressInfo->present_lot_ownership ?? null,
            'present_other_properties' => $application->personalAddressInfo->present_other_properties ?? [],
            
            // Provincial Address
            'provincial_block_street' => $application->personalAddressInfo->provincial_block_street ?? null,
            'provincial_zone_purok' => $application->personalAddressInfo->provincial_zone_purok ?? null,
            'provincial_barangay' => $application->personalAddressInfo->provincial_barangay ?? null,
            'provincial_municipality_city' => $application->personalAddressInfo->provincial_municipality_city ?? null,
            'provincial_province' => $application->personalAddressInfo->provincial_province ?? null,
            
            // Step 2
            'contact_home_phone' => $application->personalFamilyProfile->contact_home_phone ?? null,
            'contact_office_phone' => $application->personalFamilyProfile->contact_office_phone ?? null,
            'contact_mobile_phone' => $application->personalFamilyProfile->contact_mobile_phone ?? null,
            'contact_email' => $application->personalFamilyProfile->contact_email ?? null,
            'contact_spouse_name' => $application->personalFamilyProfile->contact_spouse_name ?? null,
            'contact_age' => $application->personalFamilyProfile->contact_age ?? null,
            'contact_dependents' => $application->personalFamilyProfile->contact_dependents ?? null,
            'contact_provincial_spouse' => $application->personalFamilyProfile->contact_provincial_spouse ?? null,
            'contact_mobile_no' => $application->personalFamilyProfile->contact_mobile_no ?? null,
            'information_email' => $application->personalFamilyProfile->information_email ?? null,
            'dependents_info' => !empty($application->personalFamilyProfile->dependents_info) ? 
                $application->personalFamilyProfile->dependents_info : 
                json_encode([]),
            
            // Applicant's Parents
            'applicant_father_name' => $application->personalFamilyProfile->applicant_father_name ?? null,
            'applicant_mother_name' => $application->personalFamilyProfile->applicant_mother_name ?? null,
            'applicant_occupation' => $application->personalFamilyProfile->applicant_occupation ?? null,
            'applicant_mobile_no' => $application->personalFamilyProfile->applicant_mobile_no ?? null,
            'applicant_address' => $application->personalFamilyProfile->applicant_address ?? null,
            
            // Spouse's Parents
            'spouse_father_name' => $application->personalFamilyProfile->spouse_father_name ?? null,
            'spouse_mother_name' => $application->personalFamilyProfile->spouse_mother_name ?? null,
            'spouse_occupation' => $application->personalFamilyProfile->spouse_occupation ?? null,
            'spouse_mobile_no' => $application->personalFamilyProfile->spouse_mobile_no ?? null,
            'spouse_address' => $application->personalFamilyProfile->spouse_address ?? null,
            
            // Step 3 - Parental & Credit Information - updated to individual fields
            'credit_store_bank' => $application->parentalCreditInfo->credit_store_bank ?? null,
            'credit_item_loan_amount' => $application->parentalCreditInfo->credit_item_loan_amount ?? null,
            'credit_term' => $application->parentalCreditInfo->credit_term ?? null,
            'credit_date' => $application->parentalCreditInfo->credit_date ?? null,
            'credit_balance' => $application->parentalCreditInfo->credit_balance ?? null,
            'references_full_name' => $application->parentalCreditInfo->references_full_name ?? null,
            'references_relationship' => $application->parentalCreditInfo->references_relationship ?? null,
            'references_tel_no' => $application->parentalCreditInfo->references_tel_no ?? null,
            'references_address' => $application->parentalCreditInfo->references_address ?? null,
            'source_of_income' => !empty($application->parentalCreditInfo->source_of_income) ? 
                (is_string($application->parentalCreditInfo->source_of_income) ? 
                    json_decode($application->parentalCreditInfo->source_of_income, true) : 
                    $application->parentalCreditInfo->source_of_income) : 
                [],
            
            // Step 4
            'applicant_employer' => $application->employmentPaymentDetail->applicant_employer ?? null,
            'applicant_position' => $application->employmentPaymentDetail->applicant_position ?? null,
            'applicant_block_street' => $application->employmentPaymentDetail->applicant_block_street ?? null,
            'applicant_zone_purok' => $application->employmentPaymentDetail->applicant_zone_purok ?? null,
            'applicant_barangay' => $application->employmentPaymentDetail->applicant_barangay ?? null,
            'applicant_municipality_city' => $application->employmentPaymentDetail->applicant_municipality_city ?? null,
            'applicant_province' => $application->employmentPaymentDetail->applicant_province ?? null,
            'applicant_telno' => $application->employmentPaymentDetail->applicant_telno ?? null,
            'applicant_date_started' => $application->employmentPaymentDetail->applicant_date_started ?? null,
            'applicant_name_immediate' => $application->employmentPaymentDetail->applicant_name_immediate ?? null,
            'applicant_employer_mobile_no' => $ensureString($application->employmentPaymentDetail->applicant_employer_mobile_no),
            'applicant_salary_gross' => $application->employmentPaymentDetail->applicant_salary_gross ?? null,

            'spouse_employer' => $application->employmentPaymentDetail->spouse_employer ?? null,
            'spouse_position' => $application->employmentPaymentDetail->spouse_position ?? null,
            'spouse_block_street' => $application->employmentPaymentDetail->spouse_block_street ?? null,
            'spouse_zone_purok' => $application->employmentPaymentDetail->spouse_zone_purok ?? null,
            'spouse_barangay' => $application->employmentPaymentDetail->spouse_barangay ?? null,
            'spouse_municipality' => $application->employmentPaymentDetail->spouse_municipality ?? null,
            'spouse_province' => $application->employmentPaymentDetail->spouse_province ?? null,
            'spouse_telno' => $application->employmentPaymentDetail->spouse_telno ?? null,
            'spouse_date_started' => $application->employmentPaymentDetail->spouse_date_started ?? null,
            'spouse_name_immediate' => $application->employmentPaymentDetail->spouse_name_immediate ?? null,
            'spouse_employer_mobile_no' => $ensureString($application->employmentPaymentDetail->spouse_employer_mobile_no),
            'spouse_salary_gross' => $application->employmentPaymentDetail->spouse_salary_gross ?? null,

            'personal_use' => $this->toBooleanValue($application->employmentPaymentDetail->personal_use ?? false),
            'business_use' => $this->toBooleanValue($application->employmentPaymentDetail->business_use ?? false),
            'gift' => $this->toBooleanValue($application->employmentPaymentDetail->gift ?? false),
            'use_by_relative' => $this->toBooleanValue($application->employmentPaymentDetail->use_by_relative ?? false),

            'post_dated_checks' => $this->toBooleanValue($application->employmentPaymentDetail->post_dated_checks ?? false),
            'cash_paid_to_office' => $this->toBooleanValue($application->employmentPaymentDetail->cash_paid_to_office ?? false),
            'cash_for_collection' => $this->toBooleanValue($application->employmentPaymentDetail->cash_for_collection ?? false),
            'credit_card' => $this->toBooleanValue($application->employmentPaymentDetail->credit_card ?? false),
            
            // Step 5
            'co_makers' => $application->coMakerEmploymentDetail->co_makers ?? null,
            
            // Step 6
            'sketch_residence_path' => $application->creditInquiryAuthorization->sketch_residence_path ?? null,
            'sketch_residence_url' => $application->creditInquiryAuthorization->sketch_residence_path ? 
                asset('storage/' . $application->creditInquiryAuthorization->sketch_residence_path) : null,
            'sketch_residence_comaker_path' => $application->creditInquiryAuthorization->sketch_residence_comaker_path ?? null,
            'sketch_residence_comaker_url' => $application->creditInquiryAuthorization->sketch_residence_comaker_path ? 
                asset('storage/' . $application->creditInquiryAuthorization->sketch_residence_comaker_path) : null,
            'applicant_signature_path' => $application->creditInquiryAuthorization->applicant_signature_path ?? null,
            'applicant_signature_url' => $application->creditInquiryAuthorization->applicant_signature_path ? 
                asset('storage/' . $application->creditInquiryAuthorization->applicant_signature_path) : null,
            'spouse_signature_path' => $application->creditInquiryAuthorization->spouse_signature_path ?? null,
            'spouse_signature_url' => $application->creditInquiryAuthorization->spouse_signature_path ? 
                asset('storage/' . $application->creditInquiryAuthorization->spouse_signature_path) : null,
            'comaker_signature_path' => $application->creditInquiryAuthorization->comaker_signature_path ?? null,
            'comaker_signature_url' => $application->creditInquiryAuthorization->comaker_signature_path ? 
                asset('storage/' . $application->creditInquiryAuthorization->comaker_signature_path) : null,
        ];
    }
    
    /**
     * Update the Employment Payment Details for an application
     */
    public function updateEmploymentPaymentDetails(Request $request, $id)
    {
        try {
            
            // Validate the incoming request
            $validated = $request->validate([
                // Applicant Employer Information
                'applicant_employer' => 'sometimes|nullable|string',
                'applicant_position' => 'sometimes|nullable|string',
                'applicant_block_street' => 'sometimes|nullable|string',
                'applicant_zone_purok' => 'sometimes|nullable|string',
                'applicant_barangay' => 'sometimes|nullable|string',
                'applicant_municipality_city' => 'sometimes|nullable|string',
                'applicant_province' => 'sometimes|nullable|string',
                'applicant_telno' => 'sometimes|nullable|string',
                'applicant_date_started' => 'sometimes|nullable|date',
                'applicant_name_immediate' => 'sometimes|nullable|string',
                'applicant_employer_mobile_no' => 'sometimes|nullable|string',
                'applicant_salary_gross' => 'sometimes|nullable|numeric',
                
                // Spouse Employer Information
                'spouse_employer' => 'sometimes|nullable|string',
                'spouse_position' => 'sometimes|nullable|string',
                'spouse_block_street' => 'sometimes|nullable|string',
                'spouse_zone_purok' => 'sometimes|nullable|string',
                'spouse_barangay' => 'sometimes|nullable|string',
                'spouse_municipality' => 'sometimes|nullable|string',
                'spouse_province' => 'sometimes|nullable|string',
                'spouse_telno' => 'sometimes|nullable|string',
                'spouse_date_started' => 'sometimes|nullable|date',
                'spouse_name_immediate' => 'sometimes|nullable|string',
                'spouse_employer_mobile_no' => 'sometimes|nullable|string',
                'spouse_salary_gross' => 'sometimes|nullable|numeric',
                
                // Unit to be Used For
                'personal_use' => 'sometimes|boolean',
                'business_use' => 'sometimes|boolean',
                'gift' => 'sometimes|boolean',
                'use_by_relative' => 'sometimes|boolean',
                
                // Mode of Payment
                'post_dated_checks' => 'sometimes|boolean',
                'cash_paid_to_office' => 'sometimes|boolean',
                'cash_for_collection' => 'sometimes|boolean',
                'credit_card' => 'sometimes|boolean',
            ]);

            // Find the employment payment detail by application ID
            $employmentDetail = \App\Models\EmploymentPaymentDetail::where('application_request_id', $id)->firstOrFail();
            
            // Create update data array with special handling for mobile numbers
            $updateData = $validated;
            
            // Force empty string for mobile numbers when null, undefined or not provided
            // This ensures consistent data storage and retrieval
            if (array_key_exists('applicant_employer_mobile_no', $updateData)) {
                $updateData['applicant_employer_mobile_no'] = (string)($updateData['applicant_employer_mobile_no'] ?? '');
                
            }
            
            if (array_key_exists('spouse_employer_mobile_no', $updateData)) {
                $updateData['spouse_employer_mobile_no'] = (string)($updateData['spouse_employer_mobile_no'] ?? '');
            
            }
            
           
            
            // Update the employment detail
            $employmentDetail->update($updateData);

            // Force refresh from database after update to ensure we get the actual stored values
            $employmentDetail = $employmentDetail->fresh();

            // Get the updated application with all details
            $application = ApplicationRequest::with([
                'personalAddressInfo',
                'personalFamilyProfile',
                'parentalCreditInfo',
                'employmentPaymentDetail',
                'coMakerEmploymentDetail',
                'creditInquiryAuthorization'
            ])->findOrFail($id);
            
            
            // Get application data for response, with explicit handling for mobile fields
            $responseData = $this->getApplicationWithDetails($application);
            
            // Explicitly force mobile numbers to strings for the response
            $responseData['applicant_employer_mobile_no'] = (string)($employmentDetail->applicant_employer_mobile_no ?? '');
            $responseData['spouse_employer_mobile_no'] = (string)($employmentDetail->spouse_employer_mobile_no ?? '');
            
            return response()->json([
                'success' => true,
                'message' => 'Employment payment details updated successfully',
                'data' => $responseData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employment payment details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to convert various value types to boolean
     */
    private function toBooleanValue($value)
    {
        if (is_string($value)) {
            return $value === '1' || strtolower($value) === 'true';
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Update personal and address information for an application
     */
    public function updatePersonalAddress(Request $request, $id)
    {
        try {
            $application = ApplicationRequest::findOrFail($id);
            $personalAddressInfo = $application->personalAddressInfo;

            if (!$personalAddressInfo) {
                return response()->json([
                    'message' => 'Personal address information not found'
                ], 404);
            }

            // Validate the incoming request
            $validated = $request->validate([
                'personal_first_name' => 'sometimes|string',
                'personal_middle_name' => 'sometimes|nullable|string',
                'personal_last_name' => 'sometimes|string',
                'personal_age' => 'sometimes|integer',
                'personal_nb_rb' => 'sometimes|nullable|string',
                'personal_sex' => 'sometimes|nullable|string|in:Male,Female',
                'personal_citizenship' => 'sometimes|nullable|string',
                'personal_birth_date' => 'sometimes|nullable|date',
                'personal_religion' => 'sometimes|nullable|string',
                'personal_civil_status' => 'sometimes|nullable|string|in:Single,Married,Separated,Widowed',
                'personal_tin' => 'sometimes|nullable|string',
                'personal_res_cert_no' => 'sometimes|nullable|string',
                'personal_date_issued' => 'sometimes|nullable|date',
                'personal_place_issued' => 'sometimes|nullable|string',
                
                // Present Address
                'present_block_street' => 'sometimes|nullable|string',
                'present_zone_purok' => 'sometimes|nullable|string',
                'present_barangay' => 'sometimes|nullable|string',
                'present_municipality_city' => 'sometimes|nullable|string',
                'present_province' => 'sometimes|nullable|string',
                'present_length_of_stay' => 'sometimes|nullable|string',
                'present_house_ownership' => 'sometimes|nullable|string|in:Owned,Rented,Mortgaged',
                'present_lot_ownership' => 'sometimes|nullable|string|in:Owned,Rented,Mortgaged',
                'present_other_properties' => 'sometimes|nullable|array',
                'present_other_properties.*' => 'string',
                
                // Provincial Address
                'provincial_block_street' => 'sometimes|nullable|string',
                'provincial_zone_purok' => 'sometimes|nullable|string',
                'provincial_barangay' => 'sometimes|nullable|string',
                'provincial_municipality_city' => 'sometimes|nullable|string',
                'provincial_province' => 'sometimes|nullable|string',
            ]);

            // Update only the fields that were provided
            $personalAddressInfo->update($validated);

            // Return the updated application data
            return response()->json($this->getApplicationWithDetails($application));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update personal address information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update personal and family information for an application
     */
    public function updatePersonalFamily(Request $request, $id)
    {
        try {
            // Log the request data
            Log::info('Personal & Family Profile Update Request Data:', $request->all());
            
            // Validate the request data
            $validated = $request->validate([
                'contact_home_phone' => 'required|string',
                'contact_office_phone' => 'required|string',
                'contact_mobile_phone' => 'required|string',
                'contact_email' => 'required|email',
                'contact_spouse_name' => 'nullable|string',
                'contact_age' => 'nullable|string',
                'contact_dependents' => 'nullable|string',
                'contact_provincial_spouse' => 'nullable|string',
                'contact_mobile_no' => 'required|string',
                'information_email' => 'required|email',
                'dependents_info' => 'nullable|json',
                
                // Applicant's Parents
                'applicant_father_name' => 'nullable|string',
                'applicant_mother_name' => 'nullable|string',
                'applicant_occupation' => 'nullable|string',
                'applicant_mobile_no' => 'nullable|string',
                'applicant_address' => 'nullable|string',
                
                // Spouse's Parents
                'spouse_father_name' => 'nullable|string',
                'spouse_mother_name' => 'nullable|string',
                'spouse_occupation' => 'nullable|string',
                'spouse_mobile_no' => 'nullable|string',
                'spouse_address' => 'nullable|string',
            ]);
            
            // Find the application request
            $applicationRequest = ApplicationRequest::findOrFail($id);
            
            // Get or create the PersonalFamilyProfile
            $personalFamilyProfile = $applicationRequest->personalFamilyProfile;
            if (!$personalFamilyProfile) {
                $personalFamilyProfile = new PersonalFamilyProfile();
                $personalFamilyProfile->application_request_id = $applicationRequest->id;
            }
            
            // Update the fields from the validated data
            $personalFamilyProfile->fill($validated);
            
            // Save the model
            $personalFamilyProfile->save();
            
            // Log success
            Log::info('Personal & Family Profile updated successfully', [
                'application_id' => $id, 
                'profile_id' => $personalFamilyProfile->id
            ]);
            
            // Get updated data for response
            $updatedData = $this->getApplicationWithDetails($applicationRequest);
            
            return response()->json([
                'success' => true, 
                'message' => 'Personal & family profile updated successfully', 
                'data' => $updatedData
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating personal & family profile: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to update personal & family profile. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update parental and credit information for an application
     */
    public function updateParentalCredit(Request $request, $id)
    {
        try {
            $application = ApplicationRequest::findOrFail($id);
            $parentalCreditInfo = $application->parentalCreditInfo;

            if (!$parentalCreditInfo) {
                return response()->json([
                    'message' => 'Parental credit information not found'
                ], 404);
            }

            // Validate the incoming request
            $validated = $request->validate([
                'credit_store_bank' => 'sometimes|nullable|string',
                'credit_item_loan_amount' => 'sometimes|nullable|string',
                'credit_term' => 'sometimes|nullable|string',
                'credit_date' => 'sometimes|nullable|string',
                'credit_balance' => 'sometimes|nullable|string',
                'references_full_name' => 'sometimes|nullable|string',
                'references_relationship' => 'sometimes|nullable|string',
                'references_tel_no' => 'sometimes|nullable|string',
                'references_address' => 'sometimes|nullable|string',
                'source_of_income' => 'sometimes|nullable|array',
                'source_of_income.*' => 'string',
            ]);

            // Update only the fields that were provided
            $parentalCreditInfo->update($validated);

            // Return the updated application data
            return response()->json($this->getApplicationWithDetails($application));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update parental credit information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update co-maker details for an application
     */
    public function updateCoMakerDetails(Request $request, $id)
    {
        try {
            $application = ApplicationRequest::findOrFail($id);
            $coMakerDetail = $application->coMakerEmploymentDetail;

            if (!$coMakerDetail) {
                return response()->json([
                    'message' => 'Co-maker details not found'
                ], 404);
            }

            // Validate the incoming request
            $validated = $request->validate([
                'co_makers' => 'required|array',
                'co_makers.*.firstName' => 'required|string',
                'co_makers.*.age' => 'required|string',
                'co_makers.*.sex' => 'required|string',
                'co_makers.*.civilStatus' => 'required|string',
                'co_makers.*.blockStreet' => 'required|string',
                'co_makers.*.zonePurok' => 'required|string',
                'co_makers.*.barangay' => 'required|string',
                'co_makers.*.municipalityCity' => 'required|string',
                'co_makers.*.province' => 'required|string',
                'co_makers.*.lengthOfStay' => 'required|string',
                'co_makers.*.residence' => 'required|string',
                'co_makers.*.makerBlockStreet' => 'required|string',
                'co_makers.*.makerZonePurok' => 'required|string',
                'co_makers.*.makerBarangay' => 'required|string',
                'co_makers.*.makerMunicipalityCity' => 'required|string',
                'co_makers.*.makerProvince' => 'required|string',
                'co_makers.*.relationshipWithApplicant' => 'required|string',
                'co_makers.*.birthday' => 'required|string',
                'co_makers.*.tin' => 'required|string',
                'co_makers.*.mobileNo' => 'required|string',
                'co_makers.*.presentEmployer' => 'required|string',
                'co_makers.*.dateHired' => 'required|string',
                'co_makers.*.position' => 'required|string',
                'co_makers.*.grossIncome' => 'required|string',
                'co_makers.*.employerAddress' => 'required|string',
                'co_makers.*.employmentStatus' => 'required|string',
                'co_makers.*.creditReferences' => 'required|string',
            ]);

            // Update the co-maker details
            $coMakerDetail->update([
                'co_makers' => $validated['co_makers']
            ]);

            // Return the updated application data
            return response()->json([
                'success' => true,
                'message' => 'Co-maker details updated successfully',
                'data' => $this->getApplicationWithDetails($application)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update co-maker details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}