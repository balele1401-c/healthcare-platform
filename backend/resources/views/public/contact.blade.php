@extends('layouts.public')

@section('meta_title', 'Contact & Clinic Support — HealthCare')
@section('meta_description', 'Get in touch with the HealthCare operational team for inquiries, appointment coordination, and technical support.')

@section('content')
<div class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-50 text-cyan-700 text-xs font-semibold">
                <span>Support & Clinic Information</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">
                Get in Touch with Our Team
            </h1>
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                Have questions about our medical platform, doctor availability, or clinical services? We're here to assist you.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <!-- Left Info Cards -->
            <div class="space-y-6">
                <div class="bg-slate-50 rounded-3xl p-8 border border-slate-200/80 space-y-6">
                    <h3 class="font-bold text-slate-900 text-lg">Central Facility & Contact Details</h3>

                    <dl class="space-y-4 text-xs">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold text-base shrink-0">
                                📍
                            </div>
                            <div>
                                <dt class="font-bold text-slate-900">Main Medical Center</dt>
                                <dd class="text-slate-500 mt-0.5">Healthcare Integrated Medical Platform Center<br>Metropolitan District, Jakarta & Remote Clinics</dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-base shrink-0">
                                ✉️
                            </div>
                            <div>
                                <dt class="font-bold text-slate-900">Email Inquiries</dt>
                                <dd class="text-slate-500 mt-0.5">support@healthcare.local &bull; operations@healthcare.local</dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-base shrink-0">
                                🕒
                            </div>
                            <div>
                                <dt class="font-bold text-slate-900">Operational Hours</dt>
                                <dd class="text-slate-500 mt-0.5">Monday &ndash; Saturday: 08:00 &ndash; 20:00 WIB<br>Sunday: On-Duty Teleconsultation Shifts</dd>
                            </div>
                        </div>
                    </dl>
                </div>

                <!-- Emergency Disclaimer -->
                <div class="bg-rose-50 rounded-3xl p-6 border border-rose-200 text-xs text-rose-900 space-y-2">
                    <h4 class="font-bold flex items-center gap-2">
                        <span>🚨 Immediate Medical Emergencies</span>
                    </h4>
                    <p class="leading-relaxed text-rose-800">
                        This contact channel is monitored for general administrative inquiries. In the event of a medical emergency, acute chest pain, or severe trauma, please contact local emergency medical services immediately.
                    </p>
                </div>
            </div>

            <!-- Right Inquiry Form -->
            <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm space-y-6">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Send Us an Inquiry</h3>
                    <p class="text-xs text-slate-500 mt-1">Our clinical operations desk will review and respond within 24 operational hours.</p>
                </div>

                <form onsubmit="event.preventDefault(); alert('Thank you for reaching out. Our support team has received your message.');" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-slate-700">Your Full Name</label>
                            <input type="text" required placeholder="Jane Doe"
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-slate-700">Email Address</label>
                            <input type="email" required placeholder="jane@example.com"
                                   class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Subject</label>
                        <input type="text" required placeholder="Appointment inquiry, general feedback..."
                               class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white">
                    </div>

                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Message</label>
                        <textarea rows="4" required placeholder="How can our clinical team assist you?"
                                  class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl text-xs shadow-md shadow-cyan-600/20 transition-colors">
                        Submit Message &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
