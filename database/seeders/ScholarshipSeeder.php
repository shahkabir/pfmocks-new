<?php

namespace Database\Seeders;

use App\Models\Scholarship\Scholarship;
use Illuminate\Database\Seeder;
use Symfony\Component\Intl\Countries;

/**
 * Seeds 4 sample scholarships covering different types, countries, and funding tiers.
 *
 * Run:  D:/php-8.2/php.exe artisan db:seed --class=ScholarshipSeeder
 */
class ScholarshipSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title'         => 'Chevening Scholarships 2027 (UK)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'GB',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(45)->toDateString(),
                'is_featured'   => true,
                'short_description' => 'The UK government\'s flagship scholarship — one year of master\'s study at any UK university for emerging global leaders.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a citizen of a Chevening-eligible country.</li>
  <li>Hold an undergraduate degree equivalent to UK upper-second-class honours.</li>
  <li>Have at least <strong>two years (2,800 hours)</strong> of work experience.</li>
  <li>Apply to and receive an unconditional offer from three eligible UK universities.</li>
  <li>Return to your home country for at least two years after the award ends.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition fees covered.</li>
  <li>Monthly living stipend.</li>
  <li>Travel costs to and from the UK.</li>
  <li>Arrival allowance, homeward departure allowance, visa application costs.</li>
  <li>Exclusive access to Chevening networking and leadership events.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Valid passport / national ID</li>
  <li>University transcripts (all degrees)</li>
  <li>Two reference letters</li>
  <li>Unconditional offer letters from three UK universities (by July deadline)</li>
  <li>Four Chevening essays (leadership, networking, studying in the UK, career plan)</li>
</ol>
HTML,
                'official_link_1' => 'https://www.chevening.org/',
                'official_link_2' => 'https://www.chevening.org/scholarship/bangladesh/',
            ],

            [
                'title'         => 'DAAD Scholarships for International Students (Germany)',
                'type'          => 'scholarships',
                'country_code'  => 'DE',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(90)->toDateString(),
                'is_featured'   => true,
                'short_description' => 'DAAD funds international postgraduate study in Germany across virtually every discipline — full coverage of fees, living costs, and travel.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>A bachelor\'s degree (in some programs, a master\'s) with above-average results.</li>
  <li>Less than six years since the most recent qualifying degree.</li>
  <li>Demonstrable language proficiency (German or English depending on the program).</li>
  <li>Strong motivation and an aligned research / study proposal.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Monthly stipend of €934 (postgraduates) or €1,300+ (doctoral).</li>
  <li>Tuition and semester contribution fees.</li>
  <li>Health, accident, personal-liability insurance.</li>
  <li>Travel allowance.</li>
  <li>Study and research subsidy.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>DAAD application form</li>
  <li>CV in Europass format</li>
  <li>Letter of motivation (1–2 pages)</li>
  <li>Academic transcripts and degree certificates</li>
  <li>Language certificate (IELTS / TOEFL / TestDaF / DSH)</li>
  <li>Two academic letters of recommendation</li>
</ol>
HTML,
                'official_link_1' => 'https://www.daad.de/en/study-and-research-in-germany/scholarships/',
                'official_link_2' => null,
            ],

            [
                'title'         => 'MIT Research Internship — MISTI Global Teaching Labs',
                'type'          => 'research-internship',
                'country_code'  => 'US',
                'funding_type'  => 'partially_funded',
                'program_level' => 'undergraduate',
                'deadline'      => now()->addDays(20)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'A short, project-based research and teaching placement at MIT — flights and accommodation covered, modest stipend for living costs.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Currently enrolled undergraduate (any year) in a STEM discipline.</li>
  <li>Strong English communication skills.</li>
  <li>Available for the entire 4–6 week placement window.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Round-trip travel covered.</li>
  <li>On-campus housing arranged.</li>
  <li>Stipend for incidentals.</li>
  <li>Mentorship from an MIT lab principal investigator.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>CV (max 2 pages)</li>
  <li>Current academic transcript</li>
  <li>Short statement of research interests (500 words)</li>
  <li>One faculty reference</li>
</ol>
HTML,
                'official_link_1' => 'https://misti.mit.edu/',
                'official_link_2' => null,
            ],

            [
                'title'         => 'MEXT PhD Fellowship (Japan)',
                'type'          => 'phd-fellowships',
                'country_code'  => 'JP',
                'funding_type'  => 'fully_funded',
                'program_level' => 'doctoral',
                'deadline'      => now()->addDays(160)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'Japanese government scholarship for international PhD candidates — 3 years of fully-funded doctoral research at a Japanese national / public university.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Under 35 years of age at the time of application.</li>
  <li>Hold a master\'s degree or equivalent.</li>
  <li>Specialise in a field offered by the host university.</li>
  <li>Be in good health and willing to learn Japanese.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Tuition fees waived in full.</li>
  <li>Monthly stipend of ¥145,000 – ¥148,000.</li>
  <li>Round-trip air ticket between home country and Japan.</li>
  <li>Optional Japanese language preparation course.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>MEXT application form</li>
  <li>Field of Study and Research Programme plan</li>
  <li>Bachelor\'s and master\'s transcripts and certificates</li>
  <li>Two recommendation letters</li>
  <li>Medical certificate (within 6 months)</li>
  <li>Abstracts of master\'s thesis and any published papers</li>
</ol>
HTML,
                'official_link_1' => 'https://www.studyinjapan.go.jp/en/planning/about-scholarship/mext/',
                'official_link_2' => null,
            ],
        ];

        foreach ($samples as $row) {
            // Resolve the country display name from the ISO code via Symfony Intl
            $row['country_name'] = $row['country_code']
                ? Countries::getName($row['country_code'])
                : null;
            $row['is_active'] = true;

            Scholarship::updateOrCreate(
                ['title' => $row['title']],   // idempotent on re-run
                $row
            );
        }

        $this->command?->info('Seeded ' . count($samples) . ' scholarships.');
    }
}
