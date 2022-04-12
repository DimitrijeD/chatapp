<!DOCTYPE html>
<html>
<head>
    <title>Email Validation for {{ config('app.name') }} Account</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>

    <div class="grid place-items-center h-screen">
        <div class="w-3/4 border border-gray-500">

            <div class="text-center text-blue-500 bg-gray-100">
                <p class="px-3 py-8 text-2xl font-medium">
                    Email Validation for {{ config('app.name') }} Account
                </p>
            </div>


            <div class="flex flex-col justify-center items-center select-none py-12 bg-gray-50">
                <a 
                    style="text-align: center; text-decoration: none;"
                    href="{{ $url }}" 
                    class="mx-auto p-4 bg-blue-500 hover:bg-green-500 text-gray-100 hover:text-white text-xl font-semibold focus:outline-none"
                >
                    Click here to verify your account.
                </a>
            </div>


            <div class="italic p-3 bg-gray-100 text-gray-600">
                <p class="mb-3 select-none">Having issues with the button? Copy link:</p>
                <p class="text-blue-500 underline select-all">{{ $url }}</p>
            </div>
        </div>
    </div>

</body>
</html>
