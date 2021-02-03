@inject('markdown', 'Parsedown')
@php($markdown->setSafeMode(true))

<div class="card">
    <div class="card-body">
        @if($errors->has('commentable_type'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->get('commentable_type') }}
            </div>
        @endif
        @if($errors->has('commentable_id'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->get('commentable_id') }}
            </div>
        @endif
        {{-- <form method="POST" action="{{ url('comments') }}"> --}}
            {{-- @csrf --}}
            @honeypot
            <input type="hidden" name="commentable_type" value="\{{ get_class($model) }}" />
            <input type="hidden" name="commentable_id" id="commentable_id" value="{{ $model->id }}" />

            {{-- Guest commenting --}}
            @if(isset($guest_commenting) and $guest_commenting == true)
                <div class="form-group">
                    <label for="message">Enter your name here:</label>
                    <input type="text" class="form-control @if($errors->has('guest_name')) is-invalid @endif" name="guest_name" />
                    @error('guest_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="message">Enter your email here:</label>
                    <input type="email" class="form-control @if($errors->has('guest_email')) is-invalid @endif" name="guest_email" />
                    @error('guest_email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif

            <div class="form-group">
                <label for="message">{!!Lang::get('site_lang.enter_message')!!}:</label>
                <textarea class="comment_text form-control @if($errors->has('message')) is-invalid @endif" name="message" id="message" rows="3"></textarea>
                <div class="invalid-feedback">
                    {!!Lang::get('site_lang.message_required')!!}.
                </div>
                <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> {!!Lang::get('site_lang.markdown_cheatsheet')!!}.</small>
            </div>
            <button type="button" id="create_comment" class="btn btn-sm btn-outline-success text-uppercase">{!!Lang::get('site_lang.submit_message')!!}</button>
        {{-- </form> --}}
    </div>
</div>
<br />
@push('js')
<script>
    $("#create_comment").click(function(){
        var commentable_type =$('[name=commentable_type]').val();
        var commentable_id =$('[name=commentable_id]').val(); 
        var guest_name =$('[name=guest_name]').val();
        var guest_email =$('[name=guest_email]').val();  
        var message =$('#message').val();

        $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
            type:"POST",
            url:"{{url('comments')}}",
            data:{
            'commentable_type': commentable_type,
            'commentable_id': commentable_id,
            'guest_name': guest_name,
            'guest_email': guest_email,
            'message': message,
            }, 
            success:function(data){ 
                console.log(data);
                $( ".comment_text" ).val('');
                
            var str = '<li id="comment-'+data.id+'" class="media"><div class="row"><div class="col-md-1">   <img class="img-circle" src="{{ url("storage/users/img") }}/'+data['commenter'].pic+'" width="50" alt="Admin Avatar">   </div><div class="col-md-8" style="margin-top:8px;"><h5>'+data.comment+'</h5></div><div class="col-md-3">    <div style="margin-top:8px;">                                    <button data-toggle="modal" data-target="#comment-modal-'+data.id+'" class="btn btn-sm btn-link text-uppercase">Éditer</button>  <a href="http://pti.test:8000/comments/'+data.id+'" onclick="event.preventDefault();document.getElementById("comment-delete-form-1").submit();" class="btn btn-sm btn-link text-danger text-uppercase">Supprimer</a>     <form id="comment-delete-form-'+data.id+'" action="http://pti.test:8000/comments/'+data.id+'" method="POST" style="display: none;">           <input type="hidden" name="_method" value="DELETE"> <input type="hidden" name="_token" value="LjXR3zzA4FXcsBf4lZhvvTrm7SZ60mlcR07Dc470">                </form> </div></div></div><div class="media-body">    <span class="mt-0 mb-1">Admin <small class="text-muted">- il y a 3 jours</small></span>                <div class="modal fade" id="comment-modal-1" tabindex="-1" role="dialog">      <div class="modal-dialog" role="document">                <div class="modal-content">                    <form method="POST" action="http://pti.test:8000/comments/'+data.id+'">                        <input type="hidden" name="_method" value="PUT">                            <input type="hidden" name="_token" value="LjXR3zzA4FXcsBf4lZhvvTrm7SZ60mlcR07Dc470">                            <div class="modal-header">                            <h5 class="modal-title">Edit Comment</h5>                            <button type="button" class="close" data-dismiss="modal">                            <span>×</span>                            </button>                        </div>                        <div class="modal-body">                            <div class="form-group">                                <label for="message">Update your message here:</label>                                <textarea required="" class="form-control" name="message" rows="3">test</textarea>                                <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small>                            </div>                        </div>                        <div class="modal-footer">                            <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">Annuler</button>                            <button type="submit" class="btn btn-sm btn-outline-success text-uppercase">Update</button>                        </div>                    </form>                </div>            </div>        </div> <br>   </div></li>';

            var edit_modal='@can("edit-comment", $comment)            <div class="modal fade" id="comment-modal-'+data.id+'" tabindex="-1" role="dialog">                <div class="modal-dialog" role="document">                    <div class="modal-content">                        <form method="POST" action="{{ url("comments/'+data.id+'") }}">                            @method("PUT")                     @csrf                            <div class="modal-header">            <h5 class="modal-title">Edit Comment</h5>                                <button type="button" class="close" data-dismiss="modal">                                <span>&times;</span></button></div><div class="modal-body"><div class="form-group"><label for="message">Update your message here:</label><textarea required class="form-control " name="message" rows="3" id="comment-update-'++data.id'">'+data.comment+'</textarea><small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small> </div></div><div class="modal-footer"><button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">{!!Lang::get("site_lang.cancel")!!}</button><button type="button" onclick="editComment(this)"" value="'+data.id+'" id="update_comment-'+data.id+'" class="btn btn-sm btn-outline-success text-uppercase">Update</button></div></form></div></div></div>@endcan';

            $( ".list-unstyled" ).append( $( str ) );
            $( ".container-fluid" ).append( $( edit_modal ) );
            $('.alert-warning').hide();


            }
        });
    });

    
    </script> 

<script>
    function editComment(value){
    var id=$(value).val();
    var message = $("#comment-update-"+id).val();
    console.log(message);

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
            type:"GET",
            url:"{{url('comments')}}"+'/'+id,
            data:{
            'message':message,
            }, 
            success:function(data){ 
                console.log(data);
                $("#comment-modal-"+data.id).modal('hide');
                $("#display_comment_"+data.id).html(data.comment);

            }
        });
    }

    function deleteComment(value){
    var id=$(value).val();
    console.log(message);

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
            type:"GET",
            url:"{{url('delete-comments')}}"+'/'+id,
            data:{
            'message':id,
            }, 
            success:function(data){ 
                console.log(data);
               $("#comment-"+data.id).remove();
            }
        });
    }
    function replyComment(value){
    var id=$(value).val();
    var message = $("#replied_comment_"+id).val();
    console.log(message);

    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
            type:"POST",
            url:"{{url('reply-comments')}}"+'/'+id,
            data:{
            'message':message,
            }, 
            success:function(data){ 
                console.log(data);
                var str = '<div id="comment-'+data.id+'" class="media"><div class="row"><div class="col-md-1">   <img class="img-circle" src="{{ url("storage/users/img") }}/'+data['commenter'].pic+'" width="50" alt="Admin Avatar">   </div><div class="col-md-8" style="margin-top:8px;"><h5>'+data.comment+'</h5></div><div class="col-md-3">    <div style="margin-top:8px;">                                    <button data-toggle="modal" data-target="#comment-modal-'+data.id+'" class="btn btn-sm btn-link text-uppercase">Éditer</button>  <a href="http://pti.test:8000/comments/'+data.id+'" onclick="event.preventDefault();document.getElementById("comment-delete-form-1").submit();" class="btn btn-sm btn-link text-danger text-uppercase">Supprimer</a>     <form id="comment-delete-form-'+data.id+'" action="http://pti.test:8000/comments/'+data.id+'" method="POST" style="display: none;">           <input type="hidden" name="_method" value="DELETE"> <input type="hidden" name="_token" value="LjXR3zzA4FXcsBf4lZhvvTrm7SZ60mlcR07Dc470">                </form> </div></div></div><div class="media-body">    <span class="mt-0 mb-1">Admin <small class="text-muted">- il y a 3 jours</small></span>                <div class="modal fade" id="comment-modal-1" tabindex="-1" role="dialog">            <div class="modal-dialog" role="document">                <div class="modal-content">                    <form method="POST" action="http://pti.test:8000/comments/'+data.id+'">                        <input type="hidden" name="_method" value="PUT">                            <input type="hidden" name="_token" value="LjXR3zzA4FXcsBf4lZhvvTrm7SZ60mlcR07Dc470">                            <div class="modal-header">                            <h5 class="modal-title">Edit Comment</h5>                            <button type="button" class="close" data-dismiss="modal">                            <span>×</span>                            </button>                        </div>                        <div class="modal-body">                            <div class="form-group">                                <label for="message">Update your message here:</label>                                <textarea required="" class="form-control" name="message" rows="3">test</textarea>                                <small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small>                            </div>                        </div>                        <div class="modal-footer">                            <button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">Annuler</button>                            <button type="submit" class="btn btn-sm btn-outline-success text-uppercase">Update</button>                        </div>                    </form>                </div>            </div>        </div> <br>   </div></div>';

                var edit_modal='@can("edit-comment", $comment)            <div class="modal fade" id="comment-modal-'+data.id+'" tabindex="-1" role="dialog">                <div class="modal-dialog" role="document">                    <div class="modal-content">                        <form method="POST" action="{{ url("comments/'+data.id+'") }}">                            @method("PUT")                     @csrf                            <div class="modal-header">            <h5 class="modal-title">Edit Comment</h5>                                <button type="button" class="close" data-dismiss="modal">                                <span>&times;</span></button></div><div class="modal-body"><div class="form-group"><label for="message">Update your message here:</label><textarea required class="form-control " name="message" rows="3">'+data.comment+'</textarea><small class="form-text text-muted"><a target="_blank" href="https://help.github.com/articles/basic-writing-and-formatting-syntax">Markdown</a> cheatsheet.</small> </div></div><div class="modal-footer"><button type="button" class="btn btn-sm btn-outline-secondary text-uppercase" data-dismiss="modal">{!!Lang::get("site_lang.cancel")!!}</button><button type="button" onclick="editComment(this)"" value="'+data.id+'" id="update_comment-'+data.id+'" class="btn btn-sm btn-outline-success text-uppercase">Update</button></div></form></div></div></div>@endcan ';

                $( "#comment-"+data['parent'].id ).append( $( str ) );
                $( ".container-fluid" ).append( $( edit_modal ) );
                $('#reply-modal-'+data['parent'].id).modal('hide');
            }
        });
    }
</script>
@endpush

