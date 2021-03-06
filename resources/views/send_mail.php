<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div>
            <form action="/send_mail" method="post">
                <label> Subject 
                    <input type="text" name="subject">
                </label>
                <label> From 
                    <input type="text" name="from">
                </label>
                <label> Template
                    <select name="template_name">
                        <?php foreach($files as $file) {?>
                            <option value="<?php echo $file;?>"> <?php echo $file;?> </option>
                        <?php } ?>
                    </select>
                </label>
                <div>Select attachments
                    <select name="attachments[]" multiple>
                        <?php foreach($attachments as $attach) {?>
                            <option value="<?php echo $attach;?>"> <?php echo $attach;?> </option>
                        <?php } ?>
                    </select>
                </div>
                <input type="hidden" name="_token" value="<?php echo csrf_token();?>">
                <button>Send mail</button>
            </form>
            <div style="margin-top: 20px">
                Use these tag in your template html
                <p>#*t_company_name*#</p>
<p>#*tel_no*#</p>
<p>#*t_email*#</p>
<p>#*t_event_desc*#</p>
<p>#*t_country*#</p>
            </div>
        </div>
    </body>
</html>
