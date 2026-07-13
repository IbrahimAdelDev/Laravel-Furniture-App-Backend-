<x-mail::message>
# إشعار جديد

مرحباً，
يوجد تحديث جديد في النظام يحتاج لانتباهك.

**التفاصيل:**
- **الحدث:** {{ $data['event'] }}
- **المستخدم:** {{ $data['user_name'] ?? 'زائر' }}
- **الوقت:** {{ now()->format('Y-m-d H:i') }}

<x-mail::button :url="$data['action_url'] ?? config('app.url')">
عرض التفاصيل
</x-mail::button>

شكراً،<br>
{{ config('app.name') }}
</x-mail::message>