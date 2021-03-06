@extends('layouts.app')

@section('title') {{$thread->title}} @endsection

@include('layouts.partials.twitter_cards')

@section('stylesheets')
    <link rel="amphtml" href="{{ url('/') }}/amp/p/{{$subPlebbit->name}}/comments/{{$thread->code}}/{{ str_slug($thread->title) }}">

    <link rel="stylesheet" href="{{ asset('css/thread.css') }}">
    <link rel="stylesheet" href="{{ asset('css/subplebbit.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ladda-themeless.min.css') }}">
    <style>
        .header {
            @if($subPlebbit->header)
            background: linear-gradient(rgba(0,0,0,0.2),rgba(0,0,0,0.2)),url("/images/plebbits/headers/{{$subPlebbit->header}}");
            @endif            background-position: center;
            @if($subPlebbit->header_type == 'fit')
            background-size: cover;
            @endif
            width: 100%;
            @if(!$subPlebbit->header)
            background: {{$subPlebbit->header_color}};
            @else
            margin-top: 0;
            @endif
            }
        #stripe {
            background-color: @if($subPlebbit->header_color) {{ $subPlebbit->header_color }} @else grey @endif;
            height: 20px;
            width: 100%;
            position: sticky;
            z-index: 1;
        }
        @if($subPlebbit->header_color)
                #header_name {
            color: {{$subPlebbit->color}};
        }
        #header_title {
            color: {{$subPlebbit->color}};
        }
        @endif
    </style>
    @if($subPlebbit->custom_css)
        <link rel="stylesheet" href="{{asset('cdn/css/'.$subPlebbit->name.'.css')}}">
    @endif
@endsection

@section('content')

    @if($subPlebbit->header)
        <div id="stripe" data-spy="affix"></div>
    @endif
    <div class="header">
        <h1 id="header_name">{{$subPlebbit->name}}</h1>
        <p id="header_title">{{ $subPlebbit->title }}</p>
    </div>

    <div class="container">
        <div class="panel">
            <div class="modal-header">
                <div class="row">
                    <div style="width: 40px; margin-top: -5px;" class="votes col-xs-2 col-sm-1">
                        <div style="margin-left: 20px;" class="wrap">
                            <div style="margin-bottom: -5px; font-size: 20px;" class="row stack">
                                <i id="{{$thread->id}}_up" data-voted="no" data-vote="up" data-thread="{{$thread->code}}" class="fa fa-sort-asc vote"></i>
                            </div>
                            <div class="row stack">
                                <span id="{{$thread->id}}_counter" class="stack count">{{$thread->upvotes - $thread->downvotes}}</span>
                            </div>
                            <div style="margin-top: -5px; font-size: 20px;" class="row stack">
                                <i id="{{$thread->id}}_down" data-voted="no" data-vote="down" data-thread="{{$thread->code}}" class="fa fa-sort-desc stack vote"></i>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: -4px;" class="col-xs-10 col-sm-11">
                        <h4><a href="@if($thread->link){{$thread->link}}@else @endif">{{$thread->title}}</a></h4>
                        @php
                            $user = new \App\User();
                            $postername = $user->select('username')->where('id', $thread->poster_id)->first();
                        @endphp
                        <p class="overflow" style="margin-bottom: -5px;">placed by <a href="/u/{{$postername->username}}">{{$postername->username}}</a> {{Carbon\Carbon::parse($thread->created_at)->diffForHumans()}} in
                            <a href="/p/{{$subPlebbit->name}}">{{$subPlebbit->name}}</a></p>
                    </div>
                </div>
            </div>
            @if($thread->link || $thread->post)
                <div class="post">
                    @if($thread->link)
                        @if($thread->media_type == 'image')
                            <div class="row">
                                <div class="col-md-8">
                                    <img style="max-width: 100%; max-height: 600px;" src="{{$thread->link}}" alt="{{$thread->title}}">
                                </div>
                            </div>
                        @elseif($thread->media_type == 'video')
                            <div class="row">
                                <div class="col-md-8">
                                    <video style="max-width: 100%; max-height: 600px;" controls src="{{$thread->link}}" autoplay></video>
                                </div>
                            </div>
                        @elseif($thread->media_type == 'youtube')
                            @php preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $thread->link, $matches); @endphp
                            <div class="row">
                                <div class="col-md-8">
                                    <iframe style="max-width: 100%;" width="800" height="413" src="https://www.youtube.com/embed/{{$matches[0]}}" frameborder="0" allowfullscreen></iframe>
                                </div>
                            </div>
                        @elseif($thread->media_type == 'vimeo')
                            @php $vimeoId = (int) substr(parse_url($thread->link, PHP_URL_PATH), 1); @endphp
                            <div class="row">
                                <div class="col-md-8">
                                    <iframe style="max-width: 100%;" width="800" height="413" src="https://player.vimeo.com/video/{{$vimeoId}}" frameborder="0" allowfullscreen></iframe>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-8">
                                    <a style="word-wrap: break-word;" href="{{$thread->link}}">{{$thread->link}}</a>
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    @if($thread->post)
                       {!! $thread->post !!}
                    @endif
                </div>
            @endif
            @if($mod)
                <p style="padding-left: 10px; margin-bottom: 5px;"><a href="javascript:deleteThread()">Delete</a></p>
            @endif
        </div>

        <div style="margin-top: -10px;" class="panel">
            <div class="panel-body">
                <div class="col-md-5">
                    <h5>Place a comment</h5>
                    <textarea data-thread="{{$thread->id}}" placeholder="Comment" name="comment" id="comment" cols="30" rows="4" class="form-control commentbox"></textarea>
                    <button style="margin-top: 5px;" class="btn btn-primary submitpostbtn pull-right ladda-button" data-style="slide-up">Post comment</button>
                    <div class="errors"></div>
                </div>
            </div>
        </div>


        <select onchange="sortComments($(this))" name="sortcomments">
            <option value="popular">Popular</option>
            <option value="new">New</option>
        </select>

        <div class="mynewcomments"></div>
        <div class="comments panel-body"></div>

    </div>

    @include('layouts.partials.loginModal')

@endsection

@section('scripts')
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/spin.min.js') }}"></script>
    <script src="{{ asset('js/ladda.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    @include('layouts.partials.vote')

    <script>
        $('#stripe').affix({
            offset: {
                top: $('#nav').height()
            }
        });
        // Post comments
        var thread = {{$thread->id}};

        $('.submitpostbtn').click(function() {
            _this = $(this);
            var l = Ladda.create(this);
            l.start();

            obj = _this.prev();
            comment = obj.val();
            parent = obj.attr('data-parent');

            @if(Auth::check())
                var username = '{{ Auth::user()->username }}';

                data = {'thread': thread, 'parent': parent, 'comment': comment, 'api_token': '{{Auth::user()->api_token}}'};

                $.post( "/api/comments/add", data, function( res ) {
                    if (res.warning) {
                        $('.errors').empty().append('<span>'+ res.warning +'</span>');
                    }
                    if (res.status === 'success') {
                        $('.errors').empty();
                        ago = moment(res.post.created_at).utcOffset('+0400').format('YYYY-MM-DD HH:mm')
                        ago = moment(ago).fromNow();
                        $('.mynewcomments').append(
                            '        <div class="panel comment" id="post_panel_'+res.post.id+'">' +
                            '            <div class="panel-body">' +
                            '                <div class="row">' +
                            '                    <div style="width: 40px; margin-top: -5px;" class="votes col-xs-2 col-sm-1">' +
                            '                        <div style="margin-left: 20px;" class="wrap">' +
                            '                            <div style="margin-bottom: -15px; font-size: 20px;" class="row stack">' +
                            '                                <a style="color: inherit;" href="javascript:votepost('+res.post.id+', `up`);"><i id="'+ res.post.id +'_up_post" data-voted="no" data-vote="up" data-post="'+res.post.id+'" class="fa fa-sort-asc"></i></a>' +
                            '                            </div>' +
                            '                            <div class="row stack">' +
                            '                                <span id="'+res.post.id+'_counter_post" class="stack">'+res.post.score+'</span>' +
                            '                            </div>' +
                            '                            <div style="margin-top: -15px; font-size: 20px;" class="row stack">' +
                            '                                <a style="color: inherit;" href="javascript:votepost('+res.post.id+', `down`);"><i id="'+res.post.id+'_down_post" data-voted="no" data-vote="down" data-thread="'+ res.post.id +'" class="fa fa-sort-desc stack"></i></a>' +
                            '                            </div>' +
                            '                        </div>' +
                            '                    </div>' +
                            '                    <div class="col-xs-10 col-sm11">' +
                            '                        <span><a href="/u/'+ username +'">'+username+'</a> '+ago+'</span>' +
                            '                        <p>'+res.post.comment.replace(/(?:\r\n|\r|\n)/g, '<br />')+'</p>' +
                            '                        <div class="linkwrapper"><a style="color: grey;" href="javascript:reply('+res.post.id+');">Reply</a></div>' +
                            '                        <div id="comment_box_app_'+res.post.id+'"></div>' +
                            '                    </div>' +
                            '                </div>' +
                            '            </div>' +
                            '        </div>'
                        );
                    }
                    l.stop();
                    obj.val('');
                 });
            @else
                $('#loginModal').modal('show');
                $('#loginModalMessage').text('to comment');
                l.stop();
            @endif
        });
    </script>

    <script>

        // Load comments
        var url = '/api/comments/load';

        var page = 1;
        loadcomments(page, 'popular');

        function loadcomments(page, sort) {
            load_comments = $('#load_more_comments');
            if (load_comments.length > 0) {
                document.getElementById('load_more_comments').remove();
            }
            @if(Auth::check())
                data = {'thread': thread, 'sort': sort, 'page': page, 'api_token': '{{Auth::user()->api_token}}'};
            @else
                data = {'thread': thread, 'sort': sort, 'page': page};
            @endif

            $.post(url, data, function (res) {
                if (res.error) {
                    console.log("an error occured while loading the posts");
                    return;
                }
                posts = res.posts;
                for (var i = 0; i < posts.length; i++) {
                    post = posts[i];
                    if (!post.parent_id) {
                        created = moment(post.created_at).utcOffset('+0400').format('YYYY-MM-DD HH:mm');
                        created = moment(created);
                        ago = created.fromNow();
                        if (post.score > 1000) {
                            format_score = numeral(post.score).format('0.0a');
                        } else {
                            format_score = post.score;
                        }

                        $('.comments').append(
                            '        <div style="margin-top: -10px;" class="panel comment row" id="post_panel_' + post.id + '">' +
                            '            <div class="panel-body">' +
                            '                <div class="row">' +
                            '                    <div style="width: 40px; margin-top: -5px;" class="votes col-xs-2 col-sm-1">' +
                            '                        <div style="margin-left: 20px;" class="wrap">' +
                            '                            <div style="margin-bottom: -15px; font-size: 20px;" class="row stack">' +
                            '                                <a style="color: inherit;" href="javascript:votepost(' + post.id + ', `up`);"><i id="' + post.id + '_up_post" data-voted="no" data-vote="up" data-post="' + post.id + '" class="fa fa-sort-asc"></i></a>' +
                            '                            </div>' +
                            '                            <div class="row stack">' +
                            '                                <span id="' + post.id + '_counter_post" class="stack">' + format_score + '</span>' +
                            '                            </div>' +
                            '                            <div style="margin-top: -15px; font-size: 20px;" class="row stack">' +
                            '                                <a style="color: inherit;" href="javascript:votepost(' + post.id + ', `down`);"><i id="' + post.id + '_down_post" data-voted="no" data-vote="down" data-thread="' + post.id + '" class="fa fa-sort-desc stack"></i></a>' +
                            '                            </div>' +
                            '                        </div>' +
                            '                    </div>' +
                            '                    <div class="col-xs-10 col-sm11">' +
                            '                        <span><a href="/u/' + post.user_display_name + '">' + post.user_display_name + '</a> ' + ago + '</span>' +
                            '                        <p>' + post.comment.replace(/(?:\r\n|\r|\n)/g, '<br />') + '</p>' +
                            '                        <div class="linkwrapper"><a style="color: grey;" href="javascript:reply(' + post.id + ');">Reply</a>@if($mod)<a style="margin-left:5px;" href="javascript:deleteComment('+post.id+')">Delete</a>@endif </div>' +
                            '                        <div id="comment_box_app_' + post.id + '"></div>' +
                            '                    </div>' +
                            '                </div>' +
                            '            </div>' +
                            '        </div>'
                        );
                    }
                }

                // load replies

                // sort posts
                posts.sort(function (a, b) {
                    return a.parent_id - b.parent_id;
                });

                for (var i = 0; i < posts.length; i++) {
                    post = posts[i];
                    if (post.parent_id !== null) {
                        to_append = $('#post_panel_' + post.parent_id);
                        created = moment(post.created_at).utcOffset('+0400').format('YYYY-MM-DD HH:mm');
                        created = moment(created);
                        ago = created.fromNow();
                        if (post.score > 1000) {
                            format_score = numeral(post.score).format('0.0a');
                        } else {
                            format_score = post.score;
                        }

                        to_append.append('' +
                            '                <div id="post_panel_' + post.id + '" style="margin-left: 20px; width:95%; min-width: 400px;" class="col-xs-12">' +
                            '                    <div style="width: 40px; margin-top: -5px;" class="votes col-xs-2 col-sm-1">' +
                            '                        <div style="margin-left: 20px;" class="wrap">' +
                            '                            <div style="margin-bottom: -15px; font-size: 20px;" class="row stack">' +
                            '                                <a style="color: inherit;" href="javascript:votepost(' + post.id + ', `up`);"><i id="' + post.id + '_up_post" data-voted="no" data-vote="up" data-post="' + post.id + '" class="fa fa-sort-asc"></i></a>' +
                            '                            </div>' +
                            '                            <div class="row stack">' +
                            '                                <span id="' + post.id + '_counter_post" class="stack">' + format_score + '</span>' +
                            '                            </div>' +
                            '                            <div style="margin-top: -15px; font-size: 20px;" class="row stack">' +
                            '                                <a style="color: inherit;" href="javascript:votepost(' + post.id + ', `down`);"><i id="' + post.id + '_down_post" data-voted="no" data-vote="down" data-thread="' + post.id + '" class="fa fa-sort-desc stack"></i></a>' +
                            '                            </div>' +
                            '                        </div>' +
                            '                    </div>' +
                            '                    <div class="col-xs-10 col-sm11">' +
                            '                        <span><a href="/u/' + post.user_display_name + '">' + post.user_display_name + '</a> ' + ago + '</span>' +
                            '                        <p>' + post.comment.replace(/(?:\r\n|\r|\n)/g, '<br />') + '</p>' +
                            '                        <div style="margin-bottom:3px;" class="linkwrapper"><a style="color: grey;" href="javascript:reply(' + post.id + ');">Reply</a> @if($mod)<a style="margin-left:5px;" href="javascript:deleteComment(\'+post.id+\')">Delete</a>@endif </div>' +
                            '                        <div id="comment_box_app_' + post.id + '"></div>' +
                            '                    </div>' +
                            '                </div>'
                        );
                    }
                }

                if (res.upvotes) {
                    for (var i = 0; i < res.upvotes.length; i++) {
                        upvote = res.upvotes[i];
                        if (upvote.vote === 1) {
                            $('#' + upvote.post_id + '_up_post').css('color', '#4CAF50').attr('data-voted', 'yes');
                        } else {
                            $('#' + upvote.post_id + '_down_post').css('color', '#F44336').attr('data-voted', 'yes');
                        }
                    }
                }

                page++;
                if (posts.length > 199) {
                    $('.comments').append('<a id="load_more_comments" href="javascript:loadcomments(' + page + ');">Load more comments</a>');
                }
            });
        }

        function reply(id) {
            @if(Auth::check())
//                _this = $('#post_panel_' + id);
                _this = $('#comment_box_app_' + id);

                if ($('#comment_box_' + id).length > 0) {
                    return; // Commentbox already collapsed
                }
                _this.append(
                    '         <div class="commentbox" id="comment_box_'+id+'">' +
                    '                <div style="width: 300px;" class="panel-body">' +
                    '                        <textarea placeholder="comment" class="form-control" id="reply_text_'+id+'" cols="30" rows="3"></textarea>' +
                    '                        <span style="color: red;" id="comment_box_alert_'+id+'"></span>' +
                    '                        <a id="post_reply_btn_'+id+'" href="javascript:submit_reply('+id+')" style="margin-top: 5px;" class="btn btn-primary submitpostbtn pull-right ladda-button xd" data-style="slide-up">Post comment</a>' +
                    '                        <a href="javascript:cancel_reply('+id+');" style="margin-top: 5px; margin-right: 5px;" class="btn btn-primary submitpostbtn pull-right ladda-button" data-style="slide-up">Cancel</a>' +
                    '                </div>' +
                    '            </div>');
            @else
                $('#loginModal').modal('show');
                $('#loginModalMessage').text('to reply');
            @endif
        }

        function sortComments(_this) {
            val = _this.val();
            $('.comments').empty();
            if (val === 'new') {
                loadcomments(1, 'new');
            } else {
                loadcomments(1, 'popular');
            }
        }

        @if(Auth::check())

        function cancel_reply(id) {
            _this = $('#comment_box_' + id).remove();
        }

        function submit_reply(id) {
            button = document.getElementById('post_reply_btn_'+id);
            var l = Ladda.create(button);
            l.start();

            comment = $('#reply_text_' + id).val();
            data = {'thread': thread, 'comment': comment, 'parent': id, 'api_token': '{{Auth::user()->api_token}}'};
            $.post( '/api/comments/add', data, function(res) {
                var username = '{{ Auth::user()->username }}';

                if (res.warning) {
                    $('#comment_box_alert_' + id).empty().append('<span>'+ res.warning +'</span>');
                    l.stop();
                } else {
                    to_append = $('#post_panel_' + id);
                    created = moment(res.post.created_at).utcOffset('+0400').format('YYYY-MM-DD HH:mm');
                    created = moment(created);
                    ago = created.fromNow();
                    to_append.append('' +
                        '                <div id="post_panel_' + res.post.id + '" style="margin-left: 20px; width:95%; min-width: 400px;" class="col-xs-12">' +
                        '                    <div style="width: 40px; margin-top: -5px;" class="votes col-xs-2 col-sm-1">' +
                        '                        <div style="margin-left: 20px;" class="wrap">' +
                        '                            <div style="margin-bottom: -15px; font-size: 20px;" class="row stack">' +
                        '                                <a style="color: inherit;" href="javascript:votepost(' + res.post.id + ', `up`);"><i id="' + res.post.id + '_up_post" data-voted="no" data-vote="up" data-post="' + res.post.id + '" class="fa fa-sort-asc"></i></a>' +
                        '                            </div>' +
                        '                            <div class="row stack">' +
                        '                                <span id="' + res.post.id + '_counter_post" class="stack">' + res.post.score + '</span>' +
                        '                            </div>' +
                        '                            <div style="margin-top: -15px; font-size: 20px;" class="row stack">' +
                        '                                <a style="color: inherit;" href="javascript:votepost(' + res.post.id + ', `down`);"><i id="' + res.post.id + '_down_post" data-voted="no" data-vote="down" data-thread="' + res.post.id + '" class="fa fa-sort-desc stack"></i></a>' +
                        '                            </div>' +
                        '                        </div>' +
                        '                    </div>' +
                        '                    <div class="col-xs-10 col-sm11">' +
                        '                        <span><a href="/u/' + username + '">' + username + '</a> ' + ago + '</span>' +
                        '                        <p>' + res.post.comment.replace(/(?:\r\n|\r|\n)/g, '<br />') + '</p>' +
                        '                        <div style="margin-bottom:3px;" class="linkwrapper"><a style="color: grey;" href="javascript:reply(' + res.post.id + ');">Reply</a></div>' +
                        '                        <div id="comment_box_app_' + res.post.id + '"></div>' +
                        '                    </div>' +
                        '                </div>'
                    );
                    l.stop();
                    $('#comment_box_' + id).remove();
                }
            });
        }

        @endif

        @if($mod)
            function deleteThread() {
                if (confirm("Are you sure you want to delete this thread?") == true) {
                    data = { api_token: '{{Auth::user()->api_token}}'};
                    $.post('/api/thread/delete/'+'{{$thread->code}}', data, function(res) {
                       if (res.status == 'error') {
                            alert(res.message);
                       } else {
                           alert("Thread removed");
                           location.reload();
                       }
                    });
                }
            }
            function deleteComment(id) {
                if (confirm("Are you rusre you want to delete this comment?") == true) {
                    data = { api_token: '{{Auth::user()->api_token}}'};
                    $.post('/api/comment/delete/'+ id, data, function(res) {
                        if (res.status == 'error') {
                            alert(res.message);
                        } else {
                            alert("Comment removed");
                            location.reload();
                        }
                    });
                }
            }
        @endif

    </script>

    <script>
        // Format the counter
        $('.count').each(function() {
            _this = $(this);
            if (_this.text() > 1000) {
                _this.text(numeral(_this.text()).format('0.0a'));
            }
        });
    </script>

@endsection
