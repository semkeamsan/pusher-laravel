<!DOCTYPE html>

<head>
    <title>Pusher Test</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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

    <script>
        var pusher = new Pusher(`{{ env('PUSHER_APP_KEY') }}`, {
            cluster: `{{ env('PUSHER_APP_CLUSTER') }}`
        });

        var channel = pusher.subscribe(`{{ $event->broadcastOn() }}`);
        channel.bind(`{{ $event->broadcastAs() }}`,({data}) => {
            $('#show-data').append(`
                    ${new Date}
                    <pre><code id=data>${library.json.prettyPrint(data)}</code></pre>
            `);
        });

        var library = {
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
                        .replace(jsonLine, library.json.replacer);
                }
            }
        };

        $(`form`).submit(function (e) {
            e.preventDefault();
            $.post($(this).attr('action'), {
                _token : `{{ csrf_token() }}`,
                input : $(this).find(`textarea`).val()
            }).done(res=>{
                if(res.status){
                    $(this).find(`textarea`).val('');
                }
            });
        });

    </script>
</body>
