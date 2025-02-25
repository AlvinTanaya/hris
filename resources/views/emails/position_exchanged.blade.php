<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Position Exchange Notification</title>
    </head>
    <body>
        <h2>Dear {{ $name }},</h2>
        <p>
            We would like to inform you that your job application has been moved
            to the <strong>{{ $position }}</strong> position in the
            <strong>{{ $department }}</strong> department.
        </p>
        <p>
            We believe that you are highly suitable and possess the right skills
            for this position.
        </p>

        <p>Best Regards,<br />HR Team</p>
    </body>
</html>
