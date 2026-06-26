@extends('layouts.app')

@section('title', 'ဒီဇိုင်းအပြင်အဆင် စမ်းသပ်ခြင်း - Booknest')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/test-layout.css') }}">
@endsection

@section('content')
<section class="test-section">
    <div class="container">
        <!-- Hero Section displaying Design tokens -->
        <div class="test-hero">
            <h1>Booknest Common Layout စမ်းသပ်မှု</h1>
            <p>ဤစာမျက်နှာသည် Layout အပြင်အဆင်၊ CSS Variables (အရောင်နှင့် စာလုံးဒီဇိုင်းစနစ်)၊ FontAwesome Icons များနှင့် Cart Drawer ၏ JS Interaction တို့ အလုပ်လုပ်ပုံကို စမ်းသပ်ရန် ဖြစ်ပါသည်။</p>
            <a href="#" class="test-btn" onclick="event.preventDefault(); window.openBooknestCart();">
                <i class="fa-solid fa-cart-arrow-down mr-05"></i> ဈေးဝယ်လှည်း ဖွင့်စမ်းသပ်ရန်
            </a>
            <a href="#" class="test-btn-sec" onclick="event.preventDefault(); alert('ဇယားနှင့် စတိုင်များ အောင်မြင်စွာ ချိတ်ဆက်ပြီးပါပြီ။');">
                စနစ် စမ်းသပ်ရန်
            </a>
        </div>

        <h2>ဒီဇိုင်းစနစ်စံနှုန်းများ (Design System Tokens)</h2>
        
        <div class="test-grid">
            <!-- Card 1: Primary Teal -->
            <div class="test-card">
                <i class="fa-solid fa-palette"></i>
                <h3>Teal Theme Color</h3>
                <p>မူလအသုံးပြုမည့် အရောင်ဖြစ်ပြီး `--primary: #0d9488` ကို အခြေခံထားပါသည်။ ခလုတ်များ၊ active links များနှင့် အဓိက highlight များတွင် သုံးစွဲပါမည်။</p>
            </div>

            <!-- Card 2: Font & Typography -->
            <div class="test-card">
                <i class="fa-solid fa-font"></i>
                <h3>Outfit & Inter Fonts</h3>
                <p>Heading စာသားများအတွက် 'Outfit' font နှင့် ရိုးရိုးစာသားများအတွက် 'Inter' font ကို Google Fonts မှတစ်ဆင့် စနစ်တကျ ချိတ်ဆက်အသုံးပြုထားပါသည်။</p>
            </div>

            <!-- Card 3: Responsive Design -->
            <div class="test-card">
                <i class="fa-solid fa-mobile-screen"></i>
                <h3>Responsive Layout</h3>
                <p>ဖုန်းနှင့် Tablet မျက်နှာပြင်များတွင် Navbar သည် mobile toggle menu အဖြစ်သို့ ပြောင်းလဲသွားပြီး၊ Cart Drawer သည် မျက်နှာပြင်အပြည့်အဖြစ် အလိုအလျောက် ချိန်ညှိပေးမည် ဖြစ်သည်။</p>
            </div>
        </div>
    </div>
</section>
@endsection
