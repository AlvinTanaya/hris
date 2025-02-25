<!DOCTYPE html>
<html>

<head>
    <title>Congratulations!</title>
</head>

<body>
    <h2>Congratulations, {{ $name }}!</h2>
    <p>
        You have officially been accepted as <strong>{{ $position }}</strong> in the <strong>{{ $department }}</strong> department at <strong>PT Timur Jaya Indstell</strong>.
    </p>
    <p>
        Your joining date is: <strong>{{ $joinDate }}</strong>. Please be prepared and report accordingly.
    </p>
    <p>
        We are looking forward to seeing you soon!
    </p>
    <br>
    <p>Best regards,</p>
    <p>HR Team</p>
</body>

</html>