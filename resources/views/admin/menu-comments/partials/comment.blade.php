@php
    $commentAuthor = $comment->is_admin
        ? 'Csárda'
        : ($comment->user
            ? trim($comment->user->first_name . ' ' . $comment->user->last_name)
            : __('messages.unknown_user'));
    $parentAuthor = isset($parentComment)
        ? ($parentComment->is_admin
            ? 'Csárda'
            : ($parentComment->user
                ? trim($parentComment->user->first_name . ' ' . $parentComment->user->last_name)
                : __('messages.unknown_user')))
        : null;
@endphp
<div class="review-comment-node">
    @if (isset($parentComment))
        <div class="review-reply-context">
            <span class="review-reply-context-label">
                {{ __('messages.replying_to') }}
            </span>
            <strong>{{ $parentAuthor }}</strong>
            <p>{{ \Illuminate\Support\Str::limit($parentComment->body, 180) }}</p>
        </div>
    @endif
    <div class="review-message {{ $comment->is_admin ? 'review-admin-message' : 'review-user-message' }}">
        <div class="review-message-header">
            <strong>
                @if ($comment->is_admin)
                    Csárda
                    @if ($comment->user)
                        — {{ trim($comment->user->first_name . ' ' . $comment->user->last_name) }}
                    @endif
                @else
                    {{ $commentAuthor }}
                @endif
            </strong>
            <time>
                {{ $comment->created_at->locale(app()->getLocale())->translatedFormat($dateFormat) }}
            </time>
        </div>
        <div class="review-message-body">{{ $comment->body }}</div>
        <form method="POST" action="{{ route('admin.menu-comments.destroy', $comment) }}" class="review-delete-form">
            @csrf
            @method('DELETE')
            <button type="submit" class="review-delete-button">
                {{ __('messages.delete_comment') }}
            </button>
        </form>
        <form method="POST" action="{{ route('admin.menu-comments.reply', $comment) }}" class="review-reply-form">
            @csrf
            <label for="reply-{{ $comment->id }}">
                {{ __('messages.admin_reply_label') }}
            </label>
            @error('body', 'reply_' . $comment->id)
                <div class="text-danger" role="alert">
                    {{ $message }}
                </div>
            @enderror
            <textarea id="reply-{{ $comment->id }}" name="body" rows="3" minlength="5" maxlength="2000" required>{{ session('reply_body_' . $comment->id, '') }}</textarea>
            <button type="submit" class="admin-add-button">
                {{ __('messages.save_admin_reply') }}
            </button>
        </form>
    </div>
    @if ($comment->replies->isNotEmpty())
        <div class="review-message-children">
            @foreach ($comment->replies as $reply)
                @include('admin.menu-comments.partials.comment', [
                    'comment' => $reply,
                    'dateFormat' => $dateFormat,
                ])
            @endforeach
        </div>
    @endif
</div>
