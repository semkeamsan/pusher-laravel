<!DOCTYPE html>

<head>
    <title>Pusher Test</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        pre {
            background-color: ghostwhite;
            border: 1px solid silver;
            padding: 10px 20px;
            margin: 20px;
        }

        .json-key {
            color: brown;
        }

        .json-value {
            color: navy;
        }

        .json-string {
            color: olive;
        }
    </style>
</head>

<body>
    <div class="container my-2">
        <div class="row">
            <div class="col-xl-4 mb-3">
                <form action="{{ route('api.pusher.store') }}" class="sticky-top">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ __('Write something') }}</h4>
                        </div>
                        <div class="card-body">
                            <textarea name="" class="form-control" cols="30" rows="10"></textarea>
                        </div>
                        <div class="card-footer">
                            <div class="float-end">
                                <button class="btn btn-primary" type="submit">{{ __('Send') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-xl-8 border" id="show-data">

            </div>
        </div>
    </div>

    <div id="my-modal" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="my-modal-title">Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">

                </div>
            </div>
        </div>
    </div>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Title</strong>
                <small></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Hello, world! This is a toast message.
            </div>
        </div>
    </div>

    <script>
        const PUSHER = {
        init: function () {
            this.form();
            this.setup();
        },

        setup: function () {
            var pusher = new Pusher(`{{ env('PUSHER_APP_KEY') }}`, {
                cluster: `{{ env('PUSHER_APP_CLUSTER') }}`
            });
            var channel = pusher.subscribe(`{{ $event->broadcastOn() }}`);
            channel.bind(`{{ $event->broadcastAs() }}`, ({
                data
            }) => {

                // result here
                console.log(data);
                $('#show-data').append(`
                        ${new Date}
                        <pre><code id=data>${PUSHER.json.prettyPrint(data)}</code></pre>
                `);

                $(`.modal-body`).prepend(this.template(data));
                $(`#my-modal`).modal(`show`);
                this.timeAgo();

            });
        },



        template : function(data){
            var image = `./image/${this.random(1,5)}.png`;
            var $t = $(`<div class="col-xl-3 mb-3">
                        <div class="card">
                            <img src="${image}" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title" data-toggle="time-ago" data-date="${new Date}">${PUSHER.time(new Date)}</h5>
                                <p class="card-text">Content</p>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col">
                                        <button class="btn w-100 btn-danger">Cancel</button>
                                    </div>
                                    <div class="col">
                                        <button class="btn w-100 btn-success">Ok</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`);
                $t.find(`.btn-danger`).click(function () {
                    $t.remove();
                    if ($(`.modal-body>div`).length == 0) {
                        $(`#my-modal`).modal(`hide`);
                    }
                });
                $t.find(`.btn-success`).click(function () {
                    $t.remove();
                    if ($(`.modal-body>div`).length == 0) {
                        $(`#my-modal`).modal(`hide`);
                        var $toast = $(`#liveToast`).clone();
                        $(`#liveToast`).parent().append($toast);
                        $toast.find(`small`).text(PUSHER.time(new Date));
                        $toast.toast(`show`);
                    }
                });
                return $t;
        },
        form: function () {
            $(`form`).submit(function (e) {
                e.preventDefault();
                $.post($(this).attr('action'), {
                    _token: `{{ csrf_token() }}`,
                    input: $(this).find(`textarea`).val()
                }).done(res => {
                    if (res.status) {
                        $(this).find(`textarea`).val('');
                    }
                });
            });
        },
         random : function(min, max) {
              return Math.floor(Math.random() * (max - min + 1) + min)
        },
        timeAgo:function (){
            $(`[data-toggle="time-ago"]`).each(function () {
                var t = $(this).data('date');
                var x = setInterval(() => {
                    $(this).text(PUSHER.time(t));
                }, 1000);
            });
        },
        time: function (time) {
            switch (typeof time) {
                case 'number':
                    break;
                case 'string':
                    time = +new Date(time);
                    break;
                case 'object':
                    if (time.constructor === Date) time = time.getTime();
                    break;
                default:
                    time = +new Date();
            }
            var time_formats = [
                [60, 'seconds', 1], // 60
                [120, '1 minute ago', '1 minute from now'], // 60*2
                [3600, 'minutes', 60], // 60*60, 60
                [7200, '1 hour ago', '1 hour from now'], // 60*60*2
                [86400, 'hours', 3600], // 60*60*24, 60*60
                [172800, 'Yesterday', 'Tomorrow'], // 60*60*24*2
                [604800, 'days', 86400], // 60*60*24*7, 60*60*24
                [1209600, 'Last week', 'Next week'], // 60*60*24*7*4*2
                [2419200, 'weeks', 604800], // 60*60*24*7*4, 60*60*24*7
                [4838400, 'Last month', 'Next month'], // 60*60*24*7*4*2
                [29030400, 'months', 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
                [58060800, 'Last year', 'Next year'], // 60*60*24*7*4*12*2
                [2903040000, 'years', 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
                [5806080000, 'Last century', 'Next century'], // 60*60*24*7*4*12*100*2
                [58060800000, 'centuries', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
            ];
            var seconds = (+new Date() - time) / 1000,
                token = 'ago',
                list_choice = 1;

            if (seconds == 0) {
                return 'Just now'
            }
            if (seconds < 0) {
                seconds = Math.abs(seconds);
                token = 'from now';
                list_choice = 2;
            }
            var i = 0,
                format;
            while (format = time_formats[i++])
                if (seconds < format[0]) {
                    if (typeof format[2] == 'string')
                        return format[list_choice];
                    else
                        return Math.floor(seconds / format[2]) + ' ' + format[1] + ' ' + token;
                }
            return time;

        },
        json: {
            replacer: function (match, pIndent, pKey, pVal, pEnd) {
                var key = '<span class=json-key>';
                var val = '<span class=json-value>';
                var str = '<span class=json-string>';
                var r = pIndent || '';
                if (pKey)
                    r = r + key + pKey.replace(/[": ]/g, '') + '</span>: ';
                if (pVal)
                    r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
                return r + (pEnd || '');
            },
            prettyPrint: function (obj) {
                var jsonLine = /^( *)("[\w]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
                return JSON.stringify(obj, null, 3)
                    .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
                    .replace(/</g, '&lt;').replace(/>/g, '&gt;')
                    .replace(jsonLine, PUSHER.json.replacer);
            }
        }


    };

    PUSHER.init();
    </script>
</body>
