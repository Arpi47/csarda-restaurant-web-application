<div class="review-message {{ $comment->is_admin ? 'review-admin-message' : 'review-user-message' }}">
    <div class="review-message-header">
        <strong>
            @if ($comment->is_admin)
                Csárda
                @if ($comment->user)
                    — {{ trim($comment->user->first_name . ' ' . $comment->user->last_name) }}
                @endif
            @else
                {{ $comment->user
                    ? trim($comment->user->first_name . ' ' . $comment->user->last_name)
                    : __('messages.unknown_user') }}
            @endif
        </strong>
        <time>
            {{ $comment->created_at->locale(app()->getLocale())->translatedFormat($dateFormat) }}
        </time>
    </div>
    <div class="review-message-body">{{ $comment->body }}</div>
    <form method="POST" action="{{ route('admin.menu-reviews.destroy', $comment) }}" class="review-delete-form">
        @csrf
        @method('DELETE')
        <button type="submit" class="review-delete-button">
            {{ __('messages.delete_review') }}
        </button>
    </form>
    <form method="POST" action="{{ route('admin.menu-reviews.reply', $comment) }}" class="review-reply-form">
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
            @include('admin.menu-reviews.partials.comment', [
                'comment' => $reply,
                'dateFormat' => $dateFormat,
            ])
        @endforeach
    </div>
@endif
