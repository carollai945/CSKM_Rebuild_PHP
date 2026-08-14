<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * A00 個人資料維護
 *
 * 功能編號：A00
 * 對應文件：docs/sdd/a00-personal-data-sdd.md
 */
class PersonalDataController extends Controller
{
    private function formatStaff(Staff $staff, string $status, array $actions): array
    {
        return [
            'id' => $staff->id,
            'staff_no' => $staff->staff_no,
            'name' => $staff->name,
            'phone' => $staff->phone,
            'gender' => $staff->gender,
            'blood_type' => $staff->blood_type,
            'birth_date' => $staff->birth_date?->toDateString(),
            'join_date' => $staff->join_date?->toDateString(),
            'photo_url' => $staff->photo_url,
            'registered_address' => $this->formatAddress($staff->registered_address),
            'mailing_address' => $this->formatAddress($staff->mailing_address, true),
            'language_abilities' => $this->formatLanguageAbilities($staff->language_abilities),
            'skills' => $this->formatSkills($staff->skills),
            'certifications' => $this->formatCertifications($staff->certifications),
            'family_information' => $this->formatFamilyInformation($staff->family_information),
            'work_experiences' => $this->formatWorkExperiences($staff->work_experiences),
            'handbook_url' => '/staff.pdf',
            'region' => $staff->region?->only(['id', 'name']),
            'department' => $staff->department?->only(['id', 'name']),
            'title' => $staff->title?->only(['id', 'name']),
            'status' => $staff->status,
            'currentStatus' => $status,
            'allowedActions' => $actions,
        ];
    }

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $staff = Staff::where('user_id', $user->id)->with(['region', 'department', 'title'])->firstOrFail();

        return response()->json(['data' => $this->formatStaff($staff, 'EDITABLE', ['SAVE', 'UPLOAD_PHOTO'])]);
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:M,F,OTHER',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'birth_date' => 'nullable|date',
            'registered_address' => 'nullable|array',
            'registered_address.postal_code' => 'nullable|string|max:10',
            'registered_address.city' => 'nullable|string|max:100',
            'registered_address.district' => 'nullable|string|max:100',
            'registered_address.address_line' => 'nullable|string|max:255',
            'mailing_address' => 'nullable|array',
            'mailing_address.postal_code' => 'nullable|string|max:10',
            'mailing_address.city' => 'nullable|string|max:100',
            'mailing_address.district' => 'nullable|string|max:100',
            'mailing_address.address_line' => 'nullable|string|max:255',
            'mailing_address.same_as_registered' => 'nullable|boolean',
            'language_abilities' => 'nullable|array',
            'language_abilities.english' => 'nullable|string|max:50',
            'language_abilities.japanese' => 'nullable|string|max:50',
            'language_abilities.other_languages' => 'nullable|array',
            'language_abilities.other_languages.*.language' => 'required|string|max:50',
            'language_abilities.other_languages.*.speaking' => 'nullable|string|max:50',
            'language_abilities.other_languages.*.reading' => 'nullable|string|max:50',
            'language_abilities.other_languages.*.writing' => 'nullable|string|max:50',
            'language_abilities.other_languages.*.notes' => 'nullable|string|max:255',
            'skills' => 'nullable|array',
            'skills.office_software' => 'nullable|array',
            'skills.office_software.*' => 'string|max:50',
            'skills.programming_languages' => 'nullable|array',
            'skills.programming_languages.*' => 'string|max:50',
            'skills.professional_skills' => 'nullable|array',
            'skills.professional_skills.*' => 'string|max:50',
            'skills.self_evaluation' => 'nullable|string|max:1000',
            'certifications' => 'nullable|array',
            'certifications.english' => 'nullable|array',
            'certifications.english.*.name' => 'required|string|max:100',
            'certifications.english.*.score' => 'nullable|string|max:50',
            'certifications.english.*.issued_by' => 'nullable|string|max:100',
            'certifications.english.*.acquired_on' => 'nullable|date',
            'certifications.english.*.notes' => 'nullable|string|max:255',
            'certifications.japanese' => 'nullable|array',
            'certifications.japanese.*.name' => 'required|string|max:100',
            'certifications.japanese.*.score' => 'nullable|string|max:50',
            'certifications.japanese.*.issued_by' => 'nullable|string|max:100',
            'certifications.japanese.*.acquired_on' => 'nullable|date',
            'certifications.japanese.*.notes' => 'nullable|string|max:255',
            'certifications.professional' => 'nullable|array',
            'certifications.professional.*.name' => 'required|string|max:100',
            'certifications.professional.*.license_no' => 'nullable|string|max:50',
            'certifications.professional.*.issued_by' => 'nullable|string|max:100',
            'certifications.professional.*.acquired_on' => 'nullable|date',
            'certifications.professional.*.expires_on' => 'nullable|date',
            'certifications.professional.*.notes' => 'nullable|string|max:255',
            'family_information' => 'nullable|array',
            'family_information.*.name' => 'required|string|max:100',
            'family_information.*.relationship' => 'required|string|max:50',
            'family_information.*.occupation' => 'nullable|string|max:100',
            'family_information.*.phone' => 'nullable|string|max:20',
            'family_information.*.is_emergency_contact' => 'nullable|boolean',
            'work_experiences' => 'nullable|array',
            'work_experiences.*.company_name' => 'required|string|max:150',
            'work_experiences.*.position' => 'nullable|string|max:100',
            'work_experiences.*.start_date' => 'nullable|date',
            'work_experiences.*.end_date' => 'nullable|date',
            'work_experiences.*.responsibilities' => 'nullable|string|max:1000',
        ]);

        $staff = Staff::where('user_id', $request->user()->id)->firstOrFail();

        $payload = [];
        foreach (['name', 'phone', 'gender', 'blood_type', 'birth_date'] as $key) {
            if (array_key_exists($key, $validated)) {
                $payload[$key] = $validated[$key];
            }
        }

        if ($request->exists('registered_address')) {
            $payload['registered_address'] = $this->normalizeAddress($validated['registered_address'] ?? null);
        }

        if ($request->exists('mailing_address')) {
            $baseRegisteredAddress = $payload['registered_address'] ?? $staff->registered_address;
            $payload['mailing_address'] = $this->normalizeAddress(
                $validated['mailing_address'] ?? null,
                includeSameAs: true,
                registeredAddress: $baseRegisteredAddress,
            );
        }

        if ($request->exists('language_abilities')) {
            $payload['language_abilities'] = $this->normalizeLanguageAbilities($validated['language_abilities'] ?? null);
        }

        if ($request->exists('skills')) {
            $payload['skills'] = $this->normalizeSkills($validated['skills'] ?? null);
        }

        if ($request->exists('certifications')) {
            $payload['certifications'] = $this->normalizeCertifications($validated['certifications'] ?? null);
        }

        if ($request->exists('family_information')) {
            $payload['family_information'] = $this->normalizeFamilyInformation($validated['family_information'] ?? null);
        }

        if ($request->exists('work_experiences')) {
            $payload['work_experiences'] = $this->normalizeWorkExperiences($validated['work_experiences'] ?? null);
        }

        $staff->update($payload);

        return response()->json([
            'data' => $this->formatStaff(
                $staff->fresh()->load(['region', 'department', 'title']),
                'EDITABLE',
                ['SAVE', 'UPLOAD_PHOTO'],
            ),
        ]);
    }

    public function showByStaffId(Request $request, Staff $staff): JsonResponse
    {
        Gate::authorize('management');

        $staff->load(['region', 'department', 'title']);

        return response()->json(['data' => $this->formatStaff($staff, 'READONLY', [])]);
    }

    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $staff = Staff::where('user_id', $request->user()->id)->firstOrFail();

        $file = $request->file('photo');
        $filename = 'staff_' . $staff->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('photos', $filename, 'public');
        $photoUrl = '/storage/' . $path;

        $staff->update(['photo_url' => $photoUrl]);

        return response()->json([
            'data' => [
                'photo_url' => $photoUrl,
            ],
        ]);
    }

    private function formatAddress(?array $address, bool $includeSameAs = false): array
    {
        $normalized = $this->normalizeAddress($address, $includeSameAs) ?? [];

        return [
            'postal_code' => $normalized['postal_code'] ?? null,
            'city' => $normalized['city'] ?? null,
            'district' => $normalized['district'] ?? null,
            'address_line' => $normalized['address_line'] ?? null,
            ...($includeSameAs ? ['same_as_registered' => (bool) ($normalized['same_as_registered'] ?? false)] : []),
        ];
    }

    private function normalizeAddress(?array $address, bool $includeSameAs = false, ?array $registeredAddress = null): ?array
    {
        $normalized = [
            'postal_code' => $this->normalizeString($address['postal_code'] ?? null),
            'city' => $this->normalizeString($address['city'] ?? null),
            'district' => $this->normalizeString($address['district'] ?? null),
            'address_line' => $this->normalizeString($address['address_line'] ?? null),
        ];

        if ($includeSameAs) {
            $sameAsRegistered = (bool) ($address['same_as_registered'] ?? false);
            if ($sameAsRegistered && is_array($registeredAddress)) {
                $registeredNormalized = $this->normalizeAddress($registeredAddress) ?? [];
                $normalized = [
                    'postal_code' => $registeredNormalized['postal_code'] ?? null,
                    'city' => $registeredNormalized['city'] ?? null,
                    'district' => $registeredNormalized['district'] ?? null,
                    'address_line' => $registeredNormalized['address_line'] ?? null,
                ];
            }
            $normalized['same_as_registered'] = $sameAsRegistered;
        }

        $hasValue = collect($normalized)
            ->reject(fn ($value, string $key) => $key === 'same_as_registered')
            ->filter(fn ($value) => $value !== null)
            ->isNotEmpty();

        if (! $hasValue && (! $includeSameAs || ! ($normalized['same_as_registered'] ?? false))) {
            return null;
        }

        return $normalized;
    }

    private function formatLanguageAbilities(?array $abilities): array
    {
        $normalized = $this->normalizeLanguageAbilities($abilities) ?? [];

        return [
            'english' => $normalized['english'] ?? null,
            'japanese' => $normalized['japanese'] ?? null,
            'other_languages' => $normalized['other_languages'] ?? [],
        ];
    }

    private function normalizeLanguageAbilities(?array $abilities): ?array
    {
        $english = $this->normalizeString($abilities['english'] ?? null);
        $japanese = $this->normalizeString($abilities['japanese'] ?? null);
        $otherLanguages = [];

        foreach (($abilities['other_languages'] ?? []) as $row) {
            $normalized = [
                'language' => $this->normalizeString($row['language'] ?? null),
                'speaking' => $this->normalizeString($row['speaking'] ?? null),
                'reading' => $this->normalizeString($row['reading'] ?? null),
                'writing' => $this->normalizeString($row['writing'] ?? null),
                'notes' => $this->normalizeString($row['notes'] ?? null),
            ];

            if (collect($normalized)->filter(fn ($value) => $value !== null)->isNotEmpty()) {
                $otherLanguages[] = $normalized;
            }
        }

        if ($english === null && $japanese === null && $otherLanguages === []) {
            return null;
        }

        return [
            'english' => $english,
            'japanese' => $japanese,
            'other_languages' => $otherLanguages,
        ];
    }

    private function formatSkills(?array $skills): array
    {
        $normalized = $this->normalizeSkills($skills) ?? [];

        return [
            'office_software' => $normalized['office_software'] ?? [],
            'programming_languages' => $normalized['programming_languages'] ?? [],
            'professional_skills' => $normalized['professional_skills'] ?? [],
            'self_evaluation' => $normalized['self_evaluation'] ?? null,
        ];
    }

    private function normalizeSkills(?array $skills): ?array
    {
        $normalized = [
            'office_software' => $this->normalizeStringList($skills['office_software'] ?? []),
            'programming_languages' => $this->normalizeStringList($skills['programming_languages'] ?? []),
            'professional_skills' => $this->normalizeStringList($skills['professional_skills'] ?? []),
            'self_evaluation' => $this->normalizeString($skills['self_evaluation'] ?? null),
        ];

        if ($normalized['office_software'] === []
            && $normalized['programming_languages'] === []
            && $normalized['professional_skills'] === []
            && $normalized['self_evaluation'] === null) {
            return null;
        }

        return $normalized;
    }

    private function formatCertifications(?array $certifications): array
    {
        $normalized = $this->normalizeCertifications($certifications) ?? [];

        return [
            'english' => $normalized['english'] ?? [],
            'japanese' => $normalized['japanese'] ?? [],
            'professional' => $normalized['professional'] ?? [],
        ];
    }

    private function normalizeCertifications(?array $certifications): ?array
    {
        $english = $this->normalizeCertificationEntries($certifications['english'] ?? []);
        $japanese = $this->normalizeCertificationEntries($certifications['japanese'] ?? []);
        $professional = $this->normalizeCertificationEntries($certifications['professional'] ?? [], true);

        if ($english === [] && $japanese === [] && $professional === []) {
            return null;
        }

        return [
            'english' => $english,
            'japanese' => $japanese,
            'professional' => $professional,
        ];
    }

    private function normalizeCertificationEntries(array $entries, bool $includeExpiry = false): array
    {
        $normalizedEntries = [];

        foreach ($entries as $row) {
            $normalized = [
                'name' => $this->normalizeString($row['name'] ?? null),
                ...($includeExpiry ? ['license_no' => $this->normalizeString($row['license_no'] ?? null)] : ['score' => $this->normalizeString($row['score'] ?? null)]),
                'issued_by' => $this->normalizeString($row['issued_by'] ?? null),
                'acquired_on' => $this->normalizeString($row['acquired_on'] ?? null),
                ...($includeExpiry ? ['expires_on' => $this->normalizeString($row['expires_on'] ?? null)] : []),
                'notes' => $this->normalizeString($row['notes'] ?? null),
            ];

            if (collect($normalized)->filter(fn ($value) => $value !== null)->isNotEmpty()) {
                $normalizedEntries[] = $normalized;
            }
        }

        return $normalizedEntries;
    }

    private function formatFamilyInformation(?array $familyInformation): array
    {
        return $this->normalizeFamilyInformation($familyInformation) ?? [];
    }

    private function normalizeFamilyInformation(?array $familyInformation): ?array
    {
        $normalizedEntries = [];

        foreach ($familyInformation ?? [] as $row) {
            $normalized = [
                'name' => $this->normalizeString($row['name'] ?? null),
                'relationship' => $this->normalizeString($row['relationship'] ?? null),
                'occupation' => $this->normalizeString($row['occupation'] ?? null),
                'phone' => $this->normalizeString($row['phone'] ?? null),
                'is_emergency_contact' => (bool) ($row['is_emergency_contact'] ?? false),
            ];

            if (collect($normalized)
                ->reject(fn ($value, string $key) => $key === 'is_emergency_contact')
                ->filter(fn ($value) => $value !== null)
                ->isNotEmpty()) {
                $normalizedEntries[] = $normalized;
            }
        }

        return $normalizedEntries === [] ? null : $normalizedEntries;
    }

    private function formatWorkExperiences(?array $workExperiences): array
    {
        return $this->normalizeWorkExperiences($workExperiences) ?? [];
    }

    private function normalizeWorkExperiences(?array $workExperiences): ?array
    {
        $normalizedEntries = [];

        foreach ($workExperiences ?? [] as $row) {
            $normalized = [
                'company_name' => $this->normalizeString($row['company_name'] ?? null),
                'position' => $this->normalizeString($row['position'] ?? null),
                'start_date' => $this->normalizeString($row['start_date'] ?? null),
                'end_date' => $this->normalizeString($row['end_date'] ?? null),
                'responsibilities' => $this->normalizeString($row['responsibilities'] ?? null),
            ];

            if (collect($normalized)->filter(fn ($value) => $value !== null)->isNotEmpty()) {
                $normalizedEntries[] = $normalized;
            }
        }

        return $normalizedEntries === [] ? null : $normalizedEntries;
    }

    private function normalizeStringList(array $values): array
    {
        return array_values(array_unique(array_filter(array_map(
            fn ($value) => $this->normalizeString($value),
            $values,
        ))));
    }

    private function normalizeString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
