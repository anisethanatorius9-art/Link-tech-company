<!doctype html>
<html lang="en">
<body style="font-family: Arial, sans-serif; color: #17221b; line-height: 1.6;">
    <h2>{{ $reportSubject }}</h2>
    <p><strong>Company:</strong> {{ $company }}</p>
    <p><strong>Sent by:</strong> {{ $sender }}</p>
    <hr>
    <p>{!! nl2br(e($reportBody)) !!}</p>
</body>
</html>
