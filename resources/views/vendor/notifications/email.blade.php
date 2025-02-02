@component('mail::message')
<img src="{{ $logoUrl }}" alt="Your Logo" style="width: 150px;">

# Welcome to Re’sIt!

We’re excited to have you join us! To complete your registration and start exploring, please use the following verification code:

# {{ $verificationCode }}

If you didn’t create an account, you can safely ignore this email. Your email address will not be used.

Best Regards,  
Barangay Gordon Heights  
The Re’sIt Team
@endcomponent