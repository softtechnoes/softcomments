@inject('markdown', 'Parsedown')
@php($markdown->setSafeMode(true))

@if(isset($reply) && $reply === true)
  <div id="comment-{{ $comment->id }}" class="media">
@else
  <li id="comment-{{ $comment->id }}" class="media">
@endif

    <div class="row">
        <div class="col-md-1">
            @if($comment->commenter->pic==NULL)
                <img class="img-circle" src="{{asset('storage/users/img/no_avatar.jpg')}}" width="50" alt="{{ $comment->commenter->name ?? $comment->guest_name }} Avatar">
            @else
                <img class="img-circle" src="{{asset('storage/users/img/'.$comment->commenter->pic)}}" width="50" alt="{{ $comment->commenter->name ?? $comment->guest_name }} Avatar">
            @endif
        </div>
    <div class="col-md-8" style="margin-top:8px;" ><h5 id="display_comment_{{$comment->id}}">{!! $markdown->line($comment->comment) !!}</h5></div>
    <div class="col-md-3">
        <div style="margin-top:8px;">
            @can('reply-to-comment', $comment)  
                <button data-toggle="modal" data-target="#reply-modal-{{ $comment->id }}" class="btn btn-sm btn-link text-uppercase">{!!Lang::get('site_lang.reply')!!}</button>
            @endcan
            @can('edit-comment', $comment)
                <button data-toggle="modal" data-target="#comment-modal-{{ $comment->id }}" class="btn btn-sm btn-link text-uppercase">{!!Lang::get('site_lang.edit')!!}</button>
            @endcan
            @can('delete-comment', $comment)
                {{-- <a href="{{ url('comments/' . $comment->id) }}" onclick="event.preventDefault();document.getElementById('comment-delete-form-{{ $comment->id }}').submit();" class="btn btn-sm btn-link text-danger text-uppercase">{!!Lang::get('site_lang.delete')!!}</a>
                <form id="comment-delete-form-{{ $comment->id }}" action="{{ url('comments/' . $comment->id) }}" method="POST" style="display: none;">
                    @method('DELETE')
                    @csrf
                </form> --}}
                <button onclick="deleteComment(this)" value="{{$comment->id}}" class="btn btn-sm btn-link text-danger text-uppercase">{!!Lang::get('site_lang.delete')!!}</button>
            @endcan
        </div>
    </div>
    </div>
    <div class="media-body">
        <span class="mt-0 mb-1" class="text-muted">{{ $comment->commenter->name ?? $comment->guest_name }} <small class="text-muted">- {{ $comment->created_at->diffForHumans() }}</small></span>
        @can('edit-comment', $comment)
            <div class="modal fade" id="comment-modal-{{ $comment->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        {{-- <form method="POST" action="{{ url('comments/' . $comment->id) }}"> --}}
                        
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Comment</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="message">Update your message here:</label>
                                    <textarea required class="form-control" id="comment-update-{{ $comment->id }}" name="message" rows="3">{{ $comment->comment }}</textarea>
                                    <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">{!!Lang::get('site_lang.cancel')!!}</button>
                                <button type="button" onclick='editComment(this)' value="{{ $comment->id }}" id="update_comment-{{ $comment->id }}" class="btn btn-sm btn-outline-success text-uppercase">Update</button>
                            </div>
                        {{-- </form> --}}
                    </div>
                </div>
            </div>
        @endcan

        @can('reply-to-comment', $comment)
            <div class="modal fade" id="reply-modal-{{ $comment->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        {{-- <form method="POST" action="{{ url('comments/' . $comment->id) }}"> --}}
                            {{-- @csrf --}}
                            <div class="modal-header">
                                <h5 class="modal-title">{!!Lang::get('site_lang.reply_message')!!}</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="message">{!!Lang::get('site_lang.enter_message')!!}:</label>
                                    <textarea required class="form-control" id="replied_comment_{{$comment->id}}" name="message" rows="3"></textarea>
                                    <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> {!!Lang::get('site_lang.markdown_cheatsheet')!!}.</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">{!!Lang::get('site_lang.cancel')!!}</button>
                                {{-- <button type="button" class="btn btn-sm btn-outline-success text-uppercase">{!!Lang::get('site_lang.reply')!!}</button> --}}
                                <button type="button" onclick='replyComment(this)' value="{{ $comment->id }}" id="reply_comment-{{ $comment->id }}" class="btn btn-sm btn-outline-success text-uppercase">{!!Lang::get('site_lang.reply')!!}</button>
                            </div>
                        {{-- </form> --}}
                    </div>
                </div>
            </div>
        @endcan

        <br />{{-- Margin bottom --}}

        {{-- Recursion for children --}}
        @if($grouped_comments->has($comment->id))
            @foreach($grouped_comments[$comment->id] as $child)
                @include('comments::_comment', [
                    'comment' => $child,
                    'reply' => true,
                    'grouped_comments' => $grouped_comments
                ])
            @endforeach
        @endif

    </div>
@if(isset($reply) && $reply === true)
  </div>
@else
  </li>
@endif
