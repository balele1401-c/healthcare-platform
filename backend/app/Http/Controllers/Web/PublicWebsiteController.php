<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PublicWebsiteController extends Controller
{
    /**
     * Display the public landing home page.
     */
    public function home(): View
    {
        $featuredDoctors = Doctor::with(['user', 'specialty'])
            ->where('status', 'active')
            ->orderByDesc('rating_average')
            ->take(4)
            ->get();

        $specialties = Specialty::orderBy('name')->take(8)->get();
        $totalDoctorsCount = Doctor::where('status', 'active')->count();
        $totalSpecialtiesCount = Specialty::count();

        return view('public.home', compact(
            'featuredDoctors',
            'specialties',
            'totalDoctorsCount',
            'totalSpecialtiesCount'
        ));
    }

    /**
     * Display the public About Us page.
     */
    public function about(): View
    {
        return view('public.about');
    }

    /**
     * Display the public Services overview page.
     */
    public function services(): View
    {
        return view('public.services');
    }

    /**
     * Display the public Doctor Directory page.
     */
    public function doctors(Request $request): View
    {
        $search = $request->query('search');
        $specialtyId = $request->query('specialty_id');

        $query = Doctor::with(['user', 'specialty'])
            ->where('status', 'active');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('facility', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        if (! empty($specialtyId)) {
            $query->where('specialty_id', $specialtyId);
        }

        $doctors = $query->orderByDesc('rating_average')->paginate(12)->withQueryString();
        $specialties = Specialty::orderBy('name')->get();

        return view('public.doctors', compact('doctors', 'specialties', 'search', 'specialtyId'));
    }

    /**
     * Display the public How It Works guide.
     */
    public function howItWorks(): View
    {
        return view('public.how_it_works');
    }

    /**
     * Display the public Contact & Clinic Information page.
     */
    public function contact(): View
    {
        return view('public.contact');
    }

    /**
     * Display the public FAQ page.
     */
    public function faq(): View
    {
        return view('public.faq');
    }

    /**
     * Display Privacy Policy.
     */
    public function privacy(): View
    {
        return view('public.privacy');
    }

    /**
     * Display Terms of Service.
     */
    public function terms(): View
    {
        return view('public.terms');
    }

    /**
     * Serve dynamic XML sitemap for SEO and Google Search Console.
     */
    public function sitemap(): Response
    {
        $specialties = Specialty::all();
        $doctors = Doctor::where('status', 'active')->get();

        $content = view('public.sitemap', compact('specialties', 'doctors'))->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Serve standard robots.txt for search engines.
     */
    public function robots(): Response
    {
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Allow: /about\n";
        $robots .= "Allow: /services\n";
        $robots .= "Allow: /doctors\n";
        $robots .= "Allow: /how-it-works\n";
        $robots .= "Allow: /contact\n";
        $robots .= "Allow: /faq\n";
        $robots .= "Allow: /privacy\n";
        $robots .= "Allow: /terms\n";
        $robots .= "Disallow: /admin\n";
        $robots .= "Disallow: /doctor\n";
        $robots .= "Disallow: /staff\n";
        $robots .= "Disallow: /api/\n";
        $robots .= "Disallow: /dashboard\n";
        $robots .= "Disallow: /login\n";
        $robots .= "Disallow: /register\n";
        $robots .= "Sitemap: " . url('/sitemap.xml') . "\n";

        return response($robots, 200)
            ->header('Content-Type', 'text/plain');
    }
}
