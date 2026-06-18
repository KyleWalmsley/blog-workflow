<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $emailSubject }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f5f5;font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;padding:40px 16px;">
  <tr>
    <td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">

        {{-- Header --}}
        <tr>
          <td style="padding-bottom:24px;">
            <p style="margin:0;font-size:13px;color:#737373;font-weight:500;letter-spacing:0.02em;">
              {{ \App\Models\Setting::get('from_name', 'Product Delivery') }}
            </p>
          </td>
        </tr>

        {{-- Card --}}
        <tr>
          <td style="background-color:#ffffff;border-radius:12px;border:1px solid #e5e5e5;padding:40px;">

            <p style="margin:0 0 24px 0;font-size:22px;font-weight:600;color:#171717;line-height:1.3;">
              Your blog content is ready for review
            </p>

            <p style="margin:0 0 16px 0;font-size:15px;color:#404040;line-height:1.6;">
              Hi {{ $job->client->name }},
            </p>

            @if($emailBody)
            <p style="margin:0 0 16px 0;font-size:15px;color:#404040;line-height:1.6;">
              {!! nl2br(e($emailBody)) !!}
            </p>
            @else
            <p style="margin:0 0 16px 0;font-size:15px;color:#404040;line-height:1.6;">
              We've prepared a batch of blog content for your review. Please take a moment to go through each article and let us know your thoughts — you can approve content you're happy with or leave feedback on anything you'd like adjusted.
            </p>
            <p style="margin:0 0 24px 0;font-size:15px;color:#404040;line-height:1.6;">
              The content batch is titled: <strong>{{ $job->title }}</strong>
            </p>
            @endif

            {{-- CTA Button --}}
            <table cellpadding="0" cellspacing="0" style="margin:32px 0;">
              <tr>
                <td style="background-color:#2563eb;border-radius:8px;">
                  <a href="{{ $job->reviewUrl() }}"
                     style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;letter-spacing:0.01em;">
                    Review Your Content
                  </a>
                </td>
              </tr>
            </table>

            <p style="margin:0 0 8px 0;font-size:13px;color:#737373;line-height:1.5;">
              If the button above doesn't work, copy and paste this link into your browser:
            </p>
            <p style="margin:0 0 32px 0;font-size:13px;color:#2563eb;word-break:break-all;">
              {{ $job->reviewUrl() }}
            </p>

            <hr style="border:none;border-top:1px solid #e5e5e5;margin:0 0 24px 0;">

            <p style="margin:0;font-size:13px;color:#737373;line-height:1.6;">
              This is a private review link — please don't share it with others.<br>
              If you have any questions, simply reply to this email.
            </p>

          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="padding-top:24px;text-align:center;">
            <p style="margin:0;font-size:12px;color:#a3a3a3;">
              {{ \App\Models\Setting::get('from_name', 'Product Delivery') }}
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
