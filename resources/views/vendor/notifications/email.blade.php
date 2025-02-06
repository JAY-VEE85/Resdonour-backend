@component('mail::message')
# Welcome to Re’sIt!

We’re excited to have you join us! To complete your registration and start exploring, please use the following verification code:

# {{ $verificationCode }}

If you didn’t create an account, you can safely ignore this email. Your email address will not be used.

Best Regards,  
Barangay Gordon Heights  
The Re’sIt Team

<table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" style="padding-top: 20px">
    <tr>
        <td style="padding: 0 10px;">
            <img src="{{ $logoUrl1 }}" alt="Gordon Heights Logo" style="width: 90px;">
        </td>
        <td style="padding: 0 10px;">
            <img src="{{ $logoUrl2 }}" alt="Re'sIt Logo" style="width: 90px;">
        </td>
        <td style="padding: 0 10px;">
            <img src="{{ $logoUrl3 }}" alt="Gordon Height SK Logo" style="width: 90px;">
        </td>
    </tr>
</table>
@endcomponent