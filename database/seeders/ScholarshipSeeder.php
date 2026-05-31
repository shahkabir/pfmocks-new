<?php

namespace Database\Seeders;

use App\Models\Scholarship\Scholarship;
use Illuminate\Database\Seeder;
use Symfony\Component\Intl\Countries;

/**
 * Seeds 20 sample scholarships covering different types, countries, and funding tiers.
 *
 * Run:  D:/php-8.2/php.exe artisan db:seed --class=ScholarshipSeeder
 */
class ScholarshipSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [

            // ─── 1. Chevening ─────────────────────────────────────────────────────────
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

            // ─── 2. Commonwealth Scholarship ──────────────────────────────────────────
            [
                'title'         => 'Commonwealth Scholarship 2027 (UK)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'GB',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(130)->toDateString(),
                'is_featured'   => true,
                'short_description' => 'UK government awards for citizens of developing Commonwealth countries to pursue master\'s or PhD study in the UK — focused on contributing to development in the scholar\'s home country.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a citizen of or hold refugee status from an eligible Commonwealth country.</li>
  <li>Be permanently resident in an eligible Commonwealth country.</li>
  <li>Hold a first degree of at least upper-second-class (2:1) honours, or a 2:2 plus a relevant postgraduate qualification.</li>
  <li>Not have studied or worked for more than one academic year in a high-income country.</li>
  <li>Be unable to afford to study in the UK without this scholarship.</li>
  <li>Demonstrate a clear development impact plan for your home country.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition fees (covered by an agreement with the UK university).</li>
  <li>Monthly living allowance of £1,378 (or £1,690 in London).</li>
  <li>Approved economy-class return airfare.</li>
  <li>Warm clothing allowance (where applicable).</li>
  <li>Study travel grant for course-related travel.</li>
  <li>Thesis grant and other supplemental allowances.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Completed online CSC application form</li>
  <li>Academic transcripts and degree certificates (all levels)</li>
  <li>Two academic reference letters</li>
  <li>Personal statement with development impact plan</li>
  <li>English language certificate (where applicable)</li>
  <li>Proof of admission offer from an eligible UK university</li>
</ol>
HTML,
                'official_link_1' => 'https://cscuk.fcdo.gov.uk/apply/',
                'official_link_2' => null,
            ],

            // ─── 3. Fulbright Program ─────────────────────────────────────────────────
            [
                'title'         => 'Fulbright Foreign Student Program (USA)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'US',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(120)->toDateString(),
                'is_featured'   => true,
                'short_description' => 'The US government\'s flagship international exchange — approximately 4,000 grants per year for non-US citizens to pursue master\'s degrees, PhDs, or non-degree research at accredited US universities.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a citizen of a Fulbright-eligible country (not a US citizen or permanent resident).</li>
  <li>Hold a bachelor\'s degree or equivalent (strong academic record required).</li>
  <li>Demonstrate leadership potential and commitment to community development.</li>
  <li>Agree to return to your home country after completing the programme.</li>
  <li>Meet country-specific GPA, language, and experience requirements set by the local Fulbright Commission.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition and university fees at the host institution.</li>
  <li>Monthly living stipend to cover accommodation and personal expenses.</li>
  <li>Round-trip airfare to and from the USA.</li>
  <li>Health insurance for the duration of the grant.</li>
  <li>Book and equipment allowance.</li>
  <li>Access to the global Fulbright alumni network.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Online application via local Fulbright Commission / US Embassy portal</li>
  <li>Academic transcripts and degree certificates</li>
  <li>Three reference letters (at least two academic)</li>
  <li>Statement of purpose / study objective essay</li>
  <li>English language proficiency test scores (TOEFL / IELTS — country-specific)</li>
  <li>CV / résumé</li>
</ol>
HTML,
                'official_link_1' => 'https://foreign.fulbrightonline.org/',
                'official_link_2' => null,
            ],

            // ─── 4. DAAD ─────────────────────────────────────────────────────────────
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

            // ─── 5. Erasmus Mundus ────────────────────────────────────────────────────
            [
                'title'         => 'Erasmus Mundus Joint Masters Scholarship (EU)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'EU',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(60)->toDateString(),
                'is_featured'   => true,
                'short_description' => 'EU-funded joint master\'s programmes delivered by consortia of top European universities — students study in at least two countries and receive full financial support regardless of nationality.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Hold a bachelor\'s degree in a relevant field (or be in the final year and graduate before the programme starts).</li>
  <li>Open to applicants of <strong>all nationalities</strong>.</li>
  <li>Must not have spent more than 12 months in any European country within the five years before the programme start.</li>
  <li>Meet the specific language and academic requirements of the chosen EMJM consortium.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition fees covered across all partner universities.</li>
  <li>Monthly living allowance (typically €1,000–€1,400/month).</li>
  <li>Travel and installation allowance.</li>
  <li>Study in at least two European countries within one integrated programme.</li>
  <li>Joint or multiple degree awarded upon completion.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Online application via the specific EMJM consortium portal</li>
  <li>Bachelor\'s degree certificate and transcripts</li>
  <li>Motivation letter (programme-specific format)</li>
  <li>Two academic reference letters</li>
  <li>Language proficiency certificate (IELTS / TOEFL / equivalent)</li>
  <li>CV in Europass or programme-specified format</li>
</ol>
HTML,
                'official_link_1' => 'https://www.eacea.ec.europa.eu/scholarships/erasmus-mundus-catalogue_en',
                'official_link_2' => null,
            ],

            // ─── 6. MEXT ─────────────────────────────────────────────────────────────
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

            // ─── 7. Global Korea Scholarship (GKS) ───────────────────────────────────
            [
                'title'         => 'Global Korea Scholarship — GKS Graduate (South Korea)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'KR',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(75)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'South Korean government scholarship (formerly KGSP) for international students pursuing master\'s or PhD degrees at top Korean universities, including one year of Korean language training.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a non-Korean citizen from an eligible country.</li>
  <li>For master\'s: under 40 years old; for undergraduate: under 25 years old.</li>
  <li>GPA of 80% or higher (or ranked in the top 20% of graduating class).</li>
  <li>Not have completed a degree in South Korea.</li>
  <li>Be in good physical and mental health.</li>
  <li>Apply via Embassy Track (up to 3 universities) or University Track (1 university).</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Round-trip economy airfare.</li>
  <li>Full tuition fees (up to 5 million KRW/semester; excess covered by university).</li>
  <li>One year of intensive Korean language training.</li>
  <li>Monthly allowance of KRW 900,000–1,500,000 depending on programme type.</li>
  <li>Settlement allowance upon arrival.</li>
  <li>Medical insurance.</li>
  <li>Research support allowance for thesis/fieldwork.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>GKS application form (via Study in Korea system)</li>
  <li>Degree certificates and official transcripts</li>
  <li>Two recommendation letters</li>
  <li>Personal statement and study plan</li>
  <li>Medical certificate</li>
  <li>Copy of passport</li>
  <li>Language proficiency certificate (TOPIK / IELTS / TOEFL — if applicable)</li>
</ol>
HTML,
                'official_link_1' => 'https://www.studyinkorea.go.kr/',
                'official_link_2' => null,
            ],

            // ─── 8. Chinese Government Scholarship (CSC) ─────────────────────────────
            [
                'title'         => 'Chinese Government Scholarship — CSC (China)',
                'type'          => 'scholarships',
                'country_code'  => 'CN',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(60)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'Administered by the China Scholarship Council (CSC), this fully funded award supports international students for undergraduate, master\'s, and doctoral programmes at leading Chinese universities.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a non-Chinese citizen in good health.</li>
  <li>Age limits: under 25 (undergraduate), under 35 (master\'s), under 40 (doctoral).</li>
  <li>Hold the relevant degree for the level applied (bachelor\'s for master\'s; master\'s for PhD).</li>
  <li>Not currently enrolled in a degree programme in China at the same level.</li>
  <li>Meet the academic requirements of the chosen Chinese university.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition fees waived.</li>
  <li>On-campus accommodation or accommodation allowance.</li>
  <li>Monthly stipend: CNY 2,500 (undergraduate/master\'s) to CNY 3,500 (doctoral).</li>
  <li>Comprehensive medical insurance.</li>
  <li>One-time arrival allowance.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>CSC online application form (studyinchina.csc.edu.cn)</li>
  <li>Notarised highest degree certificate and transcripts</li>
  <li>Study/research plan (in Chinese or English)</li>
  <li>Two academic recommendation letters</li>
  <li>Physical examination form</li>
  <li>Copy of valid passport</li>
  <li>Language proficiency certificate (HSK / IELTS / TOEFL — programme-dependent)</li>
  <li>Pre-admission letter from a Chinese university (recommended)</li>
</ol>
HTML,
                'official_link_1' => 'https://www.campuschina.org/',
                'official_link_2' => null,
            ],

            // ─── 9. Australia Awards Scholarships ────────────────────────────────────
            [
                'title'         => 'Australia Awards Scholarships (Australia)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'AU',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(90)->toDateString(),
                'is_featured'   => true,
                'short_description' => 'Australian Government scholarships for citizens of developing countries to study master\'s programmes in Australia — targeting fields that advance development in the scholar\'s home country.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a citizen of an eligible developing country (e.g., Bangladesh).</li>
  <li>Meet residency requirements set by country-specific guidelines.</li>
  <li>Hold a minimum bachelor\'s degree for postgraduate applications.</li>
  <li>Demonstrate leadership potential and a commitment to home-country development.</li>
  <li>Meet English language proficiency requirements (IELTS / TOEFL).</li>
  <li>Women, persons with disability, and marginalised groups are strongly encouraged to apply.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition fees at the approved Australian university.</li>
  <li>Return economy airfare between home country and Australia.</li>
  <li>Establishment allowance upon arrival.</li>
  <li>Contribution to living expenses (monthly stipend).</li>
  <li>Overseas Student Health Cover (OSHC) for the duration of the award.</li>
  <li>Introductory Academic Programme (IAP) and supplementary academic support.</li>
  <li>Fieldwork allowance for courses with compulsory fieldwork components.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Online application via the Australia Awards portal</li>
  <li>Copies of all academic transcripts and degree certificates</li>
  <li>English language test results (IELTS / TOEFL)</li>
  <li>Two professional or academic reference letters</li>
  <li>Curriculum vitae / résumé</li>
  <li>Personal statement (contribution to development priorities)</li>
  <li>Copy of valid passport or national ID</li>
</ol>
HTML,
                'official_link_1' => 'https://www.australiaawardsbangladesh.org/',
                'official_link_2' => 'https://www.dfat.gov.au/people-to-people/australia-awards',
            ],

            // ─── 10. Türkiye Bursları ─────────────────────────────────────────────────
            [
                'title'         => 'Türkiye Bursları — Turkey Scholarships',
                'type'          => 'scholarships',
                'country_code'  => 'TR',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(55)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'Turkish government scholarship covering undergraduate, master\'s, and PhD study at Turkish universities — 4,000+ awards annually, including language training, accommodation, and a health plan.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a non-Turkish citizen (Turkish-descent applicants subject to additional restrictions).</li>
  <li>Age limits: under 21 (undergraduate), under 30 (master\'s), under 35 (doctoral).</li>
  <li>Minimum GPA: 70% (undergraduate), 75% (master\'s/doctoral) or equivalent.</li>
  <li>Not currently enrolled in a Turkish university at the same level.</li>
  <li>Meet the academic prerequisites of the chosen programme.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition fees at the assigned Turkish university.</li>
  <li>Monthly stipend: TRY 800–1,600 depending on study level.</li>
  <li>Free accommodation in state-managed student dormitories.</li>
  <li>Round-trip airfare (once per year).</li>
  <li>Health insurance coverage.</li>
  <li>One year of free Turkish language training.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Online application via turkiyeburslari.gov.tr</li>
  <li>Academic transcripts and degree certificates</li>
  <li>Personal statement (motivation letter)</li>
  <li>Valid passport copy</li>
  <li>Recent passport-size photograph</li>
  <li>Language proficiency certificate (if available)</li>
  <li>Reference letter(s) (recommended)</li>
</ol>
HTML,
                'official_link_1' => 'https://www.turkiyeburslari.gov.tr/',
                'official_link_2' => null,
            ],

            // ─── 11. Stipendium Hungaricum ────────────────────────────────────────────
            [
                'title'         => 'Stipendium Hungaricum Scholarship (Hungary)',
                'type'          => 'scholarships',
                'country_code'  => 'HU',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(50)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'Hungarian government scholarship for students from 100+ partner countries to pursue bachelor\'s, master\'s, or PhD programmes at Hungarian universities — no IELTS required in many cases.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a citizen of a Stipendium Hungaricum partner country (100+ countries, including Bangladesh).</li>
  <li>Must be nominated through your home country\'s sending authority (e.g., Ministry of Education).</li>
  <li>Meet the academic entry requirements of the chosen Hungarian university programme.</li>
  <li>English or Hungarian language proficiency (MOI letter accepted in place of IELTS for many programmes).</li>
  <li>Must not hold Hungarian citizenship or permanent residence.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition fee exemption at participating Hungarian universities.</li>
  <li>Monthly stipend: HUF 43,700 (bachelor\'s/master\'s) or HUF 140,000 (doctoral).</li>
  <li>Free dormitory accommodation or a housing allowance.</li>
  <li>Comprehensive health insurance under Hungarian legislation.</li>
  <li>Travel allowance (subject to country policy).</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Application via the Stipendium Hungaricum online portal (sto.hu)</li>
  <li>Nomination from the home country sending authority</li>
  <li>Academic transcripts and highest degree certificate</li>
  <li>Motivation letter</li>
  <li>Two reference letters</li>
  <li>Language proficiency proof (IELTS / TOEFL / MOI letter)</li>
  <li>Copy of valid passport</li>
  <li>Medical certificate (HIV test result)</li>
</ol>
HTML,
                'official_link_1' => 'https://stipendiumhungaricum.hu/',
                'official_link_2' => null,
            ],

            // ─── 12. Swedish Institute Scholarships ──────────────────────────────────
            [
                'title'         => 'Swedish Institute Scholarships for Global Professionals (Sweden)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'SE',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(35)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'Awarded by the Swedish Institute to ambitious professionals from select countries for full-time English-taught master\'s studies in Sweden — focused on sustainable development leadership.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a citizen of an eligible country (includes Bangladesh and other developing nations).</li>
  <li>Hold a bachelor\'s degree relevant to the chosen master\'s programme.</li>
  <li>Have at least <strong>3,000 hours of relevant work experience</strong> (for Bangladesh applicants) prior to the application year.</li>
  <li>Demonstrate leadership experience with decision-making responsibilities.</li>
  <li>Apply for and receive an offer from an eligible Swedish master\'s programme.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition fee paid directly to the Swedish university each semester.</li>
  <li>Monthly living allowance of SEK 12,000.</li>
  <li>One-time travel grant of SEK 15,000.</li>
  <li>Membership in the SI Network for Global Professionals (NFGP) during studies.</li>
  <li>Lifetime access to the Sweden Alumni Network after the scholarship period.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>University Admissions application (universityadmissions.se) with personal application number</li>
  <li>SI scholarship application via the SI portal (Feb application window)</li>
  <li>Academic transcripts and degree certificate</li>
  <li>Two reference letters using the SI-provided template (signed and stamped)</li>
  <li>Proof of English language proficiency</li>
  <li>Copy of valid passport or national ID</li>
  <li>CV listing work experience and leadership roles</li>
</ol>
HTML,
                'official_link_1' => 'https://si.se/en/apply/scholarships/swedish-institute-scholarships-for-global-professionals/',
                'official_link_2' => null,
            ],

            // ─── 13. Orange Knowledge Programme ──────────────────────────────────────
            [
                'title'         => 'Orange Knowledge Programme — OKP (Netherlands)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'NL',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(100)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'Dutch government fellowship for mid-career professionals from eligible countries to pursue short courses or master\'s programmes at Dutch institutions — promoting professional development and knowledge sharing.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a national and resident of an OKP-eligible country (includes Bangladesh).</li>
  <li>Be employed in the public sector, civil society, or private sector in your home country.</li>
  <li>Have a relevant bachelor\'s degree and professional work experience.</li>
  <li>Not be living or working outside your home country at the time of application.</li>
  <li>Meet the admission requirements of the chosen Dutch programme.</li>
  <li>Commit to returning to your home country after completing the programme.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition and course fees.</li>
  <li>Monthly living allowance for the duration of the course.</li>
  <li>Round-trip international travel.</li>
  <li>Visa and residence permit costs.</li>
  <li>Health and accident insurance.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>OKP application form submitted through the Dutch institution</li>
  <li>Academic degree certificate and transcripts</li>
  <li>Letter of employment from current employer</li>
  <li>Employer endorsement letter (supporting release for study period)</li>
  <li>Motivation letter</li>
  <li>Two reference letters</li>
  <li>English language certificate (IELTS / TOEFL)</li>
  <li>Copy of valid passport</li>
</ol>
HTML,
                'official_link_1' => 'https://www.nuffic.nl/en/subjects/orange-knowledge-programme',
                'official_link_2' => null,
            ],

            // ─── 14. Swiss Government Excellence Scholarships ─────────────────────────
            [
                'title'         => 'Swiss Government Excellence Scholarships (Switzerland)',
                'type'          => 'phd-fellowships',
                'country_code'  => 'CH',
                'funding_type'  => 'fully_funded',
                'program_level' => 'doctoral',
                'deadline'      => now()->addDays(110)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'Swiss Confederation scholarships for postgraduate researchers and artists from 180+ countries to carry out doctoral or postdoctoral research at Swiss universities — ETH Zurich, EPFL, and all cantonal universities.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a citizen of a country with diplomatic relations with Switzerland (180+ countries).</li>
  <li>Hold a master\'s degree or equivalent (for PhD scholarships).</li>
  <li>Have a letter of support from an academic supervisor at a Swiss institution.</li>
  <li>Priority given to those who have not previously studied or conducted research in Switzerland.</li>
  <li>Age limit: born after 31 December 1991 (for research scholarships).</li>
  <li>Applicants must not already reside in Switzerland for more than one year.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Monthly stipend (CHF 1,920 for postgraduate / CHF 3,500 for postdoctoral).</li>
  <li>Full tuition fees covered.</li>
  <li>Health insurance contribution.</li>
  <li>Accommodation allowance.</li>
  <li>Round-trip flight reimbursement (for research fellowships).</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Application submitted through the Swiss Embassy in home country</li>
  <li>Completed Swiss Government scholarship application form</li>
  <li>Master\'s degree certificate and transcripts</li>
  <li>Detailed research plan with a timeline and key milestones</li>
  <li>Letter of acceptance / support from a Swiss academic supervisor (including their CV)</li>
  <li>Two academic recommendation letters</li>
  <li>Language proficiency proof (English / French / German / Italian depending on institution)</li>
  <li>Copy of valid passport</li>
</ol>
HTML,
                'official_link_1' => 'https://www.sbfi.admin.ch/en/swiss-government-excellence-scholarships',
                'official_link_2' => null,
            ],

            // ─── 15. Government of Ireland International Education Scholarships ────────
            [
                'title'         => 'Government of Ireland International Education Scholarships (Ireland)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'IE',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(80)->toDateString(),
                'is_featured'   => false,
                'short_description' => '60 fully funded scholarships per year for high-calibre non-EU international students to pursue one year of full-time master\'s or PhD study at Irish higher education institutions.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Have a domicile of origin <strong>outside the EU/EEA, Switzerland, and the UK</strong>.</li>
  <li>Hold a conditional or final offer of admission from an eligible Irish higher education institution.</li>
  <li>Be enrolled as a full-time, fee-paying international student in a taught master\'s, postgraduate diploma, or PhD programme lasting at least one year.</li>
  <li>Have not previously held a Government of Ireland International Education Scholarship.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition fee waiver (covered by the host Irish institution).</li>
  <li>€10,000 living stipend for one year of full-time study.</li>
  <li>Opportunity to access Ireland\'s world-class research environment and globally recognised qualifications.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Online application via the GOI-IES portal</li>
  <li>Conditional or final offer of admission from an eligible Irish HEI</li>
  <li>Academic transcripts and degree certificates</li>
  <li>Two reference letters (uploaded via the portal)</li>
  <li>Personal statement</li>
  <li>Copy of valid passport</li>
</ol>
HTML,
                'official_link_1' => 'https://hea.ie/policy/internationalisation/goi-ies/',
                'official_link_2' => null,
            ],

            // ─── 16. Gates Cambridge Scholarship ─────────────────────────────────────
            [
                'title'         => 'Gates Cambridge Scholarship (UK — Cambridge)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'GB',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(130)->toDateString(),
                'is_featured'   => true,
                'short_description' => 'Approximately 80 annual scholarships for outstanding non-UK applicants from any country to pursue a full-time postgraduate degree at the University of Cambridge — one of the world\'s most competitive awards.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a citizen of <strong>any country except the United Kingdom</strong>.</li>
  <li>Apply for a PhD, one-year postgraduate course (MPhil, LLM, MBA), or a two-year research degree at Cambridge.</li>
  <li>Have an outstanding academic record (average GPA of scholars is ~3.92).</li>
  <li>Demonstrate intellectual distinction, strong preparation for the proposed field, and a commitment to improving the lives of others.</li>
  <li>Must first receive admission to Cambridge; the scholarship is then assessed separately.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full University Composition Fee (tuition) at the appropriate rate.</li>
  <li>Annual maintenance allowance of approximately £21,000 (for PhD, up to 4 years).</li>
  <li>Economy return airfare at the start and end of the course.</li>
  <li>UK visa application fee and Immigration Health Surcharge covered.</li>
  <li>Academic development funding (£500–£2,000 depending on course length).</li>
  <li>Access to the global Gates Cambridge Scholars network.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Cambridge University graduate application (must include funding section for Gates Cambridge)</li>
  <li>Gates Cambridge scholarship essays (intellectual interest, leadership, research statement)</li>
  <li>Academic transcripts and degree certificates</li>
  <li>Two to three academic reference letters</li>
  <li>Research proposal (for PhD/research degrees)</li>
  <li>English language proficiency proof (if applicable)</li>
</ol>
HTML,
                'official_link_1' => 'https://www.gatescambridge.org/',
                'official_link_2' => null,
            ],

            // ─── 17. Rhodes Scholarship ───────────────────────────────────────────────
            [
                'title'         => 'Rhodes Scholarship (UK — Oxford)',
                'type'          => 'masters-scholarships',
                'country_code'  => 'GB',
                'funding_type'  => 'fully_funded',
                'program_level' => 'masters',
                'deadline'      => now()->addDays(120)->toDateString(),
                'is_featured'   => true,
                'short_description' => 'One of the world\'s oldest and most prestigious international scholarships — funding 2–3 years of postgraduate study at the University of Oxford for exceptional students from eligible countries.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be a citizen of an eligible Rhodes country or region.</li>
  <li>Age requirements vary by constituency (typically 18–28 years old at time of application).</li>
  <li>Hold a strong undergraduate degree (GPA equivalent of 3.7+ for US applicants).</li>
  <li>Demonstrate outstanding intellectual achievement, character, leadership, and commitment to service.</li>
  <li>Meet English language requirements for Oxford.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition and university fees at Oxford.</li>
  <li>Annual stipend to cover living expenses.</li>
  <li>Return airfare to Oxford.</li>
  <li>Funded for 2–3 years (depending on programme).</li>
  <li>Access to the global Rhodes Scholar network across 100+ years of alumni.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Online application via the Rhodes Trust constituency portal</li>
  <li>Academic transcripts and degree certificates</li>
  <li>Four to eight reference letters (depending on constituency)</li>
  <li>Personal statement / vision essay</li>
  <li>CV highlighting leadership and service</li>
  <li>Oxford University application (required separately)</li>
</ol>
HTML,
                'official_link_1' => 'https://www.rhodeshouse.ox.ac.uk/scholarships/',
                'official_link_2' => null,
            ],

            // ─── 18. Knight-Hennessy Scholars ────────────────────────────────────────
            [
                'title'         => 'Knight-Hennessy Scholars Program (USA — Stanford)',
                'type'          => 'phd-fellowships',
                'country_code'  => 'US',
                'funding_type'  => 'fully_funded',
                'program_level' => 'doctoral',
                'deadline'      => now()->addDays(100)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'Stanford University\'s flagship graduate scholarship — funded by a $750 million endowment — selects up to 100 scholars per year from any country and any field, including professional degrees (JD, MBA, MD).',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Open to applicants of <strong>any nationality</strong> worldwide.</li>
  <li>Must be applying to or already admitted to a graduate degree programme at Stanford University.</li>
  <li>Eligible for all Stanford graduate programmes including PhD, JD, MBA, MD, MFA, and combined degrees.</li>
  <li>Demonstrate independence of thought, purposeful leadership, and a civic mindset.</li>
  <li>Typically up to 7 years from completion of first undergraduate degree at time of applying.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition and university fees for the entire Stanford graduate programme.</li>
  <li>Annual living stipend.</li>
  <li>Reimbursement for travel to and from Stanford.</li>
  <li>Fully funded three-year KH stipend (renewable for multi-year programmes).</li>
  <li>Access to unique leadership curriculum, mentorship, and global KH network.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Knight-Hennessy Scholars application (separate from Stanford graduate admission)</li>
  <li>Stanford graduate programme application and admission offer</li>
  <li>Academic transcripts and degree certificates</li>
  <li>Three recommendation letters</li>
  <li>Short-answer essays (leadership, purpose, civic engagement)</li>
  <li>CV / résumé</li>
</ol>
HTML,
                'official_link_1' => 'https://knight-hennessy.stanford.edu/',
                'official_link_2' => null,
            ],

            // ─── 19. Vanier Canada Graduate Scholarships ──────────────────────────────
            [
                'title'         => 'Vanier Canada Graduate Scholarships (Canada)',
                'type'          => 'phd-fellowships',
                'country_code'  => 'CA',
                'funding_type'  => 'fully_funded',
                'program_level' => 'doctoral',
                'deadline'      => now()->addDays(140)->toDateString(),
                'is_featured'   => false,
                'short_description' => 'CAD $50,000 per year for three years — awarded to world-class PhD candidates (Canadian and international) who demonstrate academic excellence, research potential, and leadership, at eligible Canadian universities.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Open to Canadian citizens, permanent residents, and international students.</li>
  <li>Must be nominated by a Canadian university with a Vanier CGS allocation (cannot apply directly).</li>
  <li>Pursuing a first doctoral degree (including joint MD/PhD, DVM/PhD, JD/PhD programmes).</li>
  <li>Must have achieved a first-class average in each of the last two years of full-time study.</li>
  <li>Must not have completed more than 20 months of doctoral study as of 1 May of the award year.</li>
  <li>Must not hold a current doctoral fellowship from CIHR, NSERC, or SSHRC.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>CAD $50,000 per year for up to three years of doctoral study.</li>
  <li>Tenable only at the nominating Canadian institution.</li>
  <li>Strengthens Canadian permanent residency and immigration pathways post-PhD.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>Institutional nomination (initiated by supervisor or faculty of graduate studies)</li>
  <li>Academic transcripts from all post-secondary institutions</li>
  <li>Three reference letters (from academic supervisors / mentors)</li>
  <li>Applicant\'s leadership and research contribution statement</li>
  <li>Research proposal</li>
  <li>CV / academic record</li>
</ol>
HTML,
                'official_link_1' => 'https://vanier.gc.ca/',
                'official_link_2' => null,
            ],

            // ─── 20. Lester B. Pearson International Scholarship ─────────────────────
            [
                'title'         => 'Lester B. Pearson International Scholarship — University of Toronto (Canada)',
                'type'          => 'scholarships',
                'country_code'  => 'CA',
                'funding_type'  => 'fully_funded',
                'program_level' => 'undergraduate',
                'deadline'      => now()->addDays(80)->toDateString(),
                'is_featured'   => false,
                'short_description' => '37 full-ride undergraduate scholarships per year for exceptional international students entering the University of Toronto — covering tuition, books, fees, and residence for four years.',
                'eligibility_criteria' => <<<HTML
<ul>
  <li>Be an international student (non-Canadian citizen / permanent resident).</li>
  <li>Be enrolled in the final year of senior secondary school (graduating class of the current academic year).</li>
  <li>Intend to begin studies at the University of Toronto in September of the award year.</li>
  <li>Not currently attending any post-secondary institution.</li>
  <li>Be nominated by your secondary school (schools must register with U of T to participate).</li>
  <li>Demonstrate outstanding academic achievement, creativity, leadership, and social impact.</li>
</ul>
HTML,
                'benefits' => <<<HTML
<ul>
  <li>Full tuition fees for the four-year undergraduate programme.</li>
  <li>Books and incidental fees covered.</li>
  <li>Full on-campus residence support for four years.</li>
  <li>One of the most prestigious and comprehensive undergraduate awards in Canada.</li>
</ul>
HTML,
                'required_documents' => <<<HTML
<ol>
  <li>School nomination (submitted by the guidance counsellor or principal)</li>
  <li>University of Toronto undergraduate application (OUAC or direct)</li>
  <li>Academic transcripts (senior secondary school records)</li>
  <li>Personal profile / essays (submitted through the Pearson application portal)</li>
  <li>School report from guidance counsellor</li>
  <li>One teacher evaluation / reference letter</li>
</ol>
HTML,
                'official_link_1' => 'https://future.utoronto.ca/pearson-scholarships',
                'official_link_2' => null,
            ],

        ];

        foreach ($samples as $row) {
            // Resolve the country display name from the ISO code via Symfony Intl
            // Note: 'EU' is not a valid ISO 3166-1 code; handle gracefully
            if ($row['country_code'] && $row['country_code'] !== 'EU') {
                $row['country_name'] = Countries::getName($row['country_code']);
            } else {
                $row['country_name'] = $row['country_code'] === 'EU' ? 'European Union' : null;
            }
            $row['is_active'] = true;

            Scholarship::updateOrCreate(
                ['title' => $row['title']],   // idempotent on re-run
                $row
            );
        }

        $this->command?->info('Seeded ' . count($samples) . ' scholarships.');
    }
}
