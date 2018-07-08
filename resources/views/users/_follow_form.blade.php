@if($user->id !== Auth::user()->id)
    <div id="follow_form">
    @if(Auth::user()->isFollowing($user->id))
        {{--如果是已经关注了的用户,则显示"取消关注"--}}
        <form action="{{ route('followers.destroy', $user->id) }}" method="post">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button type="submit" class="btn btn-sm">取消关注</button>
        </form>
    @else
        {{-- 显示"关注"按钮 --}}
        <form action="{{ route('followers.store', $user->id) }}" method="post">
            {{ csrf_field() }}
            <button type="submit" class="btn btn-sm btn-primary">关注</button>
        </form>
    @endif
    </div>
@endif