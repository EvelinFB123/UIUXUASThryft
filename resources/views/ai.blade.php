@extends('layouts.app')

@section('hide_footer')
@endsection

@section('content')

@auth
<div class="flex items-center justify-between p-4 border-b bg-white sticky top-20 z-50">
  <div class="flex items-center space-x-3">
    <a href="{{ url()->previous() }}" class="text-yellow-500">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
           viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round"
              stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </a>
    <img src="{{ asset('images/user.png') }}" alt="Seller" class="w-8 h-8 rounded-full">
    <h2 class="text-lg font-semibold">Chat with Thryft</h2>
  </div>
</div>

<div class="p-4 space-y-4 overflow-y-auto h-[70vh] bg-gray-50" id="chat-messages">
  @foreach ($messages as $msg)
    @if ($msg->is_user)
      <div class="flex justify-end">
        <div class="max-w-xs p-3 rounded-lg bg-yellow-500 text-white">
          <p class="text-sm">{{ $msg->message }}</p>
          <span class="block text-[10px] mt-1 opacity-80">{{ $msg->created_at->format('H:i') }}</span>
        </div>
      </div>
    @else
      <div class="flex justify-start">
        <div class="max-w-xs p-3 rounded-lg bg-white border">
          <p class="text-sm">{{ $msg->message }}</p>
          <span class="block text-[10px] mt-1 opacity-70">{{ $msg->created_at->format('H:i') }}</span>
        </div>
      </div>
    @endif
  @endforeach
</div>


<form action="#" method="POST" class="fixed bottom-0 left-0 right-0 p-3 bg-white border-t flex space-x-2">
  <input id="input" type="text" name="message" placeholder="Type a message..." required
         class="flex-1 border rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
  <button id="button-submit" type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-full hover:bg-yellow-600 transition">
    Send
  </button>
</form>

@endauth

<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script>
  function getCurrentTime() {
    const now = new Date();
    // Pastikan selalu dua digit (01, 02, ...)
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    return `${h}:${m}`;
  }

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $(document).ready(function() {
    // Scroll ke bawah saat halaman dimuat
    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
  });

  $('#button-submit').on('click', function (e) {
    e.preventDefault();

    let value = $('#input').val();

    if (value.trim() === '') {
      return;
    }

    // Simpan waktu saat pengirim mengirim pesan
    const userMessageTime = getCurrentTime();

    // Tampilkan pesan pengguna
    $('#chat-messages').append(`
      <div class="flex justify-end">
        <div class="max-w-xs p-3 rounded-lg bg-yellow-500 text-white">
          <p class="text-sm">${value}</p>
          <span class="block text-[10px] mt-1 opacity-80">${userMessageTime}</span>
        </div>
      </div>
    `);

    $('#input').val('');
    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);

    $.ajax({
      type: 'POST',
      url: '{{ route("chat.send") }}',
      data: {
        input: value
      },
      success: function (data) {
        // Gunakan waktu real-time saat menerima balasan
        const replyTime = getCurrentTime();
        $('#chat-messages').append(`
          <div class="flex justify-start">
            <div class="max-w-xs p-3 rounded-lg bg-white border">
              <p class="text-sm">${data.reply}</p>
              <span class="block text-[10px] mt-1 opacity-70">${replyTime}</span>
            </div>
          </div>
        `);

        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
      },
      error: function () {
        // Gunakan waktu real-time untuk pesan error
        const errorTime = getCurrentTime();
        $('#chat-messages').append(`
          <div class="flex justify-start">
            <div class="max-w-xs p-3 rounded-lg bg-white border">
              <p class="text-sm">Maaf, terjadi kesalahan saat mengirim pesan.</p>
              <span class="block text-[10px] mt-1 opacity-70">${errorTime}</span>
            </div>
          </div>
        `);

        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
      }
    });
  });
</script>

@endsection