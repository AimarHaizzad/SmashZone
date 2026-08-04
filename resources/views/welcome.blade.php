<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmashZone — Premier Badminton Facility Management</title>
    <meta name="description" content="SmashZone is a professional badminton facility platform for court reservations, pro shop retail, facility operations, and member services across Malaysia.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-50">
    <nav class="fixed inset-x-0 top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="sz-container flex h-16 items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold">SZ</div>
                <span class="text-xl font-bold text-slate-900">SmashZone</span>
            </a>
            <div class="hidden items-center gap-8 md:flex">
                <a href="#facilities" class="text-sm font-medium text-slate-600 hover:text-emerald-700">Facilities</a>
                <a href="#services" class="text-sm font-medium text-slate-600 hover:text-emerald-700">Services</a>
                <a href="#operations" class="text-sm font-medium text-slate-600 hover:text-emerald-700">Operations</a>
                <a href="#contact" class="text-sm font-medium text-slate-600 hover:text-emerald-700">Contact</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-emerald-700">Sign In</a>
                <a href="{{ route('register') }}" class="sz-btn-primary">Create Account</a>
            </div>
        </div>
    </nav>

    <header class="relative overflow-hidden pt-16">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-700 via-emerald-600 to-sky-700"></div>
        <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="relative sz-container grid gap-12 py-20 lg:grid-cols-2 lg:items-center lg:py-28">
            <div class="text-white">
                <p class="mb-4 inline-flex rounded-full border border-white/30 bg-white/10 px-4 py-1 text-sm font-medium backdrop-blur">
                    Enterprise Badminton Facility Platform
                </p>
                <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl">
                    Manage courts, bookings, and retail in one professional system
                </h1>
                <p class="mt-6 max-w-xl text-lg text-emerald-50">
                    SmashZone powers modern badminton centres with real-time court scheduling,
                    integrated payments, pro shop operations, staff workflows, and facility analytics.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-emerald-700 shadow-lg hover:bg-emerald-50">
                        Book Your First Court
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl border border-white/40 px-6 py-3 text-sm font-bold text-white hover:bg-white/10">
                        Facility Staff Login
                    </a>
                </div>
            </div>
            <div class="relative">
                <img src="{{ asset('images/badminton-hero.jpg') }}" alt="SmashZone badminton facility" class="rounded-3xl border border-white/20 shadow-2xl">
                <div class="absolute -bottom-6 -left-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-soft">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Operating Hours</p>
                    <p class="mt-1 text-lg font-bold text-slate-900">8:00 AM – 11:00 PM</p>
                    <p class="text-sm text-slate-600">Daily · Including public holidays</p>
                </div>
            </div>
        </div>
    </header>

    <section id="facilities" class="py-20">
        <div class="sz-container">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="text-3xl font-bold text-slate-900">Built for professional facility operations</h2>
                <p class="mt-4 text-lg text-slate-600">
                    From court allocation to revenue reporting, SmashZone gives owners and staff
                    the tools to run a high-standard badminton centre.
                </p>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-3">
                <div class="sz-card sz-card-body">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Smart Court Booking</h3>
                    <p class="mt-2 text-slate-600">Live availability grid, dynamic hourly pricing, multi-slot reservations, and automated booking lifecycle management.</p>
                </div>
                <div class="sz-card sz-card-body">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Integrated Payments</h3>
                    <p class="mt-2 text-slate-600">Secure Stripe checkout for court fees and pro shop orders, with refund handling and payment reconciliation built in.</p>
                </div>
                <div class="sz-card sz-card-body">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-violet-700">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Facility Analytics</h3>
                    <p class="mt-2 text-slate-600">Revenue dashboards, court utilisation metrics, booking trends, and exportable reports for management review.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="border-y border-slate-200 bg-white py-20">
        <div class="sz-container grid gap-12 lg:grid-cols-2 lg:items-center">
            <div>
                <h2 class="text-3xl font-bold text-slate-900">Complete member and staff experience</h2>
                <ul class="mt-8 space-y-4 text-slate-700">
                    <li class="flex gap-3"><span class="mt-1 text-emerald-600">✓</span> Role-based dashboards for owners, staff, and members</li>
                    <li class="flex gap-3"><span class="mt-1 text-emerald-600">✓</span> Pro shop with inventory, orders, and fulfilment tracking</li>
                    <li class="flex gap-3"><span class="mt-1 text-emerald-600">✓</span> Automated booking reminders and in-app notifications</li>
                    <li class="flex gap-3"><span class="mt-1 text-emerald-600">✓</span> Mobile app integration with secure web session handoff</li>
                </ul>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="sz-stat-card"><p class="sz-stat-label">Court Types</p><p class="sz-stat-value">Premier & Standard</p></div>
                <div class="sz-stat-card"><p class="sz-stat-label">Booking Window</p><p class="sz-stat-value">Real-time</p></div>
                <div class="sz-stat-card"><p class="sz-stat-label">Currency</p><p class="sz-stat-value">MYR</p></div>
                <div class="sz-stat-card"><p class="sz-stat-label">Support</p><p class="sz-stat-value">7 Days</p></div>
            </div>
        </div>
    </section>

    <section id="operations" class="py-20">
        <div class="sz-container rounded-3xl bg-slate-900 px-8 py-12 text-white sm:px-12">
            <div class="grid gap-8 lg:grid-cols-2 lg:items-center">
                <div>
                    <h2 class="text-3xl font-bold">Ready to streamline your badminton centre?</h2>
                    <p class="mt-4 text-slate-300">Register as a member to book courts and shop equipment, or contact our team for facility partnership enquiries.</p>
                </div>
                <div class="flex flex-wrap gap-4 lg:justify-end">
                    <a href="{{ route('register') }}" class="sz-btn-primary bg-emerald-500 hover:bg-emerald-400">Get Started</a>
                    <a href="{{ route('bookings.index') }}" class="sz-btn-secondary border-slate-600 text-white hover:bg-slate-800">View Court Schedule</a>
                </div>
            </div>
        </div>
    </section>

    <footer id="contact" class="border-t border-slate-200 bg-white py-12">
        <div class="sz-container grid gap-8 md:grid-cols-3">
            <div>
                <p class="text-lg font-bold text-slate-900">SmashZone</p>
                <p class="mt-2 text-sm text-slate-600">Professional badminton facility management platform.</p>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Contact</p>
                <p class="mt-2 text-sm text-slate-700">hello@smashzone.my</p>
                <p class="text-sm text-slate-700">+60 3-1234 5678</p>
                <p class="text-sm text-slate-700">Kuala Lumpur, Malaysia</p>
            </div>
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-slate-500">Hours</p>
                <p class="mt-2 text-sm text-slate-700">Monday – Sunday</p>
                <p class="text-sm text-slate-700">8:00 AM – 11:00 PM</p>
            </div>
        </div>
        <div class="sz-container mt-8 border-t border-slate-200 pt-6 text-sm text-slate-500">
            © {{ date('Y') }} SmashZone. All rights reserved.
        </div>
    </footer>
</body>
</html>
