<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>App Upload</title>
    <style>
        body{
            background-color: #f7f7f7;
            width: 100%;
            height: 100%;
            color: #5e5873;
            direction: rtl;
            text-align: right;
        }
        main{
            margin: 3rem;
            padding: 3rem;
        }
        form{
            padding-bottom: 3rem;
        }
        input[type=text]{
            height: 2.3rem;
            width: 17rem;
            padding: 4px 0;
            padding-left: 10px;
            cursor: pointer;
            border: solid 2px #32918a;
            outline: none;
        }
        button{
            background-color: #32918a;
            border: 1px solid #5e5873;
            color: #fff;
            width: 10rem;
            height: 3rem;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
        }
        .link{
            text-decoration: underline;
            font-size: 18px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <main>
        <form action="{{url('/upload')}}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="text" placeholder="version" name="version" required> <br> <br>
            <input type="text" placeholder="build num" name="build" required> <br> <br>
            <input type="text" placeholder="Signature" name="sign" required> <br> <br>
            <input type="file" name="file" id="app"> <br><br>
            <button type="submit">Submit</button>
        </form>

        @if (session('link'))
            <div class="link">
                <a href="{{ session('link') }}"> Download</a> <br>
                Link: {{ session('link') }}
            </div>
        @endif
    </main>
</body>
</html>
