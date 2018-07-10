<!doctype html>
<html>
    <head>

        @include('components/front/meta')

    </head>

    <body>

        @include('components/front/header')
        <div class="content">

            @yield('content')

        </div>

        @include('components/front/footer')

        @include('components/front/scripts')


    </body>
</html>
