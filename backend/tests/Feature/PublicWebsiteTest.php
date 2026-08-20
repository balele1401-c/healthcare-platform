<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected Doctor $doctor;
    protected Specialty $cardiology;
    protected Specialty $dermatology;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cardiology = Specialty::create([
            'name' => 'Cardiology',
            'slug' => 'cardiology',
            'description' => 'Heart and cardiovascular care.',
        ]);

        $this->dermatology = Specialty::create([
            'name' => 'Dermatology',
            'slug' => 'dermatology',
            'description' => 'Skin, hair, and nail health.',
        ]);

        $doctorUser = User::factory()->create([
            'name' => 'Dr. Eleanor Vance',
            'email' => 'eleanor.vance@healthcare.local',
            'role' => UserRole::DOCTOR,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'specialty_id' => $this->cardiology->id,
            'license_number' => 'DOC-PUBLIC-001',
            'experience_years' => 10,
            'consultation_fee' => 150.00,
            'facility' => 'Metro Heart Institute',
            'status' => 'active',
            'rating_average' => 4.9,
            'rating_count' => 42,
        ]);
    }

    public function test_public_home_page_can_be_rendered_without_authentication(): void
    {
        $response = $this->get(route('public.home'));
        $response->assertStatus(200);
        $response->assertSee('Your Health,');
        $response->assertSee('Connected.');
        $response->assertSee('Dr. Eleanor Vance');
        $response->assertSee('Cardiology');
    }

    public function test_public_about_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.about'));
        $response->assertStatus(200);
        $response->assertSee('About Our Platform');
        $response->assertSee('Clinical Data Privacy');
    }

    public function test_public_services_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.services'));
        $response->assertStatus(200);
        $response->assertSee('Our Supported Medical Services');
        $response->assertSee('Electronic Medical Records (EMR)');
        $response->assertSee('Digital Prescriptions');
    }

    public function test_public_doctors_directory_can_be_rendered_and_lists_active_doctors(): void
    {
        $response = $this->get(route('public.doctors'));
        $response->assertStatus(200);
        $response->assertSee('Doctor Practitioner Directory');
        $response->assertSee('Dr. Eleanor Vance');
        $response->assertSee('Metro Heart Institute');
    }

    public function test_public_doctors_directory_can_be_filtered_by_specialty(): void
    {
        $response = $this->get(route('public.doctors', ['specialty_id' => $this->cardiology->id]));
        $response->assertStatus(200);
        $response->assertSee('Dr. Eleanor Vance');

        $emptyResponse = $this->get(route('public.doctors', ['specialty_id' => $this->dermatology->id]));
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertDontSee('Dr. Eleanor Vance');
    }

    public function test_public_doctors_directory_can_be_searched_by_name(): void
    {
        $response = $this->get(route('public.doctors', ['search' => 'Eleanor']));
        $response->assertStatus(200);
        $response->assertSee('Dr. Eleanor Vance');

        $noMatchResponse = $this->get(route('public.doctors', ['search' => 'NonExistentDoctor']));
        $noMatchResponse->assertStatus(200);
        $noMatchResponse->assertSee('No medical specialists found matching your search criteria.');
    }

    public function test_public_how_it_works_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.how-it-works'));
        $response->assertStatus(200);
        $response->assertSee('How HealthCare Works');
        $response->assertSee('Create Your Account');
        $response->assertSee('Financial Settlement');
    }

    public function test_public_contact_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.contact'));
        $response->assertStatus(200);
        $response->assertSee('Get in Touch with Our Team');
        $response->assertSee('Immediate Medical Emergencies');
    }

    public function test_public_faq_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.faq'));
        $response->assertStatus(200);
        $response->assertSee('Frequently Asked Questions');
        $response->assertSee('How do I book an appointment with a doctor?');
    }

    public function test_public_privacy_policy_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.privacy'));
        $response->assertStatus(200);
        $response->assertSee('Data Protection Policy');
        $response->assertSee('How We Protect Clinical Data');
    }

    public function test_public_terms_of_service_page_can_be_rendered(): void
    {
        $response = $this->get(route('public.terms'));
        $response->assertStatus(200);
        $response->assertSee('Terms of Service');
        $response->assertSee('Medical Consultation Disclaimer');
    }

    public function test_public_xml_sitemap_returns_valid_xml(): void
    {
        $response = $this->get(route('public.sitemap'));
        $response->assertStatus(200);
        $this->assertStringContainsString('text/xml', $response->headers->get('Content-Type'));
        $response->assertSee('<urlset', false);
        $response->assertSee('<loc>', false);
    }

    public function test_public_robots_txt_allows_public_and_disallows_private_routes(): void
    {
        $response = $this->get(route('public.robots'));
        $response->assertStatus(200);
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));
        $response->assertSee('User-agent: *');
        $response->assertSee('Allow: /');
        $response->assertSee('Disallow: /admin');
        $response->assertSee('Disallow: /doctor');
        $response->assertSee('Disallow: /staff');
        $response->assertSee('Disallow: /api/');
        $response->assertSee('Sitemap:');
    }

    public function test_api_v1_health_check_returns_success(): void
    {
        $response = $this->getJson(route('api.v1.health'));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'status' => 'healthy',
                'service' => 'HealthCare Integrated Medical Platform API',
                'version' => 'v1',
            ],
        ]);
    }
}
