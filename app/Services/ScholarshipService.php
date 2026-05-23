<?php

namespace App\Services;

use App\Models\Scholarship\Scholarship;
use App\Repositories\Interfaces\ScholarshipRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\Intl\Countries;

class ScholarshipService
{
    public function __construct(private ScholarshipRepositoryInterface $repo) {}

    /**
     * Returns ISO alpha-2 code → English country name (249 entries).
     * Backed by Symfony Intl (ICU/CLDR data, Unicode license — open-source).
     */
    public function countryList(): array
    {
        return Countries::getNames();
    }

    public function countryName(string $code): ?string
    {
        $code = strtoupper($code);
        return Countries::exists($code) ? Countries::getName($code) : null;
    }

    public function create(array $data, int $userId): Scholarship
    {
        $clean = $this->validateAndPrepare($data);
        $clean['created_by'] = $userId;
        return $this->repo->create($clean);
    }

    public function update(int $id, array $data): Scholarship
    {
        $clean = $this->validateAndPrepare($data, $id);
        return $this->repo->update($id, $clean);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    private function validateAndPrepare(array $data, ?int $id = null): array
    {
        $validated = Validator::make($data, [
            'title'                => 'required|string|max:220',
            'type'                 => 'required|string|max:50',
            'country_code'         => 'nullable|string|size:2',
            'funding_type'         => 'required|in:fully_funded,partially_funded',
            'program_level'        => 'nullable|in:undergraduate,masters,doctoral',
            'deadline'             => 'nullable|date',
            'short_description'    => 'nullable|string|max:1000',
            'eligibility_criteria' => 'nullable|string',
            'benefits'             => 'nullable|string',
            'required_documents'   => 'nullable|string',
            'official_link_1'      => 'nullable|url|max:500',
            'official_link_2'      => 'nullable|url|max:500',
            'is_active'            => 'sometimes|boolean',
            'is_featured'          => 'sometimes|boolean',
        ])->validate();

        // Resolve the country name from the ISO code (always trust our list, not user input)
        $code = $validated['country_code'] ?? null;
        if ($code) {
            $code = strtoupper($code);
            $validated['country_code'] = Countries::exists($code) ? $code : null;
            $validated['country_name'] = $validated['country_code']
                ? Countries::getName($validated['country_code'])
                : null;
        } else {
            $validated['country_name'] = null;
        }

        $validated['is_active']   = (bool) ($data['is_active']   ?? true);
        $validated['is_featured'] = (bool) ($data['is_featured'] ?? false);

        return $validated;
    }
}
